<script setup>
defineProps({
  autoRefresh: Boolean,
  loading: Boolean,
})

const emit = defineEmits(['update:autoRefresh', 'refresh'])
</script>

<template>
  <VCardText class="pa-6">
    <div class="flex-wrap gap-4 d-flex justify-space-between align-center">
      <div>
        <h1 class="text-h4 font-weight-semibold">
          Logs del <span class="text-primary">servidor</span>
        </h1>
        <p class="mt-1 mb-0 text-medium-emphasis">
          Lo que registran la aplicación y los procesos del servidor donde corre esta página
        </p>
      </div>

      <div class="flex-wrap gap-3 d-flex align-center">
        <VSwitch
          :model-value="autoRefresh"
          label="Actualizar cada 10 s"
          color="primary"
          density="compact"
          hide-details
          @update:model-value="emit('update:autoRefresh', $event)"
        />

        <VBtn
          icon
          variant="text"
          color="default"
          title="Refrescar"
          :loading="loading"
          @click="emit('refresh')"
        >
          <VIcon icon="bx-refresh" />
        </VBtn>
      </div>
    </div>
  </VCardText>
</template>
