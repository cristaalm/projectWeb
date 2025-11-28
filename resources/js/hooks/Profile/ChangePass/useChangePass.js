import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { deepEqual } from '@/utils/utils'
import { computed, ref } from 'vue'

export function useChangePass() {
  const loading = ref(false)
  const toast = useToastStore()
  const authStore = useAuthStore()

  const originalData = {
    password: '',
    password_confirmation: '',
    current_password: '',
  }

  const passData = ref({ ...originalData })

  const resetPassword = () => {
    passData.value = { ...originalData }
  }

  const updatePass = async () => {
    loading.value = true
    try {
      const response = await requestPost({ url: 'users/resetPassword', data: passData.value, token: authStore.accessToken })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        resetPassword()

        return true
      }
      toast.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar actualizar la contraseña:', error)
      toast.showToast({ message: 'Error al intentar actualizar la contraseña.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    updatePass,
    passData,
  }
}
