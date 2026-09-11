import { requestOrderTable } from '@/services/requests'
import { ref } from 'vue'

// GET /api/redemptions — mismo mecanismo que useRewardsList: status intrínseco
// de requestOrderTable + alliance_id como filtro custom, solo relevante para
// staff (el backend lo fuerza a la propia alianza para admin_merchant).
export function useRedemptionsList() {
  const allianceFilter = ref(null)

  const table = requestOrderTable({
    url: 'redemptions',
    params: { alliance_id: allianceFilter },
    defaults: { page: 1, perPage: 10, search: '', sortBy: [{ key: 'created_at', order: 'desc' }], status: null },
  })

  return {
    ...table,
    allianceFilter,
  }
}
