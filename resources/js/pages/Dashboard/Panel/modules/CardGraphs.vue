<script setup>
import { ref, onMounted, computed } from 'vue'
import { useTheme } from 'vuetify'
import VueApexCharts from 'vue3-apexcharts'
import { hexToRgb } from '@layouts/utils'
import { requestGet } from '@/services/requests.js'
import { useAuthStore } from '@/store/auth'

const vuetifyTheme = useTheme()
const authStore = useAuthStore()
const loading = ref(true) 
const topAlliances = ref([])


const fetchTopAlliances = async () => {
  loading.value = true
  topAlliances.value = []
  try {
    const token = authStore.getAccessToken()
    console.log('CardGraphs: Obteniendo token...', token)

    const response = await requestGet({
      url: 'history/topAlliancesByRedemptions',
      token: token,
    })
    console.log('CardGraphs: Respuesta de la API:', JSON.parse(JSON.stringify(response)))

    if (Array.isArray(response)) {
      topAlliances.value = response
      console.log('CardGraphs: Datos guardados en topAlliances:', topAlliances.value)
    } else {
      console.error('CardGraphs: La API no devolvió un array.', response)
    }
  } catch (error) {
    console.error('CardGraphs: Error en el bloque catch al llamar a la API:', error)
  } finally {
    loading.value = false
  }
}


const donutSeries = computed(() => {
  return topAlliances.value.map(item => item.quantity)
})

const donutLabels = computed(() => {
  return topAlliances.value.map(item => item.alliance?.name || 'N/A')
})

const totalRedemptions = computed(() => {
  return topAlliances.value.reduce((acc, item) => acc + item.quantity, 0)
})

const donutChartConfig = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  
  const onSurfaceColor = `rgba(${hexToRgb(currentTheme['on-surface'])},${variableTheme['high-emphasis-opacity']})`

  return {
    chart: {
      type: 'donut',
    },
    stroke: {
      show: true,
      width: 1,
      colors: [currentTheme['on-surface']],
    },
    labels: donutLabels.value, 
    colors: [
      currentTheme.primary,
      currentTheme.secondary,
      currentTheme.success,
      currentTheme.info,
      currentTheme.warning,
      currentTheme.error,
      `rgba(${hexToRgb(currentTheme.primary)}, 0.5)`, 
    ],
    plotOptions: {
      pie: {
        donut: {
          size: '70%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'Total',
              fontSize: '1.5rem',
              color: onSurfaceColor, 
              formatter: () => totalRedemptions.value.toLocaleString(),
            },
            name: {
              show: true,
              color: onSurfaceColor, 
              formatter: (val) => {
                if (val.length > 25) {
                  return val.substring(0, 22) + '...'
                }
                return val
              },
            },
            value: {
              show: true,
              color: onSurfaceColor, 
              formatter: val => val, 
            },
          },
        },
      },
    },
    legend: {
      position: 'bottom', 
      labels: {
        colors: onSurfaceColor, 
      },
    },
    dataLabels: {
      enabled: true,
      formatter: (val) => {
        return `${val.toFixed(1)}%`
      },
      style: {
        colors: ['#FFF'], 
        fontSize: '12px',
        fontWeight: 600,
      },
      dropShadow: { 
         enabled: true,
         top: 1,
         left: 1,
         blur: 1,
         opacity: 0.45,
      },
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 300,
        },
        legend: {
          position: 'bottom',
        },
      },
    }],
  }
})

onMounted(() => {
  fetchTopAlliances()
})
</script>

<template>
  <VCard>
    <VCardItem class="d-flex align-center gap-3">
      <VCardTitle>
        <VIcon
        icon="mdi mdi-chart-donut"
        color="primary"
        class="dark:!text-white"
        size="32"
        />
        Volumen de Canjes por Alianza Comercial</VCardTitle>
    </VCardItem>

    <VOverlay
      v-model="loading"
      contained
      class="align-center justify-center"
    >
      <VProgressCircular
        indeterminate
        color="primary"
      />
    </VOverlay>

    <VCardText>
 
      <template v-if="loading">
        <VSkeletonLoader type="image" />
      </template>

      <template v-else-if="topAlliances.length > 0">
        <VueApexCharts
          type="donut"
          :height="500"
          :options="donutChartConfig"
          :series="donutSeries"
        />
      </template>

      <template v-else>
        <div class="text-center pa-5">
          <VIcon
            icon="mdi-chart-bar-off"
            size="48"
            class="mb-2"
          />
          <h6 class="text-h6">
            No se pudieron cargar los datos
          </h6>
          <p class="text-sm text-medium-emphasis">
            No se encontraron datos de canjeos o hubo un error al consultar la API.
          </p>
        </div>
      </template>
    </VCardText>
  </VCard>
</template>

