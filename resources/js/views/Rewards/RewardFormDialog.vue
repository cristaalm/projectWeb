<script setup>
import { useRewardManagement } from '@/hooks/Rewards/useRewardManagement'
import { useAllianceCatalog } from '@/hooks/Users/useAllianceCatalog'
import { useAuthStore } from '@/store/auth'
import { isStaff } from '@/utils/rewardPermissions'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  mode: { type: String, default: 'create' }, // 'create' | 'edit'
  reward: { type: Object, default: null },
})

const emit = defineEmits(['update:modelValue', 'saved'])

const { loading, createReward, updateReward } = useRewardManagement()
const { alliances, loading: alliancesLoading, fetchAlliances } = useAllianceCatalog()
const authStore = useAuthStore()

const isEdit = computed(() => props.mode === 'edit')
const dialogTitle = computed(() => (isEdit.value ? 'Editar recompensa' : 'Crear recompensa'))

const staffUser = computed(() => isStaff(authStore.getUser()))
const ownAlliance = computed(() => authStore.getUser()?.alliance ?? null)

const form = ref({
  alliance_id: null,
  name: '',
  description: '',
  points_required: null,
  stock: null,
  is_exclusive: false,
  expires_at: '',
})

// Determina si la alianza en juego (la elegida por staff, o la propia del
// admin_merchant) tiene habilitadas las recompensas exclusivas — espejo de
// RewardService::assertExclusiveAllowed() para no dejar marcar el switch y
// que el backend lo rechace después.
const exclusiveAllowed = computed(() => {
  if (!staffUser.value) return Boolean(ownAlliance.value?.has_exclusive_rewards)

  return Boolean(alliances.value.find(item => item.id === form.value.alliance_id)?.has_exclusive_rewards)
})

watch(exclusiveAllowed, allowed => {
  if (!allowed) form.value.is_exclusive = false
})

const isValid = computed(() => (
  Boolean(form.value.name.trim())
  && Number(form.value.points_required) > 0
  && (!staffUser.value || Boolean(form.value.alliance_id))
))

function resetForm() {
  if (isEdit.value && props.reward) {
    form.value = {
      alliance_id: props.reward.alliance_id,
      name: props.reward.name,
      description: props.reward.description ?? '',
      points_required: props.reward.points_required,
      stock: props.reward.stock,
      is_exclusive: Boolean(props.reward.is_exclusive),
      expires_at: props.reward.expires_at ? props.reward.expires_at.slice(0, 10) : '',
    }
  } else {
    form.value = {
      alliance_id: null,
      name: '',
      description: '',
      points_required: null,
      stock: null,
      is_exclusive: false,
      expires_at: '',
    }
  }
}

watch(() => props.modelValue, open => {
  if (open) {
    resetForm()
    if (staffUser.value) fetchAlliances()
  }
})

async function submit() {
  const payload = {
    name: form.value.name,
    description: form.value.description || null,
    points_required: Number(form.value.points_required),
    stock: form.value.stock === '' || form.value.stock === null ? null : Number(form.value.stock),
    is_exclusive: form.value.is_exclusive,
    expires_at: form.value.expires_at || null,
  }

  if (staffUser.value) {
    payload.alliance_id = form.value.alliance_id
  }

  const result = isEdit.value
    ? await updateReward(props.reward.id, payload)
    : await createReward(payload)

  if (!result) return

  emit('saved')
  emit('update:modelValue', false)
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="560"
    persistent
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard :title="dialogTitle">
      <VCardText>
        <VForm @submit.prevent="submit">
          <VRow>
            <VCol
              v-if="staffUser"
              cols="12"
            >
              <VSelect
                v-model="form.alliance_id"
                :items="alliances"
                :loading="alliancesLoading"
                item-title="name"
                item-value="id"
                label="Alianza"
                required
              />
            </VCol>
            <VCol
              v-else
              cols="12"
            >
              <VTextField
                :model-value="ownAlliance?.name"
                label="Alianza"
                readonly
                hint="La recompensa se crea para tu propia alianza."
                persistent-hint
              />
            </VCol>
            <VCol cols="12">
              <VTextField
                v-model="form.name"
                label="Nombre de la recompensa"
                required
              />
            </VCol>
            <VCol cols="12">
              <VTextarea
                v-model="form.description"
                label="Descripción"
                rows="2"
              />
            </VCol>
            <VCol cols="6">
              <VTextField
                v-model.number="form.points_required"
                type="number"
                min="1"
                label="Puntos requeridos"
                required
              />
            </VCol>
            <VCol cols="6">
              <VTextField
                v-model.number="form.stock"
                type="number"
                min="0"
                label="Stock"
                hint="Vacío = ilimitado"
                persistent-hint
              />
            </VCol>
            <VCol cols="12">
              <VTextField
                v-model="form.expires_at"
                type="date"
                label="Fecha de vencimiento"
                hint="Opcional — sin fecha, la recompensa no vence"
                persistent-hint
              />
            </VCol>
            <VCol cols="12">
              <VSwitch
                v-model="form.is_exclusive"
                label="Exclusiva para miembros de la alianza"
                color="primary"
                :disabled="!exclusiveAllowed"
                hide-details
              />
              <p class="mt-1 mb-0 text-caption text-medium-emphasis">
                {{ exclusiveAllowed
                  ? 'Solo podrán canjearla los usuarios miembro enlazados a esta alianza.'
                  : 'Esta alianza no tiene habilitadas las recompensas exclusivas.' }}
              </p>
            </VCol>
            <VCol
              v-if="isEdit && !staffUser"
              cols="12"
            >
              <p class="mb-0 text-caption text-medium-emphasis">
                Editar una recompensa aprobada, pausada o rechazada la regresa a pendiente de revisión.
              </p>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn
          variant="tonal"
          @click="emit('update:modelValue', false)"
        >
          Cancelar
        </VBtn>
        <VBtn
          color="primary"
          :loading="loading"
          :disabled="!isValid"
          @click="submit"
        >
          {{ isEdit ? 'Guardar' : 'Crear' }}
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
