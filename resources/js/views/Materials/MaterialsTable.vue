<script setup>
import { useMaterialTypeCatalog } from '@/hooks/Scans/useMaterialTypeCatalog'
import MaterialsDataTable from './components/MaterialsDataTable.vue'
import MaterialsTableHeader from './components/MaterialsTableHeader.vue'
import { onMounted } from 'vue'

const { materialTypes, loading, fetchMaterialTypes } = useMaterialTypeCatalog()

onMounted(() => fetchMaterialTypes({ force: true }))
</script>

<template>
  <div class="gap-6 d-flex flex-column">
    <VCard
      class="border border-gray-200 rounded-lg dark:border-gray-700"
      style="overflow: hidden;"
    >
      <MaterialsTableHeader @refresh="fetchMaterialTypes({ force: true })" />
    </VCard>

    <VCard class="border border-gray-200 rounded-lg dark:border-gray-700">
      <MaterialsDataTable
        :items="materialTypes"
        :loading="loading"
      />
    </VCard>
  </div>
</template>
