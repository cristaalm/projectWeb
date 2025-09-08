<script lang="ts">
export default {
  inheritAttrs: false
}
</script>

<script setup lang="ts">
import { PopoverButton as HeadlessPopoverButton } from '@headlessui/vue';
import _ from 'lodash';
import { twMerge } from 'tailwind-merge';
import { computed, useAttrs } from 'vue';

interface ButtonProps extends /* @vue-ignore */ ExtractProps<typeof HeadlessPopoverButton> {
  as?: string | object
}

const { as = 'div' } = defineProps<ButtonProps>()

const attrs = useAttrs()
const computedClass = computed(() =>
  twMerge(['cursor-pointer', typeof attrs.class === 'string' && attrs.class])
)
</script>

<template>
  <HeadlessPopoverButton as="template">
    <component :is="as" :class="computedClass" v-bind="_.omit(attrs, 'class')">
      <slot></slot>
    </component>
  </HeadlessPopoverButton>
</template>
