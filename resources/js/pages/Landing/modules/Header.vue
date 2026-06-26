<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/store/auth'
import { useRouter } from 'vue-router'
import { useDialogStore } from '@/store/useAlertDialogStorage'
import { useI18n } from 'vue-i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'


const { t } = useI18n()

const handleDownloadApp = async () => {
  const dialogStore = useDialogStore()

  const result = await dialogStore.showDialog({
    title: t('landing.header.dialog.downloadTitle'),
    text: t('landing.header.dialog.downloadText'),
    type: 'confirm',
    confirmText: t('landing.header.dialog.confirm'),
    cancelText: t('landing.header.dialog.cancel'),
  })

  if (result) {
    window.open('https://bit.ly/renova-app', '_blank')
  }
}

const drawer = ref(false)
const authStore = useAuthStore()
const router = useRouter()

onMounted(() => {
  document.addEventListener('click', event => {
    let target = event.target

    while (target && target.tagName !== 'A') {
      target = target.parentElement
    }

    if (target && target.tagName === 'A') {
      const href = target.getAttribute('href')

      if (href && href.startsWith('#')) {
        event.preventDefault()

        const id = href.slice(1)
        const element = document.getElementById(id)

        if (element) {
          const offsetTop = element.offsetTop - 64

          window.scrollTo({
            top: offsetTop,
            behavior: 'smooth',
          })

          drawer.value = false
        }
      }
    }
  })
})
</script>

<template>
  <!-- Drawer móvil -->
  <VNavigationDrawer
    v-model="drawer"
    temporary
    location="right"
    width="250"
    class="bg-background border-l border-border"
  >
    <a
      href="#hero"
      class="flex items-center justify-center gap-2 w-full py-4"
    >
      <VSheet class="w-8 h-8 bg-white rounded-lg d-flex align-center justify-center">
        <img
          src="/images/logo.png"
          alt="Logo"
          class="w-full h-full object-contain"
        >
      </VSheet>
      <span class="text-4xl font-weight-bold tracking-wider mt-1 font-stella">EcoSort</span>
    </a>

    <div class="pa-6 d-flex flex-column gap-6">
      <LanguageSwitcher class="justify-center mb-4" />

      <a
        href="#como-funciona"
        class="text-body-1 font-weight-medium text-foreground text-decoration-none d-block font-poppins"
      >
        {{ t('landing.header.menu.howItWorks') }}
      </a>

      <a
        href="#beneficios"
        class="text-body-1 font-weight-medium text-foreground text-decoration-none d-block font-poppins"
      >
        {{ t('landing.header.menu.benefits') }}
      </a>

      <a
        href="#impacto"
        class="text-body-1 font-weight-medium text-foreground text-decoration-none d-block font-poppins"
      >
        {{ t('landing.header.menu.impact') }}
      </a>

      <VBtn
        v-if="authStore.accessToken"
        color="primary"
        variant="text"
        class="mt-4 text-primary-foreground font-poppins"
        @click="router.push({ name: 'panel' })"
      >
        {{ t('landing.header.actions.goToPanel') }}
      </VBtn>

      <VBtn
        color="primary"
        variant="flat"
        class="text-primary-foreground font-poppins"
        prepend-icon="mdi-cellphone"
        @click="handleDownloadApp"
      >
        {{ t('landing.header.actions.downloadApp') }}
      </VBtn>
    </div>
  </VNavigationDrawer>

  <!-- AppBar -->
  <VAppBar
    fixed
    elevation="0"
    class="bg-background/80 backdrop-blur-md border-b border-border"
    height="64"
  >
    <VContainer class="d-flex align-center justify-space-between px-4">
      <!-- Logo -->
      <a
        href="#hero"
        class="d-flex align-center gap-2 text-decoration-none"
      >
        <VSheet class="w-8 h-8 bg-white rounded-lg d-flex align-center justify-center">
          <img
            src="/images/logo.png"
            alt="Logo"
            class="w-full h-full object-contain"
          >
        </VSheet>

        <span class="text-4xl font-bold tracking-wider mt-1 font-stella">EcoSort</span>
      </a>

      <!-- Menú escritorio -->
      <VToolbarItems class="d-none d-md-flex align-center gap-8">
        <a
          href="#como-funciona"
          class="text-sm font-weight-medium text-foreground hover:text-primary transition-colors text-decoration-none font-poppins"
        >
          {{ t('landing.header.menu.howItWorks') }}
        </a>

        <a
          href="#beneficios"
          class="text-sm font-weight-medium text-foreground hover:text-primary transition-colors text-decoration-none font-poppins"
        >
          {{ t('landing.header.menu.benefits') }}
        </a>

        <a
          href="#impacto"
          class="text-sm font-weight-medium text-foreground hover:text-primary transition-colors text-decoration-none font-poppins"
        >
          {{ t('landing.header.menu.impact') }}
        </a>
      </VToolbarItems>

      <!-- Acciones -->
      <div class="flex flex-row justify-between gap-4 align-center">
        <VBtn
          v-if="authStore.accessToken"
          color="primary"
          variant="text"
          class="text-primary-foreground d-none d-md-flex font-poppins"
          @click="router.push({ name: 'panel' })"
        >
          {{ t('landing.header.actions.goToPanel') }}
        </VBtn>

        <VBtn
          color="primary"
          variant="flat"
          class="text-primary-foreground d-none d-md-flex font-poppins"
          prepend-icon="mdi mdi-cellphone"
          @click="handleDownloadApp"
        >
          {{ t('landing.header.actions.downloadApp') }}
        </VBtn>


        <LanguageSwitcher />
      </div>

      <!-- Hamburguesa -->
      <VAppBarNavIcon
        class="hidden-md-and-up"
        @click="drawer = true"
      />
    </VContainer>
  </VAppBar>
</template>

<style scoped>
.w-8 {
  inline-size: 32px;
}

.h-8 {
  block-size: 32px;
}

.gap-2 {
  gap: 8px;
}

.gap-8 {
  gap: 32px;
}

.tracking-tight {
  letter-spacing: -0.025em;
}
</style>
