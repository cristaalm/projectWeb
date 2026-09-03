<script setup>
import Lucide from '@/components/Base/Lucide/'
import { useRouter } from 'vue-router'

defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  showFeatures: {
    type: Boolean,
    default: false,
  },
})

const features = [
  { icon: 'Recycle', text: 'Clasifica plástico, aluminio, vidrio, cartón y otros' },
  { icon: 'HandCoins', text: 'Gana puntos reales por cada reciclaje' },
  { icon: 'Store', text: 'Canjéalos en comercios aliados de tu zona' },
]

const router = useRouter()

const goToLanding = () => {
  router.push({ path: '/' })
}
</script>

<template>
  <div class="auth-shell">
    <aside class="auth-panel">
      <div
        class="auth-panel__glow auth-panel__glow--top"
        aria-hidden="true"
      />
      <div
        class="auth-panel__glow"
        aria-hidden="true"
      />

      <button
        type="button"
        class="auth-panel__brand"
        @click="goToLanding"
      >
        <img
          src="/images/logo.png"
          alt=""
          class="auth-panel__mark"
        >
        <span class="font-poppins auth-panel__wordmark">EcoSort</span>
      </button>

      <div class="auth-panel__copy">
        <h1 class="font-poppins auth-panel__title">
          {{ title }}
        </h1>
        <p class="font-poppins auth-panel__desc">
          {{ description }}
        </p>
      </div>

      <ul
        v-if="showFeatures"
        class="auth-panel__features"
      >
        <li
          v-for="feature in features"
          :key="feature.text"
          class="auth-panel__feature"
        >
          <Lucide
            :icon="feature.icon"
            class="auth-panel__feature-icon"
          />
          <span class="font-poppins">{{ feature.text }}</span>
        </li>
      </ul>
    </aside>

    <main class="auth-content">
      <div class="auth-content__inner">
        <button
          type="button"
          class="font-poppins auth-back"
          @click="goToLanding"
        >
          <Lucide
            icon="ArrowLeft"
            class="w-4 h-4"
          />
          Volver al inicio
        </button>

        <slot />
      </div>
    </main>
  </div>
</template>

<style scoped>
.auth-shell {
  display: flex;
  flex-direction: column;
  background: #fff;
  min-block-size: 100dvh;
}

@media (min-width: 960px) {
  .auth-shell {
    flex-direction: row;
  }
}

/* Panel de marca */
.auth-panel {
  position: relative;
  display: flex;
  overflow: hidden;
  flex-direction: column;
  background: linear-gradient(160deg, var(--auth-teal-900) 0%, var(--auth-teal-800) 55%, var(--auth-teal-700) 100%);
  color: #fff;
  gap: 1.75rem;
  padding-block: 2.25rem;
  padding-inline: 1.5rem;
}

@media (min-width: 960px) {
  .auth-panel {
    position: sticky;
    justify-content: center;
    gap: 3rem;
    inline-size: 44%;
    inset-block-start: 0;
    min-block-size: 100dvh;
    padding-block: 3.5rem;
    padding-inline: 4rem;
  }
}

.auth-panel__glow {
  position: absolute;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(5, 209, 110, 55%) 0%, rgba(5, 209, 110, 25%) 45%, rgba(5, 209, 110, 0%) 72%);
  block-size: 34rem;
  filter: blur(70px);
  inline-size: 34rem;
  inset-block-end: -12rem;
  inset-inline-end: -14rem;
  pointer-events: none;
}

.auth-panel__glow--top {
  background: radial-gradient(circle, rgba(139, 230, 174, 30%) 0%, rgba(139, 230, 174, 0%) 70%);
  block-size: 20rem;
  filter: blur(60px);
  inline-size: 20rem;
  inset-block: -8rem auto;
  inset-inline: -10rem auto;
}

.auth-panel__brand {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  padding: 0;
  border: none;
  background: none;
  cursor: pointer;
  gap: 0.75rem;
  inline-size: fit-content;
}

.auth-panel__mark {
  block-size: 52px;
  inline-size: 52px;
  object-fit: contain;
}

.auth-panel__wordmark {
  color: #fff;
  font-size: 2.375rem;
  font-weight: 700;
  line-height: 1;
}

.auth-panel__copy {
  position: relative;
  z-index: 1;
}

.auth-panel__title {
  color: #fff;
  font-size: clamp(1.625rem, 1.1rem + 1.8vw, 2.375rem);
  font-weight: 600;
  line-height: 1.2;
  margin-block-end: 0.875rem;
  max-inline-size: 22ch;
}

.auth-panel__desc {
  color: rgba(255, 255, 255, 78%);
  font-size: 1rem;
  line-height: 1.6;
  max-inline-size: 36ch;
}

.auth-panel__features {
  position: relative;
  z-index: 1;
  display: none;
  flex-direction: column;
  padding: 0;
  margin: 0;
  gap: 1rem;
  list-style: none;
}

@media (min-width: 960px) {
  .auth-panel__features {
    display: flex;
  }
}

.auth-panel__feature {
  display: flex;
  align-items: center;
  color: rgba(255, 255, 255, 88%);
  font-size: 0.9375rem;
  gap: 0.75rem;
}

.auth-panel__feature-icon {
  flex-shrink: 0;
  block-size: 18px;
  color: #8be6ae;
  inline-size: 18px;
}

/* Contenido / formulario */
.auth-content {
  display: flex;
  flex: 1;
  justify-content: center;
  padding-block: 2.5rem 3rem;
  padding-inline: 1.5rem;
}

@media (min-width: 960px) {
  .auth-content {
    align-items: center;
    justify-content: flex-start;
    padding-block: 4rem;
    padding-inline: 6rem;
  }
}

.auth-content__inner {
  inline-size: 100%;
  max-inline-size: 400px;
}

.auth-back {
  display: flex;
  align-items: center;
  padding: 0;
  border: none;
  background: none;
  color: var(--auth-muted);
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  gap: 0.375rem;
  margin-block-end: 2rem;
}

.auth-back:hover {
  color: var(--auth-ink);
}
</style>
