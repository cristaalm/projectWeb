import { useSystemLogs } from '@/hooks/System/useSystemLogs'
import { DEFAULT_SYSTEM_LOG_LINES } from '@/utils/systemLogs'
import { refDebounced, useIntervalFn } from '@vueuse/core'
import { computed, onMounted, ref, watch } from 'vue'

const AUTO_REFRESH_MS = 10000

export function useSystemLogsView() {
  const sources = ref([])
  const selectedSource = ref(null)
  const lines = ref(DEFAULT_SYSTEM_LOG_LINES)
  const search = ref('')
  const debouncedSearch = refDebounced(search, 400)
  const log = ref(null)
  const autoRefresh = ref(false)

  const { error, loadingSources, loadingLog, getLogSources, getLog } = useSystemLogs()

  const loading = computed(() => loadingSources.value || loadingLog.value)

  // Al cambiar rápido de log o de filtro pueden cruzarse dos respuestas:
  // solo se conserva la de la última petición lanzada.
  let lastRequest = 0

  const loadLog = async () => {
    if (!selectedSource.value) return

    const request = ++lastRequest

    const data = await getLog(selectedSource.value, {
      lines: lines.value,
      search: debouncedSearch.value,
    })

    if (request !== lastRequest) return

    log.value = data

    // Si la consulta falla, se apaga la actualización automática para no
    // repetir el mismo aviso de error cada pocos segundos.
    if (error.value) autoRefresh.value = false
  }

  const loadSources = async () => {
    const list = await getLogSources()
    if (!list) return

    sources.value = list

    const current = list.find(source => source.source === selectedSource.value)
    if (current?.available) return

    // Primera carga (o el log elegido dejó de existir, por ejemplo tras un
    // deploy): se abre el primero que sí se pueda leer.
    selectedSource.value = list.find(source => source.available)?.source ?? null
  }

  const refresh = async () => {
    await loadSources()
    await loadLog()
  }

  // Cambiar de log limpia lo que se estaba viendo: mostrar las líneas del
  // anterior bajo el nombre del nuevo, aunque sea un instante, confunde.
  watch(selectedSource, () => {
    log.value = null
    loadLog()
  })
  watch([lines, debouncedSearch], loadLog)

  const { pause, resume } = useIntervalFn(refresh, AUTO_REFRESH_MS, { immediate: false })

  watch(autoRefresh, enabled => enabled ? resume() : pause())

  onMounted(loadSources)

  return {
    sources,
    selectedSource,
    lines,
    search,
    log,
    autoRefresh,
    loading,
    loadingSources,
    loadingLog,
    refresh,
  }
}
