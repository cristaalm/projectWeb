<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { useUserManagement } from '@/hooks/Users/useUserManagement'
import { storageURL } from '@/utils/constants'
import { ROLE_COLORS } from '@/utils/roles'
import { format, parseISO } from 'date-fns'
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  userId: { type: Number, default: null },
})

const emit = defineEmits(['update:modelValue'])

const { getUserDetail, getUserHistory } = useUserManagement()

const HISTORY_PAGE_SIZE = 15

const user = ref(null)
const history = ref([])
const initialLoading = ref(false)
const loadingMoreHistory = ref(false)
const historyPage = ref(1)
const historyLastPage = ref(1)

const HISTORY_ICONS = {
  user_created: { icon: 'bx-user-plus', color: 'success' },
  points_adjustment: { icon: 'bx-coin-stack', color: 'primary' },
  deactivated: { icon: 'bx-user-x', color: 'error' },
  restored: { icon: 'bx-user-check', color: 'success' },
  credentials_reset: { icon: 'bx-key', color: 'warning' },
  two_factor_disabled: { icon: 'bx-shield-x', color: 'warning' },
}

watch(() => props.modelValue, async open => {
  if (!open || !props.userId) return

  user.value = null
  history.value = []
  historyPage.value = 1
  historyLastPage.value = 1
  initialLoading.value = true

  const [detail, historyResult] = await Promise.all([
    getUserDetail(props.userId),
    getUserHistory(props.userId, { page: 1, perPage: HISTORY_PAGE_SIZE }),
  ])

  user.value = detail
  history.value = historyResult.history ?? []
  historyLastPage.value = historyResult.lastPage ?? 1
  initialLoading.value = false
})

async function loadMoreHistory() {
  if (loadingMoreHistory.value || historyPage.value >= historyLastPage.value) return

  loadingMoreHistory.value = true

  const nextPage = historyPage.value + 1
  const result = await getUserHistory(props.userId, { page: nextPage, perPage: HISTORY_PAGE_SIZE })

  history.value = [...history.value, ...(result.history ?? [])]
  historyPage.value = nextPage
  historyLastPage.value = result.lastPage ?? historyLastPage.value
  loadingMoreHistory.value = false
}

function onHistoryScroll(event) {
  const el = event.target
  const scrollThreshold = 48

  if (el.scrollTop + el.clientHeight >= el.scrollHeight - scrollThreshold) loadMoreHistory()
}

function avatarUrl(target) {
  return target?.avatar ? storageURL + target.avatar : null
}

function formatDate(value) {
  if (!value) return ''

  return format(parseISO(value), 'dd/MM/yyyy HH:mm')
}

function historyIcon(entry) {
  return HISTORY_ICONS[entry.action_type] ?? { icon: 'bx-history', color: 'default' }
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="640"
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard title="Detalle de usuario">
      <VCardText v-if="initialLoading">
        <div class="gap-4 d-flex align-center mb-4">
          <VSkeletonLoader
            type="avatar"
            width="56"
            height="56"
          />
          <div class="flex-grow-1">
            <VSkeletonLoader
              type="text"
              width="55%"
              class="mb-2"
            />
            <VSkeletonLoader
              type="text"
              width="35%"
            />
          </div>
        </div>

        <VRow dense>
          <VCol
            v-for="n in 6"
            :key="n"
            cols="6"
          >
            <VSkeletonLoader
              type="text"
              width="30%"
              class="mb-1"
            />
            <VSkeletonLoader
              type="text"
              width="65%"
            />
          </VCol>
        </VRow>

        <VDivider class="my-4" />

        <VSkeletonLoader
          type="text"
          width="160"
          class="mb-3"
        />

        <div
          v-for="n in 4"
          :key="n"
          class="gap-3 d-flex py-2"
        >
          <VSkeletonLoader
            type="avatar"
            width="32"
            height="32"
          />
          <div class="flex-grow-1">
            <VSkeletonLoader
              type="text"
              width="60%"
              class="mb-1"
            />
            <VSkeletonLoader
              type="text"
              width="40%"
            />
          </div>
        </div>
      </VCardText>

      <VCardText v-else-if="user">
        <div class="gap-4 d-flex align-center mb-4">
          <UserAvatar
            size="56"
            :name="`${user.name} ${user.last_name}`"
            :avatar-url="avatarUrl(user)"
          />
          <div>
            <div class="text-h6">
              {{ user.name }} {{ user.last_name }}
            </div>
            <div class="text-body-2 text-medium-emphasis">
              {{ user.email }}
            </div>
          </div>
        </div>

        <VRow dense>
          <VCol cols="6">
            <div class="text-caption text-medium-emphasis">
              Teléfono
            </div>
            <div class="text-body-2">
              {{ user.phone || 'Sin registrar' }}
            </div>
          </VCol>
          <VCol cols="6">
            <div class="text-caption text-medium-emphasis">
              Rol
            </div>
            <VChip
              v-if="user.role"
              :color="ROLE_COLORS[user.role.name] ?? 'default'"
              variant="tonal"
              size="small"
            >
              {{ user.role.display_name }}
            </VChip>
          </VCol>
          <VCol
            v-if="user.alliance"
            cols="6"
          >
            <div class="text-caption text-medium-emphasis">
              Alianza
            </div>
            <div class="text-body-2">
              {{ user.alliance.name }}
            </div>
          </VCol>
          <VCol cols="6">
            <div class="text-caption text-medium-emphasis">
              Saldo de puntos
            </div>
            <VChip
              :color="user.points_balance > 0 ? 'success' : 'default'"
              variant="tonal"
              size="small"
            >
              {{ user.points_balance ?? 0 }}
            </VChip>
          </VCol>
          <VCol cols="6">
            <div class="text-caption text-medium-emphasis">
              Estado
            </div>
            <div class="d-flex align-center gap-2">
              <span
                class="status-dot"
                :class="user.deleted_at ? 'bg-error' : 'bg-success'"
              />
              <span class="text-body-2">{{ user.deleted_at ? 'Dado de baja' : 'Activo' }}</span>
            </div>
          </VCol>
          <VCol cols="6">
            <div class="text-caption text-medium-emphasis">
              Verificación en dos pasos
            </div>
            <div class="text-body-2">
              {{ user.two_factor_status ? 'Activada' : 'Desactivada' }}
            </div>
          </VCol>
          <VCol cols="6">
            <div class="text-caption text-medium-emphasis">
              Creado
            </div>
            <div class="text-body-2">
              {{ formatDate(user.created_at) }}
            </div>
          </VCol>
        </VRow>

        <VDivider class="my-4" />

        <div class="text-subtitle-1 mb-3">
          Historial de cambios
        </div>

        <div
          v-if="history.length"
          class="history-list"
          @scroll="onHistoryScroll"
        >
          <div
            v-for="entry in history"
            :key="entry.id"
            class="gap-3 d-flex py-2"
          >
            <VAvatar
              size="32"
              variant="tonal"
              :color="historyIcon(entry).color"
            >
              <VIcon
                :icon="historyIcon(entry).icon"
                size="18"
              />
            </VAvatar>
            <div class="flex-grow-1">
              <div class="text-body-2 font-weight-medium">
                {{ entry.label }}
                <span
                  v-if="entry.type === 'points_adjustment'"
                  :class="entry.points >= 0 ? 'text-success' : 'text-error'"
                >
                  ({{ entry.points >= 0 ? '+' : '' }}{{ entry.points }})
                </span>
              </div>
              <div
                v-if="entry.reason"
                class="text-caption text-medium-emphasis"
              >
                {{ entry.reason }}
              </div>
              <div class="text-caption text-medium-emphasis">
                {{ entry.actor ? `${entry.actor.name} ${entry.actor.last_name}` : 'Sistema' }} · {{ formatDate(entry.created_at) }}
              </div>
            </div>
          </div>

          <div
            v-if="loadingMoreHistory"
            class="d-flex justify-center py-2"
          >
            <VProgressCircular
              indeterminate
              size="20"
              width="2"
              color="primary"
            />
          </div>
        </div>
        <div
          v-else
          class="text-body-2 text-medium-emphasis py-4 text-center"
        >
          Sin acciones registradas.
        </div>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          variant="tonal"
          @click="emit('update:modelValue', false)"
        >
          Cerrar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
.status-dot {
  inline-size: 8px;
  block-size: 8px;
  border-radius: 50%;
}

.history-list {
  max-block-size: 320px;
  overflow-y: auto;
}
</style>
