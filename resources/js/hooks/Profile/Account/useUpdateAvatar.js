import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useUpdateAvatar() {
  const loading = ref(false)
  const toast = useToastStore()
  const authStore = useAuthStore()

  const updateAvatar = async ({ avatar = null, deleteAvatar = false }) => {
    loading.value = true
    try {

      const formData = new FormData()

      formData.append('avatar', avatar)
      formData.append('delete', deleteAvatar)

      const response = await requestPost({ url: 'users/updateAvatar/' + authStore.user.id, data: formData, formData: true, token: authStore.accessToken })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })

        authStore.user.avatar = response.data.avatar_url
        
        return response
      }
      toast.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar actualizar el avatar:', error)
      toast.showToast({ message: 'Error al intentar actualizar el avatar.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    updateAvatar,
  }
}
