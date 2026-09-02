<script setup>
defineProps({
  name: { type: String, required: true },
  lastName: { type: String, required: true },
  loading: { type: Boolean, default: false },
  canSubmit: { type: Boolean, default: false },
})

const emit = defineEmits(['update:name', 'update:lastName', 'submit'])
</script>

<template>
  <VCard title="Información personal">
    <VCardText>
      <VForm @submit.prevent="emit('submit')">
        <VRow>
          <VCol
            cols="12"
            md="6"
          >
            <VTextField
              :model-value="name"
              label="Nombre"
              required
              @update:model-value="v => emit('update:name', v)"
            />
          </VCol>
          <VCol
            cols="12"
            md="6"
          >
            <VTextField
              :model-value="lastName"
              label="Apellido"
              required
              @update:model-value="v => emit('update:lastName', v)"
            />
          </VCol>
          <VCol cols="12">
            <VBtn
              type="submit"
              color="primary"
              :loading="loading"
              :disabled="!canSubmit"
            >
              Guardar
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
    </VCardText>
  </VCard>
</template>
