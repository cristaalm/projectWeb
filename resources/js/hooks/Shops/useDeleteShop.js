import { requestDelete } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useDeleteShop() {
  const loading = ref(false)
  const toast = useToastStore()

  const deleteShopFn = async shopId => {
    loading.value = true
    try {
      const response = await requestDelete({ url: 'alianzas/delete/' + shopId, token: useAuthStore().getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        return true
      }
      toast.showToast({ message: response.message || (response.errors ?? 'Error al intentar eliminar el comercio.'), tipo: 'error', duration: 6000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar eliminar el comercio:', error)
      toast.showToast({ message: 'Error al intentar eliminar el comercio.', tipo: 'error', duration: 6000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    deleteShop: deleteShopFn,
  }
}
