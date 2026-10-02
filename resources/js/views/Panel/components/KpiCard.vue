<script setup>
import { formatDashboardNumber } from '@/utils/dashboardPeriods'
import { computed } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  icon: { type: String, required: true },
  color: { type: String, default: 'primary' },
  value: { type: Number, default: 0 },

  // Valor del periodo anterior; null cuando el indicador no se compara.
  previous: { type: Number, default: null },
  caption: { type: String, default: '' },
  loading: Boolean,
})

const trend = computed(() => {
  if (props.previous === null) return null

  const diff = props.value - props.previous
  if (diff === 0) return { label: 'Sin cambio', color: 'default', icon: 'bx-minus' }

  const up = diff > 0

  // Sin base de comparación no hay porcentaje que calcular.
  const label = props.previous === 0
    ? 'Nuevo'
    : `${up ? '+' : '−'}${Math.round(Math.abs(diff) / props.previous * 100)}%`

  return {
    label,
    color: up ? 'success' : 'error',
    icon: up ? 'bx-trending-up' : 'bx-trending-down',
  }
})
</script>

<template>
  <VCard class="border border-gray-200 rounded-lg h-100 dark:border-gray-700">
    <VCardText class="pa-5">
      <div class="gap-3 d-flex align-center justify-space-between">
        <span class="text-body-2 text-medium-emphasis">{{ title }}</span>
        <VAvatar
          :color="color"
          variant="tonal"
          rounded="lg"
          size="38"
        >
          <VIcon
            :icon="icon"
            size="22"
          />
        </VAvatar>
      </div>

      <VSkeletonLoader
        v-if="loading"
        type="heading"
        class="mt-2 bg-transparent"
      />
      <div
        v-else
        class="mt-2 text-h4 font-weight-semibold"
      >
        {{ formatDashboardNumber(value) }}
      </div>

      <div
        class="flex-wrap gap-2 mt-2 d-flex align-center"
        style="min-block-size: 24px;"
      >
        <VChip
          v-if="trend && !loading"
          :color="trend.color"
          variant="tonal"
          size="small"
          title="Comparado con el periodo anterior"
        >
          <VIcon
            :icon="trend.icon"
            size="16"
            start
          />
          {{ trend.label }}
        </VChip>
        <span class="text-caption text-medium-emphasis">{{ caption }}</span>
      </div>
    </VCardText>
  </VCard>
</template>
