<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { useThemeSwitcher } from '@/hooks/layout/useThemeSwitcher'
import { storageURL } from '@/utils/constants'
import { useAuthStore } from '@/store/auth'
import { useRouter } from 'vue-router'

const { themeName, changeTheme } = useThemeSwitcher()
const authStore = useAuthStore()
const router = useRouter()

const fullName = computed(() => `${authStore.user.name} ${authStore.user.last_name}`)
const avatarUrl = computed(() => authStore.user.avatar ? storageURL + authStore.user.avatar : null)
</script>

<template>
  <div class="relative">
    <UserAvatar
      class="cursor-pointer"
      variant="tonal"
      :name="fullName"
      :avatar-url="avatarUrl"
    >
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
                  <UserAvatar
                    variant="tonal"
                    :name="fullName"
                    :avatar-url="avatarUrl"
                  />
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ authStore.user.name }} {{ authStore.user.last_name }}
            </VListItemTitle>
            <VListItemSubtitle v-if="authStore.user.role">
              {{ authStore.user.role.name }}
            </VListItemSubtitle>
          </VListItem>
          <VDivider class="my-2" />

          <!-- 👉 Perfil -->
          <VListItem @click.prevent="router.push({ name: 'profile' })">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-user"
                size="22"
              />
            </template>
            <VListItemTitle>
              Perfil
            </VListItemTitle>
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
    </UserAvatar>
  </div>
</template>
