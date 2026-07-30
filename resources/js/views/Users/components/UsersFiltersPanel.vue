<script setup>
import { useAllianceCatalog } from '@/hooks/Users/useAllianceCatalog'
import { ROLES } from '@/utils/roles'

defineProps({
  role: { type: String, default: null },
  allianceId: { type: [Number, String], default: null },
  pointsMin: { type: Number, default: null },
  pointsMax: { type: Number, default: null },
  withTrashed: Boolean,
  hasActiveFilters: Boolean,
})

const emit = defineEmits([
  'update:role',
  'update:allianceId',
  'update:pointsMin',
  'update:pointsMax',
  'update:withTrashed',
  'clear',
])

const { alliances, fetchAlliances } = useAllianceCatalog()

fetchAlliances()

function emitNumber(eventName, rawValue) {
  emit(eventName, rawValue === '' || rawValue === null ? null : Number(rawValue))
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
          :model-value="role"
          :items="ROLES"
          item-title="display_name"
          item-value="name"
          label="Rol"
          density="compact"
          clearable
          @update:model-value="emit('update:role', $event)"
        />
      </VCol>
      <VCol
        cols="12"
        md="3"
      >
        <VSelect
          :model-value="allianceId"
          :items="alliances"
          item-title="name"
          item-value="id"
          label="Alianza"
          density="compact"
          clearable
          @update:model-value="emit('update:allianceId', $event)"
        />
      </VCol>
      <VCol
        cols="6"
        md="2"
      >
        <VTextField
          :model-value="pointsMin"
          type="number"
          label="Puntos mín."
          density="compact"
          @update:model-value="emitNumber('update:pointsMin', $event)"
        />
      </VCol>
      <VCol
        cols="6"
        md="2"
      >
        <VTextField
          :model-value="pointsMax"
          type="number"
          label="Puntos máx."
          density="compact"
          @update:model-value="emitNumber('update:pointsMax', $event)"
        />
      </VCol>
      <VCol
        cols="12"
        md="2"
        class="d-flex align-center"
      >
        <VSwitch
          :model-value="withTrashed"
          label="Ver dados de baja"
          density="compact"
          color="primary"
          hide-details
          @update:model-value="emit('update:withTrashed', $event)"
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
