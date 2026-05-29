<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WompiWebhookController extends Controller
{
    /**
     * Recibe la notificación asíncrona de Wompi (Webhook)
     * Ruta: POST /api/webhooks/wompi
     */
    public function handle(Request $request)
    {
        // 1. Logueamos lo que nos manda Wompi por seguridad para revisar en la etapa de desarrollo
        Log::info("Webhook de Wompi recibido:", $request->all());

        // 2. Validamos que el evento sea de una transacción actualizada
        if ($request->input('event') !== 'transaction.updated') {
            return response()->json(['message' => 'Evento no manejado'], 200);
        }

        // 3. Extraemos la información de la transacción que manda Wompi
        $transaction = $request->input('data.transaction');
        $referenciaWompi = $transaction['reference']; // Ej: DGALA-1716...
        $statusWompi = $transaction['status'];       // APPROVED, DECLINED, REJECTED

        // 4. Buscamos el pedido en D'gala usando esa referencia única
        $pedido = Pedido::where('referencia_pago', $referenciaWompi)->first();

        if (!$pedido) {
            Log::warning("Webhook Wompi: No se encontró el pedido con referencia: {$referenciaWompi}");
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        // Si el pedido ya fue aprobado antes, no hacemos nada más (evitamos doble proceso)
        if ($pedido->estado_pago === 'aprobado') {
            return response()->json(['message' => 'El pedido ya estaba aprobado.'], 200);
        }

        // 5. Evaluamos el estado que nos manda la pasarela
        DB::beginTransaction();
        try {
            switch ($statusWompi) {
                case 'APPROVED':
                    $pedido->estado_pago = 'aprobado';

                    // TODO: ¡AQUÍ MÁS ADELANTE DISPARAMOS A MAILGUN PARA ENVIAR EL CORREO CON EL PDF!
                    Log::info("¡Platica en mano! Pedido ID {$pedido->id} aprobado por Wompi.");
                    break;

                case 'DECLINED':
                    $pedido->estado_pago = 'declinado';
                    // Opcional: Aquí se podría devolver el stock a 'lona_tallas' porque el pago falló
                    $this->revertirStock($pedido->id);
                    break;

                case 'REJECTED':
                    $pedido->estado_pago = 'rechazado';
                    $this->revertirStock($pedido->id);
                    break;

                default:
                    $pedido->estado_pago = 'pendiente';
                    break;
            }

            $pedido->save();
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Pedido actualizado con éxito'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error procesando Webhook de Wompi: " . $e->getMessage());
            return response()->json(['message' => 'Error interno al procesar el pago'], 500);
        }
    }

    /**
     * Función auxiliar para devolver el stock si el pago es rechazado o declinado
     */
    private function revertirStock($pedidoId)
    {
        $detalles = DB::table('pedido_detalles')->where('pedido_id', $pedidoId)->get();

        foreach ($detalles as $detalle) {
            DB::table('lona_tallas')
                ->where('lona_id', $detalle->lona_id)
                ->where('talla', $detalle->talla)
                ->increment('cantidad', $detalle->cantidad);
        }
        Log::info("Stock devuelto a lona_tallas para el pedido ID: {$pedidoId} debido a pago fallido.");
    }
}
