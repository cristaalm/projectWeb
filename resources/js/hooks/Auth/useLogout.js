import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

export default function useLogout() {
  const router = useRouter()
  const successLogout = ref(false)
  const errorLogout = ref(false)
  const loadingLogout = ref(false)
    
  const resetState = () => {
    successLogout.value = false
    errorLogout.value = null
    loadingLogout.value = false
  }

  const nextLogout = (logoutMode = null) => {
    
    if (typeof logoutMode === 'function') {
      logoutMode()
    }

    // verificamos si router.push esta disponible
    if (router) {
      router.push({ name: 'logout' })
    } else {
      // redirigimos a /
      window.location.href = '/'
    }
  }

  const logoutUser = async (logoutMode = null) => {
    resetState()
    loadingLogout.value = true

    const toast = useToastStore()
    const authStore = useAuthStore()

    const token = authStore.getAccessToken()
    if (!token) {
      errorLogout.value = true
      toast.showToast({ message: 'No tiene una sesión activa', tipo: 'error' })
      nextLogout(logoutMode)
    }

    try {
      const response = await requestPost({ url: 'auth/logout', token })

      if (!response.success) {
        errorLogout.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error' })
      }
      successLogout.value = true
      toast.showToast({ message: 'Sesión cerrada correctamente', tipo: 'success' })
      nextLogout(logoutMode)
    } catch (error) {
      errorLogout.value = true
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loadingLogout.value = false
    }
  }

  return {
    successLogout,
    errorLogout,
    loadingLogout,
    logoutUser,
  }
}
