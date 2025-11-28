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
      <span class="text-sm text-gray-500 dark:text-gray-300 truncate max-w-[250px]">Monitoreo de contenedores</span>
    </div>

    <!-- HEADERS -->
    <div class="grid grid-cols-12 gap-2 mt-4 border-b border-gray-200 dark:border-gray-700 pb-2">
      <div class="col-span-4 text-xs font-semibold text-gray-500 dark:text-gray-300 truncate">
        <span class="block">Contenedor</span>
      </div>
      <div class="col-span-2 text-xs font-semibold text-center text-gray-500 dark:text-gray-300 truncate">
        <span class="block">Plástico</span>
      </div>
      <div class="col-span-2 text-xs font-semibold text-center text-gray-500 dark:text-gray-300 truncate">
        <span class="block">Aluminio</span>
      </div>
      <div class="col-span-2 text-xs font-semibold text-center text-gray-500 dark:text-gray-300 truncate">
        <span class="block">No Reciclable</span>
      </div>
      <div class="col-span-2 text-xs font-semibold text-center text-gray-500 dark:text-gray-300 truncate">
        <span class="block">Estado</span>
      </div>
    </div>

    <!-- MAIN -->
    <div class="flex flex-col items-start gap-3 mt-2">
      <template v-if="!loading && containers.length > 0">
        <template
          v-for="item in containers"
          :key="item.id"
        >
          <div class="grid grid-cols-12 gap-2 w-full items-center">
            <div class="col-span-4 overflow-hidden truncate text-nowrap">
              {{ item.name }}
            </div>
            <div class="col-span-2 flex justify-center">
              <VChip
                :color="item.capacity.sensor1 > 80 ? 'error' : item.capacity.sensor1 > 50 ? 'warning' : 'success'"
                variant="text"
                size="x-small"
              >
                {{ item.capacity.sensor1 }}%
              </VChip>
            </div>
            <div class="col-span-2 flex justify-center">
              <VChip
                :color="item.capacity.sensor3 > 80 ? 'error' : item.capacity.sensor3 > 50 ? 'warning' : 'success'"
                variant="text"
                size="x-small"
              >
                {{ item.capacity.sensor3 }}%
              </VChip>
            </div>
            <div class="col-span-2 flex justify-center">
              <VChip
                :color="item.capacity.sensor2 > 80 ? 'error' : item.capacity.sensor2 > 50 ? 'warning' : 'success'"
                variant="text"
                size="x-small"
              >
                {{ item.capacity.sensor2 }}%
              </VChip>
            </div>
            <div class="col-span-2 flex justify-center">
              <VChip
                :color="item.status ? 'success' : 'error'"
                size="x-small"
              >
                {{ item.status ? 'Activo' : 'Inactivo' }}
              </VChip>
            </div>
          </div>
        </template>
      </template>
      
      <template v-if="loading">
        <div class="grid grid-cols-12 gap-2 w-full">
          <div class="col-span-4">
            <VSkeletonLoader
              type="text"
              class="[&_div.v-skeleton-loader\_\_text]:h-4"
            />
          </div>
          <div class="col-span-2 flex justify-center">
            <VSkeletonLoader
              type="chip"
              class="[&_div.v-skeleton-loader\_\_chip]:m-[0px!important]"
            />
          </div>
          <div class="col-span-2 flex justify-center">
            <VSkeletonLoader
              type="chip"
              class="[&_div.v-skeleton-loader\_\_chip]:m-[0px!important]"
            />
          </div>
          <div class="col-span-2 flex justify-center">
            <VSkeletonLoader
              type="chip"
              class="[&_div.v-skeleton-loader\_\_chip]:m-[0px!important]"
            />
          </div>
          <div class="col-span-2 flex justify-center">
            <VSkeletonLoader
              type="chip"
              class="[&_div.v-skeleton-loader\_\_chip]:m-[0px!important]"
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
