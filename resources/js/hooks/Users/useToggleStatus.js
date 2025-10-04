import { ref } from 'vue'
import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'

export default function useToggleStatus() {
  const authStore = useAuthStore()
  const toastStore = useToastStore()
  const loading = ref(false)

  const toggleStatus = async ({ id, status, justification = null  }) => {
    loading.value = true
    try {
      const response = await requestPost({ url: 'users/toggleStatusAccount', token: authStore.getAccessToken(), data: { id, status, justification } })
      
      if (response.success) {
        toastStore.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        return true
      }
      toastStore.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      console.error(response.errors)

      return false
    } catch (e) {
      toastStore.showToast({ message: 'Error al actualizar el estado de la cuenta', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return { loading, toggleStatus }
}
