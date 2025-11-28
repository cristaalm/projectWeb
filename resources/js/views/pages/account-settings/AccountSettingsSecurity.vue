<script setup>
import { useChangePass } from '@/hooks/Profile/ChangePass/useChangePass'
import { useValidations } from '@/hooks/Profile/ChangePass/useValidate'
import useGenerate2FA from '@/hooks/Profile/Auth2FA/useGenerate2FA'
import QrcodeVue from 'qrcode.vue'
import { IMask } from 'vue-imask'
import { useDarkModeStore } from '@/store/dark-mode'
import { useAuthStore } from '@/store/auth'
import { useEnable2FA } from '@/hooks/Profile/Auth2FA/useEnable2FA'
import { useDisabled2FA } from '@/hooks/Profile/Auth2FA/useDisabled2FA'

const authStore = useAuthStore()
const darkModeStore = useDarkModeStore()

const levelProcess2FA = ref(authStore.user.two_factor_status ? 4 : 1)
const isCurrentPasswordVisible = ref(false)
const isNewPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

const {  loading, updatePass, passData } = useChangePass()
const {  formValidate, formErrors, touchField, touchedFields, resetValidations } = useValidations({ passData })

const { loading: loadingGenerate2FA, data2FA, generateQR2FA } = useGenerate2FA({ levelProcess2FA })

const { enable2FA, loading: loadingEnable2FA, validate2FA } = useEnable2FA({ levelProcess2FA })

const { disable2FA, loading: loadingDisable2FA } = useDisabled2FA({ levelProcess2FA })

const token2FA = ref('')

const activeTapChangePass = ref(false)
const activeTapTwoFactor = ref(false)

const passwordRequirements = [
  'Mínimo 8 caracteres de largo - cuanto más, mejor',
  'Al menos una letra minúscula',
  'Al menos una letra mayúscula',
  'Al menos un número',
  'Al menos un símbolo',
]

async function handleSavePass() {
  if (!formValidate.value) return

  const result = await updatePass()

  if (!result) return

  resetValidations()
}

function updateToken(event) {
  const mask = IMask.createMask({
    mask: '000-000',
  })

  if (event.target.value.length === 0) return
  mask.resolve(event.target.value || event)

  token2FA.value = mask.value
}

watch(levelProcess2FA, async newVal => {
  if (newVal === 3 && !data2FA.value.qr_code_url) {
    const success = await generateQR2FA()
    if (!success) {
      levelProcess2FA.value = 2 // retroceder si falla
    }
  }
})

const handleEnable2FA = async () => {
  if (!token2FA.value) return
  if (token2FA.value.replace(/\D/g, '').length !== 6) return

  const isActivate = await enable2FA({ token2FA: token2FA.value.replace(/\D/g, '') })

  if (!isActivate) return

  token2FA.value = ''
}
</script>

<template>
  <VRow>
    <!-- SECTION: Change Password -->
    <VCol cols="12">
      <VCard>
        <div
          class="flex flex-row items-center justify-between p-4 cursor-pointer hover:bg-gray-500/10 dark:hover:bg-gray-100/10"
          @click="activeTapChangePass = !activeTapChangePass"
        >
          <h1 class="text-3xl font-semibold text-gray-800 dark:text-slate-200">
            Cambiar contraseña
          </h1>
          <div class="flex flex-row justify-start">
            <span class="mt-1 text-lg text-gray-500 p-2 rounded-lg bg-gray-200 hover:text-white hover:bg-gray-500 dark:hover:bg-gray-500 dark:bg-gray-700 dark:text-gray-300 cursor-pointer transition-colors">
              <VIcon
                icon="bx-chevron-up"
                class="transition-transform duration-300"
                :class="activeTapChangePass ? 'rotate-180' : ''"
                size="32"
              />
            </span>
          </div>
        </div>
        <Transition name="slide">
          <div
            v-show="activeTapChangePass"
            class="space-y-6"
          >
            <VForm>
              <VCardText>
                <!-- 👉 Current Password -->
                <VRow>
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <!-- 👉 current password -->
                    <VTextField
                      v-model="passData.current_password"
                      :type="isCurrentPasswordVisible ? 'text' : 'password'"
                      :append-inner-icon="isCurrentPasswordVisible ? 'bx-hide' : 'bx-show'"
                      :color="darkModeStore.darkMode ? 'white' : 'primary'" 
                      :class="formErrors.current_password ? '!max-h-[60px]' : '!max-h-[38px]'"
                      :disabled="loading"
                      :error="touchedFields.current_password && !!formErrors.current_password"
                      :error-messages="touchedFields.current_password ? formErrors.current_password : ''"
                      label="Contraseña actual"
                      placeholder="············"
                      @click:append-inner="isCurrentPasswordVisible = !isCurrentPasswordVisible"
                      @input="touchField('current_password')"
                    />
                  </VCol>
                </VRow>

                <!-- 👉 New Password -->
                <VRow>
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <!-- 👉 new password -->
                    <VTextField
                      v-model="passData.password"
                      :type="isNewPasswordVisible ? 'text' : 'password'"
                      :append-inner-icon="isNewPasswordVisible ? 'bx-hide' : 'bx-show'"
                      :color="darkModeStore.darkMode ? 'white' : 'primary'" 
                      :class="formErrors.password ? '!max-h-[60px]' : '!max-h-[38px]'"
                      :disabled="loading"
                      :error="touchedFields.password && !!formErrors.password"
                      :error-messages="touchedFields.password ? formErrors.password : ''"
                      label="Nueva contraseña"
                      autocomplete="on"
                      placeholder="············"
                      @click:append-inner="isNewPasswordVisible = !isNewPasswordVisible"
                      @input="() => {touchField('password')}"
                    />
                  </VCol>

                  <VCol
                    cols="12"
                    md="6"
                  >
                    <!-- 👉 confirm password -->
                    <VTextField
                      v-model="passData.password_confirmation"
                      :type="isConfirmPasswordVisible ? 'text' : 'password'"
                      :append-inner-icon="isConfirmPasswordVisible ? 'bx-hide' : 'bx-show'"
                      :color="darkModeStore.darkMode ? 'white' : 'primary'" 
                      :class="formErrors.password_confirmation ? '!max-h-[60px]' : '!max-h-[38px]'"
                      :disabled="loading"
                      :error="touchedFields.password_confirmation && !!formErrors.password_confirmation"
                      :error-messages="touchedFields.password_confirmation ? formErrors.password_confirmation : ''"
                      label="Confirmar contraseña"
                      placeholder="············"
                      @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                      @input="touchField('password_confirmation')"
                    />
                  </VCol>
                </VRow>
              </VCardText>

              <!-- 👉 Password Requirements -->
              <VCardText>
                <p class="text-base font-weight-medium mt-2">
                  Requisitos de contraseña:
                </p>

                <ul class="d-flex flex-column gap-y-3">
                  <li
                    v-for="item in passwordRequirements"
                    :key="item"
                    class="d-flex"
                  >
                    <div>
                      <VIcon
                        size="7"
                        icon="bxs-circle"
                        class="me-3"
                      />
                    </div>
                    <span class="font-weight-medium">{{ item }}</span>
                  </li>
                </ul>
              </VCardText>

              <!-- 👉 Action Buttons -->
              <VCardText class="d-flex flex-wrap gap-4">
                <VBtn
                  color="success"
                  variant="flat"
                  :disabled="loading || !formValidate"
                  :loading="loading"
                  prepend-icon="bx-save"
                  @click="handleSavePass"
                >
                  Cambiar contraseña
                </VBtn>
              </VCardText>
            </VForm>
          </div>
        </Transition>
      </VCard>
    </VCol>
    <!-- !SECTION -->

    <!-- SECTION Two-steps verification -->
    <VCol cols="12">
      <VCard>
        <div
          class="flex flex-row items-center justify-between p-4 cursor-pointer hover:bg-gray-400/10 dark:hover:bg-gray-100/10"
          @click="activeTapTwoFactor = !activeTapTwoFactor"
        >
          <h1 class="text-3xl font-semibold text-gray-800 dark:text-slate-200">
            Autenticación de dos factores
          </h1>
          <div class="flex flex-row justify-start">
            <span class="mt-1 text-lg text-gray-500 p-2 rounded-lg bg-gray-200 hover:text-white hover:bg-gray-500 dark:hover:bg-gray-500 dark:bg-gray-700 dark:text-gray-300 cursor-pointer transition-colors">
              <VIcon
                icon="bx-chevron-up"
                class="transition-transform duration-300"
                :class="activeTapTwoFactor ? 'rotate-180' : ''"
                size="32"
              />
            </span>
          </div>
        </div>
        <Transition name="slide">
          <div
            v-show="activeTapTwoFactor"
            class="space-y-6"
          >
            <!-- Deshabilitado -->
            <VCardText v-if="levelProcess2FA === 1">
              <p class="font-weight-semibold">
                La autenticación de dos factores no está habilitada.
              </p>
              <p>
                La autenticación de dos factores agrega una capa adicional de seguridad a su cuenta al requerir más que solo una contraseña para iniciar sesión.
              </p>

              <div class="flex flex-row justify-end gap-2">
                <VBtn
                  color="success"
                  variant="flat"
                  prepend-icon="bx-lock"
                  @click="levelProcess2FA = 2"
                >
                  Habilitar 2FA
                </VBtn>
              </div>
            </VCardText>

            <!-- Instrucciones para configurar 2FA -->
            <VCardText v-if="levelProcess2FA === 2">
              <h3 class="text-h6 font-weight-bold mb-4">
                Configura la autenticación de dos factores (2FA)
              </h3>

              <p class="mb-4">
                Para habilitar la autenticación de dos factores, sigue estos pasos:
              </p>

              <VList
                density="compact"
                class="mb-6"
              >
                <VListItem>
                  <template #prepend>
                    <VIcon
                      size="small"
                      :color="darkModeStore.darkMode ? 'white' : 'primary'" 
                    >
                      bx-mobile
                    </VIcon>
                  </template>
                  <VListItemTitle>Descarga una aplicación de autenticación en tu teléfono</VListItemTitle>
                  <VListItemSubtitle>
                    Recomendadas: <strong>Google Authenticator</strong>, <strong>Authy</strong>, <strong>Microsoft Authenticator</strong> o <strong>1Password</strong>.
                  </VListItemSubtitle>
                </VListItem>

                <VListItem>
                  <template #prepend>
                    <VIcon
                      size="small"
                      :color="darkModeStore.darkMode ? 'white' : 'primary'" 
                    >
                      bx-qr
                    </VIcon>
                  </template>
                  <VListItemTitle>Escanea el código QR que aparecerá en el siguiente paso</VListItemTitle>
                  <VListItemSubtitle>
                    La app generará un código de 6 dígitos que cambiará cada 30 segundos.
                  </VListItemSubtitle>
                </VListItem>

                <VListItem>
                  <template #prepend>
                    <VIcon
                      size="small"
                      :color="darkModeStore.darkMode ? 'white' : 'primary'" 
                    >
                      bx-key
                    </VIcon>
                  </template>
                  <VListItemTitle>Ingresa el código para verificar la configuración</VListItemTitle>
                  <VListItemSubtitle>
                    Esto confirmará que todo está funcionando correctamente.
                  </VListItemSubtitle>
                </VListItem>
              </VList>

              <div class="d-flex flex-wrap gap-4 justify-end">
                <VBtn
                  color="error"
                  variant="flat"
                  prepend-icon="bx-x"
                  @click="levelProcess2FA = 1"
                >
                  Cancelar
                </VBtn>
                <VBtn
                  color="success"
                  variant="flat"
                  prepend-icon="bx-chevron-right"
                  @click="levelProcess2FA = 3"
                >
                  Continuar
                </VBtn>
              </div>
            </VCardText>

            <!-- Paso 3: Mostrar QR y campo de verificación -->
            <VCardText v-if="levelProcess2FA === 3">
              <h3 class="text-h6 font-weight-bold mb-4">
                Escanea el código QR
              </h3>

              <p class="mb-4">
                Abre tu aplicación de autenticación y escanea el siguiente código:
              </p>

              <div class="d-flex justify-center mb-6">
                <div
                  v-if="loadingGenerate2FA"
                  class="text-center"
                >
                  <VProgressCircular
                    indeterminate
                    color="primary"
                    size="60"
                  />
                  <p class="mt-2">
                    Generando código QR...
                  </p>
                </div>
                <div
                  v-else-if="data2FA.qr_code_url"
                  class="text-center"
                >
                  <QrcodeVue
                    :value="data2FA.qr_code_url"
                    :size="240"
                    class="border rounded-lg p-2 bg-white"
                  />
                  <p class="mt-2 text-caption text-medium-emphasis">
                    {{ authStore.user.email }}
                  </p>
                </div>
                <div
                  v-else
                  class="text-center"
                >
                  <VIcon
                    icon="bx-error"
                    color="error"
                    size="48"
                  />
                  <p class="mt-2 text-error">
                    No se pudo cargar el código QR.
                  </p>
                </div>
              </div>

              <!-- Campo para ingresar el código -->
              <VTextField
                v-model="token2FA"
                label="Código de verificación"
                placeholder="###-###"
                type="text"
                maxlength="7"
                hint="Ingresa el código de 6 dígitos de tu app de autenticación"
                persistent-hint
                :disabled="loadingEnable2FA || validate2FA"
                @input="(e) => {updateToken(e)}"
                @keydown.enter="handleEnable2FA"
              />

              <div class="d-flex flex-wrap gap-4 justify-end mt-4">
                <VBtn
                  color="error"
                  variant="flat"
                  prepend-icon="bx-x"
                  :disabled="loadingEnable2FA || validate2FA"
                  @click="levelProcess2FA = 2"
                >
                  Atrás
                </VBtn>
                <VBtn
                  color="success"
                  variant="flat"
                  prepend-icon="bx-check"
                  :disabled="!token2FA || token2FA.replace(/\D/g, '').length !== 6 || loadingEnable2FA || validate2FA"
                  :loading="loadingEnable2FA"
                  @click="handleEnable2FA"
                >
                  Verificar y habilitar
                </VBtn>
              </div>
            </VCardText>

            <!-- Paso 4: 2FA habilitada -->
            <VCardText v-if="levelProcess2FA === 4">
              <div class="d-flex flex-column align-center text-center mb-6">
                <VIcon
                  icon="bx-lock"
                  color="success"
                  size="64"
                  class="mb-4"
                />
                <h3 class="text-h6 font-weight-bold">
                  Autenticación de dos factores activada
                </h3>
                <p class="mt-2 max-w-md">
                  Tu cuenta está protegida con autenticación de dos factores. Ahora necesitarás un código de tu aplicación de autenticación cada vez que inicies sesión.
                </p>
              </div>

              <VAlert
                type="info"
                variant="tonal"
                class="mb-6"
              >
                <template #prepend>
                  <VIcon icon="bx-info-circle" />
                </template>
                <span>
                  Si pierdes acceso a tu app de autenticación, contacta al soporte para recuperar tu cuenta.
                </span>
              </VAlert>

              <div class="d-flex flex-wrap gap-4 justify-end">
                <VBtn
                  color="error"
                  variant="flat"
                  prepend-icon="bx-lock-open"
                  @click="levelProcess2FA = 5"
                >
                  Desactivar 2FA
                </VBtn>
                <VBtn
                  color="success"
                  variant="flat"
                  prepend-icon="bx-check-circle"
                  disabled
                >
                  Activado
                </VBtn>
              </div>
            </VCardText>

            <!-- Paso 5: Confirmar desactivación de 2FA -->
            <VCardText v-if="levelProcess2FA === 5">
              <div class="d-flex flex-column align-center text-center mb-6">
                <VIcon
                  icon="bx-lock-open"
                  color="warning"
                  size="64"
                  class="mb-4"
                />
                <h3 class="text-h6 font-weight-bold">
                  ¿Desactivar la autenticación de dos factores?
                </h3>
                <p class="mt-2 max-w-md">
                  Al desactivar 2FA, tu cuenta será menos segura. Solo deberías hacerlo si ya no usas tu app de autenticación o has perdido acceso a ella.
                </p>
              </div>

              <VAlert
                type="warning"
                variant="tonal"
                class="mb-6"
              >
                <template #prepend>
                  <VIcon icon="bx-error" />
                </template>
                <span>
                  Esta acción reducirá la seguridad de tu cuenta. Asegúrate de saber lo que haces.
                </span>
              </VAlert>

              <div class="d-flex flex-wrap gap-4 justify-end">
                <VBtn
                  color="primary"
                  variant="flat"
                  prepend-icon="bx-chevron-left"
                  :disabled="loadingDisable2FA"
                  @click="levelProcess2FA = 4"
                >
                  Volver
                </VBtn>
                <VBtn
                  color="error"
                  variant="flat"
                  prepend-icon="bx-trash"
                  :disabled="loadingDisable2FA"
                  :loading="loadingDisable2FA"
                  @click="disable2FA"
                >
                  Desactivar definitivamente
                </VBtn>
              </div>
            </VCardText>
          </div>
        </Transition>
      </VCard>
    </VCol>
  </VRow>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
