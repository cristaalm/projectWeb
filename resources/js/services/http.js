import { apiURL, csrfCookieURL } from '@/utils/constants'
import axios from 'axios'

const http = axios.create({
  baseURL: apiURL,
  withCredentials: true,
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
  headers: {
    Accept: 'application/json',
  },
})

// Si el server respondió (401/403/422/500...), resolvemos con el body ya
// parseado — el mismo shape { success, message, data, errors, code } que
// devolvía `fetch` sin lanzar excepción. Si no hubo respuesta (red caída),
// dejamos que la promesa rechace para que el `catch` de cada hook lo tome
// como error de red.
http.interceptors.response.use(
  response => response.data,
  error => {
    if (error.response) return Promise.resolve(error.response.data)

    return Promise.reject(error)
  },
)

let csrfCookiePromise = null

// Sanctum necesita esta cookie antes del primer POST/PUT/DELETE de una
// sesión nueva (login). Cachea la promesa para no pedirla más de una vez.
export function ensureCsrfCookie() {
  if (!csrfCookiePromise) {
    csrfCookiePromise = axios.get(csrfCookieURL, { withCredentials: true })
      .catch(error => {
        csrfCookiePromise = null
        throw error
      })
  }

  return csrfCookiePromise
}

export default http
