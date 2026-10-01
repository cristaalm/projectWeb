import { requestGet } from '@/services/requests'
import { ref } from 'vue'

// Cache a nivel de módulo (no por instancia) — igual que useAllianceCatalog.js:
// el catálogo de materiales no se edita desde el dashboard.
const materialTypes = ref([])
const loaded = ref(false)
const loading = ref(false)

export function useMaterialTypeCatalog() {
  const fetchMaterialTypes = async () => {
    if (loaded.value || loading.value) return

    loading.value = true

    try {
      const response = await requestGet({ url: 'material-types/catalog' })
      if (response.success) {
        materialTypes.value = response.data.material_types
        loaded.value = true
      }
    } catch (err) {
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  return {
    materialTypes,
    loading,
    fetchMaterialTypes,
  }
}
