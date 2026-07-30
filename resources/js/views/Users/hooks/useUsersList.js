import { requestOrderTable } from '@/services/requests'

// Traduce los refs de useUsersFilters a los query params que espera
// GET /api/users y arma la instancia de requestOrderTable (paginación,
// orden y búsqueda del listado).
export function useUsersList({ roleFilter, allianceFilter, pointsMin, pointsMax, withTrashed }) {
  return requestOrderTable({
    url: 'users',
    params: {
      role: roleFilter,
      alliance_id: allianceFilter,
      points_min: pointsMin,
      points_max: pointsMax,
      with_trashed: withTrashed,
    },
    defaults: { page: 1, perPage: 10, search: '', sortBy: [{ key: 'id', order: 'asc' }] },
  })
}
