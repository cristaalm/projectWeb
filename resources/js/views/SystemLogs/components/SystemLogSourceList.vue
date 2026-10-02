<script setup>
import { formatBytes, systemLogIcon } from '@/utils/systemLogs'
import { format, parseISO } from 'date-fns'

defineProps({
  sources: { type: Array, default: () => [] },
  selected: { type: String, default: null },
  loading: Boolean,
})

const emit = defineEmits(['update:selected'])

function subtitle(source) {
  if (!source.available) return 'No existe en este servidor'
  if (source.size_bytes === 0) return 'Vacío'

  return `${formatBytes(source.size_bytes)} · ${format(parseISO(source.modified_at), 'dd/MM HH:mm')}`
}
</script>

<template>
  <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
    <VSkeletonLoader
      v-if="loading && !sources.length"
      type="list-item-avatar-two-line@6"
      class="bg-transparent"
    />
    <VList
      v-else
      lines="two"
      density="compact"
      class="py-2"
    >
      <VListItem
        v-for="source in sources"
        :key="source.source"
        :active="source.source === selected"
        :disabled="!source.available"
        color="primary"
        rounded="lg"
        class="mx-2"
        @click="emit('update:selected', source.source)"
      >
        <template #prepend>
          <VIcon
            :icon="systemLogIcon(source.source)"
            size="20"
          />
        </template>

        <VListItemTitle class="text-body-2 font-weight-medium text-wrap">
          {{ source.label }}
        </VListItemTitle>
        <VListItemSubtitle class="text-caption">
          {{ subtitle(source) }}
        </VListItemSubtitle>
      </VListItem>
    </VList>
  </VCard>
</template>
