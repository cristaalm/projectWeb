<template>
  <section
    id="impacto"
    class="py-20 px-4 bg-[#00464B] text-slate-200"
  >
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-col lg:flex-row items-center lg:items-start gap-8">
        <!-- Columna izquierda: texto -->
        <div class="w-full lg:w-1/2 space-y-6">
          <h2 class="text-4xl md:text-5xl font-bold text-balance text-white font-poppins">
            Tu impacto importa
          </h2>
          <p class="text-lg leading-relaxed text-pretty font-poppins opacity-90">
            México recicla sólo el 9.6% de sus residuos, muy por debajo del promedio de la OCDE del 20%. Con Renova,
            estamos cambiando esta realidad, una botella a la vez.
          </p>
          <p class="text-lg leading-relaxed text-pretty font-poppins opacity-90">
            Nuestro sistema contribuye directamente a los Objetivos de Desarrollo Sostenible de la ONU,
            específicamente al ODS 11 (Ciudades Sostenibles) y ODS 12 (Producción y Consumo Responsables).
          </p>
        </div>

        <!-- Columna derecha: estadísticas -->
        <div class="w-full lg:w-1/2 grid grid-cols-2 gap-6">
          <div
            v-for="(stat, index) in stats"
            :key="index"
            class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg p-6 impact-card  sm:min-h-[140px] font-poppins flex flex-col col-span-2 md:col-span-1 justify-center"
          >
            <div class="text-3xl font-bold mb-2 text-white">
              {{ stat.value }}
            </div>
            <div class="text-sm text-white opacity-80">
              {{ stat.label }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const stats = [
  {
    value: '9.6%',
    label: 'Tasa actual de reciclaje en México',
  },
  {
    value: '63%',
    label: 'Recuperación de PET en México (líder continental)',
  },
  {
    value: '82.5%',
    label: 'Mexicanos conscientes del problema',
  },
  {
    value: '51.3%',
    label: 'Separan residuos efectivamente',
  },
]
</script>

<style scoped>
.space-y-6 > * + * {
  margin-top: 24px;
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
.impact-card {
  background: rgba(255, 255, 255, 0.1) !important;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2) !important;
}
</style>
