<script setup>
import RedemptionsDataTable from './components/RedemptionsDataTable.vue'
import RedemptionsFiltersPanel from './components/RedemptionsFiltersPanel.vue'
import RedemptionsTableHeader from './components/RedemptionsTableHeader.vue'
import { useRedemptionRowActions } from './hooks/useRedemptionRowActions'
import { useRedemptionsFilters } from './hooks/useRedemptionsFilters'
import { useRedemptionsList } from './hooks/useRedemptionsList'
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
} = useRedemptionsList()

const {
  hasActiveFilters,
  activeFilterCount,
  clearFilters,
} = useRedemptionsFilters(status, allianceFilter)

const { handleDeliver } = useRedemptionRowActions(loadData)
</script>

<template>
  <div class="gap-6 d-flex flex-column">
    <VCard
      class="border border-gray-200 rounded-lg dark:border-gray-700"
      style="overflow: hidden;"
    >
      <RedemptionsTableHeader
        v-model:show-filters="showFilters"
        :has-active-filters="hasActiveFilters"
        :active-filter-count="activeFilterCount"
        @refresh="loadData"
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
          <RedemptionsFiltersPanel
            v-model:status="status"
            v-model:alliance-id="allianceFilter"
            :has-active-filters="hasActiveFilters"
            @clear="clearFilters"
          />
        </div>
      </Transition>
    </VCard>

    <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
      <RedemptionsDataTable
        v-model:page="page"
        v-model:items-per-page="perPage"
        v-model:sort-by="sortBy"
        v-model:search="search"
        :items="data"
        :total="total"
        :loading="loading"
        @deliver="handleDeliver"
      />
    </VCard>
  </div>
</template>
