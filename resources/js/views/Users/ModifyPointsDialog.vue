<script setup>
import { useUserManagement } from '@/hooks/Users/useUserManagement'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  user: { type: Object, default: null },
})

const emit = defineEmits(['update:modelValue', 'updated'])

const { loading, modifyPoints } = useUserManagement()

const points = ref(null)
const reason = ref('')

const isValid = computed(() => Number.isInteger(points.value) && points.value !== 0 && reason.value.trim().length > 0)

watch(() => props.modelValue, open => {
  if (open) {
    points.value = null
    reason.value = ''
  }
})

async function submit() {
  const ok = await modifyPoints(props.user.id, { points: points.value, reason: reason.value })
  if (!ok) return

  emit('updated')
  emit('update:modelValue', false)
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="480"
    persistent
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard title="Modificar puntos">
      <VCardText>
        <p class="mb-4">
          Saldo actual de <strong>{{ user?.name }} {{ user?.last_name }}</strong>:
          {{ user?.points_balance ?? 0 }} puntos.
        </p>
        <VTextField
          v-model.number="points"
          type="number"
          label="Ajuste (positivo o negativo)"
          class="mb-4"
        />
        <VTextarea
          v-model="reason"
          label="Motivo"
          required
        />
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
          Aplicar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
