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
</style>
