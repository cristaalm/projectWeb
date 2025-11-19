<template>
  <section
    id="como-funciona"
    class="py-20 px-4 bg-muted/30"
  >
    <VContainer class="max-w-7xl relative">
      <div class="text-center mb-16 space-y-4">
        <h2 class="text-4xl md:text-5xl font-weight-bold text-balance font-poppins">
          Tan fácil y rápido, en 3 pasos
        </h2>
        <p class="text-xl text-muted-foreground max-w-2xl mx-auto text-pretty font-poppins">
          Reciclar nunca fue tan simple. Sólo necesitas tu teléfono y ganas de hacer la diferencia.
        </p>
      </div>

      <!-- Sección de videos verticales -->
      <div class="d-flex flex-column flex-md-row justify-center gap-6 mb-16">
        <div class="d-flex flex-column align-center">
          <div class="video-container rounded-xl overflow-hidden shadow-lg max-w-[300px] w-full position-relative">
            <!-- Spinner de carga -->
            <div
              v-if="loading1"
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
              ref="video1"
              class="w-full h-auto object-cover"
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
              Tu navegador no soporta videos.
            </video>
          </div>
        </div>
        <div class="d-flex flex-column align-center">
          <div class="video-container rounded-xl overflow-hidden shadow-lg max-w-[300px] w-full position-relative">
            <!-- Spinner de carga -->
            <div
              v-if="loading2"
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
              ref="video2"
              class="w-full h-auto object-cover"
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
              Tu navegador no soporta videos.
            </video>
          </div>
        </div>
      </div>

      <!-- Pasos antiguos -->
      <VRow class="absolute -bottom-[80px]">
        <VCol
          v-for="(step, index) in steps"
          :key="index"
          cols="12"
          md="4"
        >
          <VCard class="position-relative pa-6 hover-shadow min-h-[290px] hover:scale-105 transform !transition-all !duration-300">
            <VSheet
              class="position-absolute step-number ml-1 mt-1"
              color="primary"
              rounded="circle"
            >
              <span class="text-white font-weight-bold text-2xl">{{ index + 1 }}</span>
            </VSheet>
            <VSheet class="w-16 h-16 rounded-xl mb-6 d-flex align-center justify-center !bg-opacity-10 !bg-primary">
              <VIcon
                :icon="step.icon"
                size="32"
                color="primary"
              />
            </VSheet>
            <h3 class="text-h5 font-weight-bold mb-3 font-poppins">
              {{ step.title }}
            </h3>
            <p class="text-muted-foreground leading-relaxed font-poppins">
              {{ step.description }}
            </p>
          </VCard>
        </VCol>
      </VRow>
    </VContainer>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const steps = [
  {
    icon: 'mdi mdi-qrcode',
    title: 'Escanea tu código',
    description: 'Abre la app Renova y muestra tu código QR único en el contenedor inteligente.',
  },
  {
    icon: 'mdi mdi-camera',
    title: 'Deposita tus materiales',
    description: 'Coloca tus botellas de plástico o latas de aluminio. Nuestro contenedor inteligente las identificará automáticamente.',
  },
  {
    icon: 'mdi mdi-gift',
    title: 'Acumula puntos',
    description: 'Recibe puntos al instante y canjéalos por productos, descuentos y beneficios en comercios aliados.',
  },
]

const loading1 = ref(true)
const loading2 = ref(true)

const onVideoLoaded = videoNumber => {
  if (videoNumber === 1) {
    loading1.value = false
  } else if (videoNumber === 2) {
    loading2.value = false
  }
}
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
.w-16 {
  width: 64px;
}
.h-16 {
  height: 64px;
}
.mb-6 {
  margin-bottom: 24px;
}
.step-number {
  top: -16px;
  left: -16px;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.hover-shadow {
  transition: box-shadow 0.3s;
}
.hover-shadow:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Estilos para los videos verticales */
.video-container {
  aspect-ratio: 9 / 16; /* Relación de aspecto vertical */
  position: relative;
  overflow: hidden;
}

.video-container video {
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

/* Responsive: en móviles los videos se apilan */
@media (max-width: 768px) {
  .d-flex.flex-column.flex-md-row {
    flex-direction: column !important;
    align-items: center;
  }
  .video-container {
    max-width: 50% !important;
  }
}

/* Ajuste de texto debajo del video */
p {
  margin-top: 12px;
  font-size: 0.9rem;
}
</style>
