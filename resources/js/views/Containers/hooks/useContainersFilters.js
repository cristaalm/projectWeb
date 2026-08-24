import { computed } from 'vue'

// Envuelve el `status` que ya expone useContainersList/requestOrderTable —
// aquí solo se calculan hasActiveFilters/activeFilterCount/clearFilters
// para la UI del panel de filtros.
export function useContainersFilters(statusFilter) {
  const hasActiveFilters = computed(() => statusFilter.value !== null)
  const activeFilterCount = computed(() => (statusFilter.value !== null ? 1 : 0))

  function clearFilters() {
    statusFilter.value = null
  }

  return {
    hasActiveFilters,
    activeFilterCount,
    clearFilters,
  }
}
