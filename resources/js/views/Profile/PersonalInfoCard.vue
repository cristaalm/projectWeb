<script setup>
import { useUpdateProfile } from '@/hooks/Profile/useUpdateProfile'
import { useAuthStore } from '@/store/auth'
import { ref } from 'vue'

const authStore = useAuthStore()
const { loading: profileLoading, updateProfile } = useUpdateProfile()

const profileForm = ref({
  name: authStore.user?.name ?? '',
  last_name: authStore.user?.last_name ?? '',
})

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
            >
              Guardar
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
