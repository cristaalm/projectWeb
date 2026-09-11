<script setup>
import BadgeEmblem from '@/components/BadgeEmblem.vue'
import { useBadgeManagement } from '@/hooks/Badges/useBadgeManagement'
import { CURATED_BADGE_ICONS, DEFAULT_BADGE_ICON } from '@/utils/badgeTier'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  mode: { type: String, default: 'create' }, // 'create' | 'edit'
  badge: { type: Object, default: null },
})

const emit = defineEmits(['update:modelValue', 'saved'])

const { loading, createBadge, updateBadge } = useBadgeManagement()

const ICON_LABELS = {
  'bx-recycle': 'Reciclaje',
  'bx-leaf': 'Hoja',
  'bx-bxs-tree': 'Árbol',
  'bx-water': 'Agua',
  'bx-world': 'Mundo',
  'bx-medal': 'Medalla',
  'bx-trophy': 'Trofeo',
  'bx-award': 'Premio',
  'bx-badge-check': 'Insignia verificada',
  'bx-sun': 'Sol',
}

const form = ref({
  name: '', icon: null, recycles_required: null, points_awarded: null, status: true,
})

const iconMenu = ref(false)

const isEdit = computed(() => props.mode === 'edit')
const dialogTitle = computed(() => (isEdit.value ? 'Editar insignia' : 'Crear insignia'))
const currentIconLabel = computed(() => ICON_LABELS[form.value.icon] ?? 'Medalla (predeterminado)')

const isValid = computed(() => (
  Boolean(form.value.name.trim())
  && Number(form.value.recycles_required) > 0
  && Number(form.value.points_awarded) >= 0
))

function resetForm() {
  if (isEdit.value && props.badge) {
    form.value = {
      name: props.badge.name,
      icon: props.badge.icon,
      recycles_required: props.badge.recycles_required,
      points_awarded: props.badge.points_awarded,
      status: Boolean(props.badge.status),
    }
  } else {
    form.value = { name: '', icon: null, recycles_required: null, points_awarded: null, status: true }
  }
}

watch(() => props.modelValue, open => { if (open) resetForm() })

function selectIcon(icon) {
  form.value.icon = icon
  iconMenu.value = false
}

async function submit() {
  const payload = {
    name: form.value.name,
    icon: form.value.icon,
    recycles_required: Number(form.value.recycles_required),
    points_awarded: Number(form.value.points_awarded),
    status: form.value.status,
  }

  const result = isEdit.value
    ? await updateBadge(props.badge.id, payload)
    : await createBadge(payload)

  if (!result) return

  emit('saved')
  emit('update:modelValue', false)
}
</script>

<template>
  <VDialog :model-value="modelValue" max-width="560" persistent @update:model-value="emit('update:modelValue', $event)">
    <VCard :title="dialogTitle">
      <VCardText>
        <VForm @submit.prevent="submit">
          <VRow>
            <VCol cols="12">
              <VTextField v-model="form.name" label="Nombre" maxlength="100" required />
            </VCol>
            <VCol cols="6">
              <VTextField v-model="form.recycles_required" type="number" min="1" label="Reciclajes requeridos al mes" required />
            </VCol>
            <VCol cols="6">
              <VTextField v-model="form.points_awarded" type="number" min="0" label="Puntos otorgados" required />
            </VCol>
            <VCol cols="12">
              <VSwitch v-model="form.status" label="Activa" color="primary" hide-details />
            </VCol>
            <VCol cols="12">
              <div class="icon-summary pa-3 rounded-lg border border-gray-200 d-flex align-center justify-space-between dark:border-gray-700">
                <div class="d-flex align-center gap-3">
                  <BadgeEmblem :badge="{ icon: form.icon || DEFAULT_BADGE_ICON, recycles_required: form.recycles_required || 0 }" size="40" />
                  <div>
                    <div class="text-body-2 font-weight-medium">Ícono de la insignia</div>
                    <div class="text-caption text-medium-emphasis">{{ currentIconLabel }}</div>
                  </div>
                </div>
                <VMenu v-model="iconMenu" :close-on-content-click="false" location="bottom end">
                  <template #activator="{ props: menuProps }">
                    <VBtn variant="tonal" color="primary" size="small" v-bind="menuProps">
                      <VIcon icon="bx-edit" class="me-2" /> Elegir ícono
                    </VBtn>
                  </template>
                  <VCard min-width="260">
                    <VCardText class="icon-grid">
                      <VBtn
                        v-for="icon in CURATED_BADGE_ICONS" :key="icon"
                        icon size="small" :title="ICON_LABELS[icon]"
                        :variant="form.icon === icon ? 'flat' : 'text'"
                        :color="form.icon === icon ? 'primary' : 'default'"
                        @click="selectIcon(icon)"
                      >
                        <VIcon :icon="icon" />
                      </VBtn>
                    </VCardText>
                  </VCard>
                </VMenu>
              </div>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn variant="tonal" @click="emit('update:modelValue', false)">Cancelar</VBtn>
        <VBtn color="primary" :loading="loading" :disabled="!isValid" @click="submit">{{ isEdit ? 'Guardar' : 'Crear' }}</VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
.icon-grid {
  display: grid;
  gap: 4px;
  grid-template-columns: repeat(5, 1fr);
}
</style>
