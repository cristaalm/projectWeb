<script setup>
import { formatDashboardNumber } from '@/utils/dashboardPeriods'
import PanelEmptyState from './PanelEmptyState.vue'
import { computed } from 'vue'
import { useTheme } from 'vuetify'

const props = defineProps({
  materials: { type: Array, default: () => [] },
  loading: Boolean,
})

const theme = useTheme()

// Color fijo por material (clave de tema de Vuetify), para que cada uno se
// reconozca igual en cualquier periodo. Un slug fuera de esta lista cae al
// color secundario.
const MATERIAL_COLORS = {
  plastic: 'info',
  aluminum: 'secondary',
  cardboard: 'warning',
  glass: 'primary',
  other: 'error',
}

// Solo los materiales con escaneos: una rebanada en cero no aporta nada.
const slices = computed(() => props.materials
  .map(material => ({ ...material, scans: material.valid_scans + material.failed_scans }))
  .filter(material => material.scans > 0))

const total = computed(() => slices.value.reduce((sum, material) => sum + material.scans, 0))

const series = computed(() => slices.value.map(material => material.scans))

const options = computed(() => {
  const { colors, dark } = theme.current.value
  const labelColor = dark ? 'rgba(230, 230, 241, 0.7)' : 'rgba(34, 48, 62, 0.7)'
  const valueColor = dark ? 'rgba(230, 230, 241, 0.9)' : 'rgba(34, 48, 62, 0.9)'

  return {
    chart: { type: 'donut', background: 'transparent', fontFamily: 'inherit' },
    theme: { mode: dark ? 'dark' : 'light' },
    labels: slices.value.map(material => material.name),
    colors: slices.value.map(material => colors[MATERIAL_COLORS[material.slug] ?? 'secondary']),
    stroke: { width: 2, colors: [colors.surface] },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', labels: { colors: labelColor } },
    tooltip: { y: { formatter: value => `${formatDashboardNumber(value)} escaneos` } },
    plotOptions: {
      pie: {
        donut: {
          size: '70%',
          labels: {
            show: true,
            name: { color: labelColor, fontSize: '13px' },
            value: { color: valueColor, fontSize: '22px', fontWeight: 600, formatter: value => formatDashboardNumber(Number(value)) },
            total: {
              show: true,
              label: 'Escaneos',
              color: labelColor,
              formatter: () => formatDashboardNumber(total.value),
            },
          },
        },
      },
    },
  }
})
</script>

<template>
  <VCard class="border border-gray-200 rounded-lg h-100 dark:border-gray-700">
    <VCardText class="pb-0 pa-6">
      <h2 class="text-h6 font-weight-semibold">
        Escaneos por material
      </h2>
      <p class="mb-0 text-body-2 text-medium-emphasis">
        Incluye los rechazados
      </p>
    </VCardText>

    <VCardText class="px-4 pt-2 pb-4">
      <VSkeletonLoader
        v-if="loading && !materials.length"
        type="image"
        height="300"
        class="bg-transparent"
      />
      <PanelEmptyState
        v-else-if="!total"
        icon="bx-recycle"
        text="No hay escaneos en este periodo"
        style="min-block-size: 300px;"
      />
      <VueApexCharts
        v-else
        type="donut"
        height="300"
        :options="options"
        :series="series"
      />
    </VCardText>
  </VCard>
</template>
