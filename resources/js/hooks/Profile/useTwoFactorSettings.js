import { requestGet, requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useTwoFactorSettings() {
  const error = ref(false)
  const loading = ref(false)
  const toast = useToastStore()

  const resetState = () => {
    error.value = false
    loading.value = false
  }

  const setTwoFactorStatus = enabled => {
    const authStore = useAuthStore()

    authStore.setUser({ ...authStore.user, two_factor_status: enabled })
  }

  const generateQrCode = async () => {
    resetState()
    loading.value = true

    try {
      const response = await requestGet({ url: 'auth/generateQR2FA' })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      return response.data
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const enableTwoFactor = async token2FA => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: 'auth/enable-2fa', data: { token2FA } })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      setTwoFactorStatus(true)
      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.recovery_codes
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const disableTwoFactor = async ({ token2FA, recovery_code }) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: 'auth/disable-2fa', data: { token2FA, recovery_code } })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return false
      }

      setTwoFactorStatus(false)
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

  const regenerateRecoveryCodes = async token2FA => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: 'auth/recovery-codes/regenerate', data: { token2FA } })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.recovery_codes
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  return {
    error,
    loading,
    generateQrCode,
    enableTwoFactor,
    disableTwoFactor,
    regenerateRecoveryCodes,
  }
}
