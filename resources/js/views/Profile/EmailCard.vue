<script setup>
import { useFieldTouch } from '@/composables/useFieldTouch'
import { useUpdateEmail } from '@/hooks/Profile/useUpdateEmail'
import { useAuthStore } from '@/store/auth'
import { maskSixDigitCode } from '@/utils/masks'
import { isValidEmail } from '@/utils/validators'
import { computed, ref } from 'vue'

const authStore = useAuthStore()
const { loading: emailLoading, updateEmail } = useUpdateEmail()
const emailForm = ref({ email: '', password: '', token2FA: '' })

const isPasswordVisible = ref(false)

const needsTwoFactor = computed(() => !!authStore.user?.two_factor_status)

const { touched, touch, reset: resetTouched } = useFieldTouch(['email', 'password', 'token2FA'])

const errors = computed(() => {
  const e = {}

  if (!emailForm.value.email) e.email = 'El correo es obligatorio.'
  else if (!isValidEmail(emailForm.value.email)) e.email = 'Ingresa un correo válido.'

  if (!emailForm.value.password) e.password = 'La contraseña es obligatoria.'

  if (needsTwoFactor.value && emailForm.value.token2FA.replace(/\D/g, '').length !== 6) {
    e.token2FA = 'El código debe tener 6 dígitos.'
  }

  return e
})

const canSubmit = computed(() => Object.keys(errors.value).length === 0)

function submitEmail() {
  updateEmail(emailForm.value).then(ok => {
    if (ok) {
      emailForm.value = { email: '', password: '', token2FA: '' }
      resetTouched()
    }
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
              :error="touched.email && !!errors.email"
              :error-messages="touched.email ? errors.email : ''"
              @blur="touch('email')"
            />
          </VCol>
          <VCol cols="12">
            <VTextField
              v-model="emailForm.password"
              :type="isPasswordVisible ? 'text' : 'password'"
              :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
              label="Contraseña actual"
              required
              :error="touched.password && !!errors.password"
              :error-messages="touched.password ? errors.password : ''"
              @click:append-inner="isPasswordVisible = !isPasswordVisible"
              @blur="touch('password')"
            />
          </VCol>
          <VCol
            v-if="needsTwoFactor"
            cols="12"
            md="6"
          >
            <VTextField
              :model-value="emailForm.token2FA"
              label="Código de autenticación (2FA)"
              placeholder="000000"
              :error="touched.token2FA && !!errors.token2FA"
              :error-messages="touched.token2FA ? errors.token2FA : ''"
              @update:model-value="v => emailForm.token2FA = maskSixDigitCode(v)"
              @blur="touch('token2FA')"
            />
          </VCol>
          <VCol cols="12">
            <VBtn
              type="submit"
              color="primary"
              :loading="emailLoading"
              :disabled="!canSubmit"
            >
              Actualizar correo
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
