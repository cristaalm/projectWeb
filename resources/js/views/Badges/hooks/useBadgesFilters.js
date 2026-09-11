import { computed } from 'vue'

export function useBadgesFilters(statusFilter) {
  const hasActiveFilters = computed(() => statusFilter.value !== null)
  const activeFilterCount = computed(() => (statusFilter.value !== null ? 1 : 0))

  function clearFilters() {
    statusFilter.value = null
  }

  return { hasActiveFilters, activeFilterCount, clearFilters }
}
