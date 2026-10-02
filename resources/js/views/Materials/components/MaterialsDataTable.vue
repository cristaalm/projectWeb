<script setup>
defineProps({
  items: { type: Array, default: () => [] },
  loading: Boolean,
})

// El catálogo es fijo y corto: se ordena en el cliente y sin paginar.
const HEADERS = [
  { title: 'ID', key: 'id' },
  { title: 'Material', key: 'name' },
  { title: 'Identificador', key: 'slug' },
  { title: 'Puntos', key: 'points' },
  { title: 'Estado', key: 'is_active' },
]

const DEFAULT_SORT = [{ key: 'id', order: 'asc' }]
</script>

<template>
  <VDataTable
    :headers="HEADERS"
    :items="items"
    :loading="loading"
    :sort-by="DEFAULT_SORT"
    :items-per-page="-1"
    no-data-text="No hay materiales registrados"
    class="px-2"
  >
    <template #item.id="{ value }">
      <span class="text-body-2 font-weight-medium">{{ value }}</span>
    </template>

    <template #item.name="{ value }">
      <span class="text-body-2">{{ value }}</span>
    </template>

    <template #item.slug="{ value }">
      <code class="text-body-2">{{ value }}</code>
    </template>

    <template #item.points="{ value }">
      <VChip
        :color="value > 0 ? 'success' : 'default'"
        variant="tonal"
        size="small"
      >
        {{ value > 0 ? `+${value}` : 'Sin puntos' }}
      </VChip>
    </template>

    <template #item.is_active="{ value }">
      <VChip
        :color="value ? 'success' : 'default'"
        variant="tonal"
        size="small"
      >
        {{ value ? 'Activo' : 'Inactivo' }}
      </VChip>
    </template>

    <template #bottom />
  </VDataTable>
</template>
