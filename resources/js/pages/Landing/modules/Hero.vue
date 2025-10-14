<script setup>
const scrollToSection = sectionId => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>

<template>
  <section class="pt-8 pb-20 px-4">
    <VContainer class="max-w-7xl">
      <VRow class="align-center">
        <VCol cols="12" lg="6">
          <div class="space-y-8">
            <VChip
              color="primary"
              variant="tonal"
              class="px-4 py-2"
              prepend-icon="mdi mdi-recycle"
            >
              Gana recompensas reciclando
            </VChip>

            <h1 class="text-5xl md:text-6xl lg:text-7xl font-weight-bold leading-tight text-balance">
              Recicla y gana <span class="text-primary">recompensas</span>
            </h1>

            <p class="text-xl text-muted-foreground leading-relaxed text-pretty">
              Convierte tus botellas de plástico y latas de aluminio en puntos canjeables. Renova hace que reciclar sea
              fácil, divertido y gratificante.
            </p>

            <div class="d-flex flex-column flex-sm-row gap-4">
              <VBtn
                size="x-large"
                color="primary"
                variant="flat"
                class="text-lg px-8"
                prepend-icon="mdi mdi-cellphone"
                append-icon="mdi mdi-arrow-right"
              >
                Descargar App
              </VBtn>
              <VBtn
                size="x-large"
                variant="outlined"
                class="text-lg px-8"
                @click="() => scrollToSection('como-funciona')"
              >
                Ver Cómo Funciona
              </VBtn>
            </div>

            <div class="d-flex align-center gap-8 pt-4">
              <div>
                <div class="text-h4 font-weight-bold text-primary">63%</div>
                <div class="text-sm text-muted-foreground">Tasa de reciclaje PET</div>
              </div>
              <VDivider vertical class="h-12" />
              <div>
                <div class="text-h4 font-weight-bold text-primary">+1000</div>
                <div class="text-sm text-muted-foreground">Usuarios activos</div>
              </div>
              <VDivider vertical class="h-12" />
              <div>
                <div class="text-h4 font-weight-bold text-primary">24/7</div>
                <div class="text-sm text-muted-foreground">Contenedores disponibles</div>
              </div>
            </div>
          </div>
        </VCol>

        <VCol cols="12" lg="6">
          <div class="position-relative">
            <VSheet
              class="position-relative aspect-square rounded-xl pa-8 backdrop-blur"
              style="background: linear-gradient(135deg, rgba(5, 209, 110, 0.2), rgba(2, 70, 83, 0.2))"
            >
              <div
                class="position-absolute inset-0 rounded-xl"
                style="background: linear-gradient(135deg, rgba(5, 209, 110, 0.1), transparent)"
              />
              <VImg
                src="/images/logo.png"
                alt="Contenedor inteligente Renova"
                class="position-relative z-10 w-full h-full object-contain drop-shadow-2xl"
              />
              <VTooltip activator="parent">
                <span>Imagen temporal :)</span>
              </VTooltip>
            </VSheet>
            <div class="position-absolute blur-circle blur-circle-1" />
            <div class="position-absolute blur-circle blur-circle-2" />
          </div>
        </VCol>
      </VRow>
    </VContainer>
  </section>
</template>

<style scoped>
.space-y-8 > * + * {
  margin-top: 32px;
}
.gap-4 {
  gap: 16px;
}
.gap-8 {
  gap: 32px;
}
.leading-tight {
  line-height: 1.25;
}
.leading-relaxed {
  line-height: 1.625;
}
.text-balance {
  text-wrap: balance;
}
.text-pretty {
  text-wrap: pretty;
}
.h-12 {
  height: 48px;
}
.aspect-square {
  aspect-ratio: 1;
}
.inset-0 {
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
.z-10 {
  z-index: 10;
}
.blur-circle {
  width: 128px;
  height: 128px;
  border-radius: 50%;
  filter: blur(64px);
}
.blur-circle-1 {
  bottom: -24px;
  right: -24px;
  background: rgba(5, 209, 110, 0.2);
}
.blur-circle-2 {
  top: -24px;
  left: -24px;
  background: rgba(2, 70, 83, 0.2);
}
</style>
