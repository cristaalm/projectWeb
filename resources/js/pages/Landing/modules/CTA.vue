<template>
  <section class="py-20 px-4">
    <VContainer class="max-w-5xl">
      <VCard class="position-relative overflow-hidden !p-4 sm:!p-12 cta-card">
        <div class="blur-circle blur-circle-1" />
        <div class="blur-circle blur-circle-2" />

        <div class="position-relative z-10 text-center space-y-6">
          <h2 class="text-4xl md:text-5xl font-weight-bold text-balance font-poppins">
            Comienza a reciclar hoy
          </h2>
          <p class="text-xl text-muted-foreground max-w-2xl mx-auto text-pretty font-poppins">
            Únete a miles de personas que ya están ganando recompensas mientras cuidan el planeta.
          </p>
          <div class="d-flex flex-column flex-sm-row gap-4 justify-center pt-4">
            <VBtn
              size="x-large"
              color="primary"
              variant="flat"
              class="text-lg px-8 font-poppins"
              prepend-icon="mdi mdi-cellphone"
              append-icon="mdi mdi-arrow-right"
            >
              Descargar App Gratis
            </VBtn>
            <VBtn
              size="x-large"
              variant="outlined"
              class="text-lg px-8 font-poppins"
              to="login"
            >
              Soy Renova
            </VBtn>
          </div>
        </div>
      </VCard>
    </VContainer>
  </section>
</template>

<style scoped>
.max-w-5xl {
  max-width: 1024px;
}
.space-y-6 > * + * {
  margin-top: 24px;
}
.gap-4 {
  gap: 16px;
}
.pt-4 {
  padding-top: 16px;
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
.z-10 {
  z-index: 10;
}
.cta-card {
  background: linear-gradient(135deg, rgba(5, 209, 110, 0.1), transparent, rgba(2, 70, 83, 0.1)) !important;
}
.blur-circle {
  position: absolute;
  width: 256px;
  height: 256px;
  border-radius: 50%;
  filter: blur(64px);
}
.blur-circle-1 {
  top: 0;
  right: 0;
  background: rgba(5, 209, 110, 0.2);
}
.blur-circle-2 {
  bottom: 0;
  left: 0;
  background: rgba(2, 70, 83, 0.2);
}
</style>
