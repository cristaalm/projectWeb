<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, tm, locale } = useI18n()

const steps = computed(() => tm('landing.howItWorks.steps'))

const loading1 = ref(true)
const loading2 = ref(true)

const onVideoLoaded = videoNumber => {
  if (videoNumber === 1) loading1.value = false
  if (videoNumber === 2) loading2.value = false
}
</script>


<template>
  <section
    id="como-funciona"
    class="py-20 px-4 bg-gray-100/30"
  >
    <div class="max-w-7xl mx-auto relative">
      <!-- Título -->
      <div class="text-center mb-16 space-y-4">
        <h2 class="text-4xl md:text-5xl font-bold text-balance font-poppins">
          {{ t('landing.howItWorks.title') }}
        </h2>

        <p class="text-xl text-gray-600 max-w-2xl mx-auto text-pretty font-poppins">
          {{ t('landing.howItWorks.subtitle') }}
        </p>
      </div>

      <!-- Videos -->
      <div class="flex flex-col sm:flex-row justify-center gap-6 mb-16">
        <div class="flex flex-col items-center">
          <div class="relative rounded-xl overflow-hidden shadow-lg max-w-[300px] w-full aspect-[9/16]">
            <div
              v-if="loading1"
              class="absolute top-0 left-0 w-full h-full bg-black/30 flex items-center justify-center z-10 transition-opacity"
            >
              <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin" />
            </div>

            <video
              class="w-full h-full object-cover"
              autoplay
              loop
              muted
              preload="metadata"
              @canplaythrough="onVideoLoaded(1)"
            >
              <source
                src="/videos/Video1.mp4"
                type="video/mp4"
              >
              {{ t('landing.howItWorks.videoFallback') }}
            </video>
          </div>
        </div>

        <div class="flex flex-col items-center">
          <div class="relative rounded-xl overflow-hidden shadow-lg max-w-[300px] w-full aspect-[9/16]">
            <div
              v-if="loading2"
              class="absolute top-0 left-0 w-full h-full bg-black/30 flex items-center justify-center z-10 transition-opacity"
            >
              <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin" />
            </div>

            <video
              class="w-full h-full object-cover"
              autoplay
              loop
              muted
              preload="metadata"
              @canplaythrough="onVideoLoaded(2)"
            >
              <source
                src="/videos/Video2.mp4"
                type="video/mp4"
              >
              {{ t('landing.howItWorks.videoFallback') }}
            </video>
          </div>
        </div>
      </div>

      <!-- Pasos -->
      <div class="md:absolute -bottom-[150px] w-full px-6 md:px-0">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            v-for="(step, index) in steps"
            :key="index"
            class="relative p-6 bg-white rounded-lg shadow-md min-h-[290px] hover:shadow-lg hover:scale-105 transform transition-all duration-300"
          >
            <div class="absolute top-[-16px] left-[-16px] w-12 h-12 bg-[#05D16E] rounded-full flex items-center justify-center">
              <span class="text-white font-bold text-2xl">
                {{ index + 1 }}
              </span>
            </div>

            <div class="w-16 h-16 rounded-xl mb-6 flex items-center justify-center bg-[#05D16E]/10">
              <i
                :class="step.icon"
                class="text-[#05D16E] text-2xl"
              />
            </div>

            <h3 class="text-xl font-bold mb-3 font-poppins">
              {{ step.title }}
            </h3>

            <p class="text-gray-600 leading-relaxed font-poppins">
              {{ step.description }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
