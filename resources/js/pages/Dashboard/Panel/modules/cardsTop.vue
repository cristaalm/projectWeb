<script setup>
import useGetStats from '@/hooks/Dash/useGetStats'
import { useRouter } from 'vue-router'

const router = useRouter()
const { stats, isLoading, error, getStats } = useGetStats()

const template = computed(() => [
  {
    title: 'Usuarios totales',
    icon: 'mdi mdi-account-group-outline',
    color: 'primary',
    value: stats.value.users.total,
    growthPercentage: stats.value.users.growthPercentage,
    to: '/users',
  },
  {
    title: 'Puntos otorgados',
    icon: 'bx-award',
    color: 'primary',
    value: stats.value.totalPoints.total,
    growthPercentage: stats.value.totalPoints.growthPercentage,
  },
  {
    title: 'Escaneos válidos',
    icon: 'mdi mdi-camera-outline',
    color: 'primary',
    value: stats.value.totalScans.total,
    growthPercentage: stats.value.totalScans.growthPercentage,
  },
  {
    title: 'Recompensas canjeadas',
    icon: 'mdi mdi-gift-outline',
    color: 'primary',
    value: stats.value.totalRewards.total,
    growthPercentage: stats.value.totalRewards.growthPercentage,
    to: '/rewards',
  },
])

const goTo = to => {
  if (to) {
    router.push(to)
  }
}

onMounted(() => {
  getStats()
})
</script>

<template>
  <div class="grid col-span-12 grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 min-h-[150px]">
    <template v-if="isLoading">
      <template 
        v-for="(item, index) in template" 
        :key="index"
      >
        <div class="col-span-1 rounded-lg p-4 flex flex-col justify-between bg-white dark:bg-[#2A2B3E] animate-skeleton-pulse shadow-lg">
          <div class="flex flex-row w-full justify-between">
            <div class="w-1/2 h-[32px] bg-gray-200 dark:bg-slate-300 rounded-xl animate-skeleton-pulse" />
            <div class="w-[32px] h-[32px] bg-gray-200 dark:bg-slate-300 rounded-full animate-skeleton-pulse" />
          </div>
          <div class="flex flex-col gap-1 w-full justify-between">
            <div class="w-1/4 h-[25px] bg-gray-200 dark:bg-slate-300 rounded-xl animate-skeleton-pulse" />
            <div class="w-1/2 h-[20px] bg-gray-200 dark:bg-slate-300 rounded-xl animate-skeleton-pulse" />
          </div>
        </div>
      </template>
    </template>
    <template v-else>
      <template 
        v-for="(item, index) in template" 
        :key="index"
      >
        <div 
          class="col-span-1 rounded-lg p-4 flex flex-col justify-between bg-white dark:bg-[#2A2B3E] shadow"
          :class="item.to ? 'cursor-pointer transition-all duration-300 transform hover:-translate-y-1' : ''"
          @click="goTo(item.to)"
        >
          <div class="flex justify-between w-full flex-row">
            <h2 class="text-xl font-bold">
              {{ item.title }}
            </h2>
            <VIcon 
              :icon="item.icon" 
              size="30" 
            />
          </div>
          <div class="flex justify-between w-full flex-col gap-2">
            <h2 class="text-2xl font-bold">
              {{ new Intl.NumberFormat('es-MX', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(item.value) }}
            </h2>
            <div class="truncate w-full">
              <template v-if="item.growthPercentage == 0">
                <span>Sin cambios</span>
                <span> desde el mes pasado</span>
              </template>
              <template v-else-if="item.growthPercentage > 0">
                <span class="text-green-500">+{{ item.growthPercentage }}%</span>
                <span> desde el mes pasado</span>
              </template>
              <template v-else>
                <span class="text-red-500">{{ item.growthPercentage }}%</span>
                <span> desde el mes pasado</span>
              </template>
            </div>
          </div>
        </div>
      </template>
    </template>
  </div>
</template>
