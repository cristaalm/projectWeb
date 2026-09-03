import { computed } from 'vue'

// Envuelve status/typeShopFilter (ya expuestos por useAlliancesList) — aquí
// solo se calculan hasActiveFilters/activeFilterCount/clearFilters para la
// UI del panel de filtros, igual que useContainersFilters.
export function useAlliancesFilters(statusFilter, typeShopFilter) {
  const hasActiveFilters = computed(() => statusFilter.value !== null || typeShopFilter.value !== null)

  const activeFilterCount = computed(() => [
    statusFilter.value !== null,
    typeShopFilter.value !== null,
  ].filter(Boolean).length)

  function clearFilters() {
    statusFilter.value = null
    typeShopFilter.value = null
  }

  return {
    hasActiveFilters,
    activeFilterCount,
    clearFilters,
  }
}
