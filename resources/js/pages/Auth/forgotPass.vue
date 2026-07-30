<script setup>
import LoadingIcon from '@/components/Base/LoadingIcon/'
import useForgotPass from '@/hooks/Auth/useForgotPass'
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?url'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?url'
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
        class="text-primary auth-v1-top-shape !absolute d-none d-sm-block"
        :class="{ 'shapes-hidden': success }"
      />

      <!-- 👉 Bottom shape -->
      <VImg
        :src="authV1BottomShape"
        class="text-primary auth-v1-bottom-shape !absolute d-none d-sm-block"
        :class="{ 'shapes-hidden': success }"
      />

      <!-- 👉 Cards wrapper -->
      <div class="auth-cards-wrapper">
        <!-- Card principal: Formulario -->
        <VCard
          class="auth-card auth-card--primary !bg-white/30 !shadow-2xl"
          :class="[success ? 'card-exit' : 'card-enter', $vuetify.display.smAndUp ? 'pa-6' : 'pa-0']"
          max-width="480"
        >
          <VCardItem class="justify-center">
            <div
              class="cursor-pointer app-logo d-flex align-center gap-2"
              @click="goToHome"
            >
              <VSheet class="w-12 h-12 !bg-transparent rounded-lg d-flex align-center justify-center">
                <img
                  src="/images/logo.png"
                  alt="Logo"
                  class="w-full h-full object-contain bg-transparent"
                >
              </VSheet>
              <span class="text-5xl font-weight-bold tracking-wider mt-1 font-stella text-[#13b868]">EcoSort</span>
            </div>
          </VCardItem>

          <VCardText>
            <h4 class="font-poppins relative mb-1 text-h4">
              ¿Olvidaste tu contraseña? <span class="absolute -top-2 ml-2 text-4xl transition-all duration-200 transform animate-wave">🔒</span>
            </h4>
            <p class="mb-0 font-poppins">
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
                    class="font-poppins"
                    @keyup.enter.prevent="()=> { if (!loading && !success && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) sendEmail(form) }"
                  />
                </VCol>

                <!-- submit button -->
                <VCol cols="12">
                  <VBtn
                    block
                    type="button"
                    :disabled="loading || !form.email || success || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)"
                    class="font-poppins"
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
                    class="mt-4 font-poppins"
                    @click="router.push({ name: 'login' })"
                  >
                    Regresar
                  </VBtn>
                </VCol>
              </VRow>
            </VForm>
          </VCardText>
        </VCard>

        <!-- Card secundaria: Confirmación -->
        <VCard
          class="auth-card auth-card--secondary !bg-white/30 !shadow-2xl"
          :class="[success ? 'card-enter' : 'card-exit', $vuetify.display.smAndUp ? 'pa-6' : 'pa-0']"
          max-width="480"
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

            <h2 class="mb-2 !text-3xl !font-bold text-h5 font-poppins">
              ¡Solicitud enviada con éxito!
            </h2>

            <p class="mb-6 text-body-1 !text-xl font-poppins">
              Hemos enviado un enlace de recuperación de contraseña a tu correo electrónico.
              <br>
              Por favor, revisa tu bandeja de entrada y sigue las instrucciones.
            </p>

            <VBtn
              block
              type="button"
              color="primary"
              class="mt-4 font-poppins"
              @click="router.push({ name: 'login'})"
            >
              Regresar al inicio
            </VBtn>
          </div>
        </VCard>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>

<style scoped>
/* Contenedor que define el espacio ocupado (evita colapso de altura) */
.auth-cards-wrapper {
  position: relative;
  width: 480px;
  /* La altura se toma de la card primary en su estado normal */
}

/* Ambas cards se apilan en el mismo espacio */
.auth-card--primary,
.auth-card--secondary {
  width: 100%;
  transition:
    opacity 0.4s ease,
    transform 0.4s ease,
    visibility 0.4s;
}

.auth-card--secondary {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Estado visible */
.card-enter {
  opacity: 1;
  transform: scale(1) translateY(0);
  visibility: visible;
  pointer-events: auto;
}

/* Estado oculto: se contrae hacia arriba y desaparece */
.card-exit {
  opacity: 0;
  transform: scale(0.85) translateY(-24px);
  visibility: hidden;
  pointer-events: none;
}

/* Shapes: fade out suave en lugar de display:none abrupto */
.auth-v1-top-shape,
.auth-v1-bottom-shape {
  transition: opacity 0.3s ease;
}
.shapes-hidden {
  opacity: 0 !important;
  pointer-events: none;
}
</style>
