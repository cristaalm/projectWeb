<script setup>
import { useLocale } from '@/i18n/useLocale'

const { locale, setLocale } = useLocale()
</script>

<template>
  <div class="d-flex align-center gap-2">
    <VBtn
      v-if="locale !== 'es'"
      variant="text"
      size="small"
      color="primary"
      @click="setLocale('es')"
    >
      ES
    </VBtn>

    <VBtn
      v-else
      variant="text"
      size="small"
      color="primary"
      @click="setLocale('en')"
    >
      EN
    </VBtn>
  </div>
</template>
