<script setup>
import { requestOrderTable } from '@/services/requests'
import { format } from 'date-fns'

const { 
  data: history,
  loading,
  loadData,
} = requestOrderTable({
  url: 'history/getAllSystem',
  defaults: {
    perPage: 4,
  },
  params: {
    type_history: 1,
  },
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
          icon="mdi mdi-gift-outline"
          size="32"
        />
        <h2 class="text-lg font-bold">
          Canjeos recientes
        </h2>
      </div>
      <span class="text-sm text-gray-500 dark:text-gray-300">Los últimos canjeos realizados</span>
    </div>

    <!-- MAIN -->
    <div class="flex flex-col items-start gap-3 mt-4">
      <template v-if="!loading && history.length > 0">
        <template
          v-for="item in history"
          :key="item.id"
        >
          <div class="flex flex-row items-center justify-between gap-2 w-full">
            <div class="flex flex-row items-center gap-2 w-1/2">
              <VIcon
                size="24"
                color="success"
                class="bg-primary/20 !p-4 rounded-lg"
                icon="mdi mdi-gift-outline"
              />
              <div class="flex flex-col">
                <span class="font-bold truncate max-w-[200px]">
                  {{ item.reward.name }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-300">
                  {{ item.user.name }} · {{ item.reward.points_required }} Puntos
                </span>
              </div>
            </div>
            <VChip
              color="success"
              size="small"
            >
              <VIcon
                icon="mdi mdi-check-circle-outline"
                size="small"
                class="mr-1"
              />
              Canjeado
            </VChip>
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
              v-for="item in 4"
              :key="item"
              type="button"
              class="w-[70px] [&_div.v-skeleton-loader\_\_button]:m-[0px!important]"
            />
          </div>
        </div>
      </template>
      <template v-if="!loading && history.length === 0">
        <span class="w-full h-[100px] text-2xl text-center text-gray-500 dark:text-gray-300">
          No hay canjeos recientes
        </span>
      </template>
    </div>
  </div>
</template>
