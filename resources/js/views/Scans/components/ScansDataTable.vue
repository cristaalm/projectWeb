<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { storageURL } from '@/utils/constants'
import { scanStatusColor, scanStatusLabel } from '@/utils/scanStatus'
import { format, parseISO } from 'date-fns'

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
  'view-image',
])

const ITEMS_PER_PAGE_OPTIONS = [
  { value: 10, title: '10' },
  { value: 25, title: '25' },
  { value: 50, title: '50' },
  { value: 100, title: '100' },
]

// Las keys ordenables coinciden con ScanRepository::SORTABLE_COLUMNS.
const HEADERS = [
  { title: 'Usuario', key: 'user', sortable: false },
  { title: 'Contenedor', key: 'container', sortable: false },
  { title: 'Material', key: 'material', sortable: false },
  { title: 'Puntos', key: 'points_awarded' },
  { title: 'Estado', key: 'scan_status' },
  { title: 'Fecha', key: 'scanned_at' },
  { title: '', key: 'actions', sortable: false, align: 'end' },
]

function formatDate(value) {
  if (!value) return ''

  return format(parseISO(value), 'dd/MM/yyyy HH:mm')
}

function avatarUrl(user) {
  return user?.avatar ? storageURL + user.avatar : null
}
</script>

<template>
  <VCardText class="pa-6 pb-0">
    <VTextField
      :model-value="search"
      label="Buscar por usuario, correo, código o contenedor"
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
    <template #item.user="{ item }">
      <div class="gap-3 d-flex align-center">
        <UserAvatar
          :name="`${item.user?.name ?? ''} ${item.user?.last_name ?? ''}`"
          :avatar-url="avatarUrl(item.user)"
          :size="32"
        />
        <div>
          <div class="text-body-2">
            {{ item.user?.name }} {{ item.user?.last_name }}
          </div>
          <div class="text-caption text-medium-emphasis">
            {{ item.user?.email }}
          </div>
        </div>
      </div>
    </template>

    <template #item.container="{ item }">
      <div>
        <div class="text-body-2">
          {{ item.container?.name ?? '—' }}
        </div>
        <div class="text-caption text-medium-emphasis">
          {{ item.container?.serial_number }}
        </div>
      </div>
    </template>

    <template #item.material="{ item }">
      <span class="text-body-2">{{ item.material?.name ?? '—' }}</span>
    </template>

    <template #item.points_awarded="{ value }">
      <VChip
        :color="value > 0 ? 'success' : 'default'"
        variant="tonal"
        size="small"
      >
        {{ value > 0 ? `+${value}` : value }}
      </VChip>
    </template>

    <template #item.scan_status="{ item }">
      <VChip
        :color="scanStatusColor(item.status)"
        variant="tonal"
        size="small"
        :title="item.description ?? undefined"
      >
        {{ scanStatusLabel(item.status) }}
      </VChip>
      <div
        v-if="item.description"
        class="mt-1 text-caption text-medium-emphasis"
      >
        {{ item.description }}
      </div>
    </template>

    <template #item.scanned_at="{ value }">
      <span class="text-body-2 text-medium-emphasis">{{ formatDate(value) }}</span>
    </template>

    <template #item.actions="{ item }">
      <VBtn
        v-if="item.image_url"
        icon
        variant="text"
        color="primary"
        size="small"
        title="Ver imagen"
        @click="emit('view-image', item)"
      >
        <VIcon icon="bx-image" />
      </VBtn>
    </template>
  </VDataTableServer>
</template>
