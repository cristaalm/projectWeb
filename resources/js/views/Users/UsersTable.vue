<script setup>
import CreateUserDialog from './CreateUserDialog.vue'
import ModifyPointsDialog from './ModifyPointsDialog.vue'
import ReasonActionDialog from './ReasonActionDialog.vue'
import UsersDataTable from './components/UsersDataTable.vue'
import UsersFiltersPanel from './components/UsersFiltersPanel.vue'
import UsersTableHeader from './components/UsersTableHeader.vue'
import { useUserRowActions } from './hooks/useUserRowActions'
import { useUsersFilters } from './hooks/useUsersFilters'
import { useUsersList } from './hooks/useUsersList'
import { ref } from 'vue'

const showFilters = ref(false)
const createDialog = ref(false)

const {
  roleFilter,
  allianceFilter,
  pointsMin,
  pointsMax,
  withTrashed,
  hasActiveFilters,
  activeFilterCount,
  clearFilters,
} = useUsersFilters()

const {
  data,
  total,
  loading,
  page,
  perPage,
  sortBy,
  search,
  loadData,
} = useUsersList({ roleFilter, allianceFilter, pointsMin, pointsMax, withTrashed })

const {
  loading: actionLoading,
  activeUser,
  pointsDialog,
  openPointsDialog,
  reasonDialog,
  reasonMode,
  openDeactivateDialog,
  openRestoreDialog,
  confirmReasonAction,
  handleResetCredentials,
  handleDisableTwoFactor,
} = useUserRowActions(loadData)
</script>

<template>
  <div class="gap-6 d-flex flex-column">
    <VCard
      class="border border-gray-200 rounded-lg dark:border-gray-700"
      style="overflow: hidden;"
    >
      <UsersTableHeader
        v-model:show-filters="showFilters"
        :has-active-filters="hasActiveFilters"
        :active-filter-count="activeFilterCount"
        @refresh="loadData"
        @create="createDialog = true"
      />

      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-show="showFilters"
          class="border-t border-gray-200 dark:border-gray-700"
        >
          <UsersFiltersPanel
            v-model:role="roleFilter"
            v-model:alliance-id="allianceFilter"
            v-model:points-min="pointsMin"
            v-model:points-max="pointsMax"
            v-model:with-trashed="withTrashed"
            :has-active-filters="hasActiveFilters"
            @clear="clearFilters"
          />
        </div>
      </Transition>
    </VCard>

    <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
      <UsersDataTable
        v-model:page="page"
        v-model:items-per-page="perPage"
        v-model:sort-by="sortBy"
        v-model:search="search"
        :items="data"
        :total="total"
        :loading="loading"
        @points="openPointsDialog"
        @deactivate="openDeactivateDialog"
        @restore="openRestoreDialog"
        @reset-credentials="handleResetCredentials"
        @disable-two-factor="handleDisableTwoFactor"
      />
    </VCard>
  </div>

  <CreateUserDialog
    v-model="createDialog"
    @created="loadData"
  />

  <ModifyPointsDialog
    v-model="pointsDialog"
    :user="activeUser"
    @updated="loadData"
  />

  <ReasonActionDialog
    v-model="reasonDialog"
    :title="reasonMode === 'deactivate' ? 'Dar de baja' : 'Restaurar usuario'"
    :action-label="reasonMode === 'deactivate' ? 'Dar de baja' : 'Restaurar'"
    :color="reasonMode === 'deactivate' ? 'error' : 'primary'"
    :loading="actionLoading"
    @confirm="confirmReasonAction"
  />
</template>
