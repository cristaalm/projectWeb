<script setup>
import useResetPassword from '@/hooks/Auth/useResetPassword'
import LoadingIcon from '@/components/Base/LoadingIcon/'
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?url'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?url'
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'

const props = defineProps({
  token: {
    type: String,
    required: true,
  },
  email: {
    type: String,
    required: true, 
  },
})

onMounted(() => {
  console.log('Token:', props.token)
  console.log('Email:', props.email)

  if (!props.token || !props.email) {
    router.push('/')
  }
})

const router = useRouter()

const form = ref({
  newPassword: '',
  confirmPassword: '',
  token: props.token,
  email: props.email,
})

const {
  success,
  loading,
  resetPassword,
} = useResetPassword()

const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

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
        max-width="480"
      >
        <VCardItem class="justify-center">
          <span
            class="app-logo"
            @click="goToHome"
          >
            <div class="d-flex">
              <img
                src="/images/LogoLetra.png"
                alt="RENOVA"
                class="w-[200px] mr-2"  
              >
            </div>
          </span>
        </VCardItem>

        <VCardText>
          <h4 class="font-poppins relative mb-1 text-h4">
            Restablecer Contraseña <span class="absolute -top-2 ml-2 text-4xl transition-all duration-200 transform animate-wave">🔐</span>
          </h4>
          <p class="mb-0 font-poppins">
            Ingresa tu nueva contraseña para restablecer el acceso a tu cuenta.
          </p>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="">
            <VRow>
              <!-- new password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.newPassword"
                  label="Nueva Contraseña"
                  placeholder="**********"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  autocomplete="password"
                  :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />
              </VCol>

              <!-- confirm password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.confirmPassword"
                  label="Confirmar Contraseña"
                  placeholder="**********"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  autocomplete="password"
                  :append-inner-icon="isConfirmPasswordVisible ? 'bx-hide' : 'bx-show'"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                />
              </VCol>

              <!-- submit button -->
              <VCol cols="12">
                <VBtn
                  block
                  type="submit"
                  :disabled="loading || !form.newPassword || !form.confirmPassword || success"
                  class="font-poppins"
                  @click="resetPassword(form)"
                >
                  <span v-if="loading">
                    <LoadingIcon icon="three-dots" />
                  </span>
                  <span v-else>
                    Restablecer Contraseña
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
              icon="CircleCheckBig"
              class="w-full h-full"
            />
          </VAvatar>
        
          <h2 class="mb-2 !text-3xl !font-bold text-h5 font-poppins">
            ¡Contraseña Restablecida!
          </h2>
        
          <p class="mb-6 text-body-1 !text-xl font-poppins">
            Tu contraseña ha sido restablecida con éxito.
            <br>
            Ahora puedes iniciar sesión con tu nueva contraseña.
          </p>

          <VBtn
            block
            color="primary"
            class="font-poppins"
            @click="router.push({ name: 'login' })"
          >
            Ir al Inicio
          </VBtn>
        </div>
      </VCard>
    </div>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
