import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useCreateShop() {
  const loading = ref(false)
  const toast = useToastStore()

  const authStore = useAuthStore()

  const originalData = {
    name: "",
    contact_name: "",
    contact_email: "",
    phone: "",
    address: "",
    type_shop_id: null,
    status: true,
  }

  const shopData = ref({ ...originalData })

  const createShop = async () => {
    loading.value = true
    try {
      const data = {
        name: shopData.value.name,
        contact_name: shopData.value.contact_name,
        contact_email: shopData.value.contact_email,
        phone: shopData.value.phone,
        address: shopData.value.address,
        type_shop_id: shopData.value.type_shop_id,
        status: shopData.value.status,
      }

      const response = await requestPost({ url: 'alianzas/create', data, token: authStore.getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        resetShopData()
        
        return true
      }
      toast.showToast({ message: response.message || (response.errors ?? 'Error al intentar insertar el comercio.'), tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar insertar el comercio:', error)
      toast.showToast({ message: 'Error al intentar insertar el comercio.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  const resetShopData = () => {
    shopData.value = { ...originalData }
  }

  return {
    loading,
    createShop,
    shopData,
    resetShopData,
  }
}
