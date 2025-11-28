import { requestGet } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useCatalogShops() {
  const loading = ref(false)
  const toast = useToastStore()
  const authStore = useAuthStore()
  const catShopsData = ref([])

  const loadCatShops = async () => {
    loading.value = true
    try {

      const response = await requestGet({ url: 'alianzas/catalog', token: authStore.getAccessToken() })
      if (response.success) {
        catShopsData.value = response.data

        return true
      }
      toast.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar obtener el catalogo de comercios:', error)
      toast.showToast({ message: 'Error al intentar obtener el catalogo de comercios.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    loadCatShops,
    catShopsData,
  }
}
