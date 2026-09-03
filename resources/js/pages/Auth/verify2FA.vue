<script setup>
import { useVerify2FA } from '@/hooks/Auth/useVerify2FA'
import { IMask } from 'vue-imask'
import { useRouter } from 'vue-router'
import AuthLayout from './components/AuthLayout.vue'

const router = useRouter()

const { verify2FA, loading, error, validate2FA } = useVerify2FA()

const token2FA = ref('')

function updateToken(event) {
  const mask = IMask.createMask({
    mask: '000-000',
  })

  if (event.target.value.length === 0) return
  mask.resolve(event.target.value || event)

  token2FA.value = mask.value
}

const handleVerify2FA = () => {
  if (!token2FA.value) return
  if (token2FA.value.replace(/\D/g, '').length !== 6) return

  verify2FA({ token2FA: token2FA.value.replace(/\D/g, '') })
}
</script>

<template>
  <AuthLayout
    title="Verificación en dos pasos"
    description="Confirma tu identidad con el código de tu aplicación de autenticación para proteger tu cuenta."
  >
    <h2 class="auth-heading">
      Ingresa tu código
    </h2>
    <p class="auth-subheading">
      Abre tu aplicación de autenticación y escribe el código de 6 dígitos.
    </p>

    <VForm
      class="mt-8"
      @submit.prevent="handleVerify2FA"
    >
      <VTextField
        v-model="token2FA"
        autofocus
        type="text"
        placeholder="XXX-XXX"
        class="font-poppins input-text-center"
        :error="error"
        @input="(e) => {error = false; updateToken(e)}"
        @keydown.enter="handleVerify2FA"
      />

      <div class="flex flex-col items-center justify-center w-full gap-2 mt-6">
        <VBtn
          block
          size="large"
          type="submit"
          class="font-poppins !rounded-full hover:!bg-[#08b662]"
          :disabled="loading || token2FA.replace(/\D/g, '').length !== 6 || validate2FA"
          :loading="loading"
        >
          Verificar
        </VBtn>
        <VBtn
          block
          type="button"
          variant="plain"
          color="error"
          class="font-poppins"
          :disabled="loading || validate2FA"
          @click="() => { router.push({ name: 'logout' }) }"
        >
          Regresar
        </VBtn>
      </div>
    </VForm>
  </AuthLayout>
</template>

<style scoped>
.input-text-center :deep(.v-field__input input),
.input-text-center :deep(input) {
  text-align: center !important;
  font-size: 1.25rem;
  letter-spacing: 0.25rem;
}
</style>
