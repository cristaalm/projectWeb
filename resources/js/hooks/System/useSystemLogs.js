import { requestGet } from '@/services/requests'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useSystemLogs() {
  const error = ref(false)
  const loadingSources = ref(false)
  const loadingLog = ref(false)
  const toast = useToastStore()

  // Las dos consultas comparten el manejo de errores; solo cambia qué
  // indicador de carga mueven y qué parte de la respuesta devuelven.
  const fetch = async (loading, request, pick) => {
    error.value = false
    loading.value = true

    try {
      const response = await requestGet(request)

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      return pick(response.data)
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const getLogSources = () => fetch(loadingSources, { url: 'system/logs' }, data => data.logs)

  const getLog = (source, { lines, search } = {}) => fetch(
    loadingLog,
    { url: `system/logs/${source}`, params: { lines, search: search || undefined } },
    data => data,
  )

  return {
    error,
    loadingSources,
    loadingLog,
    getLogSources,
    getLog,
  }
}
