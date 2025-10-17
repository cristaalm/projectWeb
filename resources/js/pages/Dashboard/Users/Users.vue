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
import { ModalCreate, useModalCreate } from './modules/ModalCreate'
import { ModalModifyPoints, useModalModifyPoints } from './modules/ModalModifyPoints'

const authStore = useAuthStore()
const darkModeStore = useDarkModeStore()

const verification_status = ref(null)
const role = ref(null)

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
} = requestOrderTable({ url: 'users/getAll', params: { verification_status, tipo: role } })

const countFilters = computed(() => {
  let count = 0
  if (status.value !== null) count++
  if (verification_status.value !== null) count++
  if (role.value !== null) count++
  
  return count
})

const resetFilters = () => {
  status.value = null
  verification_status.value = null
  role.value = null
}

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
const { showCreateModal, openCreateModal } = useModalCreate()
const { showModifyPointsModal, openModifyPointsModal, selectedUserToModifyPoints } = useModalModifyPoints()
</script>

<template>
  <ModalModifyPoints
    v-model="showModifyPointsModal"
    :data="selectedUserToModifyPoints"
    @modify-points="loadData"
  />

  <ModalCreate
    v-model="showCreateModal"
    @create="loadData"
  />

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
        <VBtn
          class="disabled:opacity-50"
          :color="darkModeStore.darkMode ? '#136b80' : 'primary'"
          :disabled="authStore.user.role.id != 2"
          variant="elevated"
          prepend-icon="bx-plus"
          @click="openCreateModal"
        >
          Agregar Usuario
        </VBtn>
      </div>
    </div>

    <div class="col-span-12 p-2 md:p-6 bg-white dark:bg-[#2b2c40] flex flex-col md:flex-row justify-between items-center gap-6 shadow-lg">
      <VTextField
        v-model="search"
        label="Buscar"
        placeholder="Buscar usuario"
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
              v-model="verification_status"
              label="Verificación"
              :items="[{ title: 'Todos', value: null }, { title: 'Pendiente', value: 0 }, { title: 'Verificado', value: 1 }, { title: 'Rechazado', value: 2 }, { title: 'Sin Documentos', value: 3 }]"
              placeholder="Verificación"
              prepend-inner-icon="bx-filter"
              :color="darkModeStore.darkMode ? 'white' : 'primary'"
              class="mt-0"
              variant="outlined"
              no-data-text="No opciones de filtrado"
            />
            <VSelect
              v-model="role"
              label="Tipo"
              :items="[{ title: 'Todos', value: null }, { title: 'Usuario', value: 1 }, { title: 'Administrador', value: 2 }, { title: 'Moderador', value: 3 }, { title: 'Comerciante', value: 4 }]"
              placeholder="Tipo"
              prepend-inner-icon="bx-filter"
              :color="darkModeStore.darkMode ? 'white' : 'primary'"
              class="mt-0"
              variant="outlined"
              no-data-text="No opciones de filtrado"
            />
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
              :disabled="status == null && role == null && verification_status == null"
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
          <span class="font-bold">{{ item.name }} {{ item.last_name }}</span>
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
          v-if="item.id != authStore.user.id && item.role_id != 2 && item.role_id != authStore.user?.role?.id"
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
              v-if="item.role_id != 4"
              @click="openModifyPointsModal(item)"
            >
              <template #prepend>
                <VIcon
                  icon="mdi mdi-swap-horizontal"
                  class="me-2 text-amber-500"
                />
              </template>
              <VListItemTitle class="text-amber-500">
                Modificar Puntos
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
