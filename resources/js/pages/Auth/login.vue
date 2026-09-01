<script setup>
import LoadingIcon from '@/components/Base/LoadingIcon/'
import Lucide from '@/components/Base/Lucide/'
import useGoogleLogin from '@/hooks/Auth/useGoogleLogin'
import useLogin from '@/hooks/Auth/useLogin'
import { loadGoogleIdentityServices } from '@/utils/loadGoogleIdentityServices'
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?url'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?url'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = ref({
  email: '',
  pass: '',
  remember: false,
})

const {
  user,
  success,
  error,
  loading,
  loginUser,
} = useLogin()

const {
  user: googleUser,
  success: googleSuccess,
  loading: googleLoading,
  loginWithGoogle,
} = useGoogleLogin()

const displayUser = computed(() => user.value || googleUser.value)
const anySuccess = computed(() => success.value || googleSuccess.value)

const isPasswordVisible = ref(false)
const googleBtnRef = ref(null)

const goToHome = () => {
  router.push({ name: 'panel' })
}

onMounted(async () => {
  try {
    await loadGoogleIdentityServices()

    window.google.accounts.id.initialize({
      client_id: import.meta.env.VITE_GOOGLE_CLIENT_ID_WEB,
      callback: response => loginWithGoogle(response.credential),
    })

    window.google.accounts.id.renderButton(googleBtnRef.value, {
      theme: 'outline',
      size: 'large',
      width: 340,
    })
  } catch (err) {
    console.error(err)
  }
})
</script>

<template>
  <!-- <div class="justify-center bg-gradient-to-b from-purple-500 to-blue-300 auth-wrapper d-flex align-center pa-4"> -->
  <div class="justify-center lg:bg-gradient-to-b bg-gradient-to-r  from-[#CFFFE0] to-[#8BE6AE] auth-wrapper d-flex align-center pa-4">
    <div class="position-relative my-sm-16">
      <!-- 👉 Top shape -->
      <VImg
        :src="authV1TopShape"
        class="text-primary auth-v1-top-shape !absolute d-none d-sm-block"
        :class="{ 'shapes-hidden': anySuccess }"
      />

      <!-- 👉 Bottom shape -->
      <VImg
        :src="authV1BottomShape"
        class="text-primary auth-v1-bottom-shape !absolute d-none d-sm-block"
        :class="{ 'shapes-hidden': anySuccess }"
      />

      <!-- 👉 Cards wrapper -->
      <div class="auth-cards-wrapper">
        <!-- Card principal: Login -->
        <VCard
          class="auth-card auth-card--primary !bg-white/30 !shadow-2xl"
          :class="[anySuccess ? 'card-exit' : 'card-enter', $vuetify.display.smAndUp ? 'pa-6' : 'pa-0']"
          max-width="480"
        >
          <VCardItem class="justify-center">
            <span
              class="app-logo d-flex align-center gap-2 cursor-pointer"
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
            </span>
          </VCardItem>

          <VCardText>
            <h4 class="font-poppins relative mb-1 text-h4">
              ¡EcoSort le da la bienvenida! <span class="absolute md:inline-block hidden -top-2 ml-2 text-4xl transition-all duration-200 transform animate-wave">👋🏻</span>
            </h4>
            <p class="mb-0 font-poppins">
              Inicie sesión en su cuenta y comience con su día.
            </p>
          </VCardText>

          <VCardText>
            <VForm @submit.prevent="$router.push('/')">
              <VRow>
                <!-- email -->
                <VCol cols="12">
                  <VTextField
                    v-model="form.email"
                    autofocus
                    label="Correo electrónico"
                    type="email"
                    placeholder="johndoe@email.com"
                    class="font-poppins"
                    :error="error"
                    @input="error = false"
                  />
                </VCol>

                <!-- password -->
                <VCol cols="12">
                  <VTextField
                    v-model="form.pass"
                    label="Contraseña"
                    placeholder="**********"
                    :type="isPasswordVisible ? 'text' : 'password'"
                    :error="error"
                    autocomplete="password"
                    :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
                    class="font-poppins"
                    @input="error = false"
                    @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  />

                  <!-- remember me checkbox -->
                  <div class="flex-wrap my-6 d-flex align-center justify-space-between">
                    <VCheckbox
                      v-model="form.remember"
                      class="font-poppins"
                      label="Mantener sesión"
                    />

                    <a
                      class="text-primary font-poppins"
                      href="./forgot-password"
                      @click.prevent="router.push({ name: 'forgot-password' })"
                    >
                      Olvidé mi contraseña
                    </a>
                  </div>

                  <!-- login button -->
                  <VBtn
                    block
                    type="submit"
                    class="hover:!bg-[#08b662] font-poppins"
                    :disabled="loading || googleLoading || !form.email || !form.pass || anySuccess || error"
                    @click="loginUser(form)"
                  >
                    <span v-if="loading">
                      <LoadingIcon icon="three-dots" />
                    </span>
                    <span v-else>
                      Iniciar Sesión
                    </span>
                  </VBtn>

                  <!-- login con Google -->
                  <div class="my-4 d-flex align-center">
                    <VDivider />
                    <span class="mx-3 text-medium-emphasis font-poppins">o</span>
                    <VDivider />
                  </div>

                  <div
                    v-show="!loading"
                    ref="googleBtnRef"
                    class="d-flex justify-center"
                  />
                </VCol>
              </VRow>
            </VForm>
          </VCardText>
        </VCard>

        <!-- Card secundaria: Éxito -->
        <VCard
          class="auth-card auth-card--secondary !bg-white/30 !shadow-2xl"
          :class="[anySuccess ? 'card-enter' : 'card-exit', $vuetify.display.smAndUp ? 'pa-6' : 'pa-0']"
          max-width="480"
        >
          <div class="text-center pa-6">
            <VAvatar
              size="80"
              color="primary"
              class="p-3 mb-4"
            >
              <Lucide
                icon="CircleCheckBig"
                class="w-full h-full"
              />
            </VAvatar>

            <h2 class="mb-2 !text-3xl !font-bold text-h5 font-poppins">
              ¡Bienvenido, {{ displayUser?.name || "" }}!
            </h2>

            <p class="mb-6 text-body-1 !text-xl font-poppins">
              Has iniciado sesión con éxito.
              <br>
              ¡Disfruta de tu día!
            </p>
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
