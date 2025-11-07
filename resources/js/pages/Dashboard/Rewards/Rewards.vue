<script setup>
import OrderTable from '@/components/orderTable/'
import { requestOrderTable } from '@/services/requests'
import { useDarkModeStore } from '@/store/dark-mode'
import { format } from 'date-fns'

import { ModalCreate, useModalCreate } from './modules/ModalCreate'
import { ModalUpdate, useModalUpdate } from './modules/ModalUpdate'
import { ModalDelete, useModalDelete } from './modules/ModalDelete'

const darkModeStore = useDarkModeStore()

const {
  data: rewards,
  total,
  loading,
  page,
  perPage,
  sortBy,
  search,
  status,
  loadData,
} = requestOrderTable({ url: 'reward/getAll' })

const countFilters = computed(() => {
  let count = 0
  if (status.value !== null) count++
  
  return count
})

const resetFilters = () => {
  status.value = null
}

const headers = [
  { title: '', align: 'left', key: 'logo', sortable: false },
  { title: 'Nombre', align: 'left', key: 'name' },
  { title: 'Descripción', align: 'left', key: 'description' },
  { title: 'Comercio', key: 'alliance.name', align: 'left' },
  { title: 'Puntos Requeridos', align: 'left', key: 'points_required' },
  { title: 'Stock', align: 'left', key: 'stock' },
  { title: 'Activo', align: 'center', key: 'is_active', sortable: false },
  { title: 'Expiración', key: 'expires_at', align: 'center', sortable: false },
  { title: '', key: 'actions', sortable: false, align: 'end' },
]

const { showCreateModal, openCreateModal } = useModalCreate()
const { showUpdateModal, openUpdateModal, selectedRewardToUpdate } = useModalUpdate()
const { showDeleteModal, openDeleteModal, selectedRewardToDelete } = useModalDelete()
</script>

<template>
  <ModalCreate
    v-model="showCreateModal"
    @create="loadData"
  />

  <ModalUpdate
    v-model="showUpdateModal"
    :data="selectedRewardToUpdate"
    @update="loadData"
  />

  <ModalDelete
    v-model="showDeleteModal"
    :data="selectedRewardToDelete"
    @delete="loadData"
  />

  <div class="grid grid-cols-12 gap-6 p-0">
    <div class="col-span-12 flex flex-col md:flex-row items-center justify-between gap-4 p-4">
      <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-bold dark:!text-[#136b80] text-primary flex flex-row justify-start items-center gap-2">
          <VIcon
            icon="bx-gift"
            size="small"
          /> 
          Recompensas
        </h2>
        <p class="text-sm dark:text-gray-400 text-gray-500">
          Administra las recompensas de cada comercio.
        </p>
      </div>
      <div class="flex flex-row items-center justify-end gap-2">
        <VBtn
          color="secondary"
          variant="outlined"
          prepend-icon="bx-refresh"
          @click="loadData"
        >
          Actualizar
        </VBtn>
        <VBtn
          :color="darkModeStore.darkMode ? '#136b80' : 'primary'"
          variant="elevated"
          prepend-icon="bx-plus"
          @click="openCreateModal"
        >
          Agregar Recompensa
        </VBtn>
      </div>
    </div>

    <div class="col-span-12 p-2 md:p-6 bg-white dark:bg-[#2b2c40] flex flex-col md:flex-row justify-between items-center gap-6 shadow-lg">
      <VTextField
        v-model="search"
        label="Buscar"
        placeholder="Buscar recompensa"
        prepend-inner-icon="bx-search"
        :color="darkModeStore.darkMode ? 'white' : 'primary'"
        class="w-full md:!flex-[.5]"
        variant="outlined"
      />
      <VMenu
        location="bottom end"
        offset="10"
        :close-on-content-click="false"
      >
        <template #activator="{ props }">
          <VBtn
            v-bind="props"
            variant="outlined"
            color="secondary"
            class="w-full sm:w-auto relative"
          >
            <VBadge
              v-if="countFilters > 0"
              :content="countFilters"
              color="primary"
              class="absolute top-0 right-0"
            />
            <VIcon
              icon="bx-filter"
              start
            />
            Filtrar
          </VBtn>
        </template>

        <VCard
          class="pa-4 relative"
          width="250"
        >
          <div class="flex flex-col gap-y-2">
            <VSelect
              v-model="status"
              label="Estado"
              :items="[{ title: 'Todos', value: null }, { title: 'Activo', value: 1 }, { title: 'Inactivo', value: 0 }]"
              placeholder="Estado"
              prepend-inner-icon="bx-filter"
              :color="darkModeStore.darkMode ? 'white' : 'primary'"
              class="mt-0"
              variant="outlined"
              no-data-text="No opciones de filtrado"
            />
            <VBtn
              variant="outlined"
              color="secondary"
              class="w-full"
              prepend-icon="mdi mdi-refresh"
              :disabled="status == null"
              @click="resetFilters"
            >
              Reinciar
            </VBtn>
          </div>
        </VCard>
      </VMenu>
    </div>

    <OrderTable
      :headers="headers"
      :items="rewards"
      :loading="loading"
      :page="page"
      :per-page="perPage"
      :total="total"
      :sort-by="sortBy"
      without-search
      class="col-span-12"
      @update:page="page = $event"
      @update:per-page="perPage = $event"
      @update:sort-by="sortBy = $event"
    >
      <template #item.points_required="{ item }">
        <div class="flex flex-row items-center gap-2">
          <div class="w-2 h-2 rounded-full bg-primary dark:bg-[#05D16E]" />
          {{ new Intl.NumberFormat('es-MX', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(item.points_required) }}
        </div>
      </template>

      <template #item.stock="{ item }">
        {{ item.stock == null ? 'Ilimitado' : item.stock }}
      </template>

      <template #item.is_active="{ item }">
        <VChip :color="item.is_active ? 'success' : 'error'">
          {{ item.is_active ? 'Activo' : 'Inactivo' }}
        </VChip>
      </template>

      <template #item.expires_at="{ item }">
        {{ item.expires_at ? format(new Date(item.expires_at), 'dd/MM/yyyy') : 'Indefinido' }}
      </template>

      <template #item.actions="{ item }">
        <VMenu
          offset="10"
          location="bottom end"
          width="250"
        >
          <template #activator="{ props }">
            <VBtn
              icon
              v-bind="props"
              variant="plain"
              class="!text-gray-500 hover:!text-gray-800 dark:hover:text-white"
            >
              <VIcon
                icon="bx-dots-vertical-rounded"
                class="dark:hover:text-white dark:text-white text-black"
              />
            </VBtn>
          </template>
          <VList>
            <VListItem @click="openUpdateModal(item)">
              <template #prepend>
                <VIcon
                  icon="bx-edit"
                  class="me-2 text-blue-500"
                />
              </template>
              <VListItemTitle class="text-blue-500">
                Editar
              </VListItemTitle>
            </VListItem>
            <VDivider />
            <VListItem @click="openDeleteModal(item)">
              <template #prepend>
                <VIcon
                  icon="bx-trash"
                  class="text-red-500 me-2"
                />
              </template>
              <VListItemTitle class="text-red-500">
                Eliminar
              </VListItemTitle>
            </VListItem>
          </VList>
        </VMenu>
      </template> 
    </OrderTable>
  </div>
</template>
