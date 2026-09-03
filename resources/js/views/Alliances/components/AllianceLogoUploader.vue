<script setup>
import UserAvatar from '@/components/UserAvatar.vue'
import { useAllianceManagement } from '@/hooks/Alliances/useAllianceManagement'
import { storageURL } from '@/utils/constants'
import { computed, ref } from 'vue'

const props = defineProps({
  allianceId: { type: Number, required: true },
  name: { type: String, required: true },
  logoUrl: { type: String, default: null },
})

const emit = defineEmits(['updated'])

const { loading, uploadAllianceLogo, deleteAllianceLogo } = useAllianceManagement()
const logoInput = ref(null)

const fullLogoUrl = computed(() => (props.logoUrl ? storageURL + props.logoUrl : null))

async function onLogoSelected(event) {
  const file = event.target.files?.[0]

  event.target.value = ''
  if (!file) return

  const alliance = await uploadAllianceLogo(props.allianceId, file)
  if (alliance) emit('updated', alliance)
}

async function removeLogo() {
  const alliance = await deleteAllianceLogo(props.allianceId)
  if (alliance) emit('updated', alliance)
}
</script>

<template>
  <div class="gap-3 d-flex align-center">
    <div class="logo-uploader__wrap">
      <UserAvatar
        size="64"
        :name="name"
        :avatar-url="fullLogoUrl"
      />
      <VBtn
        icon
        size="small"
        color="primary"
        :loading="loading"
        class="logo-uploader__edit-btn"
        @click="logoInput.click()"
      >
        <VIcon
          icon="bx-camera"
          size="16"
        />
      </VBtn>
      <input
        ref="logoInput"
        type="file"
        accept="image/png,image/jpeg,image/jpg,image/webp"
        class="d-none"
        @change="onLogoSelected"
      >
    </div>
    <div>
      <div class="text-body-2 font-weight-medium">
        Logo de la alianza
      </div>
      <VBtn
        v-if="logoUrl"
        variant="text"
        size="small"
        color="error"
        :loading="loading"
        class="px-0"
        @click="removeLogo"
      >
        Quitar logo
      </VBtn>
      <div
        v-else
        class="text-caption text-medium-emphasis"
      >
        Sin logo, se usan las iniciales
      </div>
    </div>
  </div>
</template>

<style scoped>
.logo-uploader__wrap {
  position: relative;
}

.logo-uploader__edit-btn {
  position: absolute;
  inset-block-end: -4px;
  inset-inline-end: -4px;
  border: 2px solid rgb(var(--v-theme-surface));
}
</style>
