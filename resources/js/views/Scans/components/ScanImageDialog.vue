<script setup>
defineProps({
  modelValue: { type: Boolean, required: true },
  scan: { type: Object, default: null },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="640"
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard :title="`Imagen del escaneo #${scan?.id ?? ''}`">
      <VCardText>
        <VImg
          v-if="scan?.image_url"
          :src="scan.image_url"
          max-height="480"
          class="rounded-lg"
        />
        <p class="mt-4 mb-0 text-body-2 text-medium-emphasis">
          {{ scan?.material?.name }} · {{ scan?.container?.name }}
        </p>
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn
          variant="tonal"
          color="secondary"
          @click="emit('update:modelValue', false)"
        >
          Cerrar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
