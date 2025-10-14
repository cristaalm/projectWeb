<script setup>
const emit = defineEmits(['open-view-terms-modal'])
</script>

<template>
  <VFooter class="!bg-[#00464B] !text-slate-200 py-12 px-4">
    <VContainer class="max-w-7xl">
      <VRow class="mb-8">
        <VCol cols="12" md="3">
          <div class="space-y-4">
            <div class="d-flex align-center gap-2">
              <div class="w-8 h-8 bg-[#00464B] rounded-lg d-flex align-center justify-center">
                <img src="/images/logo.png" alt="Logo" />
              </div>
              <span class="text-h5 font-weight-bold text-white">RENOVA</span>
            </div>
            <p class="text-sm leading-relaxed" style="opacity: 0.8">
              Transformando el reciclaje en México mediante tecnología inteligente y recompensas.
            </p>
          </div>
        </VCol>

        <VCol cols="12" md="3">
          <h3 class="font-weight-bold mb-4 text-white">Producto</h3>
          <ul class="footer-links">
            <li><a href="#como-funciona">Cómo Funciona</a></li>
            <li><a href="#beneficios">Beneficios</a></li>
            <li><a href="#impacto">Impacto</a></li>
            <li><a href="#tecnico">Tecnología</a></li>
          </ul>
        </VCol>

        <VCol cols="12" md="3">
          <h3 class="font-weight-bold mb-4 text-white">Redes Sociales</h3>
          <ul class="footer-links">
            <li><a href="#">Facebook</a></li>
            <li><a href="#">Instagram</a></li>
          </ul>
        </VCol>

        <VCol cols="12" md="3">
          <h3 class="font-weight-bold mb-4 text-white">Contacto</h3>
          <ul class="footer-contact">
            <li class="d-flex align-start gap-2">
              <VIcon size="16" class="mt-1">mdi-email</VIcon>
              <span>soyrenovaapp@gmail.com</span>
            </li>
            <li class="d-flex align-start gap-2">
              <VIcon size="16" class="mt-1">mdi-map-marker</VIcon>
              <span>Manzanillo, Colima, México.</span>
            </li>
          </ul>
        </VCol>
      </VRow>

      <VDivider class="mb-8" style="opacity: 0.2" />

      <div class="d-flex flex-column flex-md-row justify-space-between align-center gap-4 text-sm" style="opacity: 0.8">
        <p>&copy; {{ new Date().getFullYear() }} Renova. Todos los derechos reservados.</p>
        <div class="d-flex gap-6">
          <a href="#" class="footer-link" @click="emit('open-view-terms-modal')">Condiciones y términos de uso</a>
        </div>
      </div>
    </VContainer>
  </VFooter>
</template>

<style scoped>
.max-w-7xl {
  max-width: 1280px;
}
.space-y-4 > * + * {
  margin-top: 16px;
}
.gap-2 {
  gap: 8px;
}
.gap-4 {
  gap: 16px;
}
.gap-6 {
  gap: 24px;
}
.w-8 {
  width: 32px;
}
.h-8 {
  height: 32px;
}
.mb-8 {
  margin-bottom: 32px;
}
.mb-4 {
  margin-bottom: 16px;
}
.mt-1 {
  margin-top: 4px;
}
.leading-relaxed {
  line-height: 1.625;
}
.footer-links,
.footer-contact {
  list-style: none;
  padding: 0;
  margin: 0;
}
.footer-links li,
.footer-contact li {
  margin-bottom: 8px;
  font-size: 14px;
}
.footer-links a,
.footer-link {
  color: inherit;
  text-decoration: none;
  transition: color 0.3s;
}
.footer-links a:hover,
.footer-link:hover {
  color: rgb(5, 209, 110);
}
</style>
