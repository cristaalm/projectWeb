import { requestOrderTable } from '@/services/requests'
import { ref } from 'vue'

// GET /api/scans — el estado usa el `status` nativo de requestOrderTable;
// contenedor y material van como filtros custom.
export function useScansList() {
  const containerFilter = ref(null)
  const materialFilter = ref(null)

  const table = requestOrderTable({
    url: 'scans',
    params: { container_id: containerFilter, material_type_id: materialFilter },
    defaults: { page: 1, perPage: 10, search: '', sortBy: [{ key: 'scanned_at', order: 'desc' }], status: null },
  })

  return {
    ...table,
    containerFilter,
    materialFilter,
  }
}
