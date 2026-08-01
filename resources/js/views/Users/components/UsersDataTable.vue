<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { useAuthStore } from '@/store/auth'
import { storageURL } from '@/utils/constants'
import { ROLE_COLORS } from '@/utils/roles'
import { canManageAccount, canModifyPoints } from '@/utils/userPermissions'
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
  'view',
  'points',
  'deactivate',
  'restore',
  'reset-credentials',
  'disable-two-factor',
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

function showPoints(item) {
  return canModifyPoints(currentUser.value, item)
}

function showManage(item) {
  return canManageAccount(currentUser.value, item)
}

function hasAnyAction(item) {
  return item.deleted_at ? showManage(item) : showPoints(item) || showManage(item)
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
    :items-per-page-options="ITEMS_PER_PAGE_OPTIONS"
    items-per-page-text="Filas por página:"
    page-text="{0}-{1} de {2}"
    :sort-by="sortBy"
    :headers="HEADERS"
    :items="items"
    :items-length="total"
    :loading="loading"
    class="px-2 cursor-pointer"
    @update:page="emit('update:page', $event)"
    @update:items-per-page="emit('update:itemsPerPage', $event)"
    @update:sort-by="emit('update:sortBy', $event)"
    @click:row="(_, { item }) => emit('view', item)"
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
      <div
        v-if="hasAnyAction(item)"
        @click.stop
      >
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
              <VListItem
                v-if="showPoints(item)"
                @click="emit('points', item)"
              >
                <template #prepend>
                  <VIcon
                    icon="bx-coin-stack"
                    class="me-2 text-primary"
                  />
                </template>
                <VListItemTitle>Modificar puntos</VListItemTitle>
              </VListItem>
              <VListItem
                v-if="showManage(item)"
                @click="emit('reset-credentials', item)"
              >
                <template #prepend>
                  <VIcon
                    icon="bx-key"
                    class="me-2 text-warning"
                  />
                </template>
                <VListItemTitle>Resetear credenciales</VListItemTitle>
              </VListItem>
              <VListItem
                v-if="item.two_factor_status && showManage(item)"
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
              <VListItem
                v-if="showManage(item)"
                @click="emit('deactivate', item)"
              >
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
              <VListItem
                v-if="showManage(item)"
                @click="emit('restore', item)"
              >
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
      </div>
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
