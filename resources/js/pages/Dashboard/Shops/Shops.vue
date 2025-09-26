<script setup>
import OrderTable from '@/components/orderTable/'
import { requestOrderTable } from '@/services/requests'
import { useDarkModeStore } from '@/store/dark-mode'
import logo_placeholder from '@images/placeholders/shop.jpg?url'
import { format } from 'date-fns'
import { IMask } from 'vue-imask'

import { ModalCreate, useModalCreate } from './modules/ModalCreate'
import { ModalDelete, useModalDelete } from './modules/ModalDelete'
import { ModalLogo, useModalLogo } from './modules/ModalLogo'
import { ModalUpdate, useModalUpdate } from './modules/ModalUpdate'

const darkModeStore = useDarkModeStore()

const {
  data: alliances,
  total,
  loading,
  page,
  perPage,
  sortBy,
  search,
  status,
  loadData,
} = requestOrderTable({ url: 'alianzas/getAll' })

const headers = [
  { title: '', align: 'left', key: 'logo' },
  { title: 'Nombre', align: 'left', key: 'name' },
  { title: 'Contacto', align: 'start', key: 'contact_name' },
  { title: 'Télefono', align: 'start', key: 'phone', sortable: false },
  { title: 'Dirección', align: 'start', key: 'address' },
  { title: 'Tipo', align: 'start', key: 'type_shop.name' },
  { title: 'Estado', key: 'status', align: 'center', sortable: false },
  { title: 'Registro', key: 'created_at', align: 'center', sortable: true },
  { title: '', key: 'actions', sortable: false, align: 'end' },
]

function formatPhone(phone) {
  const mask = IMask.createMask({
    mask: '(000) 000-0000',
  })

  if (phone.length === 0) return
  mask.resolve(phone)

  return mask.value
}

const { showCreateModal, openCreateModal } = useModalCreate()
const { showUpdateModal, openUpdateModal, selectedShopToUpdate } = useModalUpdate()
const { showDeleteModal, openDeleteModal, selectedShopToDelete } = useModalDelete()
const { showLogoModal, openLogoModal, selectedShopForLogo } = useModalLogo()
</script>

<template>
  <ModalCreate 
    v-model="showCreateModal"
    @create="loadData"
  />

  <ModalUpdate 
    v-model="showUpdateModal"
    :data="selectedShopToUpdate"
    @update="loadData"
  />

  <ModalDelete 
    v-model="showDeleteModal"
    :data="selectedShopToDelete"
    @delete="loadData"
  />
  
  <ModalLogo 
    v-model="showLogoModal"
    :data="selectedShopForLogo"
    @update="loadData"
  />

  <div class="grid grid-cols-12 gap-6 p-0">
    <div class="col-span-12 flex flex-col md:flex-row items-center justify-between gap-4 p-4">
      <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-bold dark:!text-[#136b80] text-primary flex flex-row justify-start items-center gap-2">
          <VIcon
            icon="bx-store"
            size="small"
          /> 
          Comercios Aliados
        </h2>
        <p class="text-sm dark:text-gray-400 text-gray-500">
          Administra los comercios y alianzas registrados en el sistema.
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
          Agregar Comercio
        </VBtn>
      </div>
    </div>

    <div class="col-span-12 p-2 md:p-6 space-y-6 bg-white dark:bg-[#2b2c40] flex flex-col md:flex-row justify-between items-center gap-6 shadow-lg">
      <VTextField
        v-model="search"
        label="Buscar"
        placeholder="Buscar comercio"
        prepend-inner-icon="bx-search"
        :color="darkModeStore.darkMode ? 'white' : 'primary'"
        class="w-full md:!flex-[.5]"
        variant="outlined"
      />
      <VSelect
        v-model="status"
        label="Filtrar por"
        :items="[{ title: 'Todos', value: null }, { title: 'Activo', value: 1 }, { title: 'Inactivo', value: 0 }]"
        placeholder="Filtrar por"
        prepend-inner-icon="bx-filter"
        :color="darkModeStore.darkMode ? 'white' : 'primary'"
        class="w-full md:!flex-[.5] lg:!flex-[.2] mt-0"
        variant="outlined"
        no-data-text="No opciones de filtrado"
      />
    </div>

    <OrderTable
      :headers="headers"
      :items="alliances"
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
      <template #item.logo="{ item }">
        <VImg
          :src="item.logo ? `/storage/alliances/${item.id}/logo.${item.ext}` : logo_placeholder"
          width="50"
          height="50"
          class="rounded-full cursor-pointer my-2"
          @click="openLogoModal(item)"
        />
      </template>
    
      <template #item.name="{ item }">
        <div class="flex flex-col">
          <span class="font-bold">{{ item.name }}</span>
          <span class="text-gray-400">ID: {{ item.id }}</span>
        </div>
      </template>

      <template #item.contact_name="{ item }">
        <div class="flex flex-col items-start text-nowrap">
          <span class="font-bold">{{ item.contact_name }}</span>
          <span class="text-gray-400 truncate max-w-xs">
            <VIcon
              icon="bx-envelope"
              size="small"
            />
            {{ item.contact_email }}
          </span>
        </div>
      </template>


      <template #item.phone="{ item }">
        <div class="flex flex-row items-start">
          <VIcon
            icon="bx-phone"
            size="small"
          /> 
          {{ formatPhone(item.phone) }}
        </div>
      </template>


      <template #item.address="{ item }">
        <div class="truncate max-w-xs">
          {{ item.address }}
        </div>
      </template>

      <template #item.status="{ item }">
        <VChip :color="item.status ? 'success' : 'error'">
          {{ item.status ? 'Activo' : 'Inactivo' }}
        </VChip>
      </template>

      <template #item.created_at="{ item }">
        {{ format(new Date(item.created_at), 'dd/MM/yyyy') }}
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
            <VListItem @click="openLogoModal(item)">
              <template #prepend>
                <VIcon
                  icon="bx-image"
                  class="me-2 text-green-500"
                />
              </template>
              <VListItemTitle class="text-green-500">
                Cambiar imagen
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
