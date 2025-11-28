import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { deepEqual } from '@/utils/utils'
import { computed, ref } from 'vue'

export function useUpdateBadge() {
  const loading = ref(false)
  const toast = useToastStore()
  const originalData = ref({})

  const badgeData = ref({
    name: "",
    points_required: 0,
    points_awared: 0,
    status: true,
  })

  const isUnchanged = computed(() => deepEqual(badgeData.value, originalData.value))

  const setNewData = newData => {
    badgeData.value = { ...newData,
      status: newData.status ? true : false,
    }
    originalData.value = { ...newData,
      status: newData.status ? true : false,
    }
  }

  const updateBadge = async () => {
    loading.value = true
    try {

      const data = {
        name: badgeData.value.name,
        points_required: badgeData.value.points_required,
        points_awared: badgeData.value.points_awared,
        status: badgeData.value.status,
      }

      const response = await requestPost({ url: 'badges/update/' + badgeData.value.id, data, token: useAuthStore().getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        return true
      }
      toast.showToast({ message: response.message || (response.errors ?? 'Error al intentar actualizar los datos de la insignia.'), tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar actualizar los datos de la insignia:', error)
      toast.showToast({ message: 'Error al intentar actualizar los datos de la insignia.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    updateBadge,
    setNewData,
    isUnchanged,
    badgeData,
  }
}
