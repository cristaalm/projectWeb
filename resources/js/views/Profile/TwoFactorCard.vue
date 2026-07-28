<script setup>
import RecoveryCodesDialog from './RecoveryCodesDialog.vue'
import { useTwoFactorSettings } from '@/hooks/Profile/useTwoFactorSettings'
import { useAuthStore } from '@/store/auth'
import { maskSixDigitCode } from '@/utils/masks'
import QrcodeVue from 'qrcode.vue'
import { ref } from 'vue'

const authStore = useAuthStore()

const {
  loading: twoFactorLoading,
  generateQrCode,
  enableTwoFactor,
  disableTwoFactor,
  regenerateRecoveryCodes,
} = useTwoFactorSettings()

const recoveryCodesDialog = ref(false)
const recoveryCodes = ref([])

// ---------- Activar ----------
const enableStep = ref('idle') // idle | qr
const qrData = ref(null)
const enableCode = ref('')

async function startEnableTwoFactor() {
  const data = await generateQrCode()
  if (!data) return
  qrData.value = data
  enableStep.value = 'qr'
}

async function confirmEnableTwoFactor() {
  const codes = await enableTwoFactor(enableCode.value.replace(/\D/g, ''))
  if (!codes) return
  enableStep.value = 'idle'
  qrData.value = null
  enableCode.value = ''
  recoveryCodes.value = codes
  recoveryCodesDialog.value = true
}

// ---------- Desactivar ----------
const disableForm = ref({ token2FA: '', recovery_code: '' })
const showDisableForm = ref(false)

async function confirmDisableTwoFactor() {
  const ok = await disableTwoFactor(disableForm.value)
  if (!ok) return
  showDisableForm.value = false
  disableForm.value = { token2FA: '', recovery_code: '' }
}

// ---------- Regenerar códigos de recuperación ----------
const regenerateCode = ref('')
const showRegenerateForm = ref(false)

async function confirmRegenerateCodes() {
  const codes = await regenerateRecoveryCodes(regenerateCode.value.replace(/\D/g, ''))
  if (!codes) return
  showRegenerateForm.value = false
  regenerateCode.value = ''
  recoveryCodes.value = codes
  recoveryCodesDialog.value = true
}
</script>

<template>
  <VCard title="Autenticación de dos factores">
    <VCardText>
      <p v-if="!authStore.user?.two_factor_status">
        Tu cuenta no tiene 2FA activo.
      </p>
      <p v-else>
        Tu cuenta tiene 2FA activo.
      </p>

      <!-- Activar -->
      <template v-if="!authStore.user?.two_factor_status">
        <VBtn
          v-if="enableStep === 'idle'"
          color="primary"
          :loading="twoFactorLoading"
          @click="startEnableTwoFactor"
        >
          Activar 2FA
        </VBtn>

        <div
          v-else
          class="d-flex flex-column gap-4"
          style="max-width: 320px;"
        >
          <QrcodeVue
            v-if="qrData?.qr_code_url"
            :value="qrData.qr_code_url"
            :size="200"
          />
          <p class="text-caption">
            Escanea el código con tu app de autenticación (Google Authenticator, Authy, etc.) e ingresa el código generado.
          </p>
          <VTextField
            :model-value="enableCode"
            label="Código de 6 dígitos"
            placeholder="000000"
            @update:model-value="v => enableCode = maskSixDigitCode(v)"
          />
          <VBtn
            color="primary"
            :loading="twoFactorLoading"
            :disabled="enableCode.replace(/\D/g, '').length !== 6"
            @click="confirmEnableTwoFactor"
          >
            Confirmar y activar
          </VBtn>
        </div>
      </template>

      <!-- Desactivar / regenerar -->
      <div
        v-else
        class="d-flex flex-column gap-4"
      >
        <div class="d-flex gap-2">
          <VBtn
            color="error"
            variant="tonal"
            @click="showDisableForm = !showDisableForm"
          >
            Desactivar 2FA
          </VBtn>
          <VBtn
            color="secondary"
            variant="tonal"
            @click="showRegenerateForm = !showRegenerateForm"
          >
            Regenerar códigos de recuperación
          </VBtn>
        </div>

        <div
          v-if="showDisableForm"
          class="d-flex flex-column gap-2"
          style="max-width: 320px;"
        >
          <VTextField
            :model-value="disableForm.token2FA"
            label="Código de autenticación (2FA)"
            placeholder="000000"
            @update:model-value="v => disableForm.token2FA = maskSixDigitCode(v)"
          />
          <p class="text-caption text-center mb-0">
            o
          </p>
          <VTextField
            v-model="disableForm.recovery_code"
            label="Código de recuperación"
          />
          <VBtn
            color="error"
            :loading="twoFactorLoading"
            @click="confirmDisableTwoFactor"
          >
            Confirmar desactivación
          </VBtn>
        </div>

        <div
          v-if="showRegenerateForm"
          class="d-flex flex-column gap-2"
          style="max-width: 320px;"
        >
          <VTextField
            :model-value="regenerateCode"
            label="Código de autenticación (2FA)"
            placeholder="000000"
            @update:model-value="v => regenerateCode = maskSixDigitCode(v)"
          />
          <VBtn
            color="primary"
            :loading="twoFactorLoading"
            :disabled="regenerateCode.replace(/\D/g, '').length !== 6"
            @click="confirmRegenerateCodes"
          >
            Confirmar regeneración
          </VBtn>
        </div>
      </div>
    </VCardText>
  </VCard>

  <RecoveryCodesDialog
    v-model="recoveryCodesDialog"
    :codes="recoveryCodes"
  />
</template>
