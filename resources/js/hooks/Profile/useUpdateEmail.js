import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useUpdateEmail() {
  const error = ref(false)
  const loading = ref(false)
  const success = ref(false)
  const toast = useToastStore()

  const resetState = () => {
    success.value = false
    error.value = false
    loading.value = false
  }

  const updateEmail = async ({ email, password, token2FA, recovery_code }) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: 'profile/email', data: { email, password, token2FA, recovery_code } })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return false
      }

      useAuthStore().setUser(response.data.user)
      success.value = true
      toast.showToast({ message: response.message, tipo: 'success' })

      return true
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return false
  }

  return {
    error,
    loading,
    success,
    updateEmail,
  }
}
