<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, tm, locale } = useI18n()

const benefits = computed(() => tm('landing.benefits.items'))

const loadingVideo = ref(true)

const onVideoLoaded = () => {
  loadingVideo.value = false
}
</script>

<template>
  <section
    id="beneficios"
    class="py-20 px-4"
  >
    <VContainer class="max-w-7xl">
      <!-- Título -->
      <div class="text-center mb-16 space-y-4">
        <h2 class="text-4xl md:text-5xl font-weight-bold text-balance font-poppins">
          {{ t('landing.benefits.title') }}
        </h2>

        <p class="text-xl text-muted-foreground max-w-2xl mx-auto text-pretty font-poppins">
          {{ t('landing.benefits.subtitle') }}
        </p>
      </div>

      <!-- Layout asimétrico -->
      <div class="flex flex-col md:flex-row gap-12 items-center">
        <!-- Video -->
        <div class="md:w-1/2 w-full">
          <div class="rounded-xl overflow-hidden shadow-lg aspect-video relative">
            <div
              v-if="loadingVideo"
              class="loading-overlay flex items-center justify-center"
            >
              <VProgressCircular
                :size="50"
                :width="4"
                color="primary"
                indeterminate
              />
            </div>

            <video
              class="w-full h-full object-cover"
              autoplay
              playsinline
              controls
              loop
              muted
              preload="metadata"
              @canplaythrough="onVideoLoaded"
            >
              <source
                src="/videos/V3ProyectoIntegrador03.mp4"
                type="video/mp4"
              >
              {{ t('landing.benefits.videoFallback') }}
            </video>
          </div>
        </div>

        <!-- Beneficios -->
        <div class="md:w-1/2 w-full space-y-8">
          <div
            v-for="(benefit, index) in benefits"
            :key="index"
            class="benefit-item group"
          >
            <div class="flex items-start gap-5">
              <div
                class="min-w-12 min-h-12 rounded-xl flex items-center justify-center
                       bg-primary bg-opacity-10 text-primary transition-colors
                       group-hover:bg-opacity-20"
              >
                <VIcon
                  :icon="benefit.icon"
                  size="28"
                  color="white"
                />
              </div>

              <div>
                <h3 class="text-xl font-bold mb-2 font-poppins text-gray-900">
                  {{ benefit.title }}
                </h3>

                <p class="text-muted-foreground leading-relaxed text-pretty font-poppins">
                  {{ benefit.description }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </VContainer>
  </section>
</template>


<style scoped>
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.3);
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Beneficios como lista integrada */
.benefit-item {
  transition: transform 0.2s ease;
}
.benefit-item:hover {
  transform: translateX(4px);
}

/* Responsividad */
@media (max-width: 768px) {
  .benefit-item h3 {
    font-size: 1.125rem;
  }
}
</style>
