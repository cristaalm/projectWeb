<script setup>
import { useVerify2FA } from '@/hooks/Auth/useVerify2FA'
import { useAuthStore } from '@/store/auth'
import { IMask } from 'vue-imask'
import { useRouter } from 'vue-router'

const { user } = useAuthStore()

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
  <!-- <div class="justify-center bg-gradient-to-b from-purple-500 to-blue-300 auth-wrapper d-flex align-center pa-4"> -->
  <div class="justify-center lg:bg-gradient-to-b bg-gradient-to-r  from-[#CFFFE0] to-[#8BE6AE] auth-wrapper d-flex align-center pa-4">
    <div class="position-relative my-sm-16">
      <VCard
        class="auth-card !bg-white/30 !shadow-2xl animate-scaleUp"
        :class="[$vuetify.display.smAndUp ? 'pa-6' : 'pa-0']"
        max-width="480"
      >
        <VCardItem class="justify-center">
          <span class="app-logo d-flex align-center gap-2">
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

        <VCardText class="text-center">
          <h4 class="font-poppins relative mb-1 text-h4">
            Autenticación de dos factores
          </h4>
          <p class="mb-0 font-poppins">
            Ingrese el código de su aplicación de autenticación de dos factores.
          </p>
        </VCardText>

        

        <VCardText>
          <VRow>
            <VCol cols="12">
              <VTextField
                v-model="token2FA"
                autofocus
                type="text"
                placeholder="XXX-XXX"
                class="font-poppins input-text-center !content-center !items-center !justify-center !text-center"
                :error="error"
                @input="(e) => {error = false; updateToken(e)}"
                @keydown.enter="handleVerify2FA"
              />
            </VCol>
            <div
              cols="12"
              class="flex flex-col gap-2 items-center justify-center w-full"
            >
              <!-- login button -->
              <VBtn
                block
                type="submit"
                class="hover:!bg-[#08b662] font-poppins"
                :disabled="loading || token2FA.replace(/\D/g, '').length !== 6 || validate2FA"
                :loading="loading"
                @click="handleVerify2FA"
              >
                Verificar
              </VBtn>
              <VBtn
                block
                type="submit"
                variant="plain"
                color="error"
                class="font-poppins"
                :disabled="loading || validate2FA"
                @click="() =>{router.push({ name: 'logout' })}"
              >
                Regresar
              </VBtn>
            </div>
          </VRow>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>

<style scoped>
.input-text-center :deep(.v-field__input input),
.input-text-center :deep(input) {
  text-align: center !important;
}
</style>



