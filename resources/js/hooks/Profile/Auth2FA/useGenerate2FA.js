import { ref } from 'vue'
import { requestGet } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'

export default function useGenerate2FA({ levelProcess2FA }) {
  const loading = ref(false)
  const data2FA = ref({})
  const toast = useToastStore()
  const authStore = useAuthStore()

  const generateQR2FA = async () => {
    loading.value = true
    try {
      const response = await requestGet({ url: 'auth/generateQR2FA', token: authStore.accessToken })
      
      if (!response.success) return false

      const data = response.data

      if (data.two_factor_status && !('qr_code_url' in data)) {
        toast.showToast({ message: 'Ya tienes habilitada la autenticación de dos factores.', tipo: 'error', duration: 4000 })

        levelProcess2FA.value = 4

        return false
      }

      data2FA.value = data

      return true
    } catch (error) {
      console.error('Ocurrio un error al generar el QR:', error)
      toast.showToast({ message: 'Ocurrio un error al generar el QR.', tipo: 'error', duration: 4000 })
        
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    data2FA,
    generateQR2FA,
  }
}
