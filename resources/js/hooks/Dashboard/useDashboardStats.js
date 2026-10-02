import { requestGet } from '@/services/requests'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useDashboardStats() {
  const error = ref(false)
  const loading = ref(false)
  const toast = useToastStore()

  const getSuperadminOverview = async period => {
    error.value = false
    loading.value = true

    try {
      const response = await requestGet({ url: 'dashboard/superadmin', params: { period } })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      return response.data
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  return {
    error,
    loading,
    getSuperadminOverview,
  }
}
