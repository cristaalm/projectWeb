import { requestPut } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { deepEqual } from '@/utils/utils'
import { computed, ref } from 'vue'
import { IMask } from 'vue-imask'

export function useUpdateShop() {
  const loading = ref(false)
  const toast = useToastStore()
  const originalData = ref({})

  const mask = IMask.createMask({
    mask: '(000) 000-0000',
  })

  const shopData = ref({
    name: "",
    contact_name: "",
    contact_email: "",
    phone: "",
    address: "",
    type_shop_id: null,
    status: true,
  })

  const isUnchanged = computed(() => deepEqual(shopData.value, originalData.value))

  const setNewData = newData => {

    newData.phone ? mask.resolve(newData.phone) : null

    shopData.value = { ...newData,
      phone: newData.phone ? mask.value : null,
      status: newData.status ? true : false,
    }
    originalData.value = { ...newData,
      phone: newData.phone ? mask.value : null,
      status: newData.status ? true : false,
    }
  }

  const updateShop = async () => {
    loading.value = true
    try {

      const data = {
        name: shopData.value.name,
        contact_name: shopData.value.contact_name,
        contact_email: shopData.value.contact_email,
        phone: shopData.value.phone.replace(/\D/g, ''),
        address: shopData.value.address,
        type_shop_id: shopData.value.type_shop_id,
        status: shopData.value.status,
      }

      const response = await requestPut({ url: 'alianzas/update/' + shopData.value.id, data, token: useAuthStore().getAccessToken() })
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
    updateShop,
    setNewData,
    isUnchanged,
    shopData,
  }
}
