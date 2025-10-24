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
const selectedIndex = ref(0) 

const fetchTopAlliances = async () => {
  loading.value = true
  topAlliances.value = []
  selectedIndex.value = 0 
  try {
    const token = authStore.getAccessToken()
    console.log('CardGraphs: Obteniendo token...', token)

    const response = await requestGet({
      url: 'history/topAlliancesByRedemptions',
      token: token,
    })
    console.log('CardGraphs: Respuesta de la API:', JSON.parse(JSON.stringify(response)))

    if (Array.isArray(response.data) && response.data.length > 0) {
      topAlliances.value = response.data.sort((a, b) => b.quantity - a.quantity)
      console.log('CardGraphs: Datos guardados y ordenados en topAlliances:', topAlliances.value)
    } else {
      console.error('CardGraphs: La API no devolvió un array o está vacío.', response)
    }
  } catch (error) {
    console.error('CardGraphs: Error en el bloque catch al llamar a la API:', error)
  } finally {
    loading.value = false
  }
}

const currentAlliance = computed(() => {
  if (!topAlliances.value || topAlliances.value.length === 0) {
    return null
  }
  const allianceData = topAlliances.value[selectedIndex.value]

  if (!allianceData || !allianceData.alliance) {
    return null
  }
  
  return {
    ...allianceData.alliance,
    quantity: allianceData.quantity,
  }
})

const nextAlliance = () => {
  if (selectedIndex.value === topAlliances.value.length - 1) {
    selectedIndex.value = 0 
  } else {
    selectedIndex.value++
  }
}

const prevAlliance = () => {
  if (selectedIndex.value === 0) {
    selectedIndex.value = topAlliances.value.length - 1 
  } else {
    selectedIndex.value--
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
      width: 0,
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
          size: '60%',
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
                  return val.substring(0, 17) + '...'
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
      onItemHover: {
        highlightDataSeries: true,
      },
    },
    dataLabels: {
      enabled: true,
      formatter: (val) => {
        return `${val.toFixed(1)}%`
      },
      style: {
        colors: ['#FFF'], 
        fontSize: '17px',
        fontWeight: 600,
      },
      dropShadow: { 
         enabled: false,
         top: 1,
         left: 1,
         blur: 1,
         opacity: 0.45,
      },
    },
    responsive: [{
      breakpoint: 960, 
      options: {
        chart: {
          width: '100%',
          height: 350,
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
        Volumen de Canjes por Alianza Comercial
      </VCardTitle>
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
        <VRow>
          <VCol
            cols="12"
            md="8"
          >
            <VSkeletonLoader type="image@2" />
          </VCol>
          <VCol
            cols="12"
            md="4"
          >
            <VSkeletonLoader type="article, paragraph, paragraph, paragraph" />
          </VCol>
        </VRow>
      </template>

      <template v-else-if="topAlliances.length > 0 && currentAlliance">
        <VRow>
          <VCol
            cols="12"
            md="8"
          >
            <VueApexCharts
              type="donut"
              :height="500"
              :options="donutChartConfig"
              :series="donutSeries"
            />
          </VCol>

          <VCol
            cols="12"
            md="4"
            class="d-flex flex-column"
          >
            <div class="d-flex align-center justify-space-between mb-4">
              <VBtn
                icon
                size="small"
                @click="prevAlliance"
              >
                &lt;
              </VBtn>

              <span class="text-body-1 font-weight-medium">
                {{ selectedIndex + 1 }} / {{ topAlliances.length }}
              </span>

              <VBtn
                icon
                size="small"
                @click="nextAlliance"
              >
                &gt;
              </VBtn>
            </div>

            <div
              class="pa-4 rounded-lg w-100"
              style="border: 1px solid rgba(var(--v-theme-on-surface), 0.12);"
            >
              <h6 class="text-sm font-weight-medium text-medium-emphasis">
                Alianza Comercial
              </h6>
              <p class="text-h6 font-weight-bold mb-4">
                {{ currentAlliance.name }}
              </p>

              <h6 class="text-sm font-weight-medium text-medium-emphasis">
                Total de Canjes
              </h6>
              <p class="text-h6 mb-4">
                {{ currentAlliance.quantity.toLocaleString('es-MX') }}
              </p>

              <h6 class="text-sm font-weight-medium text-medium-emphasis">
                Nombre de Contacto
              </h6>
              <p class="text-body-1 mb-4">
                {{ currentAlliance.contact_name || 'No disponible' }}
              </p>

              <h6 class="text-sm font-weight-medium text-medium-emphasis">
                Teléfono
              </h6>
              <p class="text-body-1 mb-4">
                {{ currentAlliance.phone || 'No disponible' }}
              </p>

              <h6 class="text-sm font-weight-medium text-medium-emphasis">
                Email
              </h6>
              <p class="text-body-1 mb-4">
                {{ currentAlliance.contact_email || 'No disponible' }}
              </p>

              <h6 class="text-sm font-weight-medium text-medium-emphasis">
                Dirección
              </h6>
              <p class="text-body-1 mb-0">
                {{ currentAlliance.address || 'No disponible' }}
              </p>
            </div>
          </VCol>
        </VRow>
      </template>

      <template v-else>
        <div class="text-center pa-5 d-flex flex-column align-center justify-center" style="height: 500px;">
          <VIcon
            icon="mdi-chart-bar-off"
            size="64"
            class="mb-4 text-medium-emphasis"
          />
          <h6 class="text-h6 mb-2">
            No se pudieron cargar los datos
          </h6>
          <p class="text-body-1 text-medium-emphasis">
            No se encontraron datos de canjeos o hubo un error al consultar la API.
          </p>
        </div>
      </template>
    </VCardText>
  </VCard>
</template>

