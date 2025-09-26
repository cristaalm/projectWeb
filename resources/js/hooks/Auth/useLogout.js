import { useThemeSwitcher } from '@/hooks/layout/useThemeSwitcher'
import { router } from '@/plugins/router'
import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export default function useLogout() {
  const successLogout = ref(false)
  const errorLogout = ref(false)
  const loadingLogout = ref(false)
    
  const resetState = () => {
    successLogout.value = false
    errorLogout.value = null
    loadingLogout.value = false
  }

  const logoutUser = async () => {
    const { logoutMode } = useThemeSwitcher()

    resetState()
    loadingLogout.value = true

    const toast = useToastStore()
    const authStore = useAuthStore()

    const token = authStore.getAccessToken()
    if (!token) {
      errorLogout.value = true
      toast.showToast({ message: 'No tiene una sesión activa', tipo: 'error' })

      logoutMode()
      router.push({ name: 'login' })

      return
    }

    try {
      const response = await requestPost({ url: 'auth/logout', data: { token } })

      if (!response.success && response.message !== "Token inválido o no encontrado.") {
        errorLogout.value = true
        console.error(response.errors)
        toast.showToast({ message: response.message ?? messageError, tipo: 'error' })
        router.go(-1)

        return
      }
      successLogout.value = true
      authStore.logout()
      logoutMode()
      router.push({ name: 'login' })


      return
    } catch (error) {
      errorLogout.value = true
      toast.showToast({ message: messageError, tipo: 'error' })
      console.error(error)
      
      return
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
