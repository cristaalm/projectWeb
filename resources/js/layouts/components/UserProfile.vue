<script setup>
import { useThemeSwitcher } from '@/hooks/layout/useThemeSwitcher'
import { useAuthStore } from '@/store/auth'
import { useRouter } from 'vue-router'

const { themeName, changeTheme } = useThemeSwitcher()
const user = useAuthStore().getUser()
const router = useRouter()
</script>

<template>
  <div class="relative">
    <VAvatar
      class="cursor-pointer !bg-primary/50"
      variant="tonal"
    >
      <!-- <VImg :src="user.avatar" /> -->
      <VIcon
        icon="bx-user"
        class="dark:text-white"
      />
  
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
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <!-- <VImg :src="user.avatar" /> -->
                    <VIcon
                      icon="bx-user"
                      class="dark:text-white"
                    />
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
