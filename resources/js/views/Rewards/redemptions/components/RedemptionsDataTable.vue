<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { useAuthStore } from '@/store/auth'
import { canDeliver } from '@/utils/rewardPermissions'
import { redemptionStatusColor, redemptionStatusLabel } from '@/utils/rewardRedemptionStatus'
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
  'deliver',
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
  { title: 'Usuario', key: 'user', sortable: false },
  { title: 'Recompensa', key: 'reward', sortable: false },
  { title: 'Alianza', key: 'alliance', sortable: false },
  { title: 'Cantidad', key: 'quantity' },
  { title: 'Puntos gastados', key: 'points_spent' },
  { title: 'Estado', key: 'status' },
  { title: 'Entregado por', key: 'merchant_user', sortable: false },
  { title: 'Fecha', key: 'created_at' },
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
      label="Buscar por usuario, correo, recompensa o código"
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

    <template #item.reward="{ item }">
      <div>
        <div class="text-body-2">
          {{ item.reward?.name }}
        </div>
        <div class="text-caption text-medium-emphasis">
          {{ item.reward?.code }}
        </div>
      </div>
    </template>

    <template #item.alliance="{ item }">
      <span class="text-body-2">{{ item.alliance?.name ?? '—' }}</span>
    </template>

    <template #item.points_spent="{ item }">
      <span class="text-body-2">{{ item.points_spent * item.quantity }}</span>
    </template>

    <template #item.status="{ value }">
      <VChip
        :color="redemptionStatusColor(value)"
        variant="tonal"
        size="small"
      >
        {{ redemptionStatusLabel(value) }}
      </VChip>
    </template>

    <template #item.merchant_user="{ item }">
      <span class="text-body-2 text-medium-emphasis">
        {{ item.merchant_user ? `${item.merchant_user.name} ${item.merchant_user.last_name}` : '—' }}
      </span>
    </template>

    <template #item.created_at="{ value }">
      <span class="text-body-2 text-medium-emphasis">{{ formatDate(value) }}</span>
    </template>

    <template #item.actions="{ item }">
      <VBtn
        v-if="canDeliver(currentUser, item)"
        variant="tonal"
        color="success"
        size="small"
        @click="emit('deliver', item)"
      >
        <VIcon
          icon="bx-check"
          class="me-1"
        />
        Marcar entregado
      </VBtn>
    </template>
  </VDataTableServer>
</template>
