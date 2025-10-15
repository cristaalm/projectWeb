<script setup>
import { requestOrderTable } from '@/services/requests'

const { 
  data: containers,
  loading,
  loadData,
} = requestOrderTable({
  url: 'containers/getAll',
})
</script>

<template>
  <div class="bg-white dark:bg-[#2A2B3E] md:p-6 p-4 rounded-lg shadow min-h-[200px] flex flex-col justify-between">
    <!-- HEADER -->
    <div class="flex flex-col items-start justify-between gap-2">
      <div class="flex flex-row items-end justify-start gap-3">
        <VIcon
          color="primary"
          class="dark:!text-white"
          icon="mdi mdi-map-marker-radius-outline"
          size="32"
        />
        <h2 class="text-lg font-bold">
          Estado de contenedores
        </h2>
      </div>
      <span class="text-sm text-gray-500 dark:text-gray-300">Monitoreo de contenedores</span>
    </div>

    <!-- MAIN -->
    <div class="flex flex-col items-start gap-3 mt-4">
      <template v-if="!loading && containers.length > 0">
        <template
          v-for="item in containers"
          :key="item.id"
        >
          <div class="flex flex-row items-center justify-between gap-2 w-full">
            <div class="w-1/2 overflow-hidden truncate text-nowrap">
              {{ item.name }}
            </div>
            <div class="w-1/2 flex flex-row items-center justify-end gap-2">
              <VChip
                :color="item.capacity > 80 ? 'error' : item.capacity > 50 ? 'warning' : 'success'"
                variant="text"
                size="small"
              >
                {{ item.capacity }}%
              </VChip>
              <VChip
                :color="item.status ? 'success' : 'error'"
                size="small"
              >
                {{ item.status ? 'Activo' : 'Inactivo' }}
              </VChip>
            </div>
          </div>
        </template>
      </template>
      <template v-if="loading">
        <div class="flex flex-row items-center justify-between gap-2 w-full">
          <div class="w-1/2">
            <VSkeletonLoader
              type="list-item@5"
              class="w-full"
            />
          </div>
          <div class="w-1/2 flex flex-col gap-2 justify-center items-end">
            <VSkeletonLoader
              v-for="item in 5"
              :key="item"
              type="button"
              class="w-[70px] [&_div.v-skeleton-loader\_\_button]:m-[0px!important]"
            />
          </div>
        </div>
      </template>
      <template v-if="!loading && containers.length === 0">
        <span class="w-full h-[100px] text-2xl text-center text-gray-500 dark:text-gray-300">
          No hay contenedores
        </span>
      </template>
    </div>
  </div>
</template>
