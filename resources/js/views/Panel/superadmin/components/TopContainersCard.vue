<script setup>
import { formatDashboardNumber } from '@/utils/dashboardPeriods'
import PanelEmptyState from '../../components/PanelEmptyState.vue'
import { computed } from 'vue'

const props = defineProps({
  containers: { type: Array, default: () => [] },
  loading: Boolean,
})

// La barra de cada fila es relativa al primer lugar.
const maxScans = computed(() => Math.max(...props.containers.map(container => container.valid_scans), 1))
</script>

<template>
  <VCard class="border border-gray-200 rounded-lg h-100 dark:border-gray-700">
    <VCardText class="pb-2 pa-6">
      <h2 class="text-h6 font-weight-semibold">
        Contenedores más usados
      </h2>
      <p class="mb-0 text-body-2 text-medium-emphasis">
        Por reciclajes válidos
      </p>
    </VCardText>

    <VSkeletonLoader
      v-if="loading && !containers.length"
      type="list-item-two-line@3"
      class="bg-transparent"
    />
    <PanelEmptyState
      v-else-if="!containers.length"
      icon="bx-trash"
      text="Ningún contenedor registró reciclajes"
    />
    <div
      v-else
      class="px-6 pb-5 gap-4 d-flex flex-column"
    >
      <div
        v-for="(container, index) in containers"
        :key="container.id"
      >
        <div class="gap-3 d-flex align-center justify-space-between">
          <div class="overflow-hidden">
            <div class="text-body-2 font-weight-medium text-truncate">
              {{ index + 1 }}. {{ container.name }}
            </div>
            <div class="text-caption text-medium-emphasis text-truncate">
              {{ container.serial_number }} · {{ formatDashboardNumber(container.points) }} pts
            </div>
          </div>
          <span class="text-body-2 font-weight-semibold">{{ formatDashboardNumber(container.valid_scans) }}</span>
        </div>
        <VProgressLinear
          :model-value="container.valid_scans / maxScans * 100"
          color="primary"
          height="6"
          rounded
          class="mt-2"
        />
      </div>
    </div>
  </VCard>
</template>
