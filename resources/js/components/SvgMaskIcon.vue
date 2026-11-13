<script setup>
import { computed } from 'vue'

// Pinta un icono a partir de la URL de un SVG servido por el backend (`icon_url` de
// categorías de comercio e insignias — el mismo archivo que usa la app móvil). Se usa
// como máscara CSS para que tome el color del texto (`currentColor`, o `color`) y
// respete el tema claro/oscuro. Sin URL pinta el icono `fallback`, si se indica.
const props = defineProps({
  url: { type: String, default: null },
  size: { type: [Number, String], default: 20 },
  color: { type: String, default: null },
  fallback: { type: String, default: 'bx-store' },
})

const style = computed(() => ({
  inlineSize: `${props.size}px`,
  blockSize: `${props.size}px`,
  maskImage: `url("${props.url}")`,
  WebkitMaskImage: `url("${props.url}")`,
  ...(props.color ? { backgroundColor: props.color } : {}),
}))
</script>

<template>
  <span
    v-if="url"
    class="svg-mask-icon"
    :style="style"
    aria-hidden="true"
  />
  <VIcon
    v-else-if="fallback"
    :icon="fallback"
    :size="size"
    :color="color ?? undefined"
  />
</template>

<style scoped>
.svg-mask-icon {
  display: inline-block;
  flex-shrink: 0;
  background-color: currentcolor;
  mask-position: center;
  mask-repeat: no-repeat;
  mask-size: contain;
  -webkit-mask-position: center;
  -webkit-mask-repeat: no-repeat;
  -webkit-mask-size: contain;
}
</style>
