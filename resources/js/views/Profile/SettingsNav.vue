<script setup>
import { computed } from 'vue'
import { useTheme } from 'vuetify'

defineProps({
  sections: { type: Array, required: true },
  activeId: { type: String, default: null },
})

const emit = defineEmits(['navigate'])

const { name: themeName } = useTheme()
const activeColor = computed(() => themeName.value === 'dark' ? 'info' : 'primary')
</script>

<template>
  <VList
    class="position-sticky"
    style="top: 100px;"
    density="compact"
    nav
  >
    <VListItem
      v-for="section in sections"
      :key="section.id"
      :active="activeId === section.id"
      :color="activeColor"
      rounded="lg"
      @click="emit('navigate', section.id)"
    >
      <VListItemTitle class="dark:text-white">
        {{ section.label }}
      </VListItemTitle>
    </VListItem>
  </VList>
</template>
