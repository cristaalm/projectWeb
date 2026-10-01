<script setup>
defineProps({
  showFilters: Boolean,
  hasActiveFilters: Boolean,
  activeFilterCount: { type: Number, default: 0 },
})

const emit = defineEmits(['update:showFilters', 'refresh'])
</script>

<template>
  <VCardText class="pa-6">
    <div class="flex-wrap gap-4 d-flex justify-space-between align-center">
      <div>
        <h1 class="text-h4 font-weight-semibold">
          Historial de <span class="text-primary">escaneos</span>
        </h1>
        <p class="mt-1 mb-0 text-medium-emphasis">
          Consulta los reciclajes registrados por los contenedores y los puntos que otorgaron
        </p>
      </div>

      <div class="flex-wrap gap-3 d-flex">
        <VBtn
          icon
          variant="text"
          title="Refrescar"
          @click="emit('refresh')"
        >
          <VIcon icon="bx-refresh" />
        </VBtn>

        <VBadge
          :content="activeFilterCount"
          :model-value="hasActiveFilters"
          color="primary"
        >
          <VBtn
            :variant="showFilters ? 'flat' : 'tonal'"
            :color="showFilters ? 'primary' : 'default'"
            @click="emit('update:showFilters', !showFilters)"
          >
            <VIcon
              icon="bx-filter-alt"
              class="me-2"
            />
            Filtros
            <VIcon
              :icon="showFilters ? 'bx-chevron-up' : 'bx-chevron-down'"
              class="ms-1"
            />
          </VBtn>
        </VBadge>
      </div>
    </div>
  </VCardText>
</template>
