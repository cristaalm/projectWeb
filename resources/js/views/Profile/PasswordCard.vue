<script setup>
import { usePasswordStrength } from '@/composables/usePasswordStrength'
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

function submitPassword() {
  updatePassword(passwordForm.value).then(ok => {
    if (ok) passwordForm.value = { current_password: '', password: '', password_confirmation: '', token2FA: '' }
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
              @click:append-inner="isCurrentPasswordVisible = !isCurrentPasswordVisible"
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
              @click:append-inner="isNewPasswordVisible = !isNewPasswordVisible"
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
              @click:append-inner="isPasswordConfirmationVisible = !isPasswordConfirmationVisible"
            />
          </VCol>
          <VCol
            v-if="authStore.user?.two_factor_status"
            cols="12"
            md="6"
          >
            <VTextField
              :model-value="passwordForm.token2FA"
              label="Código de autenticación (2FA)"
              placeholder="000000"
              @update:model-value="v => passwordForm.token2FA = maskSixDigitCode(v)"
            />
          </VCol>
          <VCol cols="12">
            <VBtn
              type="submit"
              color="primary"
              :loading="passwordLoading"
              :disabled="!strength.isValid"
            >
              Actualizar contraseña
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
