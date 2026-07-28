import { requestPut } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useUpdateProfile() {
  const error = ref(false)
  const loading = ref(false)
  const success = ref(false)
  const toast = useToastStore()

  const resetState = () => {
    success.value = false
    error.value = false
    loading.value = false
  }

  const updateProfile = async ({ name, last_name }) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPut({ url: 'profile', data: { name, last_name } })

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
    updateProfile,
  }
}
