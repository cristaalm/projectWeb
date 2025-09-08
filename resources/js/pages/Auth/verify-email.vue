<script setup>
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?url'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?url'
import { useRouter } from 'vue-router'

const router = useRouter()

const goToHome = () => {
  router.push({ name: 'panel' })
}
</script>

<template>
  <div class="justify-center auth-wrapper d-flex align-center h-screen pa-4 bg-[url('/images/background.jpg')] bg-cover ">
    <div class="position-relative my-sm-16">
      <!-- 👉 Top shape -->
      <VImg
        :src="authV1TopShape"
        class="text-primary auth-v1-top-shape !absolute d-none d-sm-block"
      />

      <!-- 👉 Bottom shape -->
      <VImg
        :src="authV1BottomShape"
        class="text-primary auth-v1-bottom-shape !absolute d-none d-sm-block"
      />

      <!-- 👉 Auth Card -->
      <VCard
        class="auth-card animate-scaleUp"
        :class="$vuetify.display.smAndUp ? 'pa-6' : 'pa-0'"
        max-width="460"
      >
        <VCardItem class="justify-center">
          <span
            class="app-logo"
            @click="goToHome"
          >
            <div class="d-flex">
              <img
                src="/logo.png"
                alt="UNIAMA"
                class="w-[200px] mr-2"  
              >
            </div>
          </span>
        </VCardItem>
        <div class="text-center pa-6">
          <VAvatar
            size="80"
            color="primary"
            class="p-3 mb-4"
          >
            <Lucide
              icon="MailCheck"
              class="w-full h-full"
            />
          </VAvatar>
          <h2 class="mb-2 !text-3xl !font-bold text-h5">
            ¡Correo verificado con éxito!
          </h2>
          <p class="mb-6 text-body-1 !text-xl">
            Tu correo electrónico ha sido verificado correctamente.<br>
            Ya puedes cerrar esta pestaña.
          </p>
          <VBtn
            block
            type="button"
            color="primary"
            class="mt-4"
            @click="router.push({ path: '/panel' })"
          >
            Regresar al inicio
          </VBtn>
        </div>
      </VCard>
    </div>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
