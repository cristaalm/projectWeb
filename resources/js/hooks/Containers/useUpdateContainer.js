import { requestPut } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { deepEqual } from '@/utils/utils'
import { computed, ref } from 'vue'

export function useUpdateContainer() {
  const loading = ref(false)
  const toast = useToastStore()
  const originalData = ref({})

  const containerData = ref({
    name: "",
    serial_number: "",
    location: "",
    status: true,
  })

  const isUnchanged = computed(() => deepEqual(containerData.value, originalData.value))

  const setNewData = newData => {
    containerData.value = { ...newData,
      status: newData.status ? true : false,
    }
    originalData.value = { ...newData,
      status: newData.status ? true : false,
    }
  }

  const updateContainer = async () => {
    loading.value = true
    try {

      const data = {
        name: containerData.value.name,
        serial_number: containerData.value.serial_number,
        location: containerData.value.location,
        status: containerData.value.status,
      }

      const response = await requestPut({ url: 'containers/update/' + containerData.value.id, data, token: useAuthStore().getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        return true
      }
      toast.showToast({ message: response.message || (response.errors ?? 'Error al intentar actualizar los datos del comercio.'), tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar actualizar los datos del comercio:', error)
      toast.showToast({ message: 'Error al intentar actualizar los datos del comercio.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    updateContainer,
    setNewData,
    isUnchanged,
    containerData,
  }
}
