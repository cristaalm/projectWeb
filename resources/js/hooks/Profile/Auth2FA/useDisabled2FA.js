import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useDisabled2FA({ levelProcess2FA }) {
  const loading = ref(false)
  const toast = useToastStore()
  const authStore = useAuthStore()

  const disable2FA = async () => {
    loading.value = true
    
    try {
      const response = await requestPost({ url: 'auth/disable-2fa', token: authStore.accessToken })

      if (!response.success) {
        toast.showToast({ message: response.message, tipo: 'error', duration: 8000 })
        
        return
      }
      levelProcess2FA.value = 1
      authStore.user.two_factor_status = false
    } catch (error) {
      console.log(error)
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    disable2FA,
  }
}
