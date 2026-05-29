<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoController extends Controller
{
    /**
     * Crea un nuevo pedido con sus detalles, descuenta stock y genera referencia para Wompi.
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

            // 🌟 GENERAMOS LA REFERENCIA ÚNICA PARA WOMPI
            // Ejemplo de salida: DGALA-1716610000-0001
            $referenciaWompi = 'DGALA-' . time() . '-' . str_pad($validated['user_id'], 4, '0', STR_PAD_LEFT);

            // 3. Creamos la cabecera del Pedido con los nuevos campos de pago
            $pedido = Pedido::create([
                'user_id'           => $validated['user_id'],
                'referencia_pago'   => $referenciaWompi, // <-- Guardamos la referencia para la pasarela
                'total'             => $totalPedido,
                'direccion_entrega' => $validated['direccion_entrega'],
                'estado_pago'       => 'pendiente'       // <-- Nace pendiente hasta que Wompi diga lo contrario
            ]);

            // 4. Recorremos los productos del carrito para validar stock y guardar el desglose
            foreach ($validated['items'] as $item) {

                // 🔍 Buscamos el stock en la tabla pivote 'lona_tallas'
                $stockActual = DB::table('lona_tallas')
                    ->where('lona_id', $item['lona_id'])
                    ->where('talla', $item['talla'])
                    ->value('cantidad');

                // Si no hay suficiente tela, abortamos todo de inmediato
                if (!$stockActual || $stockActual < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stock insuficiente para la lona ID {$item['lona_id']} en talla {$item['talla']}. Disponible: " . ($stockActual ?? 0)
                    ], 400);
                }

                // Descontamos el stock en la tabla lona_tallas
                DB::table('lona_tallas')
                    ->where('lona_id', $item['lona_id'])
                    ->where('talla', $item['talla'])
                    ->decrement('cantidad', $item['cantidad']);

                // Registramos el renglón en el detalle del pedido
                PedidoDetalle::create([
                    'pedido_id'       => $pedido->id,
                    'lona_id'         => $item['lona_id'],
                    'talla'           => $item['talla'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario']
                ]);
            }

            // Si todo salió melo y sin errores, confirmamos cambios en la DB
            DB::commit();

            // Cargamos las relaciones para devolver la respuesta bien completa
            $pedido->load('detalles');

            return response()->json([
                'status' => 'success',
                'message' => 'Pedido procesado y stock descontado con éxito. Listo para pago.',
                'data' => $pedido
            ], 201);
        } catch (\Exception $e) {
            // Si algo falla, echamos todo para atrás de inmediato
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
    public function show(string $id) // <-- Le metimos el tipado estricto para quitar el warning del VS Code
    {
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
    /**
     * Esto es para generar una facura en pdf y descargarla
     * Ruta: GET /api/pedidos/{id}/factura
     */
    public function descargarFactura($id)
    {
        // Cargamos el pedido con el usuario que compró y los detalles de las lonas
        $pedido = Pedido::with(['user', 'detalles'])->findOrFail($id);

        // Pasamos el objeto $pedido a la vista del PDF
        $pdf = Pdf::loadView('pdf.factura', compact('pedido'));

        // 'stream' permite ver el PDF en el navegador. Si quiere descarga directa, use 'download'
        return $pdf->stream("factura-dgala-{$pedido->id}.pdf");
    }
}
