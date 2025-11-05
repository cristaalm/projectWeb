import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useDeleteBadge() {
  const loading = ref(false)
  const toast = useToastStore()

  const deleteBadgeFn = async badgeId => {
    loading.value = true
    try {
      const response = await requestPost({ url: 'badges/delete/' + badgeId, token: useAuthStore().getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        return true
      }
      toast.showToast({ message: response.message || (response.errors ?? 'Error al intentar eliminar la insignia.'), tipo: 'error', duration: 6000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar eliminar la insignia:', error)
      toast.showToast({ message: 'Error al intentar eliminar la insignia.', tipo: 'error', duration: 6000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    deleteBadge: deleteBadgeFn,
  }
}
