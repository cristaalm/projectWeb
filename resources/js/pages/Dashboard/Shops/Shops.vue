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

import { jsPDF } from 'jspdf'
import { autoTable } from 'jspdf-autotable'
import { useAllianceMetrics } from '@/hooks/Shops/useAllianceMetrics'

const darkModeStore = useDarkModeStore()
const { getMetrics } = useAllianceMetrics()

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

  if (!phone || phone.length === 0) return 'N/A'
  mask.resolve(phone)

  return mask.value
}

const { showCreateModal, openCreateModal } = useModalCreate()
const { showUpdateModal, openUpdateModal, selectedShopToUpdate } = useModalUpdate()
const { showDeleteModal, openDeleteModal, selectedShopToDelete } = useModalDelete()
const { showLogoModal, openLogoModal, selectedShopForLogo } = useModalLogo()

const exportToPDF = async item => {
  try {
    const metrics = await getMetrics(item.id)
    const doc = new jsPDF()

    const renovaPrimary = [5, 209, 110]   // #05d16e (Verde)
    const renovaSecondary = [2, 70, 83]  // #024653 (Azul oscuro)
    const renovaTertiary = [205, 255, 16] // #cdff10 (Verde limón)
    const bodyTextColor = [50, 50, 50] // Gris oscuro para texto general
    const lightGray = [240, 240, 240] // Gris muy claro para fondos alternos

    let yPos = 0
    const pageHeight = doc.internal.pageSize.getHeight()
    const pageWidth = doc.internal.pageSize.getWidth()
    const margin = 14
    const tableWidth = pageWidth - margin * 2

    const addSectionHeader = (title, y) => {
      doc.setFontSize(14)
      doc.setTextColor(...renovaPrimary) 
      doc.setFont(undefined, 'bold')
      doc.text(title, margin, y)
      y += 2
      doc.setDrawColor(200, 200, 200) 
      doc.setLineWidth(0.5)
      doc.line(margin, y, pageWidth - margin, y) 
      doc.setFont(undefined, 'normal')

      return y + 8
    }

    // --- Encabezado ---
    const headerHeight = 35 

    doc.setFillColor(...renovaPrimary) 
    doc.rect(0, 0, pageWidth, headerHeight, 'F')

    const logoWidth = 25
    const logoHeight = 25
    const logoMargin = 15
    const logoX = pageWidth - logoWidth - logoMargin
    const logoY = (headerHeight - logoHeight) / 2 

    doc.addImage('/images/logoDark.png', 'PNG', logoX, logoY, logoWidth, logoHeight)

    const titleMaxWidth = pageWidth - (logoWidth + logoMargin + 20) * 2 

    doc.setTextColor(255, 255, 255) 

    // Título Principal
    doc.setFontSize(20) 
    doc.setFont(undefined, 'bold')
    doc.text('REPORTE DE COMERCIO', pageWidth / 2, 8, { 
      align: 'center',
      baseline: 'top', 
      maxWidth: titleMaxWidth, 
    })

    doc.setFontSize(14)
    doc.setFont(undefined, 'normal')
    doc.text(item.name, pageWidth / 2, 18, { 
      align: 'center', 
      baseline: 'top', 
      maxWidth: titleMaxWidth, 
    })

    doc.setFontSize(12) 
    doc.text(item.contact_name, pageWidth / 2, 24, { 
      align: 'center', 
      baseline: 'top', 
      maxWidth: titleMaxWidth, 
    })

    yPos = headerHeight + 10

    // --- Información General ---
    yPos = addSectionHeader('INFORMACIÓN GENERAL', yPos)
    autoTable(doc, {
      startY: yPos,
      body: [
        ['ID del Comercio:', item.id.toString()],
        ['Nombre Comercial:', item.name],
        ['Tipo de Comercio:', item.type_shop?.name || 'N/A'],
        ['Estado Actual:', item.status ? 'Activo' : 'Inactivo'],
      ],
      theme: 'grid',
      styles: { fontSize: 10, cellPadding: 3, textColor: bodyTextColor },
      columnStyles: {
        0: { fontStyle: 'bold', cellWidth: tableWidth * 0.35, textColor: renovaSecondary, fillColor: lightGray },
        1: { cellWidth: tableWidth * 0.65 },
      },
    })
    
    yPos = doc.lastAutoTable.finalY + 10

    // --- Información de Contacto ---
    yPos = addSectionHeader('INFORMACIÓN DE CONTACTO', yPos)
    autoTable(doc, {
      startY: yPos,
      body: [
        ['Nombre del Contacto:', item.contact_name],
        ['Correo Electrónico:', item.contact_email],
        ['Teléfono:', formatPhone(item.phone)],
        ['Dirección:', item.address],
      ],
      theme: 'grid',
      styles: { fontSize: 10, cellPadding: 3, textColor: bodyTextColor },
      columnStyles: {
        0: { fontStyle: 'bold', cellWidth: tableWidth * 0.35, textColor: renovaSecondary, fillColor: lightGray }, // Etiquetas en color secundario
        1: { cellWidth: tableWidth * 0.65 },
      },
    })
    
    yPos = doc.lastAutoTable.finalY + 10

    // metricas del negocio 
    yPos = addSectionHeader('MÉTRICAS DEL NEGOCIO', yPos)

    const metricsBody = [
      ['Corte (' + metrics.corte.fecha + '):', metrics.corte.total, 'Ingreso Histórico:', metrics.estadisticas.ingresoTotal],
      ['Puntos (Corte):', metrics.corte.puntos.toString(), 'Puntos Canjeados (Hist.):', metrics.estadisticas.puntosCanjeados.toString()],
      ['Clientes Atendidos (Hist.):', metrics.estadisticas.clientesAtendidos.toString(), 'Promedio Ingreso (Hist.):', metrics.estadisticas.promedioIngreso],
      ['Transacciones (Semana):', metrics.estadisticas.transaccionesSemana.toString(), 'Ventas (Semana Ant.):', metrics.semanaAnterior.ventas.toString()],
      ['Puntos (Semana Ant.):', metrics.semanaAnterior.puntos.toString(), '', ''],
    ]

    autoTable(doc, {
      startY: yPos,
      body: metricsBody,
      theme: 'grid',
      styles: { fontSize: 9, cellPadding: 2, textColor: bodyTextColor },
      columnStyles: {
        // Distribución proporcional para cuatro columnas (etiqueta/valor / etiqueta/valor)
        0: { fontStyle: 'bold', textColor: renovaSecondary, cellWidth: tableWidth * 0.35, fillColor: lightGray },
        1: { cellWidth: tableWidth * 0.15, halign: 'right' },
        2: { fontStyle: 'bold', textColor: renovaSecondary, cellWidth: tableWidth * 0.35, fillColor: lightGray },
        3: { cellWidth: tableWidth * 0.15, halign: 'right' },
      },
    })
    
    yPos = doc.lastAutoTable.finalY + 10

    // --- Actividad Semanal ---
    if (metrics.actividad.length > 0) {
      yPos = addSectionHeader('ACTIVIDAD SEMANAL (Últ. 7 Días)', yPos)
      
      const actividadRows = metrics.actividad.map(dia => [
        dia.dia,
        dia.fecha,
        dia.total.toString(),
      ])

      autoTable(doc, {
        startY: yPos,
        margin: { left: margin, right: margin },
        tableWidth: tableWidth,
        head: [['Día', 'Fecha', 'Transacciones']],
        body: actividadRows,
        theme: 'striped',
        headStyles: { fillColor: renovaPrimary, textColor: 255, halign: 'center' },
        styles: { fontSize: 9, cellPadding: 2, halign: 'center' },
        columnStyles: {
          0: { cellWidth: tableWidth * 0.4, halign: 'center' },
          1: { cellWidth: tableWidth * 0.4, halign: 'center' },
          2: { cellWidth: tableWidth * 0.2, halign: 'center' },
        },
        didDrawPage: data => (yPos = data.cursor.y),
      })

      yPos = doc.lastAutoTable.finalY + 10
    }

    //  Top Recompensas 
    if (metrics.recompensasTop.length > 0) {
      if (yPos + 30 > pageHeight) {
        doc.addPage()
        yPos = margin
      }

      yPos = addSectionHeader('TOP RECOMPENSAS CANJEADAS', yPos)
      
      const recompensasRows = metrics.recompensasTop.map(reward => [
        reward.name,
        reward.redemptions.toString(),
      ])

      autoTable(doc, {
        startY: yPos,
        margin: { left: margin, right: margin },
        tableWidth: tableWidth,
        head: [['Recompensa', 'Canjes']],
        body: recompensasRows,
        theme: 'striped',
        headStyles: { fillColor: renovaPrimary, textColor: 255, halign: 'center' }, 
        styles: { fontSize: 9, cellPadding: 2 },
        columnStyles: {
          0: { cellWidth: tableWidth * 0.8, halign: 'left' },
          1: { cellWidth: tableWidth * 0.2, halign: 'center' },
        },
        didParseCell: data => {
          if (data.row.section === 'head') {
            if (data.column.index === 0) {
              data.cell.styles.halign = 'left'
            } else {
              data.cell.styles.halign = 'center'
            }
          }
        },
        didDrawPage: data => (yPos = data.cursor.y),
      })

      yPos = doc.lastAutoTable.finalY + 10
    }

    //  Información del Registro 
    if (yPos + 40 > pageHeight) {
      doc.addPage()
      yPos = margin
    }
    
    yPos = addSectionHeader('INFORMACIÓN DEL REGISTRO', yPos)
    autoTable(doc, {
      startY: yPos,
      body: [
        ['Fecha de Registro:', format(new Date(item.created_at), 'dd/MM/yyyy HH:mm')],
        ['Última Actualización:', format(new Date(item.updated_at), 'dd/MM/yyyy HH:mm')],
        ['Documento Generado:', format(new Date(), 'dd/MM/yyyy HH:mm')],
      ],
      theme: 'grid',
      styles: { fontSize: 10, cellPadding: 3, textColor: bodyTextColor },
      columnStyles: {
        0: { fontStyle: 'bold', cellWidth: 55, textColor: renovaSecondary, fillColor: lightGray }, // Etiquetas en color secundario
        1: { cellWidth: 125 },
      },
      didDrawPage: data => (yPos = data.cursor.y),
    })
    
    //  Pie de página 
    const pageCount = doc.internal.getNumberOfPages()
    for (let i = 1; i <= pageCount; i++) {
      doc.setPage(i)
      doc.setFontSize(8)
      doc.setTextColor(150, 150, 150)
      doc.text(
        `Este documento es de uso interno exclusivo. © ${format(new Date(), 'yyyy')} Renova S.A. de C.V.`,
        pageWidth / 2,
        pageHeight - 15,
        { align: 'center' },
      )
      doc.text(
        `Página ${i} de ${pageCount} | Emitido: ${format(new Date(), 'dd/MM/yyyy HH:mm')}`,
        pageWidth / 2,
        pageHeight - 10,
        { align: 'center' },
      )
    }
    
    const fileName = `Reporte_${item.name.replace(/\s+/g, '_')}_${format(new Date(), 'ddMMyyyy')}.pdf`

    doc.save(fileName)
    
  } catch (error) {
    console.error('Error al exportar PDF:', error)
    alert('Error al generar el PDF. Revisa la consola para más detalles.')
  }
}
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

    <div class="col-span-12 p-2 md:p-6 bg-white dark:bg-[#2b2c40] flex flex-col md:flex-row justify-between items-center gap-6 shadow-lg">
      <VTextField
        v-model="search"
        label="Buscar"
        placeholder="Buscar comercio"
        prepend-inner-icon="bx-search"
        :color="darkModeStore.darkMode ? 'white' : 'primary'"
        class="w-full md:!flex-[.5]"
        variant="outlined"
      /><VMenu
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
            <VListItem @click="exportToPDF(item)">
              <template #prepend>
                <VIcon
                  icon="bx-download"
                  class="me-2 text-blue-500"
                />
              </template>
              <VListItemTitle class="text-blue-500">
                Exportar datos
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