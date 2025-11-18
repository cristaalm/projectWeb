<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/store/auth'
import { useRouter } from 'vue-router'
import { useDialogStore } from '@/store/useAlertDialogStorage'

const handleDownloadApp = async () => {
  const dialogStore = useDialogStore()

  const result = await dialogStore.showDialog({
    title: 'Descargar App Renova',
    text: '¿Deseas descargar la app?, si continuas se descargara un archivo apk',
    type: 'confirm',
    confirmText: 'Descargar',
    cancelText: 'Cancelar',
  })

  if (result) {
    window.open('https://bit.ly/renova-app', '_blank')
  }
}

const drawer = ref(false)
const authStore = useAuthStore()
const router = useRouter()

onMounted(() => {
  // Captura todos los enlaces de anclaje
  document.addEventListener('click', event => {
    let target = event.target

    // Si el clic fue en un enlace de anclaje (href="#...")
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
          const offsetTop = element.offsetTop - 64 // altura del header

          window.scrollTo({
            top: offsetTop,
            behavior: 'smooth',
          })

          // Cierra el drawer si está abierto (en móvil)
          drawer.value = false
        }
      }
    }
  })
})
</script>

<template>
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
      <span class="text-3xl font-weight-bold tracking-wider mt-1 font-stella">RENOVA</span>
    </a>

    <div class="pa-6 d-flex flex-column gap-6">
      <a
        href="#como-funciona"
        class="text-body-1 font-weight-medium text-foreground text-decoration-none d-block font-poppins"
      >
        Cómo Funciona
      </a>
      <a
        href="#beneficios"
        class="text-body-1 font-weight-medium text-foreground text-decoration-none d-block font-poppins"
      >
        Beneficios
      </a>
      <a
        href="#impacto"
        class="text-body-1 font-weight-medium text-foreground text-decoration-none d-block font-poppins"
      >
        Impacto
      </a>
      <a
        href="#tecnico"
        class="text-body-1 font-weight-medium text-foreground text-decoration-none d-block font-poppins"
      >
        Técnico
      </a>

      <VBtn
        v-if="authStore.accessToken"
        color="primary"
        variant="text"
        class="mt-4 text-primary-foreground font-poppins"
        @click="router.push({ name: 'panel' })"
      >
        Ir al panel
      </VBtn>

      <VBtn
        color="primary"
        variant="flat"
        class="text-primary-foreground font-poppins"
        prepend-icon="mdi-cellphone"
        @click="handleDownloadApp"
      >
        Descargar App
      </VBtn>
    </div>
  </VNavigationDrawer>
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
       
        <span class="text-3xl font-bold tracking-wider mt-1 font-stella">RENOVA</span>
      </a>

      <!-- Menú de escritorio -->
      <VToolbarItems class="d-none d-md-flex align-center gap-8">
        <a
          href="#como-funciona"
          class="text-sm font-weight-medium text-foreground hover:text-primary transition-colors text-decoration-none font-poppins"
        >
          Cómo Funciona
        </a>
        <a
          href="#beneficios"
          class="text-sm font-weight-medium text-foreground hover:text-primary transition-colors text-decoration-none font-poppins"
        >
          Beneficios
        </a>
        <a
          href="#impacto"
          class="text-sm font-weight-medium text-foreground hover:text-primary transition-colors text-decoration-none font-poppins"
        >
          Impacto
        </a>
        <a
          href="#tecnico"
          class="text-sm font-weight-medium text-foreground hover:text-primary transition-colors text-decoration-none font-poppins"
        >
          Técnico
        </a>
      </VToolbarItems>

      <!-- Botón de descarga (visible en todos los tamaños) -->
      <div class="flex flex-row justify-between gap-2">
        <!-- si tiene token, mostramos la opción de ir al dashboard -->

        <VBtn
          v-if="authStore.accessToken"
          color="primary"
          variant="text"
          class="text-primary-foreground d-none d-md-flex font-poppins"
          @click="router.push({ name: 'panel' })"
        >
          Ir al panel
        </VBtn>

        <VBtn
          color="primary"
          variant="flat"
          class="text-primary-foreground d-none d-md-flex font-poppins"
          prepend-icon="mdi mdi-cellphone"
          @click="handleDownloadApp"
        >
          Descargar App
        </VBtn>
      </div>

      <!-- Menú hamburguesa para móviles -->
      <VAppBarNavIcon
        class="hidden-md-and-up"
        @click="drawer = true"
      />
    </VContainer>
  </VAppBar>
</template>

<style scoped>
.w-8 {
  width: 32px;
}
.h-8 {
  height: 32px;
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
