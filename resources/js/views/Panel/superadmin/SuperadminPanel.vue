<script setup>
import KpiCard from '../components/KpiCard.vue'
import MaterialsDonutChart from '../components/MaterialsDonutChart.vue'
import PanelHeader from '../components/PanelHeader.vue'
import ScansTimelineChart from '../components/ScansTimelineChart.vue'
import AttentionCard from './components/AttentionCard.vue'
import RecentScansCard from './components/RecentScansCard.vue'
import TopContainersCard from './components/TopContainersCard.vue'
import TopUsersCard from './components/TopUsersCard.vue'
import { useSuperadminPanel } from './hooks/useSuperadminPanel'
import { formatDashboardNumber, formatDashboardRange } from '@/utils/dashboardPeriods'
import { computed } from 'vue'

const { period, overview, loading, loadData } = useSuperadminPanel()

const kpis = computed(() => overview.value?.kpis)

const rangeLabel = computed(() => formatDashboardRange(overview.value?.period.from, overview.value?.period.to))

// Solo la primera carga muestra esqueletos; al cambiar de periodo se
// conservan las cifras anteriores hasta que llegan las nuevas.
const firstLoad = computed(() => loading.value && !overview.value)
</script>

<template>
  <div class="gap-6 d-flex flex-column">
    <VCard
      class="border border-gray-200 rounded-lg dark:border-gray-700"
      style="overflow: hidden;"
    >
      <PanelHeader
        v-model:period="period"
        :range-label="rangeLabel"
        :loading="loading"
        @refresh="loadData"
      />
    </VCard>

    <VRow>
      <VCol
        cols="12"
        sm="6"
        lg="3"
      >
        <KpiCard
          title="Reciclajes válidos"
          icon="bx-recycle"
          color="primary"
          :value="kpis?.valid_scans.value"
          :previous="kpis?.valid_scans.previous ?? null"
          caption="vs. periodo anterior"
          :loading="firstLoad"
        />
      </VCol>
      <VCol
        cols="12"
        sm="6"
        lg="3"
      >
        <KpiCard
          title="Puntos otorgados"
          icon="bx-coin-stack"
          color="warning"
          :value="kpis?.points_awarded.value"
          :previous="kpis?.points_awarded.previous ?? null"
          caption="vs. periodo anterior"
          :loading="firstLoad"
        />
      </VCol>
      <VCol
        cols="12"
        sm="6"
        lg="3"
      >
        <KpiCard
          title="Usuarios activos"
          icon="bx-group"
          color="info"
          :value="kpis?.active_users.value"
          :previous="kpis?.active_users.previous ?? null"
          :caption="`de ${formatDashboardNumber(kpis?.active_users.total)} registrados`"
          :loading="firstLoad"
        />
      </VCol>
      <VCol
        cols="12"
        sm="6"
        lg="3"
      >
        <KpiCard
          title="Contenedores activos"
          icon="bx-trash"
          color="success"
          :value="kpis?.containers.active"
          :caption="`de ${formatDashboardNumber(kpis?.containers.total)} registrados`"
          :loading="firstLoad"
        />
      </VCol>
    </VRow>

    <VRow>
      <VCol
        cols="12"
        lg="8"
      >
        <ScansTimelineChart
          :timeline="overview?.timeline"
          :granularity="overview?.period.granularity"
          :loading="loading"
        />
      </VCol>
      <VCol
        cols="12"
        lg="4"
      >
        <MaterialsDonutChart
          :materials="overview?.materials"
          :loading="loading"
        />
      </VCol>
    </VRow>

    <VRow>
      <VCol
        cols="12"
        md="6"
        lg="4"
      >
        <AttentionCard
          :attention="overview?.attention"
          :loading="loading"
        />
      </VCol>
      <VCol
        cols="12"
        md="6"
        lg="4"
      >
        <TopContainersCard
          :containers="overview?.top_containers"
          :loading="loading"
        />
      </VCol>
      <VCol
        cols="12"
        lg="4"
      >
        <TopUsersCard
          :users="overview?.top_users"
          :loading="loading"
        />
      </VCol>
    </VRow>

    <RecentScansCard
      :scans="overview?.recent_scans"
      :loading="loading"
    />
  </div>
</template>
