<script setup>
import { useUpdateProfile } from '@/hooks/Profile/useUpdateProfile'
import { useAuthStore } from '@/store/auth'
import { computed, ref } from 'vue'

const authStore = useAuthStore()
const { loading: profileLoading, updateProfile } = useUpdateProfile()

const profileForm = ref({
  name: authStore.user?.name ?? '',
  last_name: authStore.user?.last_name ?? '',
})

// Comparar contra authStore.user (reactivo) en vez de una copia fija tomada
// al montar — así, tras guardar, el botón vuelve a bloquearse solo (el
// formulario y el store quedan iguales) sin tener que sincronizar nada a mano.
const hasChanges = computed(() =>
  profileForm.value.name !== (authStore.user?.name ?? '')
  || profileForm.value.last_name !== (authStore.user?.last_name ?? ''),
)

const canSubmit = computed(() =>
  hasChanges.value && !!profileForm.value.name.trim() && !!profileForm.value.last_name.trim(),
)

function submitProfile() {
  updateProfile(profileForm.value)
}
</script>

<template>
  <VCard title="Información personal">
    <VCardText>
      <VForm @submit.prevent="submitProfile">
        <VRow>
          <VCol
            cols="12"
            md="6"
          >
            <VTextField
              v-model="profileForm.name"
              label="Nombre"
              required
            />
          </VCol>
          <VCol
            cols="12"
            md="6"
          >
            <VTextField
              v-model="profileForm.last_name"
              label="Apellido"
              required
            />
          </VCol>
          <VCol cols="12">
            <VBtn
              type="submit"
              color="primary"
              :loading="profileLoading"
              :disabled="!canSubmit"
            >
              Guardar
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
