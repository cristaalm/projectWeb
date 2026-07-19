<script setup>
import avatar from '@images/placeholders/avatar.png?url'
import { useThemeSwitcher } from '@/hooks/layout/useThemeSwitcher'
import { storageURL } from '@/utils/constants'
import { useAuthStore } from '@/store/auth'
import { useRouter } from 'vue-router'

const { themeName, changeTheme } = useThemeSwitcher()
const authStore = useAuthStore()
const user = authStore.getUser()
const router = useRouter()

const avatarImg = computed(() => {
  if (authStore.user.avatar !== null) return storageURL + authStore.user.avatar
  
  return avatar
})
</script>

<template>
  <div class="relative">
    <VAvatar
      class="cursor-pointer"
      variant="tonal"
    >
      <VImg :src="avatarImg" />
  
      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar variant="tonal">
                    <VImg :src="avatarImg" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ user.name }} {{ user.last_name }}
            </VListItemTitle>
            <VListItemSubtitle v-if="user.role">
              {{ user.role.name }}
            </VListItemSubtitle>
          </VListItem>
          <VDivider class="my-2" />
          <!-- 👉 Modo oscuro / claro -->
          <VListItem
            link
            @click="changeTheme"
          >
            <template #prepend>
              <VIcon
                class="me-2"
                :icon="themeName === 'dark' ? 'bx-sun' : 'bx-moon'"
                size="22"
              />
            </template>
            <VListItemTitle>Modo {{ themeName === 'dark' ? 'claro' : 'oscuro' }}</VListItemTitle>
          </VListItem>
  
          <VDivider class="my-2" />
  
          <!-- 👉 Logout -->
          <VListItem @click.prevent="router.push({ name: 'logout' })">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-log-out"
                size="22"
              />
            </template>
            <VListItemTitle>
              Logout
            </VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </div>
</template>
