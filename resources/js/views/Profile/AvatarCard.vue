<script setup>
import avatarPlaceholder from '@images/placeholders/avatar.png?url'
import { useUpdateAvatar } from '@/hooks/Profile/useUpdateAvatar'
import { useAuthStore } from '@/store/auth'
import { storageURL } from '@/utils/constants'
import { computed, ref } from 'vue'

const authStore = useAuthStore()
const avatarImg = computed(() => authStore.user?.avatar ? storageURL + authStore.user.avatar : avatarPlaceholder)

const { loading: avatarLoading, updateAvatar, deleteAvatar } = useUpdateAvatar()
const avatarInput = ref(null)

function onAvatarSelected(event) {
  const file = event.target.files?.[0]

  if (file) updateAvatar(file)
  event.target.value = ''
}
</script>

<template>
  <VCard title="Avatar">
    <VCardText class="d-flex align-center gap-4">
      <VAvatar size="80">
        <VImg :src="avatarImg" />
      </VAvatar>
      <div class="d-flex gap-2">
        <VBtn
          color="primary"
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
