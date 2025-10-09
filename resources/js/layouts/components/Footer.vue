<script setup>
import { ModalViewTerms, useModalViewTerms } from './ModalViewTerms'

const { showViewTermsModal, openViewTermsModal } = useModalViewTerms()
</script>

<template>
  <div class="h-100 flex flex-col md:flex-row items-center justify-between">
    <!-- 👉 Footer: left content -->
    <span class="flex flex-row items-center">
      &copy;
      {{ new Date().getFullYear() }}
      Hecho con 
      <VIcon
        icon="bx-heart"
        color="error"
        size="1.25rem"
        class="mx-1"
      />
      por <span class="text-primary ms-1">RENOVA</span>
    </span>
    <!-- 👉 Footer: right content -->
    <span class="flex-row items-center justify-end gap-x-4 text-primary hidden md:flex">
      <a
        href="/api/docs"
        target="_blank"
      >API Docs</a>
      <a
        href="#"
        class="hover:underline  !cursor-pointer"
        @click="(e) => { e.preventDefault(); openViewTermsModal() }"
      >Condiciones y términos de uso</a>
    </span>
  </div>

  <ModalViewTerms v-model="showViewTermsModal" />
</template>
