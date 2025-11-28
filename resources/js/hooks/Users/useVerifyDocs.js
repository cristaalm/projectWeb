import { ref } from 'vue'
import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'

export default function useVerifyDocs() {
  const authStore = useAuthStore()
  const toastStore = useToastStore()
  const loadingVerifyDocs = ref(false)

  const verifyDocs = async ({ id, status, justification = null }) => {
    loadingVerifyDocs.value = true
    try {
      const response = await requestPost({ 
        url: 'users/verification-user', 
        token: authStore.getAccessToken(), 
        data: { user_id: id, status, justification },
      })
      
      if (response.success) {
        toastStore.showToast({ message: response.message, tipo: 'success', duration: 4000 })

        return true
      }

      toastStore.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      console.error(response.errors)

      return false

    } catch (e) {
      toastStore.showToast({ message: 'Ocurrio un error al enviar la respuesta', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loadingVerifyDocs.value = false
    }
  }

  return { 
    loadingVerifyDocs, 
    verifyDocs, 
  }
}
