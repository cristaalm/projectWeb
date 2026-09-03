<script setup>
import LoadingIcon from '@/components/Base/LoadingIcon/'
import Lucide from '@/components/Base/Lucide/'
import useForgotPass from '@/hooks/Auth/useForgotPass'
import { useRouter } from 'vue-router'
import AuthLayout from './components/AuthLayout.vue'

const router = useRouter()
const { success, loading, sendEmail } = useForgotPass()

const form = ref({
  email: '',
})
</script>

<template>
  <AuthLayout
    title="¿Olvidaste tu contraseña?"
    description="No te preocupes, te ayudamos a recuperar el acceso a tu cuenta en un par de pasos."
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
          Recupera tu acceso
        </h2>
        <p class="auth-subheading">
          Ingresa tu correo electrónico para solicitar un cambio de contraseña.
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
            class="font-poppins"
            @keyup.enter.prevent="()=> { if (!loading && !success && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) sendEmail(form) }"
          />

          <VBtn
            block
            size="large"
            type="button"
            class="mt-6 font-poppins !rounded-full hover:!bg-[#08b662]"
            :disabled="loading || !form.email || success || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)"
            @click.prevent="sendEmail(form)"
          >
            <span v-if="loading">
              <LoadingIcon icon="three-dots" />
            </span>
            <span v-else>
              Enviar solicitud
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
            icon="MailCheck"
            class="w-7 h-7"
          />
        </div>
        <h2 class="auth-heading">
          ¡Solicitud enviada con éxito!
        </h2>
        <p class="auth-subheading">
          Enviamos un enlace de recuperación a tu correo. Revisa tu bandeja de entrada y sigue las instrucciones.
        </p>

        <VBtn
          block
          size="large"
          type="button"
          class="mt-6 font-poppins !rounded-full hover:!bg-[#08b662]"
          @click="router.push({ name: 'login' })"
        >
          Regresar al inicio
        </VBtn>
      </div>
    </Transition>
  </AuthLayout>
</template>
