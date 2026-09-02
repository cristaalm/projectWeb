<script setup>
import GoogleSignInButton from '@/components/Base/GoogleSignInButton/'
import { useSocialAccounts } from '@/hooks/Profile/useSocialAccounts'
import { useAuthStore } from '@/store/auth'
import { maskSixDigitCode } from '@/utils/masks'
import { computed, ref } from 'vue'

const authStore = useAuthStore()
const { loading: socialLoading, linkGoogleAccount, unlinkGoogleAccount } = useSocialAccounts()

const isGoogleLinked = computed(() => (authStore.user?.social_providers ?? []).includes('google'))
const hasUsablePassword = computed(() => authStore.user?.has_usable_password !== false)
const needsTwoFactor = computed(() => !!authStore.user?.two_factor_status)

// Si Google es la única forma de acceso (sin contraseña real y sin otro
// proveedor vinculado), no se puede desvincular sin quedar afuera del sistema.
const isOnlyAccessMethod = computed(() => isGoogleLinked.value && !hasUsablePassword.value)

const showUnlinkForm = ref(false)
const unlinkForm = ref({ password: '', token2FA: '' })

async function confirmUnlink() {
  const ok = await unlinkGoogleAccount(unlinkForm.value)
  if (!ok) return
  showUnlinkForm.value = false
  unlinkForm.value = { password: '', token2FA: '' }
}
</script>

<template>
  <VCard title="Cuentas vinculadas">
    <VCardText>
      <div class="d-flex align-center justify-space-between mb-4 flex-wrap gap-4">
        <div>
          <p class="mb-0 font-weight-medium">
            Google
          </p>
          <p class="mb-0 text-caption text-medium-emphasis">
            {{ isGoogleLinked ? 'Cuenta vinculada.' : 'No tenés una cuenta de Google vinculada.' }}
          </p>
        </div>

        <VBtn
          v-if="isGoogleLinked"
          color="error"
          variant="tonal"
          :disabled="isOnlyAccessMethod"
          @click="showUnlinkForm = !showUnlinkForm"
        >
          Desvincular
        </VBtn>
        <div
          v-else
          style="width: 240px;"
        >
          <GoogleSignInButton
            text="Vincular con Google"
            @credential="linkGoogleAccount"
          />
        </div>
      </div>

      <p
        v-if="isOnlyAccessMethod"
        class="text-caption text-medium-emphasis"
      >
        Es tu única forma de acceso — configurá una contraseña antes de desvincularla.
      </p>

      <div
        v-if="showUnlinkForm"
        class="d-flex flex-column gap-2"
        style="max-width: 320px;"
      >
        <VTextField
          v-if="hasUsablePassword"
          v-model="unlinkForm.password"
          type="password"
          label="Contraseña actual"
        />
        <VTextField
          v-if="needsTwoFactor"
          :model-value="unlinkForm.token2FA"
          label="Código de autenticación (2FA)"
          placeholder="000000"
          @update:model-value="v => unlinkForm.token2FA = maskSixDigitCode(v)"
        />
        <VBtn
          color="error"
          :loading="socialLoading"
          @click="confirmUnlink"
        >
          Confirmar desvinculación
        </VBtn>
      </div>
    </VCardText>
  </VCard>
</template>
