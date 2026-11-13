<script setup>
import { computed } from 'vue'

// Pinta el icono de una categoría de comercio a partir de su `icon_url` (SVG servido
// por el backend, el mismo que usa la app móvil). Se usa como máscara CSS para que
// tome el color del texto (`currentColor`) y respete el tema claro/oscuro.
const props = defineProps({
  url: { type: String, default: null },
  size: { type: [Number, String], default: 20 },
})

const style = computed(() => ({
  inlineSize: `${props.size}px`,
  blockSize: `${props.size}px`,
  maskImage: `url("${props.url}")`,
  WebkitMaskImage: `url("${props.url}")`,
}))
</script>

<template>
  <span
    v-if="url"
    class="type-shop-icon"
    :style="style"
    aria-hidden="true"
  />
  <VIcon
    v-else
    icon="bx-store"
    :size="size"
  />
</template>

<style scoped>
.type-shop-icon {
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
