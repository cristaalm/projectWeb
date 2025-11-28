import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { deepEqual } from '@/utils/utils'
import { computed, ref } from 'vue'

export default function useModifyPoints() {
  const loading = ref(false)
  const toast = useToastStore()
  const originalData = ref({})


  const userData = ref({
    points: "",
    justify: "",
  })

  const isUnchanged = computed(() => deepEqual(userData.value, originalData.value))

  const setNewData = newData => {
    userData.value = { ...newData }
    originalData.value = { ...newData }
  }

  const modifyPoints = async () => {
    loading.value = true
    try {
      const data = {
        user_id: userData.value.id,
        new_points: userData.value.points,
        description: userData.value.justify,
      }

      const response = await requestPost({ url: 'users/modifyPoints', data, token: useAuthStore().getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        return true
      }
      toast.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar modificar los puntos del usuario:', error)
      toast.showToast({ message: 'Error al intentar modificar los puntos del usuario.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    modifyPoints,
    setNewData,
    isUnchanged,
    userData,
  }
}
