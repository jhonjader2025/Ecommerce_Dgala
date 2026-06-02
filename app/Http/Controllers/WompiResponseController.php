<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WompiResponseController extends Controller
{
    public function show(Request $request)
    {
        // 1. Atrapamos el ID de transacción que Wompi nos manda por la URL (?id=XXXX)
        $transactionId = $request->query('id');

        if (!$transactionId) {
            Log::warning("Intento de acceso a la página de resultado sin ID de transacción.");
            return redirect('/')->with('error', 'No se proporcionó un ID de transacción válido.');
        }

        try {
            // 2. Le preguntamos directamente a la API de Wompi el estado real de ese ID
            // Usamos la URL de Sandbox/Pruebas. (Cuando pase a producción cambia a 'production.wompi.co')
            $response = Http::get("https://sandbox.wompi.co/v1/transactions/{$transactionId}");

            if ($response->failed()) {
                throw new \Exception("No se pudo conectar con la API de Wompi.");
            }

            $wompiData = $response->json()['data'];

            // Extraemos la referencia de D'gala (ej: DGALA-1716...) y el estado
            $referenciaWompi = $wompiData['reference'];
            $estadoTransaccion = $wompiData['status']; // APPROVED, DECLINED, REJECTED, PENDING
            $montoCentavos = $wompiData['amount_in_cents'];
            $metodoPago = $wompiData['payment_method_type'] ?? 'No especificado';

            // 3. Buscamos el pedido en nuestra base de datos para mostrar los detalles del uniforme
            $pedido = Pedido::where('referencia_pago', $referenciaWompi)->first();

            if (!$pedido) {
                Log::error("Página Resultado: No se encontró el pedido local para la referencia Wompi: {$referenciaWompi}");
                return view('wompi.resultado', [
                    'error' => 'No se encontró el pedido en nuestro sistema, pero la transacción existe.',
                    'estado' => $estadoTransaccion,
                    'transactionId' => $transactionId
                ]);
            }

            // 4. Retornamos la vista pasándole todas las variables meleras
            return view('wompi.resultado', compact('pedido', 'estadoTransaccion', 'transactionId', 'montoCentavos', 'metodoPago'));
        } catch (\Exception $e) {
            Log::error("Error en WompiResponseController: " . $e->getMessage());
            return view('wompi.resultado', [
                'error' => 'Tuvimos un problema al verificar el estado de tu pago. Por favor, revisa tu correo o contacta a soporte.',
                'estado' => 'ERROR',
                'transactionId' => $transactionId
            ]);
        }
    }
}
