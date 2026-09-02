<script setup>
import Footer from '@/layouts/components/Footer.vue'
import NavItems from '@/layouts/components/NavItems/NavItems.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'
import { useDarkModeStore } from '@/store/dark-mode'
import { useAuthStore } from '@/store/auth'
import VerticalNavLayout from '@layouts/components/VerticalNavLayout.vue'
import { getHours } from 'date-fns'
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useTheme } from 'vuetify'

const {
  name: themeName,
  global: globalTheme,
} = useTheme()

const authStore = useAuthStore()
const darkModeStore = useDarkModeStore()
const router = useRouter()

// El dashboard sigue la preferencia guardada (a diferencia de las páginas
// públicas/de invitado, que siempre se ven en claro — ver layouts/blank.vue
// y Landing.vue). Restaura tanto el tema de Vuetify como la clase `dark` de
// Tailwind en <html>, que las páginas de invitado pudieron haber forzado a
// claro solo visualmente.
onMounted(() => {
  globalTheme.name.value = darkModeStore.darkMode ? 'dark' : 'light'
  darkModeStore.updateDOMDarkMode(darkModeStore.darkMode)
})

const greeting = computed(() => {
  const hour = getHours(new Date())
  if (hour >= 5 && hour < 12) return 'Buenos días'
  if (hour >= 12 && hour < 19) return 'Buenas tardes'

  return 'Buenas noches'
})

// La barra superior (bienvenida + avatar) solo tiene sentido en el panel
// principal — en el resto de vistas ocupaba espacio de sobra (ver
// VerticalNavLayout.vue, que la colapsa a 0 en desktop cuando no es esta
// ruta). Esas acciones se replican en el pie del menú lateral vía
// UserProfile variant="sidebar" (#after-vertical-nav-items más abajo).
const isPanelRoute = computed(() => router.currentRoute.value.name === 'panel')

const goToHome = () => {
  router.push({ name: 'panel' })
}
</script>

<template>
  <VerticalNavLayout>
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <!-- 👉 Vertical nav toggle in overlay mode -->
        <IconBtn
          class="ms-n3 d-lg-none"
          @click="toggleVerticalOverlayNavActive(true)"
        >
          <VIcon icon="bx-menu" />
        </IconBtn>

        <template v-if="isPanelRoute">
          <!-- texto decorativo de bienvenida junto con el nombre -->
          <span class="text-xl font-bold">
            {{ greeting }} {{ authStore.user?.name ?? '' }}
            <span class="hidden md:inline">
              {{ authStore.user?.last_name ?? '' }}
            </span>
          </span>

          <VSpacer />

          <UserProfile />
        </template>
      </div>
    </template>

    <template #vertical-nav-header="{ toggleIsOverlayNavActive }">
      <div
        class="cursor-pointer app-logo app-title-wrapper"
        @click="goToHome"
      >
        <VSheet class="w-12 h-12 !bg-transparent rounded-lg d-flex align-center justify-center">
          <img
            :src="globalTheme.name.value == 'dark' ? '/images/logoDark.png' : '/images/logo.png'"
            alt="Logo"
            class="w-full h-full object-contain"
          >
        </VSheet>
        <span
          class="text-5xl font-weight-bold tracking-wider font-stella"
          :style="{ color: globalTheme.name.value == 'dark' ? '#0b6374' : '#13b868' }"
        >EcoSort</span>
      </div>

      <IconBtn
        class="d-block d-lg-none"
        @click="toggleIsOverlayNavActive(false)"
      >
        <VIcon icon="bx-x" />
      </IconBtn>
    </template>

    <template #vertical-nav-content>
      <NavItems />
    </template>

    <template #after-vertical-nav-items>
      <template v-if="!isPanelRoute">
        <VDivider class="mx-3" />
        <UserProfile
          variant="sidebar"
          class="my-2"
        />
      </template>
    </template>

    <!-- 👉 Pages -->
    <slot />

    <!-- 👉 Footer -->
    <template #footer>
      <Footer />
    </template>
  </VerticalNavLayout>
</template>

<style lang="scss" scoped>
.meta-key {
  border: thin solid rgba(var(--v-border-color), var(--v-border-opacity));
  border-radius: 6px;
  block-size: 1.5625rem;
  line-height: 1.3125rem;
  padding-block: 0.125rem;
  padding-inline: 0.25rem;
}

.app-logo {
  display: flex;
  align-items: center;
  column-gap: 0.75rem;

  .app-logo-title {
    font-size: 1.25rem;
    font-weight: 500;
    line-height: 1.75rem;
    text-transform: uppercase;
  }
}
</style>
