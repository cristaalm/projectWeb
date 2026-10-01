import { requestGet } from '@/services/requests'
import { ref } from 'vue'

// Lista de contenedores para selectores. No hay un endpoint de catálogo
// propio: reusa el listado administrativo con su tope de 100 por página, así
// que con más de 100 contenedores el selector quedaría incompleto. Sin cache
// a nivel de módulo, porque los contenedores sí se crean/editan desde el
// dashboard en la misma sesión.
export function useContainerCatalog() {
  const containers = ref([])
  const loading = ref(false)

  const fetchContainers = async () => {
    loading.value = true

    try {
      const response = await requestGet({
        url: 'containers',
        params: { per_page: 100, key: 'name', order: 'asc' },
      })

      if (response.success) containers.value = response.data.data
    } catch (err) {
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  return {
    containers,
    loading,
    fetchContainers,
  }
}
