import { requestOrderTable } from '@/services/requests'

export function useBadgesList() {
  return requestOrderTable({
    url: 'badges',
    defaults: { page: 1, perPage: 10, search: '', sortBy: [{ key: 'id', order: 'asc' }], status: null },
  })
}
