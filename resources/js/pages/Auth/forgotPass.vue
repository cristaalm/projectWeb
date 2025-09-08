<script setup>
import LoadingIcon from '@/components/Base/LoadingIcon/'
import useForgotPass from '@/hooks/Auth/useForgotPass'
import { useRouter } from 'vue-router'

const router = useRouter()
const { success, loading, sendEmail } = useForgotPass()

const form = ref({
  email: '',
})

const goToHome = () => {
  router.push({ name: 'panel' })
}
</script>

<template>
  <div class="justify-center lg:bg-gradient-to-b bg-gradient-to-r  from-[#CFFFE0] to-[#8BE6AE] auth-wrapper d-flex align-center pa-4">
    <div class="position-relative my-sm-16">
      <!-- 👉 Top shape -->
      <VImg
        :src="authV1TopShape"
        :class="success ? '!hidden' : ''"
        class="text-primary auth-v1-top-shape !absolute d-none d-sm-block"
      />

      <!-- 👉 Bottom shape -->
      <VImg
        :src="authV1BottomShape"
        :class="success ? '!hidden' : ''"
        class="text-primary auth-v1-bottom-shape !absolute d-none d-sm-block"
      />
      <!-- 👉 Auth Card -->
      <VCard
        class="auth-card !bg-white/30 !shadow-2xl"
        :class="[success ? 'animate-scaleDown mt-[120px]' : 'animate-scaleUp', $vuetify.display.smAndUp ? 'pa-6' : 'pa-0']"
        max-width="460"
      >
        <VCardItem class="justify-center">
          <div
            class="cursor-pointer app-logo"
            @click="goToHome"
          >
            <div class="d-flex">
              <img
                src="/images/LogoLetra.png"
                alt="RENOVA"
                class="w-[200px] mr-2"  
              >
            </div>
          </div>
        </VCardItem>

        <VCardText>
          <h4 class="relative mb-1 text-h4">
            ¿Olvidaste tu contraseña? <span class="absolute -top-2 ml-2 text-4xl transition-all duration-200 transform animate-wave">🔒</span>
          </h4>
          <p class="mb-0">
            Ingresa tu correo electrónico para solicitar un cambio de contraseña.
          </p>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent>
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <VTextField
                  v-model="form.email"
                  autofocus
                  label="Correo Electrónico"
                  type="email"
                  placeholder="johndoe@email.com"
                  @keyup.enter.prevent="()=> { if (!loading && !success && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) sendEmail(form) }"
                />
              </VCol>

              <!-- submit button -->
              <VCol cols="12">
                <VBtn
                  block
                  type="button"
                  :disabled="loading || !form.email || success || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)"
                  @click.prevent="sendEmail(form)"
                >
                  <span v-if="loading">
                    <LoadingIcon icon="three-dots" />
                  </span>
                  <span v-else>
                    Enviar Solicitud
                  </span>
                </VBtn>
                <VBtn
                  block
                  type="button"
                  variant="text"
                  :disabled="loading"
                  class="mt-4"
                  @click="router.push('/')"
                >
                  Regresar
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
      <VCard
        class="auth-card !bg-white/30 !shadow-2xl"
        :class="[success ? 'animate-scaleUp' : '!hidden', $vuetify.display.smAndUp ? 'pa-6' : 'pa-0']"
        max-width="460"
      >
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
            ¡Solicitud enviada con éxito!
          </h2>
        
          <p class="mb-6 text-body-1 !text-xl">
            Hemos enviado un enlace de recuperación de contraseña a tu correo electrónico.
            <br>
            Por favor, revisa tu bandeja de entrada y sigue las instrucciones.
          </p>

          <VBtn
            block
            type="button"
            color="primary"
            class="mt-4"
            @click="router.push('/')"
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
