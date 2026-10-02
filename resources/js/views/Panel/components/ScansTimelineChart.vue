<script setup>
import { formatDashboardDay, formatDashboardMonth } from '@/utils/dashboardPeriods'
import PanelEmptyState from './PanelEmptyState.vue'
import { computed } from 'vue'
import { useTheme } from 'vuetify'

const props = defineProps({
  timeline: { type: Array, default: () => [] },
  granularity: { type: String, default: 'day' },
  loading: Boolean,
})

const theme = useTheme()

const hasData = computed(() => props.timeline.some(point => point.valid > 0 || point.failed > 0))

const series = computed(() => [
  { name: 'Válidos', data: props.timeline.map(point => point.valid) },
  { name: 'Fallidos', data: props.timeline.map(point => point.failed) },
])

// Los colores salen del tema activo de Vuetify para que la gráfica acompañe
// al modo claro/oscuro, igual que el resto de componentes.
const options = computed(() => {
  const { colors, dark } = theme.current.value
  const labelColor = dark ? 'rgba(230, 230, 241, 0.7)' : 'rgba(34, 48, 62, 0.7)'
  const gridColor = dark ? 'rgba(230, 230, 241, 0.12)' : 'rgba(34, 48, 62, 0.12)'
  const format = props.granularity === 'month' ? formatDashboardMonth : formatDashboardDay

  return {
    chart: {
      type: 'bar',
      stacked: true,
      background: 'transparent',
      fontFamily: 'inherit',
      toolbar: { show: false },
      zoom: { enabled: false },
    },
    theme: { mode: dark ? 'dark' : 'light' },
    colors: [colors.primary, colors.error],
    plotOptions: { bar: { columnWidth: '55%', borderRadius: 3 } },
    dataLabels: { enabled: false },
    stroke: { show: false },
    grid: { borderColor: gridColor, strokeDashArray: 4, padding: { left: 8, right: 8 } },
    xaxis: {
      categories: props.timeline.map(point => format(point.bucket)),
      tickAmount: Math.min(props.timeline.length, 10),
      axisBorder: { show: false },
      axisTicks: { show: false },
      labels: { rotate: 0, hideOverlappingLabels: true, style: { colors: labelColor, fontSize: '12px' } },
    },
    yaxis: {
      min: 0,
      forceNiceScale: true,
      labels: {
        style: { colors: labelColor, fontSize: '12px' },
        formatter: value => Number.isInteger(value) ? value : '',
      },
    },
    legend: { position: 'top', horizontalAlign: 'right', labels: { colors: labelColor } },
    tooltip: { shared: true, intersect: false },
  }
})
</script>

<template>
  <VCard class="border border-gray-200 rounded-lg h-100 dark:border-gray-700">
    <VCardText class="pb-0 pa-6">
      <h2 class="text-h6 font-weight-semibold">
        Actividad de reciclaje
      </h2>
      <p class="mb-0 text-body-2 text-medium-emphasis">
        Escaneos {{ granularity === 'month' ? 'por mes' : 'por día' }}
      </p>
    </VCardText>

    <VCardText class="px-4 pt-2 pb-4">
      <VSkeletonLoader
        v-if="loading && !timeline.length"
        type="image"
        height="300"
        class="bg-transparent"
      />
      <PanelEmptyState
        v-else-if="!hasData"
        text="No hay escaneos en este periodo"
        style="min-block-size: 300px;"
      />
      <VueApexCharts
        v-else
        type="bar"
        height="300"
        :options="options"
        :series="series"
      />
    </VCardText>
  </VCard>
</template>
