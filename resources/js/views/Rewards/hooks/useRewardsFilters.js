import { computed } from 'vue'

// Envuelve status/allianceFilter (ya expuestos por useRewardsList) — aquí
// solo se calculan hasActiveFilters/activeFilterCount/clearFilters para la
// UI del panel de filtros, igual que useAlliancesFilters.
export function useRewardsFilters(statusFilter, allianceFilter) {
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
