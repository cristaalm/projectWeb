<script setup>
import Header from '@/pages/Landing/modules/Header.vue'
import Hero from '@/pages/Landing/modules/Hero.vue'
import HowItWorks from '@/pages/Landing/modules/HowItWorks.vue'
import Benefits from '@/pages/Landing/modules/Benefits.vue'
import Impact from '@/pages/Landing/modules/Impact.vue'
import TechnicalSection from '@/pages/Landing/modules/TechnicalSection.vue'
import CTA from '@/pages/Landing/modules/CTA.vue'
import Footer from '@/pages/Landing/modules/Footer.vue'
import { ModalViewTerms, useModalViewTerms } from '@/layouts/components/ModalViewTerms'
import { useThemeSwitcher } from '@/hooks/layout/useThemeSwitcher'
import { ref, onMounted, onBeforeUnmount } from 'vue'

const { changeThemeToLight } = useThemeSwitcher()
const { showViewTermsModal, openViewTermsModal } = useModalViewTerms()

const showScrollTopButton = ref(false)

const handleScroll = () => {
  showScrollTopButton.value = window.scrollY > 300
}

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  })
}

onMounted(() => {
  changeThemeToLight()
  window.addEventListener('scroll', handleScroll)
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <ModalViewTerms v-model="showViewTermsModal" />
  <Header />
  <VMain class="min-h-screen">
    <Hero />
    <HowItWorks />
    <Benefits />
    <Impact />
    <TechnicalSection />
    <CTA />
  </VMain>
  <Footer @open-view-terms-modal="openViewTermsModal" />

  <!-- Envuelve el botón en una transición -->
  <Transition name="fade-slide">
    <VBtn
      v-if="showScrollTopButton"
      icon
      size="small"
      variant="elevated"
      color="primary"
      class="scroll-to-top-btn"
      style="
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1000;
      "
      @click="scrollToTop"
    >
      <VIcon icon="mdi mdi-arrow-up" />
    </VBtn>
  </Transition>
</template>

<style scoped>
/* Animación de entrada y salida */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(20px) scale(0.9);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.9);
}
</style>
