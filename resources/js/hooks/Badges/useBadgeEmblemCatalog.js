import { requestGet } from '@/services/requests'
import { ref } from 'vue'

// Catálogo de iconos y niveles visuales de las insignias (GET /badges/icons), fuente
// única en el backend (App\Support\BadgeIcons / BadgeTiers) compartida con la app
// móvil. Solo cambia con un deploy, así que se cachea a nivel de módulo.
const icons = ref([])
const tiers = ref([])
const defaultIcon = ref(null)
const loaded = ref(false)
const loading = ref(false)

export function useBadgeEmblemCatalog() {
  const fetchCatalog = async () => {
    if (loaded.value || loading.value) return

    loading.value = true

    try {
      const response = await requestGet({ url: 'badges/icons' })
      if (response.success) {
        icons.value = response.data.icons
        tiers.value = response.data.tiers
        defaultIcon.value = response.data.default_icon
        loaded.value = true
      }
    } catch (err) {
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  function iconFor(name) {
    return icons.value.find(icon => icon.name === name)
      ?? icons.value.find(icon => icon.name === defaultIcon.value)
      ?? null
  }

  // Mismo cálculo que BadgeTiers::forRecycles() — para la vista previa del
  // formulario, antes de que la insignia exista y traiga su `tier` del backend.
  function tierFor(recyclesRequired) {
    const recycles = Number(recyclesRequired) || 0

    return tiers.value.reduce((current, tier) => (recycles >= tier.min_recycles ? tier : current), tiers.value[0] ?? null)
  }

  return {
    icons,
    tiers,
    defaultIcon,
    loading,
    fetchCatalog,
    iconFor,
    tierFor,
  }
}
