<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura de Venta - D'gala</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #333;
            line-height: 1.5;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 10px;
            font-size: 14px;
        }

        .top-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            color: #1e293b;
        }

        .text-right {
            text-align: right;
        }

        .info-block {
            margin-bottom: 30px;
        }

        .info-table {
            width: 100%;
        }

        .info-table td {
            width: 50%;
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th {
            background-color: #2563eb;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 13px;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .total-section {
            margin-top: 20px;
            float: right;
            width: 30%;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 5px;
            font-size: 14px;
        }

        .grand-total {
            font-weight: bold;
            color: #2563eb;
            font-size: 16px;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 50px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>

    <div class="invoice-box">
        <table class="top-table">
            <tr>
                <td class="title">D'gala Uniformes</td>
                <td class="text-right">
                    <strong>Factura N°:</strong> #{{ $pedido->id }}<br>
                    <strong>Fecha:</strong> {{ $pedido->created_at->format('d/m/Y') }}<br>
                    <strong>Estado de Pago:</strong> {{ strtoupper($pedido->estado_pago) }}
                </td>
            </tr>
        </table>

        <div class="info-block">
            <table class="info-table">
                <tr>
                    <td>
                        <strong>De:</strong><br>
                        D'gala Ecommerce S.A.S.<br>
                        NIT: 900.123.456-1<br>
                        Medellín, Colombia
                    </td>
                    <td>
                        <strong>Para:</strong><br>
                        {{ $pedido->user->name }}<br>
                        {{ $pedido->user->email }}<br>
                        <strong>Dirección:</strong> {{ $pedido->direccion_entrega }}
                    </td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Lona / Tela</th>
                    <th>Talla</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->detalles as $detalle)
                <tr>
                    <td>Lona Código #{{ $detalle->lona_id }}</td>
                    <td>{{ $detalle->talla }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                    <td>${{ number_format($detalle->cantidad * $detalle->precio_unitario, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <table class="total-table">
                <tr class="grand-total">
                    <td>Total:</td>
                    <td class="text-right">${{ number_format($pedido->total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Gracias por confiar en D'gala para sus dotaciones empresariales. Esto es un soporte digital de compra.
        </div>
    </div>

</body>

</html>