<script setup>
defineProps({
  token: { type: String, default: null },
  loading: Boolean,
})

const emit = defineEmits(['toggle', 'copy'])

// Mientras está oculto no hay token real en la página: lo difuminado es un
// relleno con la misma forma, así que no se puede leer inspeccionando el DOM.
const PLACEHOLDER = 'ect_000000000000000000000000'
</script>

<template>
  <div class="gap-1 d-flex align-center">
    <code
      class="token-value text-body-2"
      :class="{ 'token-value--hidden': !token }"
      :aria-label="token ? 'Token del contenedor' : 'Token oculto'"
    >{{ token ?? PLACEHOLDER }}</code>

    <VBtn
      icon
      variant="text"
      size="small"
      :loading="loading"
      :title="token ? 'Ocultar token' : 'Mostrar token'"
      @click="emit('toggle')"
    >
      <VIcon :icon="token ? 'bx-hide' : 'bx-show'" />
    </VBtn>

    <VBtn
      icon
      variant="text"
      size="small"
      color="primary"
      title="Copiar token"
      :disabled="loading"
      @click="emit('copy')"
    >
      <VIcon icon="bx-copy" />
    </VBtn>
  </div>
</template>

<style scoped>
/* Revelado, el token se parte en varias líneas para poder leerse completo
   sin ensanchar la tabla. */
.token-value {
  display: inline-block;
  max-inline-size: 260px;
  word-break: break-all;
}

.token-value--hidden {
  filter: blur(5px);
  user-select: none;
  white-space: nowrap;
}
</style>
