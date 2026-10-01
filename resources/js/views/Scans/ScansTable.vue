<script setup>
import ScanImageDialog from './components/ScanImageDialog.vue'
import ScansDataTable from './components/ScansDataTable.vue'
import ScansFiltersPanel from './components/ScansFiltersPanel.vue'
import ScansTableHeader from './components/ScansTableHeader.vue'
import { useScansFilters } from './hooks/useScansFilters'
import { useScansList } from './hooks/useScansList'
import { ref } from 'vue'

const showFilters = ref(false)
const imageDialog = ref(false)
const selectedScan = ref(null)

const {
  data,
  total,
  loading,
  page,
  perPage,
  sortBy,
  search,
  status,
  containerFilter,
  materialFilter,
  loadData,
} = useScansList()

const {
  hasActiveFilters,
  activeFilterCount,
  clearFilters,
} = useScansFilters(status, containerFilter, materialFilter)

function openImage(scan) {
  selectedScan.value = scan
  imageDialog.value = true
}
</script>

<template>
  <div class="gap-6 d-flex flex-column">
    <VCard
      class="border border-gray-200 rounded-lg dark:border-gray-700"
      style="overflow: hidden;"
    >
      <ScansTableHeader
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
          <ScansFiltersPanel
            v-model:status="status"
            v-model:container-id="containerFilter"
            v-model:material-type-id="materialFilter"
            :has-active-filters="hasActiveFilters"
            @clear="clearFilters"
          />
        </div>
      </Transition>
    </VCard>

    <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
      <ScansDataTable
        v-model:page="page"
        v-model:items-per-page="perPage"
        v-model:sort-by="sortBy"
        v-model:search="search"
        :items="data"
        :total="total"
        :loading="loading"
        @view-image="openImage"
      />
    </VCard>

    <ScanImageDialog
      v-model="imageDialog"
      :scan="selectedScan"
    />
  </div>
</template>
