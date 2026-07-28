<script setup>
import { usePasswordStrength } from '@/composables/usePasswordStrength'
import { useFieldTouch } from '@/composables/useFieldTouch'
import { useUpdatePassword } from '@/hooks/Profile/useUpdatePassword'
import { useAuthStore } from '@/store/auth'
import { maskSixDigitCode } from '@/utils/masks'
import { computed, ref } from 'vue'

const authStore = useAuthStore()
const { loading: passwordLoading, updatePassword } = useUpdatePassword()

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
  token2FA: '',
})

const strength = computed(() => usePasswordStrength(passwordForm.value.password))
const strengthColor = computed(() => ['error', 'error', 'warning', 'success', 'success'][strength.value.score])

const isCurrentPasswordVisible = ref(false)
const isNewPasswordVisible = ref(false)
const isPasswordConfirmationVisible = ref(false)

const needsTwoFactor = computed(() => !!authStore.user?.two_factor_status)

const { touched, touch, reset: resetTouched } = useFieldTouch([
  'current_password',
  'password',
  'password_confirmation',
  'token2FA',
])

const errors = computed(() => {
  const e = {}

  if (!passwordForm.value.current_password) e.current_password = 'La contraseña actual es obligatoria.'

  if (!passwordForm.value.password) e.password = 'La contraseña nueva es obligatoria.'
  else if (!strength.value.isValid) e.password = 'La contraseña no cumple los requisitos de seguridad.'

  if (!passwordForm.value.password_confirmation) e.password_confirmation = 'Confirma la contraseña nueva.'
  else if (passwordForm.value.password_confirmation !== passwordForm.value.password) e.password_confirmation = 'Las contraseñas no coinciden.'

  if (needsTwoFactor.value && passwordForm.value.token2FA.replace(/\D/g, '').length !== 6) {
    e.token2FA = 'El código debe tener 6 dígitos.'
  }

  return e
})

const canSubmit = computed(() => Object.keys(errors.value).length === 0)

function submitPassword() {
  updatePassword(passwordForm.value).then(ok => {
    if (ok) {
      passwordForm.value = { current_password: '', password: '', password_confirmation: '', token2FA: '' }
      resetTouched()
    }
  })
}
</script>

<template>
  <VCard title="Contraseña">
    <VCardText>
      <VForm @submit.prevent="submitPassword">
        <VRow>
          <VCol cols="12">
            <VTextField
              v-model="passwordForm.current_password"
              :type="isCurrentPasswordVisible ? 'text' : 'password'"
              :append-inner-icon="isCurrentPasswordVisible ? 'bx-hide' : 'bx-show'"
              label="Contraseña actual"
              required
              :error="touched.current_password && !!errors.current_password"
              :error-messages="touched.current_password ? errors.current_password : ''"
              @click:append-inner="isCurrentPasswordVisible = !isCurrentPasswordVisible"
              @blur="touch('current_password')"
            />
          </VCol>
          <VCol
            cols="12"
            md="6"
          >
            <VTextField
              v-model="passwordForm.password"
              :type="isNewPasswordVisible ? 'text' : 'password'"
              :append-inner-icon="isNewPasswordVisible ? 'bx-hide' : 'bx-show'"
              label="Contraseña nueva"
              required
              :error="touched.password && !!errors.password"
              :error-messages="touched.password ? errors.password : ''"
              @click:append-inner="isNewPasswordVisible = !isNewPasswordVisible"
              @blur="touch('password', 'password_confirmation')"
            />
            <div
              v-if="passwordForm.password"
              class="mt-2"
            >
              <VProgressLinear
                :model-value="strength.score * 25"
                :color="strengthColor"
                height="6"
                rounded
              />
              <p class="text-caption mt-1 mb-0">
                {{ strength.label }}
              </p>
              <ul
                v-if="strength.missing.length"
                class="text-caption text-medium-emphasis pl-4 mb-0"
              >
                <li
                  v-for="item in strength.missing"
                  :key="item"
                >
                  {{ item }}
                </li>
              </ul>
            </div>
          </VCol>
          <VCol
            cols="12"
            md="6"
          >
            <VTextField
              v-model="passwordForm.password_confirmation"
              :type="isPasswordConfirmationVisible ? 'text' : 'password'"
              :append-inner-icon="isPasswordConfirmationVisible ? 'bx-hide' : 'bx-show'"
              label="Confirmar contraseña nueva"
              required
              :error="touched.password_confirmation && !!errors.password_confirmation"
              :error-messages="touched.password_confirmation ? errors.password_confirmation : ''"
              @click:append-inner="isPasswordConfirmationVisible = !isPasswordConfirmationVisible"
              @blur="touch('password_confirmation', 'password')"
            />
          </VCol>
          <VCol
            v-if="needsTwoFactor"
            cols="12"
            md="6"
          >
            <VTextField
              :model-value="passwordForm.token2FA"
              label="Código de autenticación (2FA)"
              placeholder="000000"
              :error="touched.token2FA && !!errors.token2FA"
              :error-messages="touched.token2FA ? errors.token2FA : ''"
              @update:model-value="v => passwordForm.token2FA = maskSixDigitCode(v)"
              @blur="touch('token2FA')"
            />
          </VCol>
          <VCol cols="12">
            <VBtn
              type="submit"
              color="primary"
              :loading="passwordLoading"
              :disabled="!canSubmit"
            >
              Actualizar contraseña
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
