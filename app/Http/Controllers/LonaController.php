<?php

namespace App\Http\Controllers;

use App\Models\Lona;
use App\Models\LonaTalla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LonaController extends Controller
{
    /**
     * Muestra el listado de lonas del inventario.
     * Ruta: GET /api/lonas (o entorno web)
     */
    public function index()
    {
        // Jalamos las lonas cargando de una vez su dotación padre y sus tallas asociadas (Eager Loading)
        // Esto evita el problema de consultas N+1 en la base de datos.
        $lonas = Lona::with(['dotacion', 'tallas'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $lonas
        ], 200);
    }

    /**
     * Registra una nueva lona y le asigna stock inicial por tallas.
     * Ruta: POST /api/lonas
     */
    public function store(Request $request)
    {
        // 1. Validamos los datos que entran del formulario o cliente HTTP
        $validated = $request->validate([
            'dotacion_id'   => 'required|exists:dotaciones,id',
            'codigo'        => 'required|string|unique:lonas,codigo',
            'tipo_producto' => 'required|string',
            'categoria'     => 'required|string',
            'color'         => 'required|string',
            'tallas_stock'  => 'required|array', // Esperamos un arreglo tipo: ['S' => 10, 'M' => 20]
        ]);

        // 2. Abrimos una Transacción de Base de Datos
        // Si algo falla a mitad de camino, la transacción hace un Rollback y no deja datos basura.
        DB::beginTransaction();

        try {
            // 3. Creamos la cabecera de la lona
            $lona = Lona::create([
                'dotacion_id'   => $validated['dotacion_id'],
                'codigo'        => $validated['codigo'],
                'tipo_producto' => $validated['tipo_producto'],
                'categoria'     => $validated['categoria'],
                'color'         => $validated['color'],
                'estado'        => 'nuevo',
                'activa'        => true,
            ]);

            // 4. Recorremos el arreglo de tallas para insertar el stock
            // ¡OJO! Al hacer estos inserts, se activará el TRIGGER de MySQL en la base de datos
            // actualizando la tabla 'variantes_producto' de forma automática.
            foreach ($validated['tallas_stock'] as $talla => $cantidad) {
                LonaTalla::create([
                    'lona_id'  => $lona->id,
                    'talla'    => $talla,
                    'cantidad' => $cantidad,
                ]);
            }

            // Si todo salió melo, confirmamos los cambios en la DB
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Lona registrada e inventario actualizado correctamente.',
                'data'    => $lona->load('tallas') // Retornamos la lona con sus tallas cargadas
            ], 201);
        } catch (\Exception $e) {
            // Si algo se toteó, borramos cualquier intento de inserción para cuidar la integridad
            DB::rollBack();

            Log::error("Error registrando lona: " . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo registrar la lona.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el detalle completo de una lona específica junto con su bitácora de movimientos.
     * Ruta: GET /api/lonas/{id}
     */
    public function show($id)
    {
        // Buscamos la lona. Si no existe, el método findOrFail lanza un error 404 automático.
        $lona = Lona::with(['dotacion', 'tallas'])->findOrFail($id);

        // Opcional: Si queremos jalar el historial de auditoría de los triggers que creamos
        $historial = DB::table('historial_lonas')
            ->where('lona_id', $lona->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'lona'      => $lona,
                'historial' => $historial
            ]
        ], 200);
    }
}
