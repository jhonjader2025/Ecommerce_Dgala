@extends('layouts.app') {{-- Ajuste esto según el layout principal de su proyecto --}}

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5 text-center">

                    {{-- 🟢 CASO: PAGO APROBADO --}}
                    @if(isset($estadoTransaccion) && $estadoTransaccion === 'APPROVED')
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-2">¡Pago Confirmado!</h2>
                    <p class="text-muted fs-5">Muchas gracias por tu compra en D'gala. Tu pedido ya está siendo procesado por nuestro equipo de confección.</p>

                    {{-- 🟡 CASO: PAGO PENDIENTE (Ej: Efecty o PSE procesando) --}}
                    @elseif(isset($estadoTransaccion) && $estadoTransaccion === 'PENDING')
                    <div class="mb-4">
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="fw-bold text-warning mb-2">Pago en Estado Pendiente</h2>
                    <p class="text-muted fs-5">Tu pago se encuentra en verificación por parte de la entidad bancaria. Te enviaremos un correo apenas cambie el estado.</p>

                    {{-- 🔴 CASO: PAGO RECHAZADO / DECLINADO --}}
                    @else
                    <div class="mb-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="fw-bold text-danger mb-2">Transacción No Exitosa</h2>
                    <p class="text-muted fs-5">El pago fue declinado o rechazado por la pasarela de pagos. No se ha realizado ningún cobro a tu cuenta.</p>
                    @endif

                    {{-- Mensajes de error del sistema si existen --}}
                    @if(isset($error))
                    <div class="alert alert-danger my-3" role="alert">
                        {{ $error }}
                    </div>
                    @endif

                    <hr class="my-4">

                    {{-- 📊 DETALLES DE LA TRANSACCIÓN --}}
                    <div class="text-start bg-light p-4 rounded-3">
                        <h5 class="fw-bold mb-3 text-secondary text-center">Resumen del Pago</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Referencia D'gala:</span>
                            <span class="fw-semibold">{{ $pedido->referencia_pago ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ID Transacción Wompi:</span>
                            <span class="text-truncate ms-3 fw-semibold text-end" style="max-width: 250px;">{{ $transactionId }}</span>
                        </div>
                        @if(isset($montoCentavos))
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Pagado:</span>
                            <span class="fw-bold text-dark">${{ number_format($montoCentavos / 100, 2) }} COP</span>
                        </div>
                        @endif
                        @if(isset($metodoPago))
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Método de Pago:</span>
                            <span class="fw-semibold text-uppercase">{{ $metodoPago }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ url('/') }}" class="btn btn-primary btn-lg px-4 fw-bold shadow-sm">Volver al Inicio</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection