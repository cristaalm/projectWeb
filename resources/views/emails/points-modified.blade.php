<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tus puntos han sido actualizados</title>
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
              <td style="background-color: #c3e3d8; padding: 24px 0; text-align: center">
                <img
                  src="https://renova-3q4h.onrender.com/images/LogoLetra.png"
                  alt="ECOSORT Logo"
                  style="max-width: 180px; height: auto"
                />
              </td>
            </tr>

            <!-- Content -->
            <tr>
              <td style="padding: 32px; color: #333">
                <h2 style="margin-top: 0; font-size: 22px; color: #1f2937">
                  ¡Hola, {{ explode(' ', $user->name)[0] ?? 'usuario' }}! 👋
                </h2>

                <p style="font-size: 16px; margin-bottom: 20px;">
                  Tus puntos en <strong>ECOSORT</strong> han sido actualizados.
                </p>

                @php
                    $action = $delta >= 0 ? 'sumaron' : 'restaron';
                    $absDelta = abs($delta);
                    $color = $delta >= 0 ? '#10b981' : '#ef4444';
                @endphp

                <div style="background-color: {{ $delta >= 0 ? '#f0fdf4' : '#fef2f2' }}; border-left: 4px solid {{ $color }}; padding: 16px; margin: 20px 0; border-radius: 4px;">
                  <p style="margin: 8px 0; font-size: 15px;">
                    <strong>Saldo anterior:</strong> {{ $previousBalance }}
                  </p>
                  <p style="margin: 8px 0; font-size: 15px;">
                    <strong>Saldo actual:</strong> {{ $newBalance }}
                  </p>
                  <p style="margin: 8px 0; font-size: 15px; color: {{ $color }};">
                    <strong>{{ $absDelta }} puntos {{ $action }}</strong>
                  </p>
                </div>
                <div style="background-color: #f0f1fd; border-left: 4px solid #209CEE; padding: 16px; margin: 10px 0; border-radius: 4px;">
                  <p style="margin: 8px 0; font-size: 15px;">
                    <strong>Nota:</strong> {{ $reason }}
                  </p>
                </div>

                <p style="font-size: 16px; margin-bottom: 24px;">
                  Si tienes dudas sobre este cambio, no dudes en contactar a soporte.
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
                © {{ date('Y') }} ECOSORT. Todos los derechos reservados.<br />
                <span style="color: #9ca3af">¡Gracias por ser parte de ECOSORT!</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
