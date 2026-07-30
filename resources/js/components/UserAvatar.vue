<script setup>
import { getAvatarColor, getInitials } from '@/utils/avatar'
import { computed } from 'vue'

const props = defineProps({
  name: { type: String, required: true },
  avatarUrl: { type: String, default: null },
  size: { type: [Number, String], default: 40 },
  variant: { type: String, default: undefined },
})

const initialsStyle = computed(() => ({
  backgroundColor: getAvatarColor(props.name),
  fontSize: `${Number(props.size) * 0.4}px`,
}))
</script>

<template>
  <VAvatar
    :size="size"
    :variant="variant"
  >
    <VImg
      v-if="avatarUrl"
      :src="avatarUrl"
    />
    <div
      v-else
      class="avatar-initials"
      :style="initialsStyle"
    >
      {{ getInitials(name) }}
    </div>

    <slot />
  </VAvatar>
</template>

<style scoped>
.avatar-initials {
  display: flex;
  align-items: center;
  justify-content: center;
  inline-size: 100%;
  block-size: 100%;
  color: white;
  font-weight: 600;
}
</style>
