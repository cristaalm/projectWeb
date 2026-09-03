<script setup>
import GoogleSignInButton from '@/components/Base/GoogleSignInButton/'
import LoadingIcon from '@/components/Base/LoadingIcon/'
import Lucide from '@/components/Base/Lucide/'
import useGoogleLogin from '@/hooks/Auth/useGoogleLogin'
import useLogin from '@/hooks/Auth/useLogin'
import { useRouter } from 'vue-router'
import AuthLayout from './components/AuthLayout.vue'

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
</script>

<template>
  <AuthLayout
    title="Bienvenido de vuelta"
    description="Escanea, deposita y acumula puntos canjeables en comercios aliados cada vez que reciclas."
    show-features
  >
    <Transition
      name="auth-fade"
      mode="out-in"
    >
      <div
        v-if="!anySuccess"
        key="form"
      >
        <h2 class="auth-heading">
          Inicia sesión
        </h2>
        <p class="auth-subheading">
          Ingresa tus datos para acceder a tu cuenta.
        </p>

        <VForm
          class="mt-8"
          @submit.prevent
        >
          <VTextField
            v-model="form.email"
            variant="underlined"
            autofocus
            label="Correo electrónico"
            type="email"
            placeholder="johndoe@email.com"
            class="font-poppins mb-3"
            :error="error"
            @input="error = false"
          />

          <VTextField
            v-model="form.pass"
            variant="underlined"
            label="Contraseña"
            placeholder="**********"
            :type="isPasswordVisible ? 'text' : 'password'"
            :error="error"
            autocomplete="password"
            :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
            class="font-poppins"
            @input="error = false"
            @keyup.enter="() => { if (!loading && !googleLoading && form.email && form.pass && !anySuccess && !error) loginUser(form) }"
            @click:append-inner="isPasswordVisible = !isPasswordVisible"
          />

          <div class="flex flex-wrap items-center justify-between gap-2 my-5">
            <VCheckbox
              v-model="form.remember"
              class="font-poppins"
              label="Mantener sesión"
              hide-details
            />

            <a
              class="auth-link"
              href="./forgot-password"
              @click.prevent="router.push({ name: 'forgot-password' })"
            >
              Olvidé mi contraseña
            </a>
          </div>

          <VBtn
            block
            size="large"
            type="button"
            class="font-poppins !rounded-full hover:!bg-[#08b662]"
            :disabled="loading || googleLoading || !form.email || !form.pass || anySuccess || error"
            @click="loginUser(form)"
          >
            <span v-if="loading">
              <LoadingIcon icon="three-dots" />
            </span>
            <span v-else>
              Iniciar sesión
            </span>
          </VBtn>

          <div class="my-5 d-flex align-center">
            <VDivider />
            <span class="mx-3 text-medium-emphasis font-poppins text-sm">o</span>
            <VDivider />
          </div>

          <GoogleSignInButton
            :loading="googleLoading"
            text="Continuar con Google"
            size="large"
            pill
            @credential="loginWithGoogle"
          />
        </VForm>
      </div>

      <div
        v-else
        key="success"
      >
        <div class="auth-success-icon">
          <Lucide
            icon="CircleCheckBig"
            class="w-7 h-7"
          />
        </div>
        <h2 class="auth-heading">
          ¡Bienvenido, {{ displayUser?.name || "" }}!
        </h2>
        <p class="auth-subheading">
          Has iniciado sesión con éxito. Te estamos redirigiendo…
        </p>
      </div>
    </Transition>
  </AuthLayout>
</template>
