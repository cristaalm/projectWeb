<script setup>
import ReasonActionDialog from '@/components/ReasonActionDialog.vue'
import RewardsDataTable from './components/RewardsDataTable.vue'
import RewardsFiltersPanel from './components/RewardsFiltersPanel.vue'
import RewardsTableHeader from './components/RewardsTableHeader.vue'
import { useRewardRowActions } from './hooks/useRewardRowActions'
import { useRewardsFilters } from './hooks/useRewardsFilters'
import { useRewardsList } from './hooks/useRewardsList'
import RewardFormDialog from './RewardFormDialog.vue'
import { ref } from 'vue'

const showFilters = ref(false)

const {
  data,
  total,
  loading,
  page,
  perPage,
  sortBy,
  search,
  status,
  allianceFilter,
  loadData,
} = useRewardsList()

const {
  hasActiveFilters,
  activeFilterCount,
  clearFilters,
} = useRewardsFilters(status, allianceFilter)

const {
  formDialog,
  formMode,
  activeReward,
  openCreateDialog,
  openEditDialog,
  handleDelete,
  handleApprove,
  rejectDialog,
  rejectLoading,
  openRejectDialog,
  confirmReject,
  handlePause,
  handleReactivate,
} = useRewardRowActions(loadData)
</script>

<template>
  <div class="gap-6 d-flex flex-column">
    <VCard
      class="border border-gray-200 rounded-lg dark:border-gray-700"
      style="overflow: hidden;"
    >
      <RewardsTableHeader
        v-model:show-filters="showFilters"
        :has-active-filters="hasActiveFilters"
        :active-filter-count="activeFilterCount"
        @refresh="loadData"
        @create="openCreateDialog"
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
          <RewardsFiltersPanel
            v-model:status="status"
            v-model:alliance-id="allianceFilter"
            :has-active-filters="hasActiveFilters"
            @clear="clearFilters"
          />
        </div>
      </Transition>
    </VCard>

    <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
      <RewardsDataTable
        v-model:page="page"
        v-model:items-per-page="perPage"
        v-model:sort-by="sortBy"
        v-model:search="search"
        :items="data"
        :total="total"
        :loading="loading"
        @edit="openEditDialog"
        @delete="handleDelete"
        @approve="handleApprove"
        @reject="openRejectDialog"
        @pause="handlePause"
        @reactivate="handleReactivate"
      />
    </VCard>
  </div>

  <RewardFormDialog
    v-model="formDialog"
    :mode="formMode"
    :reward="activeReward"
    @saved="loadData"
  />

  <ReasonActionDialog
    v-model="rejectDialog"
    title="Rechazar recompensa"
    action-label="Rechazar"
    color="error"
    :loading="rejectLoading"
    @confirm="confirmReject"
  />
</template>
