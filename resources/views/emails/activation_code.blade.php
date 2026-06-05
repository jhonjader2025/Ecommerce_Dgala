<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Activa tu cuenta en D'gala</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;">
                    <tr>
                        <td style="background-color: #212529; padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; letter-spacing: 1px;">👔 D'gala</h1>
                            <p style="color: #adb5bd; margin: 5px 0 0 0; font-size: 14px;">Tienda Virtual de Uniformes</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin-top: 0; color: #212529; font-size: 22px;">¡Hola, bienvenido a la familia!</h2>
                            <p style="font-size: 16px; line-height: 1.6; color: #495057;">
                                Estamos muy felices de tenerte con nosotros. Para poder empezar a gestionar tus pedidos y mirar los mejores uniformes, necesitamos que confirmes tu cuenta con el siguiente código de activación:
                            </p>

                            <div style="background-color: #e9ecef; border-left: 5px solid #0d6efd; padding: 20px; text-align: center; margin: 30px 0; border-radius: 4px;">
                                <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #0d6efd;">
                                    {{ $codigo }}
                                </span>
                            </div>

                            <p style="font-size: 14px; line-height: 1.6; color: #6c757d;">
                                Este código es de un solo uso. Si tú no solicitaste este registro, puedes ignorar este correo con total tranquilidad.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #6c757d; border-top: 1px solid #dee2e6;">
                            &copy; {{ date('Y') }} D'gala S.A.S. - Todos los derechos reservados.<br>
                            Proyecto de Tecnología en Análisis y Desarrollo de Software.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>