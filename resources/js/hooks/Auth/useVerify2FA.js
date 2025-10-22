import { router } from '@/plugins/router'
import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useVerify2FA() {
  const error = ref(false)
  const loading = ref(false)
  const validate2FA = ref(false)
  const toast = useToastStore()
    
  const resetState = () => {
    validate2FA.value = false
    error.value = null
    loading.value = false
  }

  const validate = ({ token2FA }) => {
    if (!token2FA) {
      error.value = true
      toast.showToast({ message: 'Por favor, complete los campos', tipo: 'warning' })
      
      return false
    }
    
    return true
  }

  const verify2FA = async form => {
    const { token2FA } = form
    if (!validate({ token2FA })) return
    resetState()
    loading.value = true
    
    try {
      const response = await requestPost({ url: 'auth/verify-2fa', data: { token2FA }, token: useAuthStore().accessToken })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })
        
        return
      }
      validate2FA.value = true
      router.push({ name: 'panel' })

    } catch (error) {
      error.value = true
      console.log(error)
    } finally {
      loading.value = false
    }
  }

  return {
    validate2FA,
    error,
    loading,
    verify2FA,
  }
}
