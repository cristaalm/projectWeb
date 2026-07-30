import { computed, ref } from 'vue'

// Estado de los filtros de la tabla de usuarios — separado de useUsersList
// (que solo sabe pedir datos) para que cada uno tenga una única responsabilidad.
export function useUsersFilters() {
  const roleFilter = ref(null)
  const allianceFilter = ref(null)
  const pointsMin = ref(null)
  const pointsMax = ref(null)
  const withTrashed = ref(false)

  const hasActiveFilters = computed(() => Boolean(
    roleFilter.value || allianceFilter.value || pointsMin.value !== null || pointsMax.value !== null || withTrashed.value,
  ))

  const activeFilterCount = computed(() => [
    roleFilter.value,
    allianceFilter.value,
    pointsMin.value !== null,
    pointsMax.value !== null,
    withTrashed.value,
  ].filter(Boolean).length)

  function clearFilters() {
    roleFilter.value = null
    allianceFilter.value = null
    pointsMin.value = null
    pointsMax.value = null
    withTrashed.value = false
  }

  return {
    roleFilter,
    allianceFilter,
    pointsMin,
    pointsMax,
    withTrashed,
    hasActiveFilters,
    activeFilterCount,
    clearFilters,
  }
}
