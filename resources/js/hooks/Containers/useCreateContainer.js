import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useCreateContainer() {
  const loading = ref(false)
  const toast = useToastStore()

  const authStore = useAuthStore()

  const originalData = {
    name: "",
    serial_number: "",
    location: "",
    status: true,
  }

  const containerData = ref({ ...originalData })

  const createContainer = async () => {
    loading.value = true
    try {
      const data = {
        name: containerData.value.name,
        serial_number: containerData.value.serial_number,
        location: containerData.value.location,
        status: containerData.value.status,
      }

      const response = await requestPost({ url: 'containers/create', data, token: authStore.getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        resetContainerData()
        
        return true
      }
      toast.showToast({ message: response.message || (response.errors ?? 'Error al intentar insertar el contenedor.'), tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar insertar el contenedor:', error)
      toast.showToast({ message: 'Error al intentar insertar el contenedor.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  const resetContainerData = () => {
    containerData.value = { ...originalData }
  }

  return {
    loading,
    createContainer,
    containerData,
    resetContainerData,
  }
}
