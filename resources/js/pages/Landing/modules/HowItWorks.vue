<template>
  <section
    id="como-funciona"
    class="py-20 px-4 bg-gray-100/30"
  >
    <div class="max-w-7xl mx-auto relative">
      <div class="text-center mb-16 space-y-4">
        <h2 class="text-4xl md:text-5xl font-bold text-balance font-poppins">
          Tan fácil y rápido, en 3 pasos
        </h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto text-pretty font-poppins">
          Reciclar nunca fue tan simple. Sólo necesitas tu teléfono y ganas de hacer la diferencia.
        </p>
      </div>

      <!-- Sección de videos verticales -->
      <div class="flex flex-col sm:flex-row justify-center gap-6 mb-16">
        <div class="flex flex-col items-center">
          <div class="relative rounded-xl overflow-hidden shadow-lg max-w-[300px] w-full aspect-[9/16]">
            <!-- Spinner de carga -->
            <div
              v-if="loading1"
              class="absolute top-0 left-0 w-full h-full bg-black/30 flex items-center justify-center z-10 transition-opacity"
            >
              <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin" />
            </div>
            <video
              ref="video1"
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
              Tu navegador no soporta videos.
            </video>
          </div>
        </div>
        <div class="flex flex-col items-center">
          <div class="relative rounded-xl overflow-hidden shadow-lg max-w-[300px] w-full aspect-[9/16]">
            <!-- Spinner de carga -->
            <div
              v-if="loading2"
              class="absolute top-0 left-0 w-full h-full bg-black/30 flex items-center justify-center z-10 transition-opacity"
            >
              <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin" />
            </div>
            <video
              ref="video2"
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
              Tu navegador no soporta videos.
            </video>
          </div>
        </div>
      </div>

      <!-- Pasos antiguos -->
      <div class="md:absolute -bottom-[150px] w-full px-6 md:px-0">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            v-for="(step, index) in steps"
            :key="index"
            class="relative p-6 bg-white rounded-lg shadow-md min-h-[290px] hover:shadow-lg hover:scale-105 transform transition-all duration-300"
          >
            <div class="absolute top-[-16px] left-[-16px] w-12 h-12 bg-[#05D16E] rounded-full flex items-center justify-center">
              <span class="text-white font-bold text-2xl">{{ index + 1 }}</span>
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
