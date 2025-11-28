<script setup>
import { useDialogStore } from '@/store/useAlertDialogStorage'
import { ref } from 'vue'

const loadingMobileApp = ref(true)

const handleDownloadApp = async () => {
  const dialogStore = useDialogStore()

  const result = await dialogStore.showDialog({
    title: 'Descargar App Renova',
    text: '¿Deseas descargar la app?, si continuas se descargará un archivo apk',
    type: 'confirm',
    confirmText: 'Descargar',
    cancelText: 'Cancelar',
  })

  if (result) {
    window.open('https://bit.ly/renova-app', '_blank')
  }
}
</script>

<template>
  <section class="py-20 px-4">
    <VContainer class="max-w-5xl">
      <VCard class="relative overflow-hidden p-4 sm:p-12 bg-gradient-to-br from-green-50 to-blue-50">
        <!-- Efectos de blur -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-green-400 rounded-full filter blur-3xl opacity-20" />
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400 rounded-full filter blur-3xl opacity-20" />

        <!-- Contenido principal: imagen a la izquierda, texto y botones a la derecha -->
        <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
          <!-- Imagen del móvil (izquierda) -->
          <div class="w-full w-1/2 lg:flex flex-row items-center justify-center relative hidden">
            <div
              v-if="loadingMobileApp"
              class="absolute inset-0 flex items-center justify-center bg-gray-200/50 rounded-lg z-10"
            >
              <VProgressCircular
                :size="40"
                :width="3"
                color="primary"
                indeterminate
              />
            </div>
            <img
              src="/images/phone.jpg"
              alt="App Renova en dispositivo móvil"
              class="w-auto max-h-[600px] object-contain rounded-xl shadow-lg font-poppins"
              @load="loadingMobileApp = false"
              @error="loadingMobileApp = false"
            >
          </div>

          <!-- Texto y botones (derecha) -->
          <div class="w-full lg:w-1/2 text-center md:text-left space-y-6 font-poppins">
            <h2 class="text-4xl md:text-5xl font-bold text-balance">
              Comienza a reciclar hoy
            </h2>
            <p class="text-xl text-muted-foreground max-w-2xl mx-auto md:mx-0 text-pretty font-poppins">
              Únete a miles de personas que ya están ganando recompensas mientras cuidan el planeta.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start pt-4">
              <VBtn
                size="x-large"
                color="primary"
                variant="flat"
                class="text-lg px-8 font-poppins"
                prepend-icon="mdi mdi-cellphone"
                append-icon="mdi mdi-arrow-right"
                @click="handleDownloadApp"
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
        </div>
      </VCard>
    </VContainer>
  </section>
</template>

<style scoped>
/* Eliminamos todos los estilos personalizados */
</style>
