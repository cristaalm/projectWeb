<template>
  <section
    id="impacto"
    class="py-20 px-4 bg-[#00464B] text-slate-200"
  >
    <VContainer class="max-w-7xl">
      <VRow class="align-center">
        <VCol
          cols="12"
          lg="6"
        >
          <div class="space-y-6">
            <h2 class="text-4xl md:text-5xl font-weight-bold text-balance text-white font-poppins">
              Tu impacto importa
            </h2>
            <p
              class="text-lg leading-relaxed text-pretty font-poppins"
              style="opacity: 0.9"
            >
              México recicla sólo el 9.6% de sus residuos, muy por debajo del promedio de la OCDE del 20%. Con Renova,
              estamos cambiando esta realidad, una botella a la vez.
            </p>
            <p
              class="text-lg leading-relaxed text-pretty font-poppins"
              style="opacity: 0.9"
            >
              Nuestro sistema contribuye directamente a los Objetivos de Desarrollo Sostenible de la ONU,
              específicamente al ODS 11 (Ciudades Sostenibles) y ODS 12 (Producción y Consumo Responsables).
            </p>
          </div>
        </VCol>

        <VCol
          cols="12"
          lg="6"
        >
          <VRow>
            <VCol
              v-for="(stat, index) in stats"
              :key="index"
              cols="6"
            >
              <VCard class="pa-6 impact-card min-h-[200px] sm:min-h-[140px] font-poppins">
                <div class="text-h4 font-weight-bold mb-2 text-white">
                  {{ stat.value }}
                </div>
                <div
                  class="text-sm text-white"
                  style="opacity: 0.8"
                >
                  {{ stat.label }}
                </div>
              </VCard>
            </VCol>
          </VRow>
        </VCol>
      </VRow>
    </VContainer>
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
