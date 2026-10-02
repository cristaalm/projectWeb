<script setup>
const emit = defineEmits(['refresh'])
</script>

<template>
  <VCardText class="pa-6">
    <div class="flex-wrap gap-4 d-flex justify-space-between align-center">
      <div>
        <h1 class="text-h4 font-weight-semibold">
          Catálogo de <span class="text-primary">materiales</span>
        </h1>
        <p class="mt-1 mb-0 text-medium-emphasis">
          Consulta los tipos de material que reconocen los contenedores y los puntos que otorga cada uno
        </p>
      </div>

      <div class="flex-wrap gap-3 d-flex">
        <VBtn
          icon
          variant="text"
          title="Refrescar"
          @click="emit('refresh')"
        >
          <VIcon icon="bx-refresh" />
        </VBtn>
      </div>
    </div>
  </VCardText>
</template>
