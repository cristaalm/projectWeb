<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useDialogStore } from '@/store/useAlertDialogStorage'

const { t } = useI18n()
const loadingMobileApp = ref(true)

const handleDownloadApp = async () => {
  const dialogStore = useDialogStore()

  const result = await dialogStore.showDialog({
    title: t('landing.cta.dialog.title'),
    text: t('landing.cta.dialog.text'),
    type: 'confirm',
    confirmText: t('landing.cta.dialog.confirm'),
    cancelText: t('landing.cta.dialog.cancel'),
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
        <!-- Blurs decorativos -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-green-400 rounded-full blur-3xl opacity-20" />
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400 rounded-full blur-3xl opacity-20" />

        <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
          <!-- Imagen -->
          <div class="w-full w-1/2 lg:flex items-center justify-center relative hidden">
            <div
              v-if="loadingMobileApp"
              class="absolute inset-0 flex items-center justify-center bg-gray-200/50 rounded-lg z-10"
            >
              <VProgressCircular
                indeterminate
                size="40"
                width="3"
                color="primary"
              />
            </div>
            <img
              src="/images/phone.jpg"
              :alt="t('landing.cta.imageAlt')"
              class="w-auto max-h-[600px] object-contain rounded-xl shadow-lg"
              @load="loadingMobileApp = false"
              @error="loadingMobileApp = false"
            >
          </div>

          <!-- Texto -->
          <div class="w-full lg:w-1/2 text-center md:text-left space-y-6 font-poppins">
            <h2 class="text-4xl md:text-5xl font-bold text-balance">
              {{ t('landing.cta.title') }}
            </h2>

            <p class="text-xl text-muted-foreground max-w-2xl mx-auto md:mx-0 text-pretty">
              {{ t('landing.cta.subtitle') }}
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start pt-4">
              <VBtn
                size="x-large"
                color="primary"
                variant="flat"
                class="text-lg px-8"
                prepend-icon="mdi mdi-cellphone"
                append-icon="mdi mdi-arrow-right"
                @click="handleDownloadApp"
              >
                {{ t('landing.cta.buttons.download') }}
              </VBtn>

              <VBtn
                size="x-large"
                variant="outlined"
                class="text-lg px-8"
                to="login"
              >
                {{ t('landing.cta.buttons.login') }}
              </VBtn>
            </div>
          </div>
        </div>
      </VCard>
    </VContainer>
  </section>
</template>
