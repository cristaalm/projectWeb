import useAuthToken from '@/hooks/Auth/useAuthToken'
import { useAuthStore } from '@/store/auth'

export async function requireAuth(to, from, next, onSuccess) {
  const authStore = useAuthStore()
  const accessToken = authStore.getAccessToken()
  const meta = to.meta
  const isDashboard = meta?.isDashboard

  if (to.name === 'logout') return next()

  if (!accessToken && !isDashboard) return next()

  if (!accessToken) return next({ name: 'login' })
  
  const { authToken, twoFactor } = useAuthToken()
  const isValid = await authToken()
  
  if (!isValid) return next({ name: 'logout' })

  if (twoFactor.value && to.name === 'verify2FA') return next()
  
  if (twoFactor.value) return next({ name: 'verify2FA' })

  if (!to.meta.isDashboard) return next({ name: 'panel' })
  
  // ✅ Si se pasa onSuccess, lo ejecuta; si no, llama a next()
  if (typeof onSuccess === 'function') {
    return onSuccess()
  } else {
    next()
  }
}
