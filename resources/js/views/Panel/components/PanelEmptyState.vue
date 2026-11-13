<script setup>
defineProps({
  icon: { type: String, default: 'bx-bar-chart-alt-2' },
  text: { type: String, required: true },
})
</script>

<template>
  <div class="py-8 text-center d-flex flex-column align-center justify-center flex-grow-1">
    <VIcon
      :icon="icon"
      size="36"
      class="mb-2 text-disabled"
    />
    <span class="text-body-2 text-medium-emphasis">{{ text }}</span>
  </div>
</template>
