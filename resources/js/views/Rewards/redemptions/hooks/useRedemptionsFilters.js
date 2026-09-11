import { computed } from 'vue'

export function useRedemptionsFilters(statusFilter, allianceFilter) {
  const hasActiveFilters = computed(() => statusFilter.value !== null || allianceFilter.value !== null)

  const activeFilterCount = computed(() => [
    statusFilter.value !== null,
    allianceFilter.value !== null,
  ].filter(Boolean).length)

  function clearFilters() {
    statusFilter.value = null
    allianceFilter.value = null
  }

  return {
    hasActiveFilters,
    activeFilterCount,
    clearFilters,
  }
}
