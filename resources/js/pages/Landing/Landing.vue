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

const { changeThemeToLight } = useThemeSwitcher()
const { showViewTermsModal, openViewTermsModal } = useModalViewTerms()

onMounted(() => {
  changeThemeToLight()
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
</template>
