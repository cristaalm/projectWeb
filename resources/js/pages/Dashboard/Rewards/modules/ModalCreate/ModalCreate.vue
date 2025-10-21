<script setup>
import { useCreateReward } from '@/hooks/Rewards/useCreateReward'
import { useValidations } from '@/hooks/Rewards/useValidations'
import { useCatalogShops } from '@/hooks/Shops/useCatalogShops'
import { useDarkModeStore } from '@/store/dark-mode'
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
})

const emit = defineEmits(['update:modelValue', 'create'])

const darkModeStore = useDarkModeStore()

const { loading, createReward, rewardData, resetRewardData } = useCreateReward()
const { loading: loadingCatShops, loadCatShops, catShopsData } = useCatalogShops()
const isOpen = ref(props.modelValue)

const {
  formValidate,
  formErrors,
  touchField,
  touchedFields,
  resetValidations,
  isUnlimitedStock,
  isIndefiniteExpiration,
} = useValidations({ rewardData })

const handleSaveReward = async () => {
  if (!formValidate.value) return

  const result = await createReward({ isUnlimitedStock, isIndefiniteExpiration })

  if (!result) return

  resetValidations()
  resetRewardData()
  emit('create')
  isOpen.value = false
}

watch(() => props.modelValue, val => {
  isOpen.value = val
  if (val) {
    loadCatShops()
    isUnlimitedStock.value = false
    isIndefiniteExpiration.value = false
  }
  resetValidations()
})

watch(isOpen, val => {
  emit('update:modelValue', val)
  resetRewardData()
})
</script>

<template>
  <VDialog
    v-model="isOpen"
    max-width="800px"
    persistent
  >
    <VCard>
      <VCardTitle class="text-xl font-semibold">
        📝 Agregar Comercio
      </VCardTitle>

      <VCardText class="space-y-8">
        <div class="flex flex-col gap-4">
          <VSelect
            v-model="rewardData.alliance_id"
            :items="catShopsData"
            item-title="name"
            item-value="id"
            label="Comercio"
            placeholder="Comercio"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :loading="loadingCatShops"
            :disabled="loadingCatShops || loading"
            @input="touchField('alliance_id')"
          />
          <VTextField
            v-model="rewardData.name"
            label="Nombre de la recompensa"
            placeholder="Nombre de la recompensa"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.name ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.name && !!formErrors.name"
            :error-messages="touchedFields.name ? formErrors.name : ''"
            @enter="handleSaveReward"
            @input="touchField('name')"
          />
          <VTextField
            v-model="rewardData.description"
            label="Descripcion de la recompensa"
            placeholder="Descripcion de la recompensa"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.description ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.description && !!formErrors.description"
            :error-messages="touchedFields.description ? formErrors.description : ''"
            @enter="handleSaveReward"
            @input="touchField('description')"
          />
          <VTextField
            v-model="rewardData.points_required"
            v-number-only
            label="Puntos requeridos"
            placeholder="Puntos requeridos"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.points_required ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.points_required && !!formErrors.points_required"
            :error-messages="touchedFields.points_required ? formErrors.points_required : ''"
            @enter="handleSaveReward"
            @keyup="touchField('points_required')"
          />
          <div class="flex flex-col md:flex-row gap-2">
            <VTextField
              v-model="rewardData.stock"
              v-number-only
              label="Stock"
              placeholder="Stock"
              outlined
              class="!flex-[.8]"
              :color="darkModeStore.darkMode ? 'white' : 'primary'" 
              :class="formErrors.stock ? '!max-h-[60px]' : '!max-h-[38px]'"
              :disabled="loading || isUnlimitedStock"
              :error="touchedFields.stock && !!formErrors.stock"
              :error-messages="touchedFields.stock ? formErrors.stock : ''"
              @enter="handleSaveReward"
              @keyup="touchField('stock')"
            />
            <VCheckbox
              v-model="isUnlimitedStock"
              label="Ilimitado"
              class="!flex-[.2]"
            />
          </div>
          <div class="flex flex-col md:flex-row gap-2">
            <div class="flex-[.81]">
              <Litepicker 
                v-model="rewardData.expires_at"
                :options="{
                  autoApply: true,
                  singleMode: true,
                  numberOfColumns: 2,
                  numberOfMonths: 2,
                  showWeekNumbers: true,
                  dropdowns: {
                    months: true,
                    years: true
                  },
                  format: 'DD/MM/YYYY'
                }" 
                placeholder="DD/MM/YYYY"
                :class="formErrors.expires_at && touchedFields.expires_at ? '!bg-red-500/20' : '!bg-slate-200 dark:read-only:!bg-[#313245]'"
                class="block w-full dark:!text-slate-200 px-4 py-2 dark:placeholder:!text-slate-400 text-center !cursor-pointer disabled:text-gray-300"
                readonly
                :disabled="loading || isIndefiniteExpiration"
                @update:model-value="() => touchField('expires_at')"
              />
              <p
                v-if="formErrors.expires_at && touchedFields.expires_at"
                class="text-red-500 text-sm mt-0"
              >
                {{ formErrors.expires_at }}
              </p>
            </div>
            <VCheckbox
              v-model="isIndefiniteExpiration"
              label="Indefinido"
              class="!flex-[.2]"
            />
          </div>
        </div>

        <!-- Estado del plan -->
        <VTable class="mt-4 text-no-wrap">
          <thead>
            <tr>
              <th>Configuración</th>
              <th>Valor</th>
              <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            <tr
              class=" select-none cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800"
              @click="() => rewardData.is_active = !rewardData.is_active"
            >
              <td>Estado</td>
              <td>
                <VCheckbox v-model="rewardData.is_active" />
              </td>
              <td class="text-sm text-gray-500 dark:text-slate-300">
                La recompensa estara habilitada o deshabilitada
              </td>
            </tr>
          </tbody>
        </VTable>
      </VCardText>

      <VDivider />

      <VCardActions class="justify-end mt-2 space-x-2">
        <VBtn
          variant="elevated"
          color="grey darken-1"
          :disabled="loading"
          @click="isOpen = false"
        >
          Cancelar
        </VBtn>
        <VBtn
          color="success"
          variant="flat"
          :disabled="loading || !formValidate"
          :loading="loading"
          prepend-icon="bx-save"
          @click="handleSaveReward"
        >
          Guardar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
