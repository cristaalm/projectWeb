<script setup>
import { useAllianceCatalog } from '@/hooks/Users/useAllianceCatalog'
import { useAuthStore } from '@/store/auth'
import { REWARD_STATUS } from '@/utils/rewardStatus'
import { isStaff } from '@/utils/rewardPermissions'
import { computed, onMounted } from 'vue'

defineProps({
  status: { type: Number, default: null },
  allianceId: { type: Number, default: null },
  hasActiveFilters: Boolean,
})

const emit = defineEmits(['update:status', 'update:allianceId', 'clear'])

const authStore = useAuthStore()
const showAllianceFilter = computed(() => isStaff(authStore.getUser()))

const { alliances, loading: alliancesLoading, fetchAlliances } = useAllianceCatalog()

onMounted(() => {
  if (showAllianceFilter.value) fetchAlliances()
})

function emitStatus(rawValue) {
  emit('update:status', rawValue === '' || rawValue === null ? null : Number(rawValue))
}

function emitAllianceId(rawValue) {
  emit('update:allianceId', rawValue === '' || rawValue === null ? null : Number(rawValue))
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
          :items="REWARD_STATUS"
          item-title="label"
          item-value="value"
          label="Estado"
          density="compact"
          clearable
          @update:model-value="emitStatus"
        />
      </VCol>
      <VCol
        v-if="showAllianceFilter"
        cols="12"
        md="4"
      >
        <VSelect
          :model-value="allianceId"
          :items="alliances"
          :loading="alliancesLoading"
          item-title="name"
          item-value="id"
          label="Alianza"
          density="compact"
          clearable
          @update:model-value="emitAllianceId"
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
