<script setup>
const props = defineProps({
  modelValue: { type: Boolean, required: true },
  codes: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue'])

function close() {
  emit('update:modelValue', false)
}

function copy() {
  navigator.clipboard.writeText(props.codes.join('\n'))
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="480"
    persistent
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard title="Guarda tus códigos de recuperación">
      <VCardText>
        <p class="mb-4">
          Guárdalos en un lugar seguro — no se van a volver a mostrar. Cada uno se puede usar una sola vez si pierdes acceso a tu app de autenticación.
        </p>
        <VSheet
          class="pa-4 mb-4"
          rounded
          color="grey-100"
        >
          <div
            v-for="code in codes"
            :key="code"
            class="font-weight-medium text-on-surface"
          >
            {{ code }}
          </div>
        </VSheet>
        <VBtn
          color="primary"
          variant="tonal"
          @click="copy"
        >
          Copiar
        </VBtn>
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn
          color="primary"
          @click="close"
        >
          Ya los guardé
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
