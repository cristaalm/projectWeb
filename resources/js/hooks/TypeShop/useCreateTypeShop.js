import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useCreateTypeShop() {
  const loading = ref(false)
  const toast = useToastStore()

  const authStore = useAuthStore()

  const originalData = {
    name: "",
  }

  const typeShopData = ref({ ...originalData })

  const createTypeShop = async () => {
    loading.value = true
    try {
      const data = {
        name: typeShopData.value.name,
      }

      const response = await requestPost({ url: 'typeShop/create', data, token: authStore.getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        resetTypeShopData()
        
        return true
      }
      toast.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar insertar la categoria:', error)
      toast.showToast({ message: 'Error al intentar insertar la categoria.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  const resetTypeShopData = () => {
    typeShopData.value = { ...originalData }
  }

  return {
    loading,
    createTypeShop,
    typeShopData,
    resetTypeShopData,
  }
}
