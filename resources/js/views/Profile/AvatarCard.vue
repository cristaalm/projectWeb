<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { useTheme } from 'vuetify'
import { useUpdateAvatar } from '@/hooks/Profile/useUpdateAvatar'
import { useAuthStore } from '@/store/auth'
import { storageURL } from '@/utils/constants'
import { computed, ref } from 'vue'

const authStore = useAuthStore()
const fullName = computed(() => `${authStore.user?.name ?? ''} ${authStore.user?.last_name ?? ''}`.trim())
const avatarUrl = computed(() => authStore.user?.avatar ? storageURL + authStore.user.avatar : null)

const { loading: avatarLoading, updateAvatar, deleteAvatar } = useUpdateAvatar()
const avatarInput = ref(null)

function onAvatarSelected(event) {
  const file = event.target.files?.[0]

  if (file) updateAvatar(file)
  event.target.value = ''
}

const { name: themeName } = useTheme()
const activeColor = computed(() => themeName.value === 'dark' ? 'info' : 'primary')
</script>

<template>
  <VCard title="Avatar">
    <VCardText class="gap-4 d-flex align-center">
      <UserAvatar
        size="80"
        :name="fullName"
        :avatar-url="avatarUrl"
      />
      <div class="gap-2 d-flex">
        <VBtn
          :color="activeColor"
          variant="tonal"
          :loading="avatarLoading"
          @click="avatarInput.click()"
        >
          Cambiar
        </VBtn>
        <VBtn
          v-if="authStore.user?.avatar"
          color="error"
          variant="tonal"
          :loading="avatarLoading"
          @click="deleteAvatar"
        >
          Eliminar
        </VBtn>
        <input
          ref="avatarInput"
          type="file"
          accept="image/png,image/jpeg,image/jpg,image/webp"
          class="d-none"
          @change="onAvatarSelected"
        >
      </div>
    </VCardText>
  </VCard>
</template>
