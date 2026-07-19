import http from '@/services/http'
import { useAuthStore } from '@/store/auth'
import { ref } from 'vue'

export default function useAuthToken() {
  const user = ref(null)
  const success = ref(false)
  const error = ref(false)
  const loading = ref(false)
  const twoFactor = ref(false)

  const resetState = () => {
    success.value = false
    error.value = null
    loading.value = false
    twoFactor.value = false
  }

  // Válido tanto para sesión por cookie (web) como para bearer token (móvil):
  // auth:sanctum resuelve cualquiera de los dos, el backend no distingue aquí.
  //
  // Usa `http` directo (no `requestGet`) a propósito: esta llamada ES el
  // chequeo de "¿estoy logueado?" que usa el guard de rutas, así que un 401
  // aquí es un resultado normal (visitante anónimo), no una sesión que se
  // cayó a medio uso — no debe disparar el auto-redirect a logout de
  // `requestGet`, el guard ya decide qué hacer con el resultado.
  const checkSession = async () => {
    resetState()
    loading.value = true

    try {
      const response = await http.get('auth/me')

      if (typeof response.data === 'object' && response.data && 'two_factor' in response.data) {
        twoFactor.value = response.data.two_factor
      }

      if (!response.success) {
        error.value = true

        return false
      }

      success.value = true
      user.value = response.data.user
      useAuthStore().setUser(response.data.user)

      return true
    } catch (err) {
      error.value = true
      console.error(err)
    } finally {
      loading.value = false
    }

    return false
  }

  return {
    user,
    success,
    error,
    loading,
    twoFactor,
    checkSession,
  }
}
