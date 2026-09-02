<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { useThemeSwitcher } from '@/hooks/layout/useThemeSwitcher'
import { storageURL } from '@/utils/constants'
import { useAuthStore } from '@/store/auth'
import { useRouter } from 'vue-router'

const props = defineProps({
  // 'navbar': solo el avatar (barra superior, únicamente en el panel
  // principal). 'sidebar': avatar + nombre + rol, fila completa clickeable
  // (pie del menú lateral, visible en el resto de vistas — ver
  // DefaultLayoutWithVerticalNav.vue).
  variant: {
    type: String,
    default: 'navbar',
  },
})

const { themeName, changeTheme } = useThemeSwitcher()
const authStore = useAuthStore()
const router = useRouter()

const fullName = computed(() => `${authStore.user.name} ${authStore.user.last_name}`)
const avatarUrl = computed(() => authStore.user.avatar ? storageURL + authStore.user.avatar : null)
</script>

<template>
  <div
    class="relative flex items-center cursor-pointer"
    :class="variant === 'sidebar' ? 'gap-3 w-full rounded-lg px-2 py-2 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors' : ''"
  >
    <UserAvatar
      variant="tonal"
      :name="fullName"
      :avatar-url="avatarUrl"
    />

    <div
      v-if="variant === 'sidebar'"
      class="flex flex-col min-w-0"
    >
      <span class="text-sm font-semibold truncate">{{ fullName }}</span>
      <span
        v-if="authStore.user.role"
        class="text-xs text-gray-500 dark:text-slate-400 truncate"
      >{{ authStore.user.role.name }}</span>
    </div>

    <!-- SECTION Menu -->
    <VMenu
      activator="parent"
      width="230"
      :location="variant === 'sidebar' ? 'top end' : 'bottom end'"
      offset="14px"
    >
      <VList>
        <!--
          👉 User Avatar & Name — solo en variant="navbar": en "sidebar" ya
          se ve el avatar/nombre/rol en la fila que abre este menú, así que
          repetirlo aquí sería redundante.
        -->
        <template v-if="variant === 'navbar'">
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
        </template>

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
  </div>
</template>
