<script setup>
import { useRewardManagement } from '@/hooks/Rewards/useRewardManagement'
import { storageURL } from '@/utils/constants'
import { computed, ref } from 'vue'

const props = defineProps({
  rewardId: { type: Number, required: true },
  imageUrl: { type: String, default: null },
})

const emit = defineEmits(['updated'])

const { loading, uploadRewardImage, deleteRewardImage } = useRewardManagement()
const imageInput = ref(null)

const fullImageUrl = computed(() => (props.imageUrl ? storageURL + props.imageUrl : null))

async function onImageSelected(event) {
  const file = event.target.files?.[0]

  event.target.value = ''
  if (!file) return

  const reward = await uploadRewardImage(props.rewardId, file)
  if (reward) emit('updated', reward)
}

async function removeImage() {
  const reward = await deleteRewardImage(props.rewardId)
  if (reward) emit('updated', reward)
}
</script>

<template>
  <div class="gap-3 d-flex align-center">
    <div class="image-uploader__wrap">
      <VAvatar
        size="64"
        rounded="lg"
        color="primary"
        variant="tonal"
      >
        <VImg
          v-if="fullImageUrl"
          :src="fullImageUrl"
          cover
        />
        <VIcon
          v-else
          icon="bx-gift"
          size="28"
        />
      </VAvatar>
      <VBtn
        icon
        size="small"
        color="primary"
        :loading="loading"
        class="image-uploader__edit-btn"
        @click="imageInput.click()"
      >
        <VIcon
          icon="bx-camera"
          size="16"
        />
      </VBtn>
      <input
        ref="imageInput"
        type="file"
        accept="image/png,image/jpeg,image/jpg,image/webp"
        class="d-none"
        @change="onImageSelected"
      >
    </div>
    <div>
      <div class="text-body-2 font-weight-medium">
        Imagen del producto
      </div>
      <VBtn
        v-if="imageUrl"
        variant="text"
        size="small"
        color="error"
        :loading="loading"
        class="px-0"
        @click="removeImage"
      >
        Quitar imagen
      </VBtn>
      <div
        v-else
        class="text-caption text-medium-emphasis"
      >
        Sin imagen, se usa un ícono por defecto. JPG, PNG o WEBP, máx. 2 MB.
      </div>
    </div>
  </div>
</template>

<style scoped>
.image-uploader__wrap {
  position: relative;
}

.image-uploader__edit-btn {
  position: absolute;
  inset-block-end: -4px;
  inset-inline-end: -4px;
  border: 2px solid rgb(var(--v-theme-surface));
}
</style>
