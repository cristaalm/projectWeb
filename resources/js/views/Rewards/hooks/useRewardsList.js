import { requestOrderTable } from '@/services/requests'
import { ref } from 'vue'

// GET /api/rewards — el status intrínseco de requestOrderTable cubre el
// filtro de estado (pendiente/aprobada/rechazada/pausada); alliance_id viaja
// como filtro custom vía `params`, solo relevante para staff (el backend lo
// fuerza a la propia alianza sin importar qué mande un admin_merchant).
export function useRewardsList() {
  const allianceFilter = ref(null)

  const table = requestOrderTable({
    url: 'rewards',
    params: { alliance_id: allianceFilter },
    defaults: { page: 1, perPage: 10, search: '', sortBy: [{ key: 'created_at', order: 'desc' }], status: null },
  })

  return {
    ...table,
    allianceFilter,
  }
}
