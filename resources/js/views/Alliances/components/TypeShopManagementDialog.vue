<script setup>
import { useTypeShopCatalog } from '@/hooks/TypeShop/useTypeShopCatalog'
import { useTypeShopManagement } from '@/hooks/TypeShop/useTypeShopManagement'
import { requestOrderTable } from '@/services/requests'
import { useDialogStore } from '@/store/useAlertDialogStorage'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
})

const emit = defineEmits(['update:modelValue'])

const { loading, createTypeShop, updateTypeShop, deleteTypeShop } = useTypeShopManagement()
const { invalidate: invalidateCatalog, fetchTypeShops: refetchCatalog } = useTypeShopCatalog()
const dialogStore = useDialogStore()

const {
  data,
  total,
  loading: listLoading,
  page,
  perPage,
  search,
  loadData,
} = requestOrderTable({
  url: 'type-shop',
  defaults: { page: 1, perPage: 5, search: '', sortBy: [{ key: 'id', order: 'asc' }] },
  config: { autoload: false },
})

const editingId = ref(null)
const form = ref({ name: '', is_active: true })
const isEditing = computed(() => editingId.value !== null)

watch(() => props.modelValue, open => {
  if (open) {
    resetForm()
    loadData()
  }
})

function resetForm() {
  editingId.value = null
  form.value = { name: '', is_active: true }
}

function startEdit(item) {
  editingId.value = item.id
  form.value = { name: item.name, is_active: Boolean(item.is_active) }
}

async function refreshEverywhere() {
  invalidateCatalog()
  await Promise.all([loadData(), refetchCatalog()])
}

async function submit() {
  if (!form.value.name.trim()) return

  const result = isEditing.value
    ? await updateTypeShop(editingId.value, form.value)
    : await createTypeShop(form.value)

  if (!result) return

  resetForm()
  await refreshEverywhere()
}

async function handleDelete(item) {
  const confirmed = await dialogStore.showDialog({
    title: 'Eliminar categoría',
    text: `Se eliminará la categoría "${item.name}" permanentemente. ¿Continuar?`,
    type: 'confirm',
    confirmText: 'Eliminar',
  })

  if (!confirmed) return

  const ok = await deleteTypeShop(item.id)
  if (!ok) return

  if (editingId.value === item.id) resetForm()
  await refreshEverywhere()
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="560"
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard title="Gestionar categorías">
      <VCardText>
        <p class="mb-4 text-body-2 text-medium-emphasis">
          Las categorías clasifican a las alianzas (ej. Supermercado, Farmacia) y alimentan el
          selector de categoría al crear o editar una alianza.
        </p>

        <VForm
          class="gap-3 d-flex align-center mb-4"
          @submit.prevent="submit"
        >
          <VTextField
            v-model="form.name"
            label="Nombre de la categoría"
            density="compact"
            hide-details
            class="flex-grow-1"
          />
          <VSwitch
            v-model="form.is_active"
            label="Activa"
            color="primary"
            density="compact"
            hide-details
          />
          <VBtn
            color="primary"
            :loading="loading"
            :disabled="!form.name.trim()"
            @click="submit"
          >
            {{ isEditing ? 'Guardar' : 'Agregar' }}
          </VBtn>
          <VBtn
            v-if="isEditing"
            variant="text"
            @click="resetForm"
          >
            Cancelar
          </VBtn>
        </VForm>

        <VTextField
          v-model="search"
          label="Buscar categoría"
          prepend-inner-icon="bx-search"
          density="compact"
          variant="outlined"
          rounded="lg"
          clearable
          class="mb-3"
        />

        <VList
          v-if="data.length"
          density="compact"
          class="border border-gray-200 rounded-lg dark:border-gray-700"
        >
          <VListItem
            v-for="item in data"
            :key="item.id"
          >
            <VListItemTitle>{{ item.name }}</VListItemTitle>
            <template #append>
              <VChip
                :color="item.is_active ? 'success' : 'secondary'"
                variant="tonal"
                size="small"
                class="me-2"
              >
                {{ item.is_active ? 'Activa' : 'Inactiva' }}
              </VChip>
              <VBtn
                icon
                variant="text"
                size="small"
                @click="startEdit(item)"
              >
                <VIcon
                  icon="bx-edit"
                  size="18"
                  class="text-primary"
                />
              </VBtn>
              <VBtn
                icon
                variant="text"
                size="small"
                @click="handleDelete(item)"
              >
                <VIcon
                  icon="bx-trash"
                  size="18"
                  class="text-error"
                />
              </VBtn>
            </template>
          </VListItem>
        </VList>
        <p
          v-else-if="!listLoading"
          class="text-center text-medium-emphasis py-6"
        >
          Todavía no hay categorías registradas.
        </p>

        <div
          v-if="total > perPage"
          class="d-flex justify-center mt-3"
        >
          <VPagination
            v-model="page"
            :length="Math.ceil(total / perPage)"
            density="compact"
          />
        </div>
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn
          variant="tonal"
          @click="emit('update:modelValue', false)"
        >
          Cerrar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
