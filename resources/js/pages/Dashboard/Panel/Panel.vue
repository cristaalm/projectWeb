<script setup>
import CardsTop from './modules/cardsTop.vue'
import CardContainers from './modules/CardContainers.vue'
import CardHistory from './modules/CardHistory.vue'
import Graphs from './modules/CardGraphs.vue'
import { useAuthStore } from '@/store/auth'
import { ref, onMounted, onBeforeUnmount } from 'vue'

const authStore = useAuthStore()

const points = computed(() => {
  return authStore?.user?.total_points ?? 0
})

const pointsToMoney = computed(() => {
  return points.value / 100
})

const format = value => {
  return new Intl.NumberFormat('es-MX', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value)
}

// --- Reloj ---
const currentTime = ref('')
const currentIcon = ref('🌞')

const updateClock = () => {
  const now = new Date()
  const hours = now.getHours()
  const minutes = String(now.getMinutes()).padStart(2, '0')

  currentTime.value = `${hours}:${minutes}`

  currentIcon.value = hours >= 6 && hours < 18 ? 'mdi mdi-weather-sunny' : 'mdi mdi-weather-night'
}

onMounted(() => {
  updateClock()

  const interval = setInterval(updateClock, 1000) 

  window.clockInterval = interval
})

onBeforeUnmount(() => {
  if (window.clockInterval) {
    clearInterval(window.clockInterval)
  }
})
</script>

<template>
  <div class="w-full grid grid-cols-12 gap-6">
    <!-- Tarjeta de puntos -->
    <div class="bg-white col-span-12 md:col-span-7 lg:col-span-8 dark:bg-[#2A2B3E] rounded-lg p-5 shadow flex flex-row items-center justify-between min-w-0">
      <div class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex flex-row items-center gap-4 min-w-0">
        <span class="text-nowrap">Tus puntos:</span>
        <span class="text-2xl font-bold text-[#08B662] text-nowrap">
          {{ format(points) }} pts.
        </span>
      </div>

      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 min-w-0">
        <VIcon
          :icon="currentIcon"
          size="32"
        />
        <span class="font-bold text-xl text-gray-800 dark:text-gray-200 text-nowrap">{{ currentTime }}</span>
      </div>
    </div>

    <!-- Tarjeta del prototipo -->
    <div class="bg-white col-span-12 md:col-span-5 lg:col-span-4 dark:bg-[#2A2B3E] rounded-lg p-5 shadow flex flex-row items-center justify-between min-w-0">
      <div class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex flex-row items-center gap-4 text-nowrap min-w-0">
        <span class="truncate">Ambiente del Prototipo:</span>
      </div>

      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <VBtn
          color="secondary"
          variant="outlined"
          role="a"
          href="https://app.losant.com/dashboards/68f4076e7e4c704359d84e1c"
          target="_blank"
        >
          Ver detalles
        </VBtn>
      </div>
    </div>

    <CardsTop />
    <div class="grid col-span-12 grid-cols-1 md:grid-cols-2 gap-4">
      <CardContainers class="col-span-1" />
      <CardHistory class="col-span-1" />
    </div>
    <Graphs />
  </div>
</template>
