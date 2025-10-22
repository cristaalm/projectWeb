<script setup>
import avatar from '@images/placeholders/avatar.png?url'
import { useUpdateAvatar } from '@/hooks/Profile/Account/useUpdateAvatar'
import { useUpdateData } from '@/hooks/Profile/Account/useUpdateData'
import { useDarkModeStore } from '@/store/dark-mode'
import { storageURL } from '@/utils/constants'
import { useAuthStore } from '@/store/auth'
import { computed } from 'vue'
import { IMask } from 'vue-imask'

const authStore = useAuthStore()

const {
  data: dataUser,
  validateData,
  isChanged,
  resetBaseData: resetForm,
  updateUser,
  loadingUpdateUser,
} = useUpdateData({
  name: authStore.user.name,
  last_name: authStore.user.last_name,
  phone: formatPhone(authStore.user.phone),
  curp: authStore.user.curp,
})

const darkModeStore = useDarkModeStore()
const refInputEl = ref()
const deleteingAvatar = ref(false)
const updateingAvatar = ref(false)

const { loading: loadingAvatar, updateAvatar } = useUpdateAvatar()

const changeAvatar = async file => {
  try {
    updateingAvatar.value = true

    const { files } = file.target
    if (files && files.length) {
      const response = await updateAvatar({ avatar: files[0] })
  
      refInputEl.value.value = ''
    }
  } finally {
    updateingAvatar.value = false
  }
}

const resetAvatar = async () => {
  try {
    deleteingAvatar.value = true
    if (await updateAvatar({ deleteAvatar: true })) {
      authStore.user.avatar = null
    }
  } finally {
    deleteingAvatar.value = false
  }
}

const avatarImg = computed(() => {
  if (authStore.user.avatar !== null) return storageURL + authStore.user.avatar
  
  return avatar
})

function formatPhone(phone) {
  const mask = IMask.createMask({
    mask: '(000) 000-0000',
  })

  if (phone.length === 0) return
  mask.resolve(phone)

  return mask.value
}

function updatePhone(event) {
  const mask = IMask.createMask({
    mask: '(000) 000-0000',
  })

  if (event.target.value.length === 0) return
  mask.resolve(event.target.value || event)

  dataUser.value.phone = mask.value
}
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Detalles de la cuenta">
        <VCardText class="d-flex">
          <!-- 👉 Avatar -->
          <div class="me-6">
            <VImg
              v-if="authStore.user.avatar"
              class="w-24 h-24 rounded-lg"
              :src="avatarImg"
            />
            <VImg
              v-else
              class="w-24 h-24 rounded-lg"
              :src="avatarImg"
            />
          </div>

          <!-- 👉 Upload Photo -->
          <form class="d-flex flex-column justify-center gap-5">
            <div class="d-flex flex-wrap gap-2">
              <VBtn
                color="primary"
                :disabled="loadingAvatar"
                :loading="updateingAvatar"
                @click="refInputEl?.click()"
              >
                <VIcon
                  icon="bx-cloud-upload"
                  class="d-sm-none"
                />
                <span class="d-none d-sm-block">Cargar imagen</span>
              </VBtn>

              <input
                ref="refInputEl"
                type="file"
                name="file"
                accept=".jpeg,.png,.jpg,GIF"
                hidden
                @input="changeAvatar"
              >

              <VBtn
                v-if="authStore.user.avatar !== null"
                type="reset"
                color="error"
                variant="tonal"
                :disabled="loadingAvatar"
                :loading="deleteingAvatar"
                @click="resetAvatar"
              >
                <span class="d-none d-sm-block">Restaurar</span>
                <VIcon
                  icon="bx-refresh"
                  class="d-sm-none"
                />
              </VBtn>
            </div>

            <p class="text-body-1 mb-0">
              Permitido JPG, JPEG o PNG. Tamaño máximo de 2MB
            </p>
          </form>
        </VCardText>

        <VDivider />

        <VCardText>
          <!-- 👉 Form -->
          <VForm class="mt-6">
            <VRow>
              <!-- 👉 First Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="dataUser.name"
                  :color="darkModeStore.darkMode ? 'white' : 'primary'"
                  :error="!validateData.name"
                  error-message="Nombre inválido"
                  placeholder="Nombre"
                  label="Nombre"
                />
              </VCol>

              <!-- 👉 Last Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="dataUser.last_name"
                  :color="darkModeStore.darkMode ? 'white' : 'primary'"
                  :error="!validateData.last_name"
                  error-message="Apellido inválido"
                  placeholder="Apellido"
                  label="Apellido"
                />
              </VCol>

              <!-- 👉 Phone -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="dataUser.phone"
                  :color="darkModeStore.darkMode ? 'white' : 'primary'"
                  :error="!validateData.phone"
                  error-message="Teléfono inválido"
                  placeholder="(###) ###-####"
                  label="Teléfono"
                  @input="(e) => {updatePhone(e)}"
                />
              </VCol>


              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="dataUser.curp"
                  :color="darkModeStore.darkMode ? 'white' : 'primary'"
                  :error="!validateData.curp"
                  error-message="CURP inválido"
                  placeholder="##################"
                  label="CURP"
                />
              </VCol>
              <Transition
                name="slide-fade"
                mode="out-in"
              >
                <VCol
                  v-if="isChanged"
                  key="save-button"
                  md="6"
                  cols="12"
                  class="d-flex flex-wrap gap-4"
                >
                  <VBtn 
                    :disabled="!validateData.success || loadingUpdateUser || !isChanged"
                    @click="updateUser"
                  >
                    Guardar cambios
                  </VBtn>
                </VCol>
              </Transition>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style scoped>
/* Animación personalizada: fade + slide desde abajo */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}
.slide-fade-leave-active {
  transition: all 0.2s ease-in;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
.slide-fade-enter-to,
.slide-fade-leave-from {
  opacity: 1;
  transform: translateY(0);
}
</style>
