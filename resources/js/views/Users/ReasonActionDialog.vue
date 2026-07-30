<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  title: { type: String, required: true },
  actionLabel: { type: String, required: true },
  color: { type: String, default: 'primary' },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue', 'confirm'])

const reason = ref('')

const isValid = computed(() => reason.value.trim().length > 0)

watch(() => props.modelValue, open => {
  if (open) reason.value = ''
})

function confirm() {
  emit('confirm', reason.value)
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="480"
    persistent
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard :title="title">
      <VCardText>
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
          :color="color"
          :loading="loading"
          :disabled="!isValid"
          @click="confirm"
        >
          {{ actionLabel }}
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
