<script setup>
import SystemLogSourceList from './components/SystemLogSourceList.vue'
import SystemLogToolbar from './components/SystemLogToolbar.vue'
import SystemLogViewer from './components/SystemLogViewer.vue'
import SystemLogsHeader from './components/SystemLogsHeader.vue'
import { useSystemLogsView } from './hooks/useSystemLogsView'
import { useToastStore } from '@/store/useToastStore'
import { computed } from 'vue'

const toast = useToastStore()

const {
  sources,
  selectedSource,
  lines,
  search,
  log,
  autoRefresh,
  loading,
  loadingSources,
  loadingLog,
  refresh,
} = useSystemLogsView()

const selectedLabel = computed(() => sources.value.find(source => source.source === selectedSource.value)?.label)

async function copyLog() {
  try {
    await navigator.clipboard.writeText(log.value.lines.join('\n'))
    toast.showToast({ message: 'Log copiado al portapapeles', tipo: 'success' })
  } catch (err) {
    console.error(err)
    toast.showToast({ message: 'No se pudo copiar el log', tipo: 'error' })
  }
}
</script>

<template>
  <div class="gap-6 d-flex flex-column">
    <VCard
      class="border border-gray-200 rounded-lg dark:border-gray-700"
      style="overflow: hidden;"
    >
      <SystemLogsHeader
        v-model:auto-refresh="autoRefresh"
        :loading="loading"
        @refresh="refresh"
      />
    </VCard>

    <VRow>
      <VCol
        cols="12"
        md="4"
        lg="3"
      >
        <SystemLogSourceList
          v-model:selected="selectedSource"
          :sources="sources"
          :loading="loadingSources"
        />
      </VCol>

      <VCol
        cols="12"
        md="8"
        lg="9"
      >
        <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
          <SystemLogToolbar
            v-model:search="search"
            v-model:lines="lines"
            :title="selectedLabel"
            :can-copy="Boolean(log?.lines.length)"
            @copy="copyLog"
          />

          <SystemLogViewer
            :log="log"
            :loading="loadingLog || loadingSources"
          />
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
