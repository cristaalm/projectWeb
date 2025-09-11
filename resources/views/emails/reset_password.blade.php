<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>Contraseña restablecida</title>
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
                <h2 style="margin-top: 0; color: #0b4d91">
                  Contraseña restablecida
                </h2>
                <p>
                  Hola {{ explode(' ', $user->name)[0] ?? 'usuario' }},
                </p>
                <p>
                  Se ha completado un cambio de contraseña, puedes iniciar sesión con tu nueva contraseña.
                </p>
                <p>Si no fuiste tú, comunícate con el equipo de soporte, para proteger tu cuenta.</p>
                <p style="text-align: center">
                  <a
                    href="{{ url('/login') }}"
                    style="
                      background-color: #08a75f;
                      color: white;
                      padding: 12px 20px;
                      text-decoration: none;
                      border-radius: 4px;
                    "
                    >Iniciar sesión</a
                  >
                </p>
                <p>Gracias por formar parte de RENOVA.</p>
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
