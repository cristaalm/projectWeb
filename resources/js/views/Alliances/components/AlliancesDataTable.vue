<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { allianceStatusColor, allianceStatusLabel } from '@/utils/allianceStatus'
import { storageURL } from '@/utils/constants'
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
  { title: 'Alianza', key: 'name' },
  { title: 'Contacto', key: 'contact_name', sortable: false },
  { title: 'Categoría', key: 'type_shop', sortable: false },
  { title: 'Enlace a miembros', key: 'has_exclusive_rewards', sortable: false },
  { title: 'Estado', key: 'status' },
  { title: 'Creada', key: 'created_at' },
  { title: '', key: 'actions', sortable: false, align: 'end' },
]

function logoUrl(item) {
  return item.logo_url ? storageURL + item.logo_url : null
}

function formatDate(value) {
  if (!value) return ''

  return format(parseISO(value), 'dd/MM/yyyy HH:mm')
}
</script>

<template>
  <VCardText class="pa-6 pb-0">
    <VTextField
      :model-value="search"
      label="Buscar por nombre, contacto, correo, teléfono o dirección"
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
      <div class="gap-3 d-flex align-center">
        <UserAvatar
          :name="item.name"
          :avatar-url="logoUrl(item)"
          :size="36"
        />
        <span class="font-weight-medium">{{ item.name }}</span>
      </div>
    </template>

    <template #item.contact_name="{ item }">
      <div class="text-body-2">
        <div>{{ item.contact_name }}</div>
        <div class="text-medium-emphasis">
          {{ item.contact_email }}
        </div>
      </div>
    </template>

    <template #item.type_shop="{ item }">
      <span class="text-body-2">{{ item.type_shop?.name ?? '—' }}</span>
    </template>

    <template #item.has_exclusive_rewards="{ value }">
      <VChip
        v-if="value"
        color="info"
        variant="tonal"
        size="small"
      >
        Enlaza miembros
      </VChip>
      <span
        v-else
        class="text-medium-emphasis"
      >—</span>
    </template>

    <template #item.status="{ value }">
      <VChip
        :color="allianceStatusColor(value)"
        variant="tonal"
        size="small"
      >
        {{ allianceStatusLabel(value) }}
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
