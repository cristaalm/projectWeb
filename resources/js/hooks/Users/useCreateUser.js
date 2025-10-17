import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { ref } from 'vue'

export function useCreateUser() {
  const loading = ref(false)
  const toast = useToastStore()

  const authStore = useAuthStore()

  const originalData = {
    name: "",
    last_name: "",
    email: "",
    phone: "",
    curp: "",
    role: null,
    alliance: null,
  }

  const userData = ref({ ...originalData })

  const createUser = async () => {
    loading.value = true
    try {
      const data = {
        name: userData.value.name,
        last_name: userData.value.last_name,
        email: userData.value.email,
        phone: userData.value.phone,
        curp: userData.value.curp,
        role: userData.value.role,
        alliance: userData.value.alliance,
      }

      const response = await requestPost({ url: 'users/create', data, token: authStore.getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        resetUserData()
        
        return true
      }
      toast.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar insertar el comercio:', error)
      toast.showToast({ message: 'Error al intentar insertar el comercio.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  const resetUserData = () => {
    userData.value = { ...originalData }
  }

  return {
    loading,
    createUser,
    userData,
    resetUserData,
  }
}
