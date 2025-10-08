<script setup>
import OrderTable from '@/components/orderTable/'
import { requestOrderTable } from '@/services/requests'
import { useDarkModeStore } from '@/store/dark-mode'
import { storageURL } from '@/utils/constants'
import { format } from 'date-fns'
import { useAuthStore } from '@/store/auth'

import { ModalToggleStatus, useModalToggleStatus } from './modules/ModalToggleStatus'
import { ModalResetPass, useModalResetPass } from './modules/ModalResetPass'
import { ModalVerifyDocs, useModalVerifyDocs } from './modules/ModalVerifyDocs'

const authStore = useAuthStore()
const darkModeStore = useDarkModeStore()

const {
  data: users,
  total,
  loading,
  page,
  perPage,
  sortBy,
  search,
  status,
  loadData,
} = requestOrderTable({ url: 'users/getAll' })

const headers = [
  { title: '', align: 'left', key: 'avatar', sortable: false },
  { title: 'Nombre', align: 'left', key: 'name' },
  { title: 'Email', align: 'start', key: 'email' },
  { title: 'Puntos', align: 'start', key: 'total_points' },
  { title: 'Verificación', align: 'center', key: 'verification_status', sortable: false },
  { title: 'Tipo', align: 'start', key: 'role.display_name', sortable: false },
  { title: 'Estado', key: 'status', align: 'center', sortable: false },
  { title: 'Registro', key: 'created_at', align: 'center' },
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

const { showToggleStatusModal, openToggleStatusModal, selectedUserToToggleStatus } = useModalToggleStatus()
const { showResetPassModal, openResetPassModal, selectedUserToResetPass } = useModalResetPass()
const { showVerifyDocsModal, openVerifyDocsModal, selectedUserToVerifyDocs } = useModalVerifyDocs()
</script>

<template>
  <ModalToggleStatus
    v-model="showToggleStatusModal"
    :data="selectedUserToToggleStatus"
    @toggle-status="loadData"
  />

  <ModalResetPass
    v-model="showResetPassModal"
    :data="selectedUserToResetPass"
    @reset-pass="loadData"
  />

  <ModalVerifyDocs
    v-model="showVerifyDocsModal"
    :data="selectedUserToVerifyDocs"
    @verify-docs="loadData"
  />

  <div class="grid grid-cols-12 gap-6 p-0">
    <div class="col-span-12 flex flex-col md:flex-row items-center justify-between gap-4 p-4">
      <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-bold dark:!text-[#136b80] text-primary flex flex-row justify-start items-center gap-2">
          <VIcon
            icon="bx-user"
            size="small"
          /> 
          Usuarios Registrados
        </h2>
        <p class="text-sm dark:text-gray-400 text-gray-500">
          Administra los usuarios registrados en el sistema.
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
      </div>
    </div>

    <div class="col-span-12 p-2 md:p-6 space-y-6 bg-white dark:bg-[#2b2c40] flex flex-col md:flex-row justify-between items-center gap-6 shadow-lg">
      <VTextField
        v-model="search"
        label="Buscar"
        placeholder="Buscar usuario"
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
      :items="users"
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
      <template #item.avatar="{ item }">
        <VImg
          v-if="item.avatar"
          :src="storageURL + item.avatar"
          width="50"
          height="50"
          class="rounded-full cursor-pointer my-2"
        />
        <VIcon
          v-else
          icon="bx-user"
          class="rounded-full cursor-pointer my-2"
        />
      </template>
    
      <template #item.name="{ item }">
        <div class="flex flex-col">
          <span class="font-bold">{{ item.name }}</span>
          <span class="text-gray-400">ID: {{ item.id }}</span>
        </div>
      </template>

      <template #item.email="{ item }">
        <div class="flex flex-col items-start text-nowrap">
          <span class="font-bold">{{ item.email }}</span>
          <span class="text-gray-400 truncate max-w-xs">
            <VIcon
              icon="bx-phone"
              size="small"
            />
            {{ formatPhone(item.phone) }}
          </span>
        </div>
      </template>

      <template #item.total_points="{ item }">
        <div class="truncate max-w-xs">
          {{ item.total_points }}
        </div>
      </template>

      <template #item.verification_status="{ item }">
        <VChip :color="item.verification_status == 0 ? 'warning' : item.verification_status == 1 ? 'success' : item.verification_status == 2 ? 'error' : 'secondary'">
          {{ item.verification_status == 0 ? 'Pendiente' : item.verification_status == 1 ? 'Verificado' : item.verification_status == 2 ? 'Rechazado' : 'Sin documentos' }}
        </VChip>
      </template>

      <template #item.status="{ item }">
        <VChip :color="item.status == 0 ? 'error' : 'success'">
          {{ item.status == 0 ? 'Inactivo' : 'Activo' }}
        </VChip>
      </template>

      <template #item.created_at="{ item }">
        {{ format(new Date(item.created_at), 'dd/MM/yyyy') }}
      </template>

      <template #item.actions="{ item }">
        <VMenu
          v-if="item.id != authStore.user.id"
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
            <VListItem @click="openResetPassModal(item)">
              <template #prepend>
                <VIcon
                  icon="bx-refresh"
                  class="me-2 text-blue-500"
                />
              </template>
              <VListItemTitle class="text-blue-500">
                Restablecer contraseña
              </VListItemTitle>
            </VListItem>
            <VDivider />
            <VListItem 
              v-if="item.verification_status == 0"
              @click="openVerifyDocsModal(item)"
            >
              <template #prepend>
                <VIcon
                  icon="bx-user-check"
                  class="me-2 text-green-500"
                />
              </template>
              <VListItemTitle class="text-green-500">
                Verificar Identidad
              </VListItemTitle>
            </VListItem>
            <VDivider />
            <VListItem 
              v-if="item.status == 1"
              @click="openToggleStatusModal({ user: item, status: 0 })"
            >
              <template #prepend>
                <VIcon
                  icon="bx-lock"
                  class="text-red-500 me-2"
                />
              </template>
              <VListItemTitle class="text-red-500">
                Desactivar cuenta
              </VListItemTitle>
            </VListItem>
            <VDivider />
            <VListItem 
              v-if="item.status == 0"
              @click="openToggleStatusModal({ user: item, status: 1 })"
            >
              <template #prepend>
                <VIcon
                  icon="bx-lock-open"
                  class="text-green-500 me-2"
                />
              </template>
              <VListItemTitle class="text-green-500">
                Activar cuenta
              </VListItemTitle>
            </VListItem>
          </VList>
        </VMenu>
        <VIcon
          v-else
          icon="bx-lock"
          class="text-red-500 me-2"
        />
      </template> 
    </OrderTable>
  </div>
</template>
