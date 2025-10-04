<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cuenta desactivada</title>
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
            <!-- Header -->
            <tr >
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
                <h2 style="margin-top: 0; font-size: 22px; color: #1f2937">
                  Hola, {{ explode(' ', $user->name)[0] ?? 'usuario' }} 👋
                </h2>

                <p style="font-size: 16px; margin-bottom: 20px;">
                  Lamentamos informarte que tu cuenta en <strong>RENOVA</strong> ha sido desactivada temporalmente.
                </p>

                @if($justification)
                  <div style="background-color: #fef9f9; border-left: 4px solid #ef4444; padding: 14px 16px; margin: 20px 0; border-radius: 0 6px 6px 0;">
                    <p style="margin: 0; font-style: italic; color: #dc2626; font-size: 15px;">
                      <strong>Motivo:</strong> {{ $justification }}
                    </p>
                  </div>
                @endif

                <p style="font-size: 16px; margin-bottom: 20px;">
                  Sabemos que esto puede ser frustrante, y queremos ayudarte. Si crees que se trata de un error o deseas más información, 
                  no dudes en ponerte en contacto con nuestro equipo de soporte.
                </p>

                <p style="font-size: 16px; margin-bottom: 24px;">
                  Estamos aquí para escucharte y encontrar una solución juntos.
                </p>

                <div style="text-align: center; margin-top: 24px;">
                  <a
                    href="mailto:{{ config('mail.from.address') }}"
                    style="
                      display: inline-block;
                      background-color: #10b981;
                      color: white;
                      text-decoration: none;
                      padding: 10px 24px;
                      border-radius: 6px;
                      font-weight: 600;
                      font-size: 16px;
                    "
                  >
                    Contactar a Soporte
                  </a>
                </div>
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
                <span style="color: #9ca3af">Gracias por ser parte de nuestra comunidad.</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
