<script setup>
import { SYSTEM_LOG_LINES_OPTIONS } from '@/utils/systemLogs'

defineProps({
  title: { type: String, default: '' },
  search: { type: String, default: '' },
  lines: { type: Number, required: true },
  canCopy: Boolean,
})

const emit = defineEmits(['update:search', 'update:lines', 'copy'])
</script>

<template>
  <VCardText class="pb-4 pa-6">
    <div class="flex-wrap gap-4 d-flex justify-space-between align-center">
      <h2 class="text-h6 font-weight-semibold">
        {{ title || 'Selecciona un log' }}
      </h2>

      <div class="flex-wrap gap-3 d-flex align-center">
        <VTextField
          :model-value="search"
          label="Buscar en el log"
          prepend-inner-icon="bx-search"
          density="compact"
          variant="outlined"
          rounded="lg"
          clearable
          hide-details
          style="min-inline-size: 240px;"
          @update:model-value="emit('update:search', $event ?? '')"
        />

        <VSelect
          :model-value="lines"
          :items="SYSTEM_LOG_LINES_OPTIONS"
          density="compact"
          variant="outlined"
          rounded="lg"
          hide-details
          style="min-inline-size: 150px;"
          @update:model-value="emit('update:lines', $event)"
        />

        <VBtn
          variant="tonal"
          color="primary"
          :disabled="!canCopy"
          @click="emit('copy')"
        >
          <VIcon
            icon="bx-copy"
            start
          />
          Copiar
        </VBtn>
      </div>
    </div>
  </VCardText>
</template>
