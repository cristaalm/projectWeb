<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { storageURL } from '@/utils/constants'
import { formatDashboardNumber } from '@/utils/dashboardPeriods'
import PanelEmptyState from '../../components/PanelEmptyState.vue'

defineProps({
  users: { type: Array, default: () => [] },
  loading: Boolean,
})

function fullName(user) {
  return `${user.name ?? ''} ${user.last_name ?? ''}`.trim()
}

function avatarUrl(user) {
  return user.avatar ? storageURL + user.avatar : null
}
</script>

<template>
  <VCard class="border border-gray-200 rounded-lg h-100 dark:border-gray-700">
    <VCardText class="pb-2 pa-6">
      <h2 class="text-h6 font-weight-semibold">
        Usuarios destacados
      </h2>
      <p class="mb-0 text-body-2 text-medium-emphasis">
        Por puntos ganados
      </p>
    </VCardText>

    <VSkeletonLoader
      v-if="loading && !users.length"
      type="list-item-avatar-two-line@3"
      class="bg-transparent"
    />
    <PanelEmptyState
      v-else-if="!users.length"
      icon="bx-user"
      text="Ningún usuario ganó puntos"
    />
    <div
      v-else
      class="px-6 pb-5 gap-4 d-flex flex-column"
    >
      <div
        v-for="user in users"
        :key="user.id"
        class="gap-3 d-flex align-center"
      >
        <UserAvatar
          :name="fullName(user)"
          :avatar-url="avatarUrl(user)"
          :size="36"
        />
        <div class="overflow-hidden flex-grow-1">
          <div class="text-body-2 font-weight-medium text-truncate">
            {{ fullName(user) }}
          </div>
          <div class="text-caption text-medium-emphasis">
            {{ formatDashboardNumber(user.valid_scans) }} {{ user.valid_scans === 1 ? 'reciclaje' : 'reciclajes' }}
          </div>
        </div>
        <VChip
          color="success"
          variant="tonal"
          size="small"
        >
          +{{ formatDashboardNumber(user.points) }}
        </VChip>
      </div>
    </div>
  </VCard>
</template>
