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

      <!-- Layout asimétrico: video + beneficios -->
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
              ref="horizontalVideo"
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
              Tu navegador no soporta videos.
            </video>
          </div>
        </div>

        <!-- Beneficios como lista vertical estilizada -->
        <div class="md:w-1/2 w-full space-y-8">
          <div
            v-for="(benefit, index) in benefits"
            :key="index"
            class="benefit-item group"
          >
            <div class="flex items-start gap-5">
              <!-- Ícono con fondo sutil -->
              <div class="min-w-12 min-h-12 rounded-xl flex items-center justify-center bg-primary bg-opacity-10 text-primary transition-colors group-hover:bg-opacity-20">
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

<script setup>
import { ref } from 'vue'

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

const onVideoLoaded = () => {
  loadingVideo.value = false
}
</script>

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
