<script setup>
import { containerStatusColor, containerStatusLabel } from '@/utils/containerStatus'
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
  'edit',
  'delete',
])

const ITEMS_PER_PAGE_OPTIONS = [
  { value: 10, title: '10' },
  { value: 25, title: '25' },
  { value: 50, title: '50' },
  { value: 100, title: '100' },
]

const HEADERS = [
  { title: 'Nombre', key: 'name' },
  { title: 'Serie', key: 'serial_number' },
  { title: 'Ubicación', key: 'location', sortable: false },
  { title: 'Estado', key: 'status' },
  { title: 'Creado', key: 'created_at' },
  { title: '', key: 'actions', sortable: false, align: 'end' },
]

function formatDate(value) {
  if (!value) return ''

  return format(parseISO(value), 'dd/MM/yyyy HH:mm')
}
</script>

<template>
  <VCardText class="pa-6 pb-0">
    <VTextField
      :model-value="search"
      label="Buscar por nombre, serie o ubicación"
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
    <template #item.status="{ value }">
      <VChip
        :color="containerStatusColor(value)"
        variant="tonal"
        size="small"
      >
        {{ containerStatusLabel(value) }}
      </VChip>
    </template>

    <template #item.created_at="{ item }">
      <span class="text-body-2 text-medium-emphasis">{{ formatDate(item.created_at) }}</span>
    </template>

    <template #item.actions="{ item }">
      <VMenu location="bottom end">
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
          <VListItem @click="emit('edit', item)">
            <template #prepend>
              <VIcon
                icon="bx-edit"
                class="me-2 text-primary"
              />
            </template>
            <VListItemTitle>Editar</VListItemTitle>
          </VListItem>
          <VListItem @click="emit('delete', item)">
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
