<script setup>
import { useI18n } from 'vue-i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

const emit = defineEmits(['open-view-terms-modal'])
const { t } = useI18n()
</script>

<template>
  <VFooter class="!bg-[#00464B] !text-slate-200 py-12 px-4">
    <VContainer class="max-w-7xl">
      <VRow class="mb-8">
        <!-- Logo + descripción -->
        <VCol
          cols="12"
          md="3"
        >
          <div class="space-y-4">
            <div class="d-flex align-center gap-2">
              <div class="w-8 h-8 bg-[#00464B] rounded-lg d-flex align-center justify-center">
                <img
                  src="/images/logo.png"
                  alt="Logo"
                >
              </div>
              <span class="text-h5 font-weight-bold text-white font-poppins">EcoSort</span>
            </div>
            <p class="text-sm leading-relaxed font-poppins opacity-80">
              {{ t('landing.footer.description') }}
            </p>
          </div>
        </VCol>

        <!-- Producto -->
        <VCol
          cols="12"
          md="3"
        >
          <h3 class="font-weight-bold mb-4 text-white font-poppins">
            {{ t('landing.footer.product.title') }}
          </h3>
          <ul class="footer-links font-poppins">
            <li><a href="#como-funciona">{{ t('landing.footer.product.howItWorks') }}</a></li>
            <li><a href="#beneficios">{{ t('landing.footer.product.benefits') }}</a></li>
            <li><a href="#impacto">{{ t('landing.footer.product.impact') }}</a></li>
          </ul>
        </VCol>

        <!-- Redes -->
        <VCol
          cols="12"
          md="3"
        >
          <h3 class="font-weight-bold mb-4 text-white font-poppins">
            {{ t('landing.footer.social.title') }}
          </h3>
          <ul class="footer-links font-poppins">
            <li>
              <a
                href="https://www.facebook.com/share/1AduhKGkXr/"
                target="_blank"
                rel="noopener noreferrer"
              >
                {{ t('landing.footer.social.facebook') }}
              </a>
            </li>
            <li>
              <a
                href="https://www.instagram.com/ecosort.innovation?igsh=czRkcHRpc3I1OGJs"
                target="_blank"
                rel="noopener noreferrer"
              >
                {{ t('landing.footer.social.instagram') }}
              </a>
            </li>
          </ul>
        </VCol>

        <!-- Contacto -->
        <VCol
          cols="12"
          md="3"
        >
          <h3 class="font-weight-bold mb-4 text-white font-poppins">
            {{ t('landing.footer.contact.title') }}
          </h3>
          <ul class="footer-contact font-poppins">
            <li class="d-flex align-start gap-2">
              <VIcon
                size="16"
                class="mt-1"
                icon="mdi mdi-email"
              />
              <span>somosecosort@gmail.com</span>
            </li>
            <li class="d-flex align-start gap-2">
              <VIcon
                size="16"
                class="mt-1"
                icon="mdi mdi-map-marker"
              />
              <span>{{ t('landing.footer.contact.location') }}</span>
            </li>
          </ul>
        </VCol>
      </VRow>

      <VDivider class="mb-8 opacity-20" />

      <!-- Legal -->
      <div class="d-flex flex-column flex-md-row justify-space-between align-center gap-4 text-sm opacity-80">
        <p class="font-poppins">
          &copy; {{ new Date().getFullYear() }} EcoSort. {{ t('landing.footer.legal.rights') }}
        </p>

        <div class="d-flex align-center gap-6">
          <LanguageSwitcher />

          <a
            href="#"
            class="footer-link font-poppins"
            @click.prevent="emit('open-view-terms-modal')"
          >
            {{ t('landing.footer.legal.terms') }}
          </a>
        </div>
      </div>
    </VContainer>
  </VFooter>
</template>
