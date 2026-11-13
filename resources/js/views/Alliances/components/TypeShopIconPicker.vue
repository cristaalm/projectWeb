<script setup>
import SvgMaskIcon from '@/components/SvgMaskIcon.vue'
import { useTypeShopIcons } from '@/hooks/TypeShop/useTypeShopIcons'
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: null },
})

const emit = defineEmits(['update:modelValue'])

const { icons, loading, fetchIcons } = useTypeShopIcons()

const open = ref(false)
const search = ref('')

onMounted(fetchIcons)

const selected = computed(() => icons.value.find(icon => icon.name === props.modelValue) ?? null)

const filtered = computed(() => {
  const term = search.value?.trim().toLowerCase()
  if (!term) return icons.value

  return icons.value.filter(icon => icon.label.toLowerCase().includes(term) || icon.name.includes(term))
})

function pick(icon) {
  emit('update:modelValue', icon.name)
  open.value = false
  search.value = ''
}
</script>

<template>
  <VMenu
    v-model="open"
    :close-on-content-click="false"
    location="bottom start"
  >
    <template #activator="{ props: activatorProps }">
      <VBtn
        v-bind="activatorProps"
        variant="tonal"
        color="primary"
        icon
        :loading="loading && !icons.length"
        :title="selected ? `Icono: ${selected.label}` : 'Elegir icono'"
        aria-label="Elegir icono de la categoría"
      >
        <SvgMaskIcon
          :url="selected?.url"
          :size="22"
        />
      </VBtn>
    </template>

    <VCard
      width="340"
      class="pa-3"
    >
      <VTextField
        v-model="search"
        placeholder="Buscar icono"
        prepend-inner-icon="bx-search"
        density="compact"
        hide-details
        clearable
        autofocus
        class="mb-3"
      />
      <div class="icon-grid">
        <button
          v-for="icon in filtered"
          :key="icon.name"
          type="button"
          class="icon-option"
          :class="{ 'icon-option--active': icon.name === modelValue }"
          :title="icon.label"
          @click="pick(icon)"
        >
          <SvgMaskIcon
            :url="icon.url"
            :size="22"
          />
          <span class="icon-option__label">{{ icon.label }}</span>
        </button>
      </div>
      <p
        v-if="!filtered.length"
        class="mb-0 py-4 text-center text-body-2 text-medium-emphasis"
      >
        Ningún icono coincide con la búsqueda.
      </p>
    </VCard>
  </VMenu>
</template>

<style scoped>
.icon-grid {
  display: grid;
  gap: 4px;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  max-block-size: 280px;
  overflow-x: hidden;
  overflow-y: auto;
}

.icon-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  border: 1px solid transparent;
  border-radius: 8px;
  background: transparent;
  color: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity));
  cursor: pointer;
  gap: 4px;
  min-inline-size: 0;
  padding-block: 8px;
  padding-inline: 4px;
}

.icon-option:hover {
  background: rgba(var(--v-theme-on-surface), var(--v-hover-opacity));
}

.icon-option--active {
  border-color: rgb(var(--v-theme-primary));
  background: rgba(var(--v-theme-primary), 0.12);
  color: rgb(var(--v-theme-primary));
}

.icon-option__label {
  overflow: hidden;
  font-size: 0.6875rem;
  inline-size: 100%;
  line-height: 1.2;
  text-align: center;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
