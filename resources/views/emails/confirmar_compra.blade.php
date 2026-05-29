<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>¡Gracias por su compra!</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2 style="color: #2563eb;">¡Hola, {{ $pedido->user->name }}! 🎉</h2>
    <p>Queremos confirmarle que recibimos su pago correctamente a través de Wompi.</p>
    <p>Su pedido <strong>#{{ $pedido->id }}</strong> ya pasó al área de producción y despacho en Medellín.</p>

    <p>En el archivo adjunto de este correo encontrará su **Factura Digital de Venta** con el detalle de las lonas y uniformes adquiridos.</p>

    <br>
    <p>Atentamente,</p>
    <strong>El equipo de D'gala Uniformes</strong>
</body>

</html>