<script setup>
import LoadingIcon from '@/components/Base/LoadingIcon/'
import Lucide from '@/components/Base/Lucide/'
import useResetPassword from '@/hooks/Auth/useResetPassword'
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from './components/AuthLayout.vue'

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

const router = useRouter()

onMounted(() => {
  if (!props.token || !props.email) {
    router.push('/')
  }
})

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

const submitReset = () => {
  if (loading.value || !form.value.newPassword || !form.value.confirmPassword || success.value) return
  resetPassword(form.value)
}
</script>

<template>
  <AuthLayout
    title="Crea una nueva contraseña"
    description="Elige una contraseña segura para proteger tu cuenta y los puntos que ya has ganado."
  >
    <Transition
      name="auth-fade"
      mode="out-in"
    >
      <div
        v-if="!success"
        key="form"
      >
        <h2 class="auth-heading">
          Restablecer contraseña
        </h2>
        <p class="auth-subheading">
          Ingresa tu nueva contraseña para recuperar el acceso a tu cuenta.
        </p>

        <VForm
          class="mt-8"
          @submit.prevent="submitReset"
        >
          <VTextField
            v-model="form.newPassword"
            label="Nueva contraseña"
            placeholder="**********"
            variant="underlined"
            class="font-poppins mb-3"
            :type="isPasswordVisible ? 'text' : 'password'"
            autocomplete="password"
            :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
            @keyup.enter="submitReset"
            @click:append-inner="isPasswordVisible = !isPasswordVisible"
          />

          <VTextField
            v-model="form.confirmPassword"
            label="Confirmar contraseña"
            placeholder="**********"
            variant="underlined"
            class="font-poppins"
            :type="isConfirmPasswordVisible ? 'text' : 'password'"
            autocomplete="password"
            :append-inner-icon="isConfirmPasswordVisible ? 'bx-hide' : 'bx-show'"
            @keyup.enter="submitReset"
            @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
          />

          <VBtn
            block
            size="large"
            type="submit"
            class="mt-6 font-poppins !rounded-full hover:!bg-[#08b662]"
            :disabled="loading || !form.newPassword || !form.confirmPassword || success"
          >
            <span v-if="loading">
              <LoadingIcon icon="three-dots" />
            </span>
            <span v-else>
              Restablecer contraseña
            </span>
          </VBtn>

          <VBtn
            block
            type="button"
            variant="text"
            :disabled="loading"
            class="mt-2 font-poppins !rounded-full"
            @click="router.push({ name: 'login' })"
          >
            Regresar
          </VBtn>
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
          ¡Contraseña restablecida!
        </h2>
        <p class="auth-subheading">
          Tu contraseña se actualizó con éxito. Ya puedes iniciar sesión con tu nueva contraseña.
        </p>

        <VBtn
          block
          size="large"
          type="button"
          class="mt-6 font-poppins !rounded-full hover:!bg-[#08b662]"
          @click="router.push({ name: 'login' })"
        >
          Ir al inicio
        </VBtn>
      </div>
    </Transition>
  </AuthLayout>
</template>
