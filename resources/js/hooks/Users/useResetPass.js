import { ref } from 'vue'
import { requestPost } from '@/services/requests'
import { useToastStore } from '@/store/useToastStore'

export default function useResetPass() {
  const toastStore = useToastStore()
  const loading = ref(false)

  const resetPass = async ({ email }) => {
    loading.value = true
    try {
      const response = await requestPost({ url: 'auth/forgot-password', data: { email } })
      
      if (response.success) {
        toastStore.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        return true
      }
      toastStore.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      console.error(response.errors)

      return false
    } catch (e) {
      toastStore.showToast({ message: 'Error al restablecer la contraseña', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return { loading, resetPass }
}
