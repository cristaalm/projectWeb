<script setup>
import { useAuthStore } from '@/store/auth'
import {
  canApproveOrReject,
  canManageReward,
  canPause,
  canReactivate,
} from '@/utils/rewardPermissions'
import { rewardStatusColor, rewardStatusLabel } from '@/utils/rewardStatus'
import { format, parseISO } from 'date-fns'
import { computed } from 'vue'

defineProps({
  items: { type: Array, default: () => [] },
  total: { type: Number, default: 0 },
  loading: Boolean,
  page: { type: Number, required: true },
  itemsPerPage: { type: Number, required: true },
  sortBy: { type: Array, required: true },
  search: { type: String, default: '' },
})

const emit = defineEmits([
  'update:page',
  'update:itemsPerPage',
  'update:sortBy',
  'update:search',
  'edit',
  'delete',
  'approve',
  'reject',
  'pause',
  'reactivate',
])

const authStore = useAuthStore()
const currentUser = computed(() => authStore.getUser())

const ITEMS_PER_PAGE_OPTIONS = [
  { value: 10, title: '10' },
  { value: 25, title: '25' },
  { value: 50, title: '50' },
  { value: 100, title: '100' },
]

const HEADERS = [
  { title: 'Recompensa', key: 'name' },
  { title: 'Alianza', key: 'alliance', sortable: false },
  { title: 'Puntos', key: 'points_required' },
  { title: 'Stock', key: 'stock' },
  { title: 'Exclusiva', key: 'is_exclusive', sortable: false },
  { title: 'Estado', key: 'status' },
  { title: 'Vence', key: 'expires_at' },
  { title: '', key: 'actions', sortable: false, align: 'end' },
]

function formatDate(value) {
  if (!value) return 'Sin vencimiento'

  return format(parseISO(value), 'dd/MM/yyyy HH:mm')
}

function hasAnyAction(item) {
  return canManageReward(currentUser.value, item) || canApproveOrReject(currentUser.value, item)
}
</script>

<template>
  <VCardText class="pa-6 pb-0">
    <VTextField
      :model-value="search"
      label="Buscar por nombre, descripción o código"
      prepend-inner-icon="bx-search"
      density="compact"
      variant="outlined"
      rounded="lg"
      clearable
      style="max-width: 420px;"
      @update:model-value="emit('update:search', $event)"
    />
  </VCardText>

  <VDataTableServer
    :page="page"
    :items-per-page="itemsPerPage"
    :items-per-page-options="ITEMS_PER_PAGE_OPTIONS"
    items-per-page-text="Filas por página:"
    page-text="{0}-{1} de {2}"
    :sort-by="sortBy"
    :headers="HEADERS"
    :items="items"
    :items-length="total"
    :loading="loading"
    class="px-2"
    @update:page="emit('update:page', $event)"
    @update:items-per-page="emit('update:itemsPerPage', $event)"
    @update:sort-by="emit('update:sortBy', $event)"
  >
    <template #item.name="{ item }">
      <div>
        <div class="font-weight-medium">
          {{ item.name }}
        </div>
        <div class="text-caption text-medium-emphasis">
          {{ item.code }}
        </div>
      </div>
    </template>

    <template #item.alliance="{ item }">
      <span class="text-body-2">{{ item.alliance?.name ?? '—' }}</span>
    </template>

    <template #item.stock="{ value }">
      <span class="text-body-2">{{ value ?? 'Ilimitado' }}</span>
    </template>

    <template #item.is_exclusive="{ value }">
      <VChip
        v-if="value"
        color="info"
        variant="tonal"
        size="small"
      >
        Exclusiva
      </VChip>
      <span
        v-else
        class="text-medium-emphasis"
      >—</span>
    </template>

    <template #item.status="{ item }">
      <VTooltip
        v-if="item.rejection_reason"
        location="top"
      >
        <template #activator="{ props: tooltipProps }">
          <VChip
            v-bind="tooltipProps"
            :color="rewardStatusColor(item.status)"
            variant="tonal"
            size="small"
          >
            {{ rewardStatusLabel(item.status) }}
          </VChip>
        </template>
        {{ item.rejection_reason }}
      </VTooltip>
      <VChip
        v-else
        :color="rewardStatusColor(item.status)"
        variant="tonal"
        size="small"
      >
        {{ rewardStatusLabel(item.status) }}
      </VChip>
    </template>

    <template #item.expires_at="{ value }">
      <span class="text-body-2 text-medium-emphasis">{{ formatDate(value) }}</span>
    </template>

    <template #item.actions="{ item }">
      <VMenu
        v-if="hasAnyAction(item)"
        location="bottom end"
      >
        <template #activator="{ props: menuProps }">
          <VBtn
            icon
            variant="tonal"
            size="small"
            color="primary"
            class="opacity-80"
            v-bind="menuProps"
          >
            <VIcon icon="bx-dots-vertical-rounded" />
          </VBtn>
        </template>
        <VList density="compact">
          <VListItem
            v-if="canApproveOrReject(currentUser, item)"
            @click="emit('approve', item)"
          >
            <template #prepend>
              <VIcon
                icon="bx-check-circle"
                class="me-2 text-success"
              />
            </template>
            <VListItemTitle>Aprobar</VListItemTitle>
          </VListItem>
          <VListItem
            v-if="canApproveOrReject(currentUser, item)"
            @click="emit('reject', item)"
          >
            <template #prepend>
              <VIcon
                icon="bx-x-circle"
                class="me-2 text-error"
              />
            </template>
            <VListItemTitle>Rechazar</VListItemTitle>
          </VListItem>
          <VListItem
            v-if="canPause(currentUser, item)"
            @click="emit('pause', item)"
          >
            <template #prepend>
              <VIcon
                icon="bx-pause-circle"
                class="me-2 text-warning"
              />
            </template>
            <VListItemTitle>Pausar</VListItemTitle>
          </VListItem>
          <VListItem
            v-if="canReactivate(currentUser, item)"
            @click="emit('reactivate', item)"
          >
            <template #prepend>
              <VIcon
                icon="bx-play-circle"
                class="me-2 text-success"
              />
            </template>
            <VListItemTitle>Reactivar</VListItemTitle>
          </VListItem>
          <VListItem
            v-if="canManageReward(currentUser, item)"
            @click="emit('edit', item)"
          >
            <template #prepend>
              <VIcon
                icon="bx-edit"
                class="me-2 text-primary"
              />
            </template>
            <VListItemTitle>Editar</VListItemTitle>
          </VListItem>
          <VListItem
            v-if="canManageReward(currentUser, item)"
            @click="emit('delete', item)"
          >
            <template #prepend>
              <VIcon
                icon="bx-trash"
                class="me-2 text-error"
              />
            </template>
            <VListItemTitle class="text-error">
              Eliminar
            </VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
    </template>
  </VDataTableServer>
</template>
