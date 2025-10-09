<script setup>
import ViewPDF from 'pdf-vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(props.modelValue)

watch(() => props.modelValue, val => {
  isOpen.value = val
})

watch(isOpen, val => {
  emit('update:modelValue', val)
})
</script>

<template>
  <VDialog
    v-model="isOpen"
    max-width="800px"
  >
    <VCard class="overflow-hidden p-2">
      <VCardTitle class="text-xl font-semibold !flex !flex-row !justify-between !items-center">
        Condiciones y términos de uso
        <div class="!flex !items-center !gap-2">
          <VBtn
            icon
            variant="text"
            color="error"
            title="Cerrar"
            @click.stop="isOpen = false"
          >
            <VIcon
              icon="bx-x"
              size="32"
            />
          </VBtn>
        </div>
      </VCardTitle>

      <VDivider />

      <VCardText class="!p-0 max-h-none">
        <ViewPDF
          src="/documents/Condiciones y terminos de uso.pdf"
          class="[&_div.pdf-vue3-scroller]:max-h-[730px!important]"
          @on-progress="(e) => console.log(e)"
        />
      </VCardText>
    </VCard>
  </VDialog>
</template>
