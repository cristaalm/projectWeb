<script setup>
import { useUpdateEmail } from '@/hooks/Profile/useUpdateEmail'
import { useAuthStore } from '@/store/auth'
import { maskSixDigitCode } from '@/utils/masks'
import { ref } from 'vue'

const authStore = useAuthStore()
const { loading: emailLoading, updateEmail } = useUpdateEmail()
const emailForm = ref({ email: '', password: '', token2FA: '' })

const isPasswordVisible = ref(false)

function submitEmail() {
  updateEmail(emailForm.value).then(ok => {
    if (ok) emailForm.value = { email: '', password: '', token2FA: '' }
  })
}
</script>

<template>
  <VCard title="Correo">
    <VCardText>
      <p class="mb-4">
        Correo actual: <strong>{{ authStore.user?.email }}</strong>
      </p>
      <VForm @submit.prevent="submitEmail">
        <VRow>
          <VCol cols="12">
            <VTextField
              v-model="emailForm.email"
              type="email"
              label="Correo nuevo"
              required
            />
          </VCol>
          <VCol cols="12">
            <VTextField
              v-model="emailForm.password"
              :type="isPasswordVisible ? 'text' : 'password'"
              :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
              label="Contraseña actual"
              required
              @click:append-inner="isPasswordVisible = !isPasswordVisible"
            />
          </VCol>
          <VCol
            v-if="authStore.user?.two_factor_status"
            cols="12"
            md="6"
          >
            <VTextField
              :model-value="emailForm.token2FA"
              label="Código de autenticación (2FA)"
              placeholder="000000"
              @update:model-value="v => emailForm.token2FA = maskSixDigitCode(v)"
            />
          </VCol>
          <VCol cols="12">
            <VBtn
              type="submit"
              color="primary"
              :loading="emailLoading"
            >
              Actualizar correo
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
