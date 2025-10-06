<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Documentos no verificados</title>
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
            <tr>
              <td style="background-color: #fee2e2; padding: 24px 0; text-align: center">
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
                  Lamentamos informarte que tus documentos de identidad <strong>han sido rechazados</strong>.
                </p>

                @if($justification)
                  <p style="font-size: 16px; margin-bottom: 16px; background-color: #fef3c7; padding: 12px; border-radius: 6px; border-left: 4px solid #f59e0b;">
                    <strong>Motivo:</strong> {{ $justification }}
                  </p>
                @endif

                <p style="font-size: 16px; margin-bottom: 20px;">
                  No te preocupes. Puedes volver a subir tus documentos corrigiendo los errores señalados.
                </p>

                <p style="font-size: 16px; margin-bottom: 24px;">
                  Si necesitas ayuda, nuestro equipo de soporte está disponible para guiarte en el proceso.
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
                <span style="color: #9ca3af">Estamos aquí para ayudarte a reciclar mejor.</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
