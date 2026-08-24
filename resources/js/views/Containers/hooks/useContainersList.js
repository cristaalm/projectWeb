import { requestOrderTable } from '@/services/requests'

// GET /api/containers — el filtro de estado usa el `status` que ya expone
// requestOrderTable de forma nativa (no es un filtro custom vía `params`,
// que quedaría pisado por ese mismo campo interno).
export function useContainersList() {
  return requestOrderTable({
    url: 'containers',
    defaults: { page: 1, perPage: 10, search: '', sortBy: [{ key: 'id', order: 'asc' }], status: null },
  })
}
