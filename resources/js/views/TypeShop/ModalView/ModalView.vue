<script setup>
import OrderTable from '@/components/orderTable/'
import { requestOrderTable } from '@/services/requests'
import { useDarkModeStore } from '@/store/dark-mode'
import { ref, watch } from 'vue'
import { ModalCreate, useModalCreate } from '../ModalCreate'
import { ModalDelete, useModalDelete } from '../ModalDelete'

const props = defineProps({
  modelValue: Boolean,
})

const emit = defineEmits(['update:modelValue', 'change'])

const {
  data: typeShops,
  total,
  loading,
  sortBy,
  search,
  loadData,
} = requestOrderTable({ url: 'typeShop/getAll', defaults: { page: 1, perPage: 1000 }, config: { autoload: false } })

const darkModeStore = useDarkModeStore()

const isOpen = ref(props.modelValue)

const changes = ref(false)

const headers = [
  { title: 'Categoria', align: 'left', key: 'name' },
  { title: '', key: 'actions', sortable: false, align: 'end' },
]


watch(() => props.modelValue, val => {
  isOpen.value = val
  if (val) {
    loadData()
  }
  emit('change', changes.value)
})

watch(isOpen, val => {
  emit('update:modelValue', val)
})

const { showCreateModal, openCreateModal } = useModalCreate()
const { showDeleteModal, selectedTypeShopToDelete, openDeleteModal } = useModalDelete()
</script>

<template>
  <ModalCreate 
    v-model="showCreateModal"
    @create="(e) => {changes = true; loadData()}"
  />
  <ModalDelete 
    v-model="showDeleteModal"
    :data="selectedTypeShopToDelete"
    @delete="(e) => {changes = true; loadData()}"
  />
  <VDialog
    v-model="isOpen"
    max-width="500px"
    persistent
  >
    <VCard>
      <VCardTitle class="text-xl font-semibold !flex !flex-row items-center !justify-between">
        Tipos de Comercios
        <VBtn
          variant="flat"
          color="primary"
          size="small"
          @click="openCreateModal"
        >
          <VIcon
            icon="bx-plus"
            size="20"
          />
        </VBtn>
      </VCardTitle>

      <OrderTable
        :headers="headers"
        :items="typeShops"
        :loading="loading"
        :search="search"
        :page="1"
        :per-page="1000"
        :total="total"
        :sort-by="sortBy"
        :view-pagination="false"
        class="px-2 h-[calc(100vh-400px)] overflow-y-auto"
        @update:sort-by="sortBy = $event"
        @update:search="search = $event"
      >
        <template #item.actions="{ item }">
          <VBtn
            variant="flat"
            color="error"
            size="small"
            @click="openDeleteModal(item)"
          >
            <VIcon icon="bx-trash" />
          </VBtn>
        </template>
      </OrderTable>

      <VDivider />
      <VCardActions class="justify-end mt-2 space-x-2">
        <VBtn
          variant="flat"
          color="primary"
          :disabled="loading"
          @click="isOpen = false"
        >
          Cerrar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
