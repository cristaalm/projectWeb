import { requestGet } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useCatalogTypeShop() {
  const loading = ref(false)
  const toast = useToastStore()
  const typeShopData = ref([])
  const authStore = useAuthStore()

  const loadCatalogTypeShop = async () => {
    loading.value = true
    try {
      const response = await requestGet({ url: 'typeShop/catalog', token: authStore.getAccessToken() })
      if (response.success) {        
        typeShopData.value = response.data
        
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
    loadCatalogTypeShop,
    typeShopData,
  }
}
