<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

// route.matched trae la cadena completa de rutas anidadas que llevaron a la
// actual (incluye el layout wrapper sin meta.title, que se descarta solo).
// Cuando se agreguen rutas anidadas de verdad, esto arma el breadcrumb solo
// sin tocar este componente — cada nivel solo necesita su meta.title.
const items = computed(() =>
  route.matched
    .filter(record => record.meta?.title)
    .map(record => ({
      title: record.meta.title,
      to: record.name ? { name: record.name } : undefined,
    })),
)
</script>

<template>
  <VBreadcrumbs
    v-if="items.length"
    :items="items"
    icon="bx-home"
    density="compact"
    class="px-0 pb-4"
  />
</template>
