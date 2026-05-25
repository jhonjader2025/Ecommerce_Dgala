<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PedidoController extends Controller
{
    /**
     * Crea un nuevo pedido con sus detalles y descuenta stock.
     * Ruta: POST /api/pedidos
     */
    public function store(Request $request)
    {
        // 1. Validamos la estructura básica del pedido y el carrito (items)
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'direccion_entrega' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.lona_id' => 'required|exists:lonas,id',
            'items.*.talla' => 'required|string|max:10',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        // Iniciamos la transacción para asegurar la integridad de D'gala
        DB::beginTransaction();

        try {
            // 2. Calculamos el total acumulado del pedido
            $totalPedido = 0;
            foreach ($validated['items'] as $item) {
                $totalPedido += $item['cantidad'] * $item['precio_unitario'];
            }

            // 3. Creamos la cabecera del Pedido
            $pedido = Pedido::create([
                'user_id' => $validated['user_id'],
                'total' => $totalPedido,
                'direccion_entrega' => $validated['direccion_entrega'],
                'estado' => 'pendiente'
            ]);

            // 4. Recorremos los productos del carrito para validar stock y guardar el desglose
            foreach ($validated['items'] as $item) {

                // 🔍 OJO: Aquí buscamos el stock en su tabla pivote 'lona_tallas'
                $stockActual = DB::table('lona_tallas')
                    ->where('lona_id', $item['lona_id'])
                    ->where('talla', $item['talla'])
                    ->value('cantidad'); // Asumiendo que la columna se llama 'cantidad' o 'stock'

                // Si no hay suficiente tela o uniformes, abortamos todo de inmediato
                if (!$stockActual || $stockActual < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stock insuficiente para la lona ID {$item['lona_id']} en talla {$item['talla']}. Disponible: " . ($stockActual ?? 0)
                    ], 400);
                }

                //  Descontamos el stock en la tabla lona_tallas
                DB::table('lona_tallas')
                    ->where('lona_id', $item['lona_id'])
                    ->where('talla', $item['talla'])
                    ->decrement('cantidad', $item['cantidad']);

                //  Registramos el renglón en el detalle del pedido
                PedidoDetalle::create([
                    'pedido_id' => $pedido->id,
                    'lona_id' => $item['lona_id'],
                    'talla' => $item['talla'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario']
                ]);
            }

            // Si todo salió melo y sin errores, guardamos los cambios en la DB definitivamente
            DB::commit();

            // Cargamos las relaciones para devolver la respuesta bien completa
            $pedido->load('detalles');

            return response()->json([
                'status' => 'success',
                'message' => 'Pedido procesado y stock descontado con éxito.',
                'data' => $pedido
            ], 201);
        } catch (\Exception $e) {
            // Si algo falla a nivel de servidor o base de datos, deshacemos todo
            DB::rollBack();
            Log::error("Error procesando pedido en D'gala: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo procesar el pedido interno.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Lista todos los pedidos del e-commerce (Para el Admin)
     * Ruta: GET /api/pedidos
     */
    public function index()
    {
        // Traemos los pedidos con la info del usuario que compró
        $pedidos = Pedido::with('usuario:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $pedidos
        ], 200);
    }

    /**
     * Muestra el detalle completo de un solo pedido
     * Ruta: GET /api/pedidos/{id}
     */
    public function show ($id)
    {
        // Buscamos el pedido con sus detalles y la lona que se compró en cada detalle
        $pedido = Pedido::with(['usuario:id,name,email', 'detalles.lona'])
            ->find($id);

        if (!$pedido) {
            return response()->json([
                'status' => 'error',
                'message' => 'El pedido no existe en D\'gala.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $pedido
        ], 200);
    }
    
}
