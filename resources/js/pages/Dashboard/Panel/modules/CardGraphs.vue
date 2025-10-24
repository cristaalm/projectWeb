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
const selectedIndex = ref(0) // Estado para el ÍNDICE de la alianza seleccionada

const fetchTopAlliances = async () => {
  loading.value = true
  topAlliances.value = []
  selectedIndex.value = 0 // Reiniciar al cargar
  try {
    const token = authStore.getAccessToken()
    console.log('CardGraphs: Obteniendo token...', token)

    const response = await requestGet({
      url: 'history/topAlliancesByRedemptions',
      token: token,
    })
    console.log('CardGraphs: Respuesta de la API:', JSON.parse(JSON.stringify(response)))

    if (Array.isArray(response.data) && response.data.length > 0) {
      // ******* CAMBIO AQUÍ: Ordenar los datos por cantidad de canjes de forma descendente *******
      topAlliances.value = response.data.sort((a, b) => b.quantity - a.quantity)
      // El índice 0 ahora siempre apuntará al de mayor canjeo
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

// --- Nuevas propiedades computadas y funciones ---

// Propiedad computada para obtener los detalles de la alianza actual
const currentAlliance = computed(() => {
  if (!topAlliances.value || topAlliances.value.length === 0) {
    return null
  }
  // Obtener los datos basados en el índice seleccionado
  const allianceData = topAlliances.value[selectedIndex.value]

  if (!allianceData || !allianceData.alliance) {
    return null
  }
  
  // Combinamos los detalles de la alianza y la cantidad de canjes
  return {
    ...allianceData.alliance,
    quantity: allianceData.quantity,
  }
})

// Función para ir a la siguiente alianza (con ciclo)
const nextAlliance = () => {
  if (selectedIndex.value === topAlliances.value.length - 1) {
    selectedIndex.value = 0 // Vuelve al inicio
  } else {
    selectedIndex.value++
  }
}

// Función para ir a la alianza anterior (con ciclo)
const prevAlliance = () => {
  if (selectedIndex.value === 0) {
    selectedIndex.value = topAlliances.value.length - 1 // Vuelve al final
  } else {
    selectedIndex.value--
  }
}


// --- Propiedades computadas existentes (modificadas) ---

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
      // Eliminamos los eventos de clic, la gráfica ya no es interactiva
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
      // Dejamos el hover por defecto, pero quitamos la acción de clic
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
      breakpoint: 960, // Ajustado breakpoint para md
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
        <!-- Esqueleto que simula ambas columnas --><VRow>
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
        <!-- Layout de dos columnas --><VRow>
          <!-- Columna de la Gráfica --><VCol
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

          <!-- Columna de Información Detallada con Paginador --><VCol
            cols="12"
            md="4"
            class="d-flex flex-column"
          >
            <!-- Controles del Paginador --><div class="d-flex align-center justify-space-between mb-4">
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

            <!-- Tarjeta de Detalles --><div
              class="pa-4 rounded-lg w-100"
              style="border: 1px solid rgba(var(--v-theme-on-surface), 0.12);"
            >
              <!-- Título --><h6 class="text-sm font-weight-medium text-medium-emphasis">
                Alianza Comercial
              </h6>
              <p class="text-h6 font-weight-bold mb-4">
                {{ currentAlliance.name }}
              </p>

              <!-- Total de Canjes --><h6 class="text-sm font-weight-medium text-medium-emphasis">
                Total de Canjes
              </h6>
              <p class="text-h6 mb-4">
                {{ currentAlliance.quantity.toLocaleString('es-MX') }}
              </p>

              <!-- Contacto --><h6 class="text-sm font-weight-medium text-medium-emphasis">
                Nombre de Contacto
              </h6>
              <p class="text-body-1 mb-4">
                {{ currentAlliance.contact_name || 'No disponible' }}
              </p>

              <!-- Teléfono --><h6 class="text-sm font-weight-medium text-medium-emphasis">
                Teléfono
              </h6>
              <p class="text-body-1 mb-4">
                {{ currentAlliance.phone || 'No disponible' }}
              </p>

              <!-- Email --><h6 class="text-sm font-weight-medium text-medium-emphasis">
                Email
              </h6>
              <p class="text-body-1 mb-4">
                {{ currentAlliance.contact_email || 'No disponible' }}
              </p>

              <!-- Dirección --><h6 class="text-sm font-weight-medium text-medium-emphasis">
                Dirección
              </h6>
              <p class="text-body-1 mb-0">
                {{ currentAlliance.address || 'No disponible' }}
              </p>
            </div>
          </VCol>
        </VRow>
      </template>

      <!-- Estado de Error o Sin Datos --><template v-else>
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

