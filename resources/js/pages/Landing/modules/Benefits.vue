<template>
  <section
    id="beneficios"
    class="py-20 px-4"
  >
    <VContainer class="max-w-7xl">
      <div class="text-center mb-16 space-y-4">
        <h2 class="text-4xl md:text-5xl font-weight-bold text-balance font-poppins">
          ¿Por qué elegir Renova?
        </h2>
        <p class="text-xl text-muted-foreground max-w-2xl mx-auto text-pretty font-poppins">
          Más que una app de reciclaje, es un ecosistema completo que te recompensa por hacer lo correcto.
        </p>
      </div>

      <!-- Video horizontal -->
      <div class="d-flex justify-center mb-16">
        <div class="video-horizontal-container rounded-xl overflow-hidden shadow-lg max-w-4xl w-full position-relative">
          <!-- Spinner de carga -->
          <div
            v-if="loadingVideo"
            class="loading-overlay d-flex align-center justify-center"
          >
            <VProgressCircular
              :size="50"
              :width="4"
              color="primary"
              indeterminate
            />
          </div>
          <video
            ref="horizontalVideo"
            class="w-full h-auto object-cover"
            autoplay
            loop
            muted
            preload="metadata"
            @canplaythrough="onVideoLoaded"
          >
            <source
              src="/videos/V3ProyectoIntegrador03.mp4"
              type="video/mp4"
            >
            Tu navegador no soporta videos.
          </video>

          <!-- Botón de sonido (solo visible después de cargar el video) -->
          <VBtn
            v-if="!loadingVideo"
            fab
            size="small"
            color="white"
            class="sound-button position-absolute"
            @click="toggleSound"
          >
            <VIcon
              :icon="soundIcon"
              color="primary"
            />
          </VBtn>
        </div>
      </div>

      <VRow>
        <VCol
          v-for="(benefit, index) in benefits"
          :key="index"
          cols="12"
          md="6"
        >
          <VCard class="pa-8 hover-card min-h-[300px]">
            <VSheet class="w-14 h-14 rounded-xl mb-6 d-flex align-center justify-center !bg-opacity-10 !bg-primary">
              <VIcon
                :icon="benefit.icon"
                size="28"
                color="primary"
              />
            </VSheet>
            <h3 class="text-h5 font-weight-bold mb-3 font-poppins">
              {{ benefit.title }}
            </h3>
            <p class="text-muted-foreground leading-relaxed text-pretty font-poppins">
              {{ benefit.description }}
            </p>
          </VCard>
        </VCol>
      </VRow>
    </VContainer>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'

const benefits = [
  {
    icon: 'mdi mdi-cash-multiple',
    title: 'Gana mientras cuidas el planeta',
    description: 'Cada botella o lata que reciclas se convierte en puntos reales que puedes usar en tus tiendas favoritas.',
  },
  {
    icon: 'mdi mdi-trending-up',
    title: 'Sistema de recompensas gamificado',
    description: 'Desbloquea logros, sube de nivel y accede a recompensas exclusivas mientras más reciclas.',
  },
  {
    icon: 'mdi mdi-account-group',
    title: 'Apoya a comercios locales',
    description: 'Canjea tus puntos en negocios aliados de tu comunidad y fortalece la economía local.',
  },
  {
    icon: 'mdi mdi-creation',
    title: 'Tecnología inteligente',
    description: 'Nuestra IA identifica automáticamente los materiales. Sin complicaciones, sin errores.',
  },
]

const loadingVideo = ref(true)
const soundOn = ref(false)

const onVideoLoaded = () => {
  loadingVideo.value = false


  // El video comienza en muted, así que soundOn debe reflejar eso
  const video = document.querySelector('.video-horizontal-container video')
  if (video) {
    soundOn.value = !video.muted // Si está muted, entonces soundOn es false
  }
}

const toggleSound = () => {
  const video = document.querySelector('.video-horizontal-container video')

  if (video) {
    // Cambia el estado del video
    video.muted = !video.muted

    // Actualiza el estado de la UI
    soundOn.value = !video.muted
  }
}

const soundIcon = computed(() => soundOn.value ? 'mdi mdi-volume-high' : 'mdi mdi-volume-mute')
</script>

<style scoped>
.space-y-4 > * + * {
  margin-top: 16px;
}
.mb-16 {
  margin-bottom: 64px;
}
.max-w-2xl {
  max-width: 672px;
}
.max-w-4xl {
  max-width: 896px;
}
.mx-auto {
  margin-left: auto;
  margin-right: auto;
}
.text-balance {
  text-wrap: balance;
}
.text-pretty {
  text-wrap: pretty;
}
.leading-relaxed {
  line-height: 1.625;
}
.w-14 {
  width: 56px;
}
.h-14 {
  height: 56px;
}
.mb-6 {
  margin-bottom: 24px;
}
.hover-card {
  transition: all 0.3s;
}
.hover-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  transform: translateY(-4px);
}
.video-horizontal-container {
  position: relative;
  overflow: hidden;
  /* Relación de aspecto 16:9 para video horizontal */
  aspect-ratio: 16 / 9;
}
.video-horizontal-container video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
/* Overlay de carga */
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.3);
  z-index: 10;
  transition: opacity 0.3s ease;
}
/* Botón de sonido */
.sound-button {
  bottom: 16px;
  right: 16px;
  z-index: 20;
  background-color: rgba(255, 255, 255, 0.8) !important;
  backdrop-filter: blur(4px);
  transition: background-color 0.2s;
}
.sound-button:hover {
  background-color: rgba(255, 255, 255, 0.9) !important;
}
</style>
