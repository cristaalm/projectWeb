<script setup>
import { useTypeShopCatalog } from '@/hooks/TypeShop/useTypeShopCatalog'
import { ALLIANCE_STATUS } from '@/utils/allianceStatus'
import { onMounted } from 'vue'

defineProps({
  status: { type: Number, default: null },
  typeShopId: { type: Number, default: null },
  hasActiveFilters: Boolean,
})

const emit = defineEmits(['update:status', 'update:typeShopId', 'clear'])

const { typeShops, loading: typeShopsLoading, fetchTypeShops } = useTypeShopCatalog()

onMounted(fetchTypeShops)

function emitStatus(rawValue) {
  emit('update:status', rawValue === '' || rawValue === null ? null : Number(rawValue))
}

function emitTypeShopId(rawValue) {
  emit('update:typeShopId', rawValue === '' || rawValue === null ? null : Number(rawValue))
}
</script>

<template>
  <VCardText class="pa-6">
    <VRow>
      <VCol
        cols="12"
        md="3"
      >
        <VSelect
          :model-value="status"
          :items="ALLIANCE_STATUS"
          item-title="label"
          item-value="value"
          label="Estado"
          density="compact"
          clearable
          @update:model-value="emitStatus"
        />
      </VCol>
      <VCol
        cols="12"
        md="4"
      >
        <VSelect
          :model-value="typeShopId"
          :items="typeShops"
          :loading="typeShopsLoading"
          item-title="name"
          item-value="id"
          label="Categoría"
          density="compact"
          clearable
          @update:model-value="emitTypeShopId"
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
