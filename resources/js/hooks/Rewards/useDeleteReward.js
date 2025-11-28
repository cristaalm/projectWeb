import { requestDelete } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useDeleteReward() {
  const loading = ref(false)
  const toast = useToastStore()

  const deleteRewardFn = async rewardId => {
    loading.value = true
    try {
      const response = await requestDelete({ url: 'reward/delete/' + rewardId, token: useAuthStore().getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        return true
      }
      toast.showToast({ message: response.message, tipo: 'error', duration: 6000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar eliminar la recompensa:', error)
      toast.showToast({ message: 'Error al intentar eliminar la recompensa.', tipo: 'error', duration: 6000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    deleteReward: deleteRewardFn,
  }
}
