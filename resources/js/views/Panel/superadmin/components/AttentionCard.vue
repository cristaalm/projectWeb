<script setup>
import PanelEmptyState from '../../components/PanelEmptyState.vue'
import { format, parseISO } from 'date-fns'
import { computed } from 'vue'

const props = defineProps({
  attention: { type: Object, default: null },
  loading: Boolean,
})

// Cada aviso enlaza a la vista donde se resuelve.
const items = computed(() => {
  if (!props.attention) return []

  const list = []
  const { pending_rewards: pendingRewards, silent_container_days: days } = props.attention

  if (pendingRewards > 0) {
    list.push({
      key: 'rewards',
      icon: 'bx-gift',
      color: 'warning',
      title: pendingRewards === 1
        ? '1 recompensa pendiente de revisión'
        : `${pendingRewards} recompensas pendientes de revisión`,
      subtitle: 'Esperan aprobación o rechazo',
      to: { name: 'rewards' },
    })
  }

  props.attention.silent_containers.forEach(container => {
    list.push({
      key: `silent-${container.id}`,
      icon: 'bx-wifi-off',
      color: 'error',
      title: `${container.name} sin actividad`,
      subtitle: container.last_scan_at
        ? `Último escaneo: ${format(parseISO(container.last_scan_at), 'dd/MM/yyyy')}`
        : `Sin escaneos desde hace más de ${days} días`,
      to: { name: 'containers' },
    })
  })

  props.attention.high_failure_containers.forEach(container => {
    const total = container.valid_scans + container.failed_scans

    list.push({
      key: `failure-${container.id}`,
      icon: 'bx-error',
      color: 'warning',
      title: `${container.name} con muchos rechazos`,
      subtitle: `${container.failed_scans} de ${total} escaneos fallidos en el periodo`,
      to: { name: 'scans' },
    })
  })

  return list
})
</script>

<template>
  <VCard class="border border-gray-200 rounded-lg h-100 dark:border-gray-700">
    <VCardText class="pb-2 pa-6">
      <div class="gap-2 d-flex align-center justify-space-between">
        <h2 class="text-h6 font-weight-semibold">
          Requiere atención
        </h2>
        <VChip
          v-if="items.length"
          color="warning"
          variant="tonal"
          size="small"
        >
          {{ items.length }}
        </VChip>
      </div>
    </VCardText>

    <VSkeletonLoader
      v-if="loading && !attention"
      type="list-item-avatar-two-line@3"
      class="bg-transparent"
    />
    <PanelEmptyState
      v-else-if="!items.length"
      icon="bx-check-circle"
      text="Todo en orden, no hay nada pendiente"
    />
    <VList
      v-else
      lines="two"
      class="py-0 mb-2 attention-list"
    >
      <VListItem
        v-for="item in items"
        :key="item.key"
        :to="item.to"
        class="px-6"
      >
        <template #prepend>
          <VAvatar
            :color="item.color"
            variant="tonal"
            rounded="lg"
            size="38"
          >
            <VIcon
              :icon="item.icon"
              size="20"
            />
          </VAvatar>
        </template>

        <VListItemTitle class="text-body-2 font-weight-medium text-wrap">
          {{ item.title }}
        </VListItemTitle>
        <VListItemSubtitle class="text-caption">
          {{ item.subtitle }}
        </VListItemSubtitle>

        <template #append>
          <VIcon
            icon="bx-chevron-right"
            size="20"
            class="text-disabled"
          />
        </template>
      </VListItem>
    </VList>
  </VCard>
</template>

<style scoped>
.attention-list {
  max-block-size: 320px;
  overflow-y: auto;
}
</style>
