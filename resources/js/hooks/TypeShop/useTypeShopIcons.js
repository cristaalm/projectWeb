import { requestGet } from '@/services/requests'
import { ref } from 'vue'

// Catálogo cerrado de iconos para categorías (App\Support\TypeShopIcons). No cambia
// en runtime — solo con un deploy —, así que se cachea a nivel de módulo sin invalidate().
const icons = ref([])
const loaded = ref(false)
const loading = ref(false)

export function useTypeShopIcons() {
  const fetchIcons = async () => {
    if (loaded.value || loading.value) return

    loading.value = true

    try {
      const response = await requestGet({ url: 'type-shop/icons' })
      if (response.success) {
        icons.value = response.data.icons
        loaded.value = true
      }
    } catch (err) {
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  return {
    icons,
    loading,
    fetchIcons,
  }
}
