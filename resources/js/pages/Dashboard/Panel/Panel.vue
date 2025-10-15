<script setup>
import CardsTop from './modules/cardsTop.vue'
import CardContainers from './modules/CardContainers.vue'
import CardHistory from './modules/CardHistory.vue'
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

  // Sol entre 6 AM y 6 PM (ajustable)
  currentIcon.value = hours >= 6 && hours < 18 ? 'mdi mdi-weather-sunny' : 'mdi mdi-weather-night'
}

onMounted(() => {
  updateClock()

  const interval = setInterval(updateClock, 1000) // cada segundo

  // Guardamos el intervalo para limpiarlo después
  window.clockInterval = interval
})

onBeforeUnmount(() => {
  if (window.clockInterval) {
    clearInterval(window.clockInterval)
  }
})
</script>

<template>
  <div class="w-full flex flex-col gap-6">
    <!-- Tarjeta de puntos -->
    <div class="bg-white dark:bg-[#2A2B3E] rounded-lg p-5 shadow flex flex-row items-center justify-between">
      <div class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex flex-row items-center gap-4">
        Tus puntos:
        <span class="text-2xl font-bold text-[#08B662]">
          {{ format(points) }} pts.
        </span>
      </div>

      <!-- Reloj con ícono -->
      <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <VIcon
          :icon="currentIcon"
          size="32"
        />
        <span class="font-bold text-xl text-gray-800 dark:text-gray-200">{{ currentTime }}</span>
      </div>
    </div>
    <CardsTop />


    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <CardContainers class="col-span-1" />
      <CardHistory class="col-span-1" />
    </div>
  </div>
</template>
