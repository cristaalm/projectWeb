<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { storageURL } from '@/utils/constants'
import { ROLE_COLORS } from '@/utils/roles'
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
  'points',
  'deactivate',
  'restore',
  'reset-credentials',
  'disable-two-factor',
])

const HEADERS = [
  { title: 'Usuario', key: 'name' },
  { title: 'Teléfono', key: 'phone', sortable: false },
  { title: 'Rol', key: 'role' },
  { title: 'Puntos', key: 'points_balance' },
  { title: 'Estado', key: 'deleted_at', sortable: false },
  { title: 'Creado', key: 'created_at' },
  { title: '', key: 'actions', sortable: false, align: 'end' },
]

function formatDate(value) {
  if (!value) return ''

  return format(parseISO(value), 'dd/MM/yyyy HH:mm')
}

function avatarUrl(item) {
  return item.avatar ? storageURL + item.avatar : null
}
</script>

<template>
  <VCardText class="pa-6 pb-0">
    <VTextField
      :model-value="search"
      label="Buscar por nombre, correo o teléfono"
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
      <div class="d-flex align-center gap-3 py-2">
        <UserAvatar
          size="36"
          :name="`${item.name} ${item.last_name}`"
          :avatar-url="avatarUrl(item)"
        />
        <div>
          <div class="font-weight-medium">
            {{ item.name }} {{ item.last_name }}
          </div>
          <div class="text-caption text-medium-emphasis">
            {{ item.email }}
          </div>
        </div>
      </div>
    </template>

    <template #item.role="{ item }">
      <VChip
        v-if="item.role"
        :color="ROLE_COLORS[item.role.name] ?? 'default'"
        variant="tonal"
        size="small"
      >
        {{ item.role.display_name }}
      </VChip>
    </template>

    <template #item.points_balance="{ value }">
      <VChip
        :color="value > 0 ? 'success' : 'default'"
        variant="tonal"
        size="small"
      >
        {{ value }}
      </VChip>
    </template>

    <template #item.deleted_at="{ item }">
      <div class="d-flex align-center gap-2">
        <span
          class="status-dot"
          :class="item.deleted_at ? 'bg-error' : 'bg-success'"
        />
        <span class="text-body-2">{{ item.deleted_at ? 'Dado de baja' : 'Activo' }}</span>
      </div>
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
          <template v-if="!item.deleted_at">
            <VListItem @click="emit('points', item)">
              <template #prepend>
                <VIcon
                  icon="bx-coin-stack"
                  class="me-2 text-primary"
                />
              </template>
              <VListItemTitle>Modificar puntos</VListItemTitle>
            </VListItem>
            <VListItem @click="emit('reset-credentials', item)">
              <template #prepend>
                <VIcon
                  icon="bx-key"
                  class="me-2 text-warning"
                />
              </template>
              <VListItemTitle>Resetear credenciales</VListItemTitle>
            </VListItem>
            <VListItem
              v-if="item.two_factor_status"
              @click="emit('disable-two-factor', item)"
            >
              <template #prepend>
                <VIcon
                  icon="bx-shield-x"
                  class="me-2 text-warning"
                />
              </template>
              <VListItemTitle>Deshabilitar 2FA</VListItemTitle>
            </VListItem>
            <VListItem @click="emit('deactivate', item)">
              <template #prepend>
                <VIcon
                  icon="bx-user-x"
                  class="me-2 text-error"
                />
              </template>
              <VListItemTitle class="text-error">
                Dar de baja
              </VListItemTitle>
            </VListItem>
          </template>
          <template v-else>
            <VListItem @click="emit('restore', item)">
              <template #prepend>
                <VIcon
                  icon="bx-user-check"
                  class="me-2 text-success"
                />
              </template>
              <VListItemTitle>Restaurar</VListItemTitle>
            </VListItem>
          </template>
        </VList>
      </VMenu>
    </template>
  </VDataTableServer>
</template>

<style scoped>
.status-dot {
  inline-size: 8px;
  block-size: 8px;
  border-radius: 50%;
}
</style>
