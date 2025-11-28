<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contraseña cambiada</title>
  </head>

  <body
    style="
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9fafb;
      margin: 0;
      padding: 0;
      line-height: 1.6;
      color: #374151;
    "
  >
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0">
      <tr>
        <td align="center">
          <table
            width="600"
            cellpadding="0"
            cellspacing="0"
            style="
              background-color: #ffffff;
              border-radius: 12px;
              overflow: hidden;
              box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
              border: 1px solid #e5e7eb;
            "
          >
            <!-- Header con color #c3e3d8 -->
            <tr>
              <td style="background-color: #c3e3d8; padding: 24px 0; text-align: center">
                <img
                  src="https://renova-3q4h.onrender.com/images/LogoLetra.png"
                  alt="RENOVA Logo"
                  style="max-width: 180px; height: auto"
                />
              </td>
            </tr>

            <!-- Content -->
            <tr>
              <td style="padding: 32px; color: #333">
                <h2 style="margin-top: 0; font-size: 22px; color: #d9534f">
                  ¡Tu contraseña ha sido cambiada!
                </h2>

                <p style="font-size: 16px; margin-bottom: 20px;">
                  Hola, {{ explode(' ', $user->name)[0] ?? 'usuario' }} 👋
                </p>

                <p style="font-size: 16px; margin-bottom: 24px;">
                  Te informamos que la contraseña de tu cuenta en <strong>RENOVA</strong> fue actualizada recientemente.
                </p>

                <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 0 6px 6px 0; margin: 24px 0;">
                  <p style="margin: 0; font-size: 15px; color: #92400e;">
                    <strong>¿No realizaste este cambio?</strong><br />
                    Por favor, contacta inmediatamente a nuestro equipo de soporte para proteger tu cuenta.
                  </p>
                </div>

                <p style="font-size: 16px; margin-bottom: 0;">
                  Gracias por ayudarnos a mantener tu cuenta segura.
                </p>
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <td
                style="
                  background-color: #f9fafb;
                  padding: 20px;
                  text-align: center;
                  font-size: 13px;
                  color: #6b7280;
                  border-top: 1px solid #e5e7eb;
                "
              >
                © {{ date('Y') }} RENOVA. Todos los derechos reservados.<br />
                <span style="color: #9ca3af">Tu seguridad es lo primero.</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
