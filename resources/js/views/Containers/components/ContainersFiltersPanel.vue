<script setup>
import { CONTAINER_STATUS } from '@/utils/containerStatus'

defineProps({
  status: { type: Number, default: null },
  hasActiveFilters: Boolean,
})

const emit = defineEmits(['update:status', 'clear'])

function emitStatus(rawValue) {
  emit('update:status', rawValue === '' || rawValue === null ? null : Number(rawValue))
}
</script>

<template>
  <VCardText class="pa-6">
    <VRow>
      <VCol
        cols="12"
        md="3"
      >
        <VSelect
          :model-value="status"
          :items="CONTAINER_STATUS"
          item-title="label"
          item-value="value"
          label="Estado"
          density="compact"
          clearable
          @update:model-value="emitStatus"
        />
      </VCol>
    </VRow>
    <div
      v-if="hasActiveFilters"
      class="d-flex justify-end mt-2"
    >
      <VBtn
        variant="text"
        size="small"
        color="error"
        @click="emit('clear')"
      >
        <VIcon
          icon="bx-x"
          class="me-1"
        />
        Limpiar filtros
      </VBtn>
    </div>
  </VCardText>
</template>
