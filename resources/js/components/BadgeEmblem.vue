<script setup>
import SvgMaskIcon from '@/components/SvgMaskIcon.vue'
import { useBadgeEmblemCatalog } from '@/hooks/Badges/useBadgeEmblemCatalog'
import { computed, onMounted } from 'vue'

// Emblema circular de una insignia: fondo con el color de su nivel y el icono encima.
// Usa `icon_url`/`tier` tal como los manda el backend (los mismos que recibe la app
// móvil); si faltan — vista previa de una insignia aún sin guardar — los resuelve con
// el catálogo de GET /badges/icons, que comparte las mismas reglas.
const props = defineProps({
  badge: { type: Object, required: true }, // { icon, recycles_required, icon_url?, tier? }
  size: { type: [Number, String], default: 40 },
})

const { fetchCatalog, iconFor, tierFor } = useBadgeEmblemCatalog()

onMounted(fetchCatalog)

const iconUrl = computed(() => props.badge.icon_url ?? iconFor(props.badge.icon)?.url ?? null)
const tier = computed(() => props.badge.tier ?? tierFor(props.badge.recycles_required))
const iconSize = computed(() => Math.round(Number(props.size) * 0.55))
</script>

<template>
  <VAvatar
    :size="size"
    :style="{ backgroundColor: tier?.background }"
    :title="tier ? `Nivel ${tier.label}` : undefined"
  >
    <SvgMaskIcon
      :url="iconUrl"
      :size="iconSize"
      :color="tier?.color"
      fallback="bx-medal"
    />
  </VAvatar>
</template>
