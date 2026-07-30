<script setup>
import { useAllianceCatalog } from '@/hooks/Users/useAllianceCatalog'
import { useUserManagement } from '@/hooks/Users/useUserManagement'
import { ALLIANCE_REQUIRED_ROLES, CREATABLE_ROLES } from '@/utils/roles'
import { isValidEmail } from '@/utils/validators'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
})

const emit = defineEmits(['update:modelValue', 'created'])

const { loading, createUser } = useUserManagement()
const { alliances, loading: alliancesLoading, fetchAlliances } = useAllianceCatalog()

const form = ref({
  name: '',
  last_name: '',
  email: '',
  phone: '',
  role_id: null,
  alliance_id: null,
})

const selectedRole = computed(() => CREATABLE_ROLES.find(role => role.id === form.value.role_id) ?? null)
const needsAllianceField = computed(() => selectedRole.value?.name !== 'moderador' && selectedRole.value !== null)
const allianceRequired = computed(() => ALLIANCE_REQUIRED_ROLES.includes(selectedRole.value?.name))

const isValid = computed(() => (
  Boolean(form.value.name.trim())
  && Boolean(form.value.last_name.trim())
  && isValidEmail(form.value.email)
  && Boolean(form.value.role_id)
  && (!allianceRequired.value || Boolean(form.value.alliance_id))
))

function resetForm() {
  form.value = { name: '', last_name: '', email: '', phone: '', role_id: null, alliance_id: null }
}

watch(() => props.modelValue, open => {
  if (open) {
    resetForm()
    fetchAlliances()
  }
})

watch(selectedRole, () => {
  form.value.alliance_id = null
})

async function submit() {
  const payload = {
    name: form.value.name,
    last_name: form.value.last_name,
    email: form.value.email,
    phone: form.value.phone || null,
    role_id: form.value.role_id,
  }

  if (needsAllianceField.value && form.value.alliance_id) {
    payload.alliance_id = form.value.alliance_id
  }

  const user = await createUser(payload)
  if (!user) return

  emit('created')
  emit('update:modelValue', false)
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="520"
    persistent
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard title="Crear usuario">
      <VCardText>
        <VForm @submit.prevent="submit">
          <VRow>
            <VCol cols="6">
              <VTextField
                v-model="form.name"
                label="Nombre"
                required
              />
            </VCol>
            <VCol cols="6">
              <VTextField
                v-model="form.last_name"
                label="Apellido"
                required
              />
            </VCol>
            <VCol cols="12">
              <VTextField
                v-model="form.email"
                type="email"
                label="Correo electrónico"
                required
              />
            </VCol>
            <VCol cols="12">
              <VTextField
                v-model="form.phone"
                label="Teléfono (opcional)"
              />
            </VCol>
            <VCol cols="12">
              <VSelect
                v-model="form.role_id"
                :items="CREATABLE_ROLES"
                item-title="display_name"
                item-value="id"
                label="Rol"
                required
              />
            </VCol>
            <VCol
              v-if="needsAllianceField"
              cols="12"
            >
              <VSelect
                v-model="form.alliance_id"
                :items="alliances"
                :loading="alliancesLoading"
                item-title="name"
                item-value="id"
                :label="allianceRequired ? 'Alianza (comercio)' : 'Alianza (comercio, opcional)'"
                :required="allianceRequired"
                clearable
              />
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
          Crear
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
