<script setup>
import BadgeFormDialog from './BadgeFormDialog.vue'
import BadgesDataTable from './components/BadgesDataTable.vue'
import BadgesFiltersPanel from './components/BadgesFiltersPanel.vue'
import BadgesTableHeader from './components/BadgesTableHeader.vue'
import { useBadgeRowActions } from './hooks/useBadgeRowActions'
import { useBadgesFilters } from './hooks/useBadgesFilters'
import { useBadgesList } from './hooks/useBadgesList'
import { ref } from 'vue'

const showFilters = ref(false)

const {
  data, total, loading, page, perPage, sortBy, search, status, loadData,
} = useBadgesList()

const {
  hasActiveFilters, activeFilterCount, clearFilters,
} = useBadgesFilters(status)

const {
  formDialog, formMode, activeBadge,
  openCreateDialog, openEditDialog, handleDelete,
} = useBadgeRowActions(loadData)
</script>

<template>
  <div class="gap-6 d-flex flex-column">
    <VCard class="border border-gray-200 rounded-lg dark:border-gray-700" style="overflow: hidden;">
      <BadgesTableHeader
        v-model:show-filters="showFilters"
        :has-active-filters="hasActiveFilters"
        :active-filter-count="activeFilterCount"
        @refresh="loadData"
        @create="openCreateDialog"
      />
      <Transition
        enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0"
      >
        <div v-show="showFilters" class="border-t border-gray-200 dark:border-gray-700">
          <BadgesFiltersPanel v-model:status="status" :has-active-filters="hasActiveFilters" @clear="clearFilters" />
        </div>
      </Transition>
    </VCard>

    <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
      <BadgesDataTable
        v-model:page="page" v-model:items-per-page="perPage" v-model:sort-by="sortBy" v-model:search="search"
        :items="data" :total="total" :loading="loading"
        @edit="openEditDialog" @delete="handleDelete"
      />
    </VCard>
  </div>

  <BadgeFormDialog v-model="formDialog" :mode="formMode" :badge="activeBadge" @saved="loadData" />
</template>
