<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\EnviarFacturaMail;
use Illuminate\Support\Facades\Mail;

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

        // =========================================================================
        // 🛡️ 2. [NUEVO] ESCUDO DE SEGURIDAD: VALIDACIÓN DE LA FIRMA DE WOMPI
        // =========================================================================
        $signatureData = $request->input('signature');

        if (!$signatureData || !isset($signatureData['properties']) || !isset($signatureData['checksum'])) {
            Log::warning("Webhook Wompi: Intento de acceso sospechoso sin firma.");
            return response()->json(['message' => 'Firma ausente'], 400);
        }

        // Reconstruimos la cadena de texto uniendo los valores en el orden que exige Wompi
        $concatenatedString = "";
        foreach ($signatureData['properties'] as $property) {
            // Trampa de Wompi: Ellos mandan 'transaction.id', pero en el JSON está dentro de 'data.transaction.id'
            $path = str_starts_with($property, 'transaction.') ? 'data.' . $property : $property;
            $concatenatedString .= $request->input($path);
        }

        // Le concatenamos al final la llave secreta para eventos que configuramos en el .env
        $concatenatedString .= env('WOMPI_EVENTS_SECRET');

        // Generamos nuestro propio hash SHA256 para comparar
        $calculatedChecksum = hash('sha256', $concatenatedString);

        if ($calculatedChecksum !== $signatureData['checksum']) {
            Log::error("Webhook Wompi: ¡ALERTA DE FRAUDE! Las firmas no coinciden. Petición rechazada.");
            return response()->json(['message' => 'Firma inválida'], 401);
        }
        // =========================================================================

        // 3. Validamos que el evento sea de una transacción actualizada
        if ($request->input('event') !== 'transaction.updated') {
            return response()->json(['message' => 'Evento no manejado'], 200);
        }

        // 4. Extraemos la información de la transacción que manda Wompi
        $transaction = $request->input('data.transaction');
        $referenciaWompi = $transaction['reference']; // Ej: DGALA-1716...
        $statusWompi = $transaction['status'];       // APPROVED, DECLINED, REJECTED

        // 5. Buscamos el pedido usando la referencia única
        $pedido = Pedido::with('user')->where('referencia_pago', $referenciaWompi)->first();

        if (!$pedido) {
            Log::warning("Webhook Wompi: No se encontró el pedido con referencia: {$referenciaWompi}");
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        // Si el pedido ya fue aprobado antes, no hacemos nada más (evitamos doble proceso)
        if ($pedido->estado_pago === 'aprobado') {
            return response()->json(['message' => 'El pedido ya estaba aprobado.'], 200);
        }

        // 6. Evaluamos el estado que nos manda la pasarela
        DB::beginTransaction();
        try {
            switch ($statusWompi) {
                case 'APPROVED':
                    $pedido->estado_pago = 'aprobado';

                    // 🚀 ¡AQUÍ SE DISPARA LA MAGIA! Mandamos la factura al correo del cliente
                    Mail::to($pedido->user->email)->send(new EnviarFacturaMail($pedido));

                    Log::info("¡Platica en mano! Pedido ID {$pedido->id} aprobado por Wompi y factura enviada.");
                    break;

                case 'DECLINED':
                    $pedido->estado_pago = 'declinado';
                    // Devolvemos el stock a 'lona_tallas' porque el pago falló
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
