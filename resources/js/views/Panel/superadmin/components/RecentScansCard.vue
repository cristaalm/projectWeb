<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { storageURL } from '@/utils/constants'
import { scanStatusColor, scanStatusLabel } from '@/utils/scanStatus'
import PanelEmptyState from '../../components/PanelEmptyState.vue'
import { format, parseISO } from 'date-fns'

defineProps({
  scans: { type: Array, default: () => [] },
  loading: Boolean,
})

function fullName(user) {
  return `${user?.name ?? ''} ${user?.last_name ?? ''}`.trim()
}

function avatarUrl(user) {
  return user?.avatar ? storageURL + user.avatar : null
}

function formatDate(value) {
  if (!value) return ''

  return format(parseISO(value), 'dd/MM/yyyy HH:mm')
}
</script>

<template>
  <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
    <VCardText class="pb-2 pa-6">
      <div class="flex-wrap gap-3 d-flex align-center justify-space-between">
        <div>
          <h2 class="text-h6 font-weight-semibold">
            Actividad reciente
          </h2>
          <p class="mb-0 text-body-2 text-medium-emphasis">
            Últimos escaneos registrados
          </p>
        </div>
        <VBtn
          variant="tonal"
          color="primary"
          size="small"
          :to="{ name: 'scans' }"
        >
          Ver todos
          <VIcon
            icon="bx-chevron-right"
            end
          />
        </VBtn>
      </div>
    </VCardText>

    <VSkeletonLoader
      v-if="loading && !scans.length"
      type="list-item-avatar-two-line@4"
      class="bg-transparent"
    />
    <PanelEmptyState
      v-else-if="!scans.length"
      icon="bx-scan"
      text="Todavía no hay escaneos registrados"
    />
    <VTable
      v-else
      class="px-2 pb-2 text-no-wrap"
    >
      <tbody>
        <tr
          v-for="scan in scans"
          :key="scan.id"
        >
          <td>
            <div class="gap-3 py-2 d-flex align-center">
              <UserAvatar
                :name="fullName(scan.user)"
                :avatar-url="avatarUrl(scan.user)"
                :size="32"
              />
              <div>
                <div class="text-body-2">
                  {{ fullName(scan.user) || '—' }}
                </div>
                <div class="text-caption text-medium-emphasis">
                  {{ scan.container?.name ?? '—' }}
                </div>
              </div>
            </div>
          </td>
          <td class="text-body-2">
            {{ scan.material?.name ?? '—' }}
          </td>
          <td>
            <VChip
              :color="scan.points_awarded > 0 ? 'success' : 'default'"
              variant="tonal"
              size="small"
            >
              {{ scan.points_awarded > 0 ? `+${scan.points_awarded}` : scan.points_awarded }}
            </VChip>
          </td>
          <td>
            <VChip
              :color="scanStatusColor(scan.status)"
              variant="tonal"
              size="small"
            >
              {{ scanStatusLabel(scan.status) }}
            </VChip>
          </td>
          <td class="text-body-2 text-medium-emphasis text-end">
            {{ formatDate(scan.scanned_at) }}
          </td>
        </tr>
      </tbody>
    </VTable>
  </VCard>
</template>
