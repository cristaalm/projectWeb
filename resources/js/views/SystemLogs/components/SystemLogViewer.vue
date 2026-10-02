<script setup>
import { formatBytes, systemLogLineLevel } from '@/utils/systemLogs'
import { format, parseISO } from 'date-fns'
import { computed, nextTick, ref, watch } from 'vue'

const props = defineProps({
  log: { type: Object, default: null },
  loading: Boolean,
})

const container = ref(null)

const rows = computed(() => (props.log?.lines ?? []).map(text => ({ text, level: systemLogLineLevel(text) })))

const summary = computed(() => {
  if (!props.log) return ''

  const { count, size_bytes: size, modified_at: modifiedAt, scanned_bytes: scanned, reached_start: reachedStart } = props.log

  return [
    `${count} ${count === 1 ? 'línea' : 'líneas'}`,
    `archivo de ${formatBytes(size)}`,
    `última escritura ${format(parseISO(modifiedAt), 'dd/MM/yyyy HH:mm:ss')}`,
    reachedStart ? 'es todo el archivo' : `se revisaron los últimos ${formatBytes(scanned)}`,
  ].join(' · ')
})

const emptyText = computed(() => {
  if (!props.log) return 'No hay ningún log disponible en este servidor'
  if (props.log.search) return 'Ninguna línea reciente contiene ese texto'

  return 'El log está vacío'
})

// Lo más reciente queda al final: cada vez que llegan líneas nuevas se baja
// hasta ahí, como en una terminal.
watch(rows, async () => {
  await nextTick()
  if (container.value) container.value.scrollTop = container.value.scrollHeight
})
</script>

<template>
  <div class="px-6 pb-6">
    <VSkeletonLoader
      v-if="loading && !log"
      type="paragraph@4"
      class="bg-transparent"
    />
    <div
      v-else-if="!rows.length"
      class="py-12 text-center d-flex flex-column align-center"
    >
      <VIcon
        icon="bx-file-blank"
        size="36"
        class="mb-2 text-disabled"
      />
      <span class="text-body-2 text-medium-emphasis">{{ emptyText }}</span>
    </div>
    <div
      v-else
      ref="container"
      class="log-viewer"
    >
      <div
        v-for="(row, index) in rows"
        :key="index"
        class="log-line"
        :class="`log-line--${row.level}`"
      >
        {{ row.text }}
      </div>
    </div>

    <p
      v-if="summary"
      class="mt-3 mb-0 text-caption text-medium-emphasis"
    >
      {{ summary }}
    </p>
  </div>
</template>

<style scoped>
.log-viewer {
  overflow: auto;
  border-radius: 8px;
  background: rgba(var(--v-theme-on-surface), 0.04);
  block-size: calc(100vh - 420px);
  font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
  font-size: 12.5px;
  line-height: 1.55;
  min-block-size: 360px;
  padding-block: 12px;
  padding-inline: 16px;
}

.log-line {
  color: rgba(var(--v-theme-on-surface), 0.9);
  overflow-wrap: anywhere;
  white-space: pre-wrap;
}

.log-line--error {
  color: rgb(var(--v-theme-error));
}

.log-line--warning {
  color: rgb(var(--v-theme-warning-darken-1));
}

.log-line--trace {
  color: rgba(var(--v-theme-on-surface), 0.55);
}
</style>
