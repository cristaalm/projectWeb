import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useCreateBadge() {
  const loading = ref(false)
  const toast = useToastStore()

  const authStore = useAuthStore()

  const originalData = {
    name: "",
    points_required: 0,
    points_awared: 0,
    status: true,
  }

  const badgeData = ref({ ...originalData })

  const createBadge = async () => {
    loading.value = true
    try {
      const data = {
        name: badgeData.value.name,
        points_required: badgeData.value.points_required,
        points_awared: badgeData.value.points_awared,
        status: badgeData.value.status,
      }

      const response = await requestPost({ url: 'badges/create', data, token: authStore.getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        resetBadgeData()
        
        return true
      }
      toast.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar crear la insignia:', error)
      toast.showToast({ message: 'Error al intentar crear la insignia.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  const resetBadgeData = () => {
    badgeData.value = { ...originalData }
  }

  return {
    loading,
    createBadge,
    badgeData,
    resetBadgeData,
  }
}
