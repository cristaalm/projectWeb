<script setup>
import { useAllianceManagement } from '@/hooks/Alliances/useAllianceManagement'
import { useTypeShopCatalog } from '@/hooks/TypeShop/useTypeShopCatalog'
import { computed, ref, watch } from 'vue'
import AllianceLogoUploader from './components/AllianceLogoUploader.vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  mode: { type: String, default: 'create' }, // 'create' | 'edit'
  alliance: { type: Object, default: null },
})

const emit = defineEmits(['update:modelValue', 'saved'])

const { loading, createAlliance, updateAlliance } = useAllianceManagement()
const { typeShops, loading: typeShopsLoading, fetchTypeShops } = useTypeShopCatalog()

const typeShopItems = computed(() => typeShops.value.map(item => ({
  ...item,
  label: item.is_active ? item.name : `${item.name} (inactiva)`,
})))

const form = ref({
  name: '',
  contact_name: '',
  contact_email: '',
  phone: '',
  address: '',
  type_shop_id: null,
  has_exclusive_rewards: false,
  status: true,
})

// Guarda una copia local de la alianza en edición para poder refrescar el
// logo apenas se sube/quita sin esperar a que se recargue la tabla completa.
const editedAlliance = ref(null)

const isEdit = computed(() => props.mode === 'edit')
const dialogTitle = computed(() => (isEdit.value ? 'Editar alianza' : 'Crear alianza'))

const isValid = computed(() => (
  Boolean(form.value.name.trim())
  && Boolean(form.value.contact_name.trim())
  && Boolean(form.value.contact_email.trim())
  && Boolean(form.value.phone.trim())
  && Boolean(form.value.address.trim())
  && Boolean(form.value.type_shop_id)
))

function resetForm() {
  if (isEdit.value && props.alliance) {
    form.value = {
      name: props.alliance.name,
      contact_name: props.alliance.contact_name,
      contact_email: props.alliance.contact_email,
      phone: props.alliance.phone,
      address: props.alliance.address,
      type_shop_id: props.alliance.type_shop_id,
      has_exclusive_rewards: Boolean(props.alliance.has_exclusive_rewards),
      status: Boolean(props.alliance.status),
    }
    editedAlliance.value = props.alliance
  } else {
    form.value = {
      name: '', contact_name: '', contact_email: '', phone: '', address: '',
      type_shop_id: null, has_exclusive_rewards: false, status: true,
    }
    editedAlliance.value = null
  }
}

watch(() => props.modelValue, open => {
  if (open) {
    resetForm()
    fetchTypeShops()
  }
})

function onLogoUpdated(alliance) {
  editedAlliance.value = alliance
  emit('saved')
}

async function submit() {
  const payload = {
    name: form.value.name,
    contact_name: form.value.contact_name,
    contact_email: form.value.contact_email,
    phone: form.value.phone,
    address: form.value.address,
    type_shop_id: form.value.type_shop_id,
    has_exclusive_rewards: form.value.has_exclusive_rewards,
    status: form.value.status,
  }

  const result = isEdit.value
    ? await updateAlliance(props.alliance.id, payload)
    : await createAlliance(payload)

  if (!result) return

  emit('saved')
  emit('update:modelValue', false)
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="560"
    persistent
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard :title="dialogTitle">
      <VCardText>
        <div
          v-if="isEdit && editedAlliance"
          class="mb-4"
        >
          <AllianceLogoUploader
            :alliance-id="editedAlliance.id"
            :name="editedAlliance.name"
            :logo-url="editedAlliance.logo_url"
            @updated="onLogoUpdated"
          />
        </div>
        <p
          v-else
          class="mb-4 text-caption text-medium-emphasis"
        >
          Podrás subir el logo después de crear la alianza.
        </p>

        <VForm @submit.prevent="submit">
          <VRow>
            <VCol cols="12">
              <VTextField
                v-model="form.name"
                label="Nombre de la alianza"
                required
              />
            </VCol>
            <VCol cols="6">
              <VTextField
                v-model="form.contact_name"
                label="Nombre de contacto"
                required
              />
            </VCol>
            <VCol cols="6">
              <VTextField
                v-model="form.contact_email"
                type="email"
                label="Correo de contacto"
                required
              />
            </VCol>
            <VCol cols="6">
              <VTextField
                v-model="form.phone"
                label="Teléfono"
                required
              />
            </VCol>
            <VCol cols="6">
              <VSelect
                v-model="form.type_shop_id"
                :items="typeShopItems"
                :loading="typeShopsLoading"
                item-title="label"
                item-value="id"
                label="Categoría"
                required
              />
            </VCol>
            <VCol cols="12">
              <VTextField
                v-model="form.address"
                label="Dirección"
                required
              />
            </VCol>
            <VCol cols="12">
              <VSwitch
                v-model="form.status"
                label="Activa"
                color="primary"
                hide-details
              />
            </VCol>
            <VCol cols="12">
              <VSwitch
                v-model="form.has_exclusive_rewards"
                label="Permite enlazar usuarios miembro"
                color="primary"
                hide-details
              />
              <p class="mt-1 mb-0 text-caption text-medium-emphasis">
                Los usuarios con rol Miembro solo pueden vincularse a alianzas con esta opción activa.
              </p>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn
          variant="tonal"
          @click="emit('update:modelValue', false)"
        >
          Cancelar
        </VBtn>
        <VBtn
          color="primary"
          :loading="loading"
          :disabled="!isValid"
          @click="submit"
        >
          {{ isEdit ? 'Guardar' : 'Crear' }}
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
