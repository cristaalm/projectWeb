import { computed } from 'vue'

export function useScansFilters(statusFilter, containerFilter, materialFilter) {
  const filters = [statusFilter, containerFilter, materialFilter]

  const activeFilterCount = computed(() => filters.filter(filter => filter.value !== null).length)
  const hasActiveFilters = computed(() => activeFilterCount.value > 0)

  function clearFilters() {
    filters.forEach(filter => {
      filter.value = null
    })
  }

  return {
    hasActiveFilters,
    activeFilterCount,
    clearFilters,
  }
}
