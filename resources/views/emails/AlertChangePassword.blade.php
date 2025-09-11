<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>Contraseña cambiada</title>
  </head>

  <body
    style="
      font-family: Arial, sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 0;
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
              border-radius: 8px;
              overflow: hidden;
              box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            "
          >
            <tr style="background-color: #c3e3d8">
              <td
                style="
                  padding: 20px;
                  text-align: center;
                  color: #ffffff;
                  font-size: 24px;
                  display: flex;
                  justify-content: center;
                  align-items: center;
                "
              >
                <img
                  src="https://renova-3q4h.onrender.com/images/LogoLetra.png"
                  alt="RENOVA Logo"
                  style="max-width: 180px"
                />
              </td>
            </tr>
            <tr>
              <td style="padding: 30px; color: #333">
                <h2 style="margin-top: 0; color: #d9534f">
                  ¡Tu contraseña ha sido cambiada!
                </h2>
                <p>Hola {{ explode(' ', $user->name)[0] ?? 'usuario' }},</p>
                <p>
                  Te notificamos que la contraseña de tu cuenta en RENOVA ha
                  sido modificada recientemente.
                </p>
                <p
                  style="
                    background: #fff3cd;
                    color: #856404;
                    padding: 16px;
                    border-radius: 4px;
                    border: 1px solid #ffeeba;
                  "
                >
                  Si <strong>NO</strong> realizaste este cambio, por favor
                  contacta inmediatamente con nuestro equipo de soporte para
                  proteger tu cuenta.
                </p>
                <p style="margin-bottom: 0">
                  Saludos,<br />El equipo de RENOVA
                </p>
              </td>
            </tr>
            <tr>
              <td
                style="
                  background-color: #f1f1f1;
                  padding: 20px;
                  text-align: center;
                  font-size: 12px;
                  color: #777;
                "
              >
                © {{ date('Y') }} RENOVA. Todos los derechos reservados.
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
