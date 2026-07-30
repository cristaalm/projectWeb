import { requestGet } from '@/services/requests'
import { ref } from 'vue'

// Cache a nivel de módulo (no por instancia): el catálogo de alianzas activas
// no cambia seguido, no tiene sentido volver a pedirlo cada vez que se abre
// el diálogo de crear usuario dentro de la misma sesión de la SPA.
const alliances = ref([])
const loaded = ref(false)
const loading = ref(false)

export function useAllianceCatalog() {
  const fetchAlliances = async () => {
    if (loaded.value || loading.value) return

    loading.value = true

    try {
      const response = await requestGet({ url: 'alliances/catalog' })
      if (response.success) {
        alliances.value = response.data.alliances
        loaded.value = true
      }
    } catch (err) {
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  return {
    alliances,
    loading,
    fetchAlliances,
  }
}
