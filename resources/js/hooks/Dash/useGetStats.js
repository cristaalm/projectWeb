import { ref } from 'vue'
import { requestGet } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'

export default function useGetStats() {
  const authStore = useAuthStore()
  const toastStore = useToastStore()

  const stats = ref({
    users: {
      total: 0,
      lastMonthTotal: 0,
      growthPercentage: 0,
    },
    totalPoints: {
      total: 0,
      lastMonthTotal: 0,
      growthPercentage: 0,
    },
    totalScans: {
      total: 0,
      lastMonthTotal: 0,
      growthPercentage: 0,
    },
    totalRewards: {
      total: 0,
      lastMonthTotal: 0,
      growthPercentage: 0,
    },
  })

  const isLoading = ref(false)
  const error = ref(null)

  const getStats = async () => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await requestGet({ url: 'dash/getStats', token: authStore.getAccessToken() })

      stats.value = response.data
    } catch (err) {
      error.value = err
      toastStore.showToast({
        message: 'Ocurrio un error al obtener los datos estadisticos',
        tipo: 'error',
        duration: 5000,
        persistente: false,
        disabled: true,
      })
    } finally {
      isLoading.value = false
    }
  }

  return {
    stats,
    isLoading,
    error,
    getStats,
  }
}
    