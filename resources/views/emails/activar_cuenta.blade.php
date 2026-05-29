<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .card {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #eef0f5;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #1e293b;
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px 0;
            line-height: 1.6;
        }

        .btn {
            display: block;
            width: 200px;
            margin: 25px auto;
            text-align: center;
            background: #2563eb;
            color: #ffffff !important;
            padding: 12px 20px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 20px;
            border-top: 1px solid #eef0f5;
            padding-top: 15px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="header">
            <h1>¡Bienvenido a D'gala! 👕👔</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            <p>Gracias por registrarte en nuestra plataforma de dotaciones y uniformes empresariales. Para asegurar la validez de tu cuenta y empezar a gestionar tus pedidos, necesitamos que confirmes tu correo electrónico pulsando el botón de abajo:</p>

            <a href="{{ $enlaceActivacion }}" class="btn">Activar mi Cuenta</a>

            <p>Si el botón no funciona, puedes copiar y pegar este enlace en tu navegador:</p>

            
            #esto es para que el enlace se muestre completo aunque sea muy largo, evitando que se corte en la visualización del correo
            <p style="word-break: break-all; color: #2563eb; font-size: 13px;">{!! $enlaceActivacion !!}</p>
        </div>
        <div class="footer">
            <p>Este es un correo automático de auditoría para D'gala Ecommerce. Por favor no lo responda.</p>
        </div>
    </div>
</body>

</html>