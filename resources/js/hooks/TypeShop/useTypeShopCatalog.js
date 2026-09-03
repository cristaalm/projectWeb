import { requestGet } from '@/services/requests'
import { ref } from 'vue'

// Cache a nivel de módulo (no por instancia) — igual que useAllianceCatalog.js.
// A diferencia de aquel (nadie muta alianzas desde el mismo flujo que las lee),
// TypeShopManagementDialog sí puede crear/editar/desactivar una categoría en la
// misma sesión de SPA en la que el form de Alliance ya cacheó el catálogo — por
// eso se expone invalidate(), para forzar un refetch la próxima vez sin recargar.
const typeShops = ref([])
const loaded = ref(false)
const loading = ref(false)

export function useTypeShopCatalog() {
  const fetchTypeShops = async () => {
    if (loaded.value || loading.value) return

    loading.value = true

    try {
      const response = await requestGet({ url: 'type-shop/catalog' })
      if (response.success) {
        typeShops.value = response.data.type_shops
        loaded.value = true
      }
    } catch (err) {
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  function invalidate() {
    loaded.value = false
  }

  return {
    typeShops,
    loading,
    fetchTypeShops,
    invalidate,
  }
}
