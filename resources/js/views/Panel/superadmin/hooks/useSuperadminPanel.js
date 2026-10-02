import { useDashboardStats } from '@/hooks/Dashboard/useDashboardStats'
import { DEFAULT_DASHBOARD_PERIOD } from '@/utils/dashboardPeriods'
import { onMounted, ref, watch } from 'vue'

export function useSuperadminPanel() {
  const period = ref(DEFAULT_DASHBOARD_PERIOD)
  const overview = ref(null)

  const { loading, error, getSuperadminOverview } = useDashboardStats()

  // Al cambiar de periodo rápido pueden cruzarse dos respuestas: solo se
  // conserva la de la última petición lanzada.
  let lastRequest = 0

  const loadData = async () => {
    const request = ++lastRequest
    const data = await getSuperadminOverview(period.value)

    if (request === lastRequest && data) overview.value = data
  }

  watch(period, loadData)
  onMounted(loadData)

  return {
    period,
    overview,
    loading,
    error,
    loadData,
  }
}
