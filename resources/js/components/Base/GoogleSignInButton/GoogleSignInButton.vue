<script setup lang="ts">
import { loadGoogleIdentityServices } from '@/utils/loadGoogleIdentityServices'
import { onBeforeUnmount, onMounted, ref } from 'vue'

/**
 * El botón que renderiza el SDK de Google vive dentro de un iframe con
 * estilos fijos — no se puede maquetar para que combine con Vuetify. Se
 * mantiene invisible mientras recibe el click real (Google exige que el
 * gesto del usuario caiga sobre su propio botón), superpuesto exactamente
 * encima de un VBtn puramente visual que sí respeta el tema del dashboard.
 */
const props = withDefaults(
  defineProps<{
    text?: string
    disabled?: boolean
    loading?: boolean
    size?: string
    pill?: boolean
  }>(),
  {
    text: 'Continuar con Google',
    disabled: false,
    loading: false,
    size: undefined,
    pill: false,
  }
)

const emit = defineEmits<{
  credential: [idToken: string]
}>()

const wrapRef = ref<HTMLElement | null>(null)
const hiddenBtnRef = ref<HTMLElement | null>(null)
let resizeObserver: ResizeObserver | null = null

function renderGoogleButton() {
  if (!hiddenBtnRef.value || !wrapRef.value || !window.google?.accounts?.id) return

  const width = Math.round(wrapRef.value.getBoundingClientRect().width) || 280

  hiddenBtnRef.value.innerHTML = ''
  window.google.accounts.id.renderButton(hiddenBtnRef.value, {
    type: 'standard',
    theme: 'outline',
    size: 'large',
    width,
  })
}

onMounted(async () => {
  try {
    await loadGoogleIdentityServices()

    window.google.accounts.id.initialize({
      client_id: import.meta.env.VITE_GOOGLE_CLIENT_ID_WEB,
      callback: (response: { credential: string }) => emit('credential', response.credential),
    })

    renderGoogleButton()

    resizeObserver = new ResizeObserver(() => renderGoogleButton())
    if (wrapRef.value) resizeObserver.observe(wrapRef.value)
  } catch (err) {
    console.error(err)
  }
})

onBeforeUnmount(() => {
  resizeObserver?.disconnect()
})
</script>

<template>
  <div
    ref="wrapRef"
    class="google-signin-wrap"
    :class="{ 'pointer-events-none': disabled || loading }"
  >
    <VBtn
      block
      variant="outlined"
      color="secondary"
      :size="size"
      class="google-signin-visual"
      :class="{ '!rounded-full': pill }"
      :disabled="disabled || loading"
    >
      <VProgressCircular
        v-if="loading"
        indeterminate
        size="18"
        width="2"
        class="mr-2"
      />
      <svg
        v-else
        class="mr-2"
        width="18"
        height="18"
        viewBox="0 0 18 18"
      >
        <path
          fill="#4285F4"
          d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844a4.14 4.14 0 0 1-1.796 2.716v2.259h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"
        />
        <path
          fill="#34A853"
          d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"
        />
        <path
          fill="#FBBC05"
          d="M3.964 10.706A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.706V4.962H.957A8.997 8.997 0 0 0 0 9c0 1.452.348 2.827.957 4.038l3.007-2.332z"
        />
        <path
          fill="#EA4335"
          d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.962L3.964 7.294C4.672 5.167 6.656 3.58 9 3.58z"
        />
      </svg>
      {{ text }}
    </VBtn>

    <div
      v-show="!loading"
      ref="hiddenBtnRef"
      class="google-signin-hidden"
    />
  </div>
</template>

<style scoped>
.google-signin-wrap {
  position: relative;
  display: block;
}

.google-signin-visual {
  pointer-events: none;
}

.google-signin-hidden {
  position: absolute;
  inset: 0;
  opacity: 0;
  overflow: hidden;
  z-index: 2;
}
</style>
