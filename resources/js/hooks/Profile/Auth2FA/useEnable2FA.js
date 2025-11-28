import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useEnable2FA({ levelProcess2FA }) {
  const loading = ref(false)
  const validate2FA = ref(false)
  const toast = useToastStore()
  const authStore = useAuthStore()
    
  const resetState = () => {
    validate2FA.value = false
    loading.value = false
  }

  const validate = ({ token2FA }) => {
    if (!token2FA) {
      toast.showToast({ message: 'Por favor, complete los campos', tipo: 'warning' })
      
      return false
    }
    
    return true
  }

  const enable2FA = async form => {
    const { token2FA } = form
    if (!validate({ token2FA })) return
    resetState()
    loading.value = true
    
    try {
      const response = await requestPost({ url: 'auth/enable-2fa', data: { token2FA }, token: authStore.accessToken })

      if (!response.success) {
        toast.showToast({ message: response.message, tipo: 'error', duration: 8000 })
        
        return false
      }
      validate2FA.value = true
      levelProcess2FA.value = 4
      authStore.user.two_factor_status = true

      return true
    } catch (error) {
      console.log(error)
      toast.showToast({ message: 'Ocurrio un error al activar la autenticación de dos factores.', tipo: 'error', duration: 8000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    validate2FA,
    loading,
    enable2FA,
  }
}
