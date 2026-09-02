import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useSocialAccounts() {
  const error = ref(false)
  const loading = ref(false)
  const toast = useToastStore()

  const resetState = () => {
    error.value = false
    loading.value = false
  }

  const linkGoogleAccount = async idToken => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({
        url: 'profile/social',
        data: { provider: 'google', id_token: idToken },
      })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return false
      }

      useAuthStore().setUser(response.data.user)
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

  const unlinkGoogleAccount = async ({ password, token2FA, recovery_code }) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({
        url: 'profile/social/unlink',
        data: { provider: 'google', password, token2FA, recovery_code },
      })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return false
      }

      useAuthStore().setUser(response.data.user)
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
    linkGoogleAccount,
    unlinkGoogleAccount,
  }
}
