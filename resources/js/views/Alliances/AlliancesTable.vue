<script setup>
import AllianceFormDialog from './AllianceFormDialog.vue'
import AlliancesDataTable from './components/AlliancesDataTable.vue'
import AlliancesFiltersPanel from './components/AlliancesFiltersPanel.vue'
import AlliancesTableHeader from './components/AlliancesTableHeader.vue'
import TypeShopManagementDialog from './components/TypeShopManagementDialog.vue'
import { useAllianceRowActions } from './hooks/useAllianceRowActions'
import { useAlliancesFilters } from './hooks/useAlliancesFilters'
import { useAlliancesList } from './hooks/useAlliancesList'
import { ref } from 'vue'

const showFilters = ref(false)
const typeShopDialog = ref(false)

const {
  data,
  total,
  loading,
  page,
  perPage,
  sortBy,
  search,
  status,
  typeShopFilter,
  loadData,
} = useAlliancesList()

const {
  hasActiveFilters,
  activeFilterCount,
  clearFilters,
} = useAlliancesFilters(status, typeShopFilter)

const {
  formDialog,
  formMode,
  activeAlliance,
  openCreateDialog,
  openEditDialog,
  handleDelete,
} = useAllianceRowActions(loadData)
</script>

<template>
  <div class="gap-6 d-flex flex-column">
    <VCard
      class="border border-gray-200 rounded-lg dark:border-gray-700"
      style="overflow: hidden;"
    >
      <AlliancesTableHeader
        v-model:show-filters="showFilters"
        :has-active-filters="hasActiveFilters"
        :active-filter-count="activeFilterCount"
        @refresh="loadData"
        @create="openCreateDialog"
        @manage-categories="typeShopDialog = true"
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
          <AlliancesFiltersPanel
            v-model:status="status"
            v-model:type-shop-id="typeShopFilter"
            :has-active-filters="hasActiveFilters"
            @clear="clearFilters"
          />
        </div>
      </Transition>
    </VCard>

    <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
      <AlliancesDataTable
        v-model:page="page"
        v-model:items-per-page="perPage"
        v-model:sort-by="sortBy"
        v-model:search="search"
        :items="data"
        :total="total"
        :loading="loading"
        @edit="openEditDialog"
        @delete="handleDelete"
      />
    </VCard>
  </div>

  <AllianceFormDialog
    v-model="formDialog"
    :mode="formMode"
    :alliance="activeAlliance"
    @saved="loadData"
  />

  <TypeShopManagementDialog v-model="typeShopDialog" />
</template>
