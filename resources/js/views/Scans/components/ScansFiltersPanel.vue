<script setup>
import { useContainerCatalog } from '@/hooks/Containers/useContainerCatalog'
import { useMaterialTypeCatalog } from '@/hooks/Scans/useMaterialTypeCatalog'
import { SCAN_STATUS } from '@/utils/scanStatus'
import { onMounted } from 'vue'

defineProps({
  status: { type: Number, default: null },
  containerId: { type: Number, default: null },
  materialTypeId: { type: Number, default: null },
  hasActiveFilters: Boolean,
})

const emit = defineEmits(['update:status', 'update:containerId', 'update:materialTypeId', 'clear'])

const { containers, loading: containersLoading, fetchContainers } = useContainerCatalog()
const { materialTypes, loading: materialsLoading, fetchMaterialTypes } = useMaterialTypeCatalog()

onMounted(() => {
  fetchContainers()
  fetchMaterialTypes()
})

function emitNumber(event, rawValue) {
  emit(event, rawValue === '' || rawValue === null ? null : Number(rawValue))
}
</script>

<template>
  <VCardText class="pa-6">
    <VRow>
      <VCol
        cols="12"
        md="4"
      >
        <VSelect
          :model-value="status"
          :items="SCAN_STATUS"
          item-title="label"
          item-value="value"
          label="Estado"
          density="compact"
          clearable
          @update:model-value="emitNumber('update:status', $event)"
        />
      </VCol>
      <VCol
        cols="12"
        md="4"
      >
        <VAutocomplete
          :model-value="containerId"
          :items="containers"
          :loading="containersLoading"
          item-title="name"
          item-value="id"
          label="Contenedor"
          density="compact"
          clearable
          @update:model-value="emitNumber('update:containerId', $event)"
        />
      </VCol>
      <VCol
        cols="12"
        md="4"
      >
        <VSelect
          :model-value="materialTypeId"
          :items="materialTypes"
          :loading="materialsLoading"
          item-title="name"
          item-value="id"
          label="Material"
          density="compact"
          clearable
          @update:model-value="emitNumber('update:materialTypeId', $event)"
        />
      </VCol>
    </VRow>
    <div
      v-if="hasActiveFilters"
      class="d-flex justify-end mt-2"
    >
      <VBtn
        variant="text"
        size="small"
        color="error"
        @click="emit('clear')"
      >
        <VIcon
          icon="bx-x"
          class="me-1"
        />
        Limpiar filtros
      </VBtn>
    </div>
  </VCardText>
</template>
