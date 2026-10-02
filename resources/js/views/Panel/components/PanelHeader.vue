<script setup>
import { DASHBOARD_PERIODS } from '@/utils/dashboardPeriods'

defineProps({
  period: { type: String, required: true },
  rangeLabel: { type: String, default: '' },
  loading: Boolean,
})

const emit = defineEmits(['update:period', 'refresh'])
</script>

<template>
  <VCardText class="pa-6">
    <div class="flex-wrap gap-4 d-flex justify-space-between align-center">
      <div>
        <h1 class="text-h4 font-weight-semibold">
          Panel de <span class="text-primary">actividad</span>
        </h1>
        <p class="mt-1 mb-0 text-medium-emphasis">
          {{ rangeLabel || 'Resumen del uso del sistema' }}
        </p>
      </div>

      <div class="flex-wrap gap-3 d-flex align-center">
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

        <VSelect
          :model-value="period"
          :items="DASHBOARD_PERIODS"
          label="Periodo"
          prepend-inner-icon="bx-calendar"
          density="compact"
          variant="outlined"
          rounded="lg"
          hide-details
          style="min-inline-size: 220px;"
          @update:model-value="emit('update:period', $event)"
        />
      </div>
    </div>
  </VCardText>
</template>
