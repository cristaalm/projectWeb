<script setup>
import Footer from '@/layouts/components/Footer.vue'
import NavItems from '@/layouts/components/NavItems/NavItems.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'
import { useAuthStore } from '@/store/auth'
import VerticalNavLayout from '@layouts/components/VerticalNavLayout.vue'
import { useRouter } from 'vue-router'
import { useTheme } from 'vuetify'
import { getHours } from 'date-fns'

const {
  name: themeName,
  global: globalTheme,
} = useTheme()

const authStore = useAuthStore()
const router = useRouter()

const greeting = computed(() => {
  const hour = getHours(new Date())
  if (hour >= 5 && hour < 12) return 'Buenos días'
  if (hour >= 12 && hour < 19) return 'Buenas tardes'
  
  return 'Buenas noches'
})

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

        <!-- texto decorativo de bienvenida junto con el nombre -->
        <span class="text-xl font-bold">
          {{ greeting }} {{ authStore.user?.name ?? '' }}
          <span class="hidden md:inline">
            {{ authStore.user?.last_name ?? '' }}
          </span>
        </span>

        <VSpacer />

        <UserProfile />
      </div>
    </template>

    <template #vertical-nav-header="{ toggleIsOverlayNavActive }">
      <div
        class="cursor-pointer app-logo app-title-wrapper"
        @click="goToHome"
      >
        <!-- eslint-disable vue/no-v-html -->
        <div class="d-flex">
          <img
            :src="globalTheme.name.value == 'dark' ? '/images/LogoLetraDark.png' : '/images/LogoLetra.png'"
            alt="RENOVA"
            class="h-[45px]"
          >
        </div>
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
