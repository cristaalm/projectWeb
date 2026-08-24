<script setup>
import ContainerFormDialog from './ContainerFormDialog.vue'
import ContainersDataTable from './components/ContainersDataTable.vue'
import ContainersFiltersPanel from './components/ContainersFiltersPanel.vue'
import ContainersTableHeader from './components/ContainersTableHeader.vue'
import { useContainerRowActions } from './hooks/useContainerRowActions'
import { useContainersFilters } from './hooks/useContainersFilters'
import { useContainersList } from './hooks/useContainersList'
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
  loadData,
} = useContainersList()

const {
  hasActiveFilters,
  activeFilterCount,
  clearFilters,
} = useContainersFilters(status)

const {
  formDialog,
  formMode,
  activeContainer,
  openCreateDialog,
  openEditDialog,
  handleDelete,
} = useContainerRowActions(loadData)
</script>

<template>
  <div class="gap-6 d-flex flex-column">
    <VCard
      class="border border-gray-200 rounded-lg dark:border-gray-700"
      style="overflow: hidden;"
    >
      <ContainersTableHeader
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
          <ContainersFiltersPanel
            v-model:status="status"
            :has-active-filters="hasActiveFilters"
            @clear="clearFilters"
          />
        </div>
      </Transition>
    </VCard>

    <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
      <ContainersDataTable
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

  <ContainerFormDialog
    v-model="formDialog"
    :mode="formMode"
    :container="activeContainer"
    @saved="loadData"
  />
</template>
