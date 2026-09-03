import { requestOrderTable } from '@/services/requests'
import { ref } from 'vue'

// GET /api/alliances — combina el `status` intrínseco de requestOrderTable
// (activo/pausado) con un filtro custom `type_shop_id` vía `params`, ya que
// ambos mecanismos conviven sin problema en el composable (confirmado: uno
// solo lo usa useContainersList, el otro solo useUsersList, pero soporta los
// dos al mismo tiempo).
export function useAlliancesList() {
  const typeShopFilter = ref(null)

  const table = requestOrderTable({
    url: 'alliances',
    params: { type_shop_id: typeShopFilter },
    defaults: { page: 1, perPage: 10, search: '', sortBy: [{ key: 'id', order: 'asc' }], status: null },
  })

  return {
    ...table,
    typeShopFilter,
  }
}
