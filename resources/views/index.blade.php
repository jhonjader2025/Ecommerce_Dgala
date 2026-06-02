<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D'gala</title>
    <!-- El script oficial del Widget de Wompi -->
    <script type="text/javascript" src="https://checkout.wompi.co/widget.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            max-width: 450px;
            width: 100%;
            border: 1px solid #e2e8f0;
        }

        h1 {
            color: #2563eb;
            font-size: 24px;
            margin-top: 0;
        }

        .producto-info {
            border-bottom: 2px dashed #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .precio {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            margin: 10px 0;
        }

        .btn-pagar {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-pagar:hover {
            background-color: #1d4ed8;
        }

        .info-envio {
            font-size: 13px;
            color: #64748b;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="card">
        <h1> D'gala Ecommerce</h1>
        <p>Continue para proceder a pagar su pedido .</p>

        <div class="producto-info">
            <strong>Combo Uniformes Lona Premium</strong>
            <p class="info-envio">Talla: M | Cantidad: 2</p>
            <p class="precio">$128.000 COP</p>
        </div>

        <p><strong>Dirección de entrega :</strong><br> Calle 50 #10-20, Medellín</p>

        <!-- Botón de fuego -->
        <button class="btn-pagar" onclick="pagarConWompi()">Confirmar y Pagar</button>
    </div>

    <script>
        function pagarConWompi() {
            // 1. Preparamos los datos del carrito tal cual los pide su validación en el store()
            const datosPedido = {
                user_id: 1, // Usuario de prueba de la base de datos
                direccion_entrega: "Calle 50 #10-20, Medellín",
                items: [{
                    lona_id: 1, // Asegúrese de que la lona ID 1 exista en su DB
                    talla: "M",
                    cantidad: 2,
                    precio_unitario: 64000
                }]
            };

            // 2. Le pegamos a la API que creamos en routes/api.php usando fetch nativo
            fetch('/api/pedidos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(datosPedido)
                })
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        // 3. ¡Aquí está la magia! Jalamos la configuración que calculó Laravel
                        const config = res.wompi_config;

                        // 4. Invocamos el Widget original de Wompi en la pantalla
                        var checkout = new WidgetCheckout({
                            currency: config.currency,
                            amountInCents: config.amount_in_cents,
                            reference: config.reference,
                            publicKey: config.public_key,
                            signature: config.signature, // La firma blindada que hicimos en Laravel
                            redirectUrl: 'https://google.com' // Temporalmente lo mandamos a Google al terminar
                        });

                        // 5. Abrimos el pasaporte de pago
                        checkout.open(function(result) {
                            console.log('Transacción finalizada en interfaz: ', result.transaction);
                        });

                    } else {
                        alert('Error en el pedido: ' + res.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Pailas, hubo un problema al conectar con el servidor.');
                });
        }
    </script>

</body>

</html>