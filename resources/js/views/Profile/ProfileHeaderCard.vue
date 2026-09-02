<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { useUpdateAvatar } from '@/hooks/Profile/useUpdateAvatar'
import { useAuthStore } from '@/store/auth'
import { storageURL } from '@/utils/constants'
import { computed, ref } from 'vue'
import { useTheme } from 'vuetify'

const props = defineProps({
  fullName: { type: String, required: true },
})

const authStore = useAuthStore()
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
  <VCard
    variant="flat"
    class="profile-header"
  >
    <VIcon
      icon="bx-recycle"
      size="240"
      class="profile-header__glyph"
    />

    <VCardText class="profile-header__content">
      <div class="profile-header__identity">
        <p
          v-if="props.fullName"
          class="text-h4 font-weight-medium mb-1 text-truncate"
        >
          {{ props.fullName }}
        </p>
        <p
          v-else
          class="text-h5 font-weight-medium font-italic text-medium-emphasis mb-1"
        >
          Agregá tu nombre abajo
        </p>
        <p class="text-body-2 text-medium-emphasis mb-0 text-truncate">
          {{ authStore.user?.email }}
        </p>
      </div>

      <div class="profile-header__avatar-block">
        <div class="profile-header__avatar-wrap">
          <UserAvatar
            size="88"
            :name="props.fullName"
            :avatar-url="avatarUrl"
          />
          <VBtn
            icon
            size="small"
            :color="activeColor"
            :loading="avatarLoading"
            class="profile-header__edit-btn"
            @click="avatarInput.click()"
          >
            <VIcon
              icon="bx-camera"
              size="18"
            />
          </VBtn>
          <input
            ref="avatarInput"
            type="file"
            accept="image/png,image/jpeg,image/jpg,image/webp"
            class="d-none"
            @change="onAvatarSelected"
          >
        </div>
        <VBtn
          v-if="authStore.user?.avatar"
          variant="text"
          size="small"
          color="error"
          :loading="avatarLoading"
          @click="deleteAvatar"
        >
          Eliminar foto
        </VBtn>
      </div>
    </VCardText>
  </VCard>
</template>

<style scoped>
.profile-header {
  position: relative;
  overflow: hidden;
}

.profile-header__glyph {
  position: absolute;
  inset-block-start: -50px;
  inset-inline-end: -40px;
  color: rgb(var(--v-theme-primary));
  opacity: 0.08;
  pointer-events: none;
}

.profile-header__content {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
}

.profile-header__identity {
  min-inline-size: 0;
  flex: 1 1 auto;
}

.profile-header__avatar-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.profile-header__avatar-wrap {
  position: relative;
}

.profile-header__edit-btn {
  position: absolute;
  inset-block-end: -4px;
  inset-inline-end: -4px;
  border: 2px solid rgb(var(--v-theme-surface));
}

@media (max-width: 600px) {
  .profile-header__content {
    flex-direction: column-reverse;
    text-align: center;
  }

  .profile-header__identity {
    inline-size: 100%;
  }
}
</style>
