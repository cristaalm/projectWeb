<script setup>
import { badgeTierColor, badgeTierContrast, DEFAULT_BADGE_ICON } from '@/utils/badgeTier'
import { computed } from 'vue'

const props = defineProps({
  badge: { type: Object, required: true }, // { icon, recycles_required }
  size: { type: [Number, String], default: 40 },
})

const bgColor = computed(() => badgeTierColor(props.badge.recycles_required))
const iconColor = computed(() => badgeTierContrast(props.badge.recycles_required))
const iconSize = computed(() => Number(props.size) * 0.55)
</script>

<template>
  <VAvatar
    :size="size"
    :style="{ backgroundColor: bgColor }"
  >
    <VIcon
      :icon="badge.icon || DEFAULT_BADGE_ICON"
      :color="iconColor"
      :size="iconSize"
    />
  </VAvatar>
</template>
