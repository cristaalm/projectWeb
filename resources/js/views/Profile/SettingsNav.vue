<script setup>
defineProps({
  sections: { type: Array, required: true },
  activeId: { type: String, default: null },
})

const emit = defineEmits(['navigate'])
</script>

<template>
  <VList
    class="position-sticky"
    style="top: 80px;"
    density="compact"
    nav
  >
    <VListItem
      v-for="section in sections"
      :key="section.id"
      :active="activeId === section.id"
      color="primary"
      rounded="lg"
      @click="emit('navigate', section.id)"
    >
      <VListItemTitle>{{ section.label }}</VListItemTitle>
    </VListItem>
  </VList>
</template>
