import { ref } from 'vue'
import { requestGet } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'

export default function useCatalogContainers() {
  const authStore = useAuthStore()
  const catalog = ref([])
  const loading = ref(false)
  const error = ref(null)
  const toastStore = useToastStore()

  const fetchCatalog = async () => {
    try {
      loading.value = true

      const response = await requestGet({ url: 'containers/catalog', token: authStore.accessToken })

      if (!response.success) {
        toastStore.showToast({
          message: response.message,
          tipo: 'error',
          duration: 5000,
        })

        return false
      }

      catalog.value = response.data

      return true
    } catch (error) {
      error.value = error
      toastStore.showToast({
        message: error.message,
        tipo: 'error',
        duration: 5000,
      })

      return false
    } finally {
      loading.value = false
    }
  }

  return {
    catalog,
    loading,
    error,
    fetchCatalog,
  }
}
