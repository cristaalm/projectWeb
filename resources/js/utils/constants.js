// SPA y API viven en el mismo dominio (misma app Laravel), por eso son rutas
// relativas: funcionan igual en local, staging y producción sin variables de
// entorno adicionales.
export const apiURL = "/api/"
export const storageURL = "/storage/"
export const csrfCookieURL = "/sanctum/csrf-cookie"

export const messageError = "Ocurrió un error inesperado, por favor intente de nuevo mas tarde"
