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
     * Crea un nuevo pedido con sus detalles, descuenta stock y genera referencia + firma para Wompi.
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

            // GENERAMOS LA REFERENCIA ÚNICA PARA WOMPI
            $referenciaWompi = 'DGALA-' . time() . '-' . str_pad($validated['user_id'], 4, '0', STR_PAD_LEFT);

            // 3. Creamos la cabecera del Pedido con los nuevos campos de pago
            $pedido = Pedido::create([
                'user_id'           => $validated['user_id'],
                'referencia_pago'   => $referenciaWompi,
                'total'             => $totalPedido,
                'direccion_entrega' => $validated['direccion_entrega'],
                'estado_pago'       => 'pendiente'
            ]);

            // 4. Recorremos los productos del carrito para validar stock y guardar el desglose
            foreach ($validated['items'] as $item) {

                $stockActual = DB::table('lona_tallas')
                    ->where('lona_id', $item['lona_id'])
                    ->where('talla', $item['talla'])
                    ->value('cantidad');

                if (!$stockActual || $stockActual < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stock insuficiente para la lona ID {$item['lona_id']} en talla {$item['talla']}. Disponible: " . ($stockActual ?? 0)
                    ], 400);
                }

                DB::table('lona_tallas')
                    ->where('lona_id', $item['lona_id'])
                    ->where('talla', $item['talla'])
                    ->decrement('cantidad', $item['cantidad']);

                PedidoDetalle::create([
                    'pedido_id'       => $pedido->id,
                    'lona_id'         => $item['lona_id'],
                    'talla'           => $item['talla'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario']
                ]);
            }

            DB::commit();

            $pedido->load('detalles');

            // Forzamos entero puro para evitar decimales extraños en la firma
            $valorEnCentavos = (int) ($totalPedido * 100);
            $moneda = 'COP';
            $llaveIntegridad = env('WOMPI_INTEGRITY_SECRET');

            $cadenaFirma = $referenciaWompi . $valorEnCentavos . $moneda . $llaveIntegridad;
            $firmaWompi = hash('sha256', $cadenaFirma);

            Log::info("--- DIAGNÓSTICO DE FIRMA D'GALA ---");
            Log::info("Referencia: " . $referenciaWompi);
            Log::info("Centavos: " . $valorEnCentavos);
            Log::info("Cadena completa antes de SHA256: " . $cadenaFirma);
            Log::info("Firma generada: " . $firmaWompi);

            return response()->json([
                'status' => 'success',
                'message' => 'Pedido procesado y stock descontado con éxito. Listo para pago.',
                'data' => $pedido,
                'wompi_config' => [
                    'public_key' => env('WOMPI_PUBLIC_KEY'),
                    'reference' => $referenciaWompi,
                    'amount_in_cents' => $valorEnCentavos,
                    'currency' => $moneda,
                    'signature' => $firmaWompi
                ]
            ], 201);
        } catch (\Exception $e) {
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
     */
    public function show(string $id)
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
            'data' => $pedido // 🌟 ¡Corregido aquí! (Antes decía $pedidos)
        ], 200);
    }

    /**
     * Genera una factura en PDF
     */
    public function descargarFactura($id)
    {
        $pedido = Pedido::with(['user', 'detalles'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.factura', compact('pedido'));

        return $pdf->stream("factura-dgala-{$pedido->id}.pdf");
    }
}
