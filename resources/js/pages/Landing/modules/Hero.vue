<script setup>
import { useDialogStore } from '@/store/useAlertDialogStorage'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const handleDownloadApp = async () => {
  const dialogStore = useDialogStore()

  const result = await dialogStore.showDialog({
    title: t('landing.header.dialog.downloadTitle'),
    text: t('landing.header.dialog.downloadText'),
    type: 'confirm',
    confirmText: t('landing.header.dialog.confirm'),
    cancelText: t('landing.header.dialog.cancel'),
  })

  if (result) {
    window.open('https://bit.ly/renova-app', '_blank')
  }
}

const scrollToSection = sectionId => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>

<template>
  <section
    id="hero"
    class="pt-8 pb-20 px-4"
  >
    <VContainer class="max-w-7xl">
      <VRow class="align-center">
        <VCol
          cols="12"
          lg="6"
        >
          <div class="space-y-8">
            <VChip
              color="primary"
              variant="tonal"
              class="px-4 py-2 font-poppins"
              prepend-icon="mdi mdi-recycle"
            >
              {{ t('landing.hero.chip') }}
            </VChip>

            <h1 class="text-5xl md:text-6xl lg:text-7xl font-weight-bold leading-tight text-balance font-poppins">
              {{ t('landing.hero.title.main') }}
              <span class="text-primary">
                {{ t('landing.hero.title.highlight') }}
              </span>
            </h1>

            <p class="text-xl text-muted-foreground leading-relaxed text-pretty font-poppins">
              {{ t('landing.hero.description') }}
            </p>

            <div class="d-flex flex-column flex-sm-row gap-4">
              <VBtn
                size="x-large"
                color="primary"
                variant="flat"
                class="text-lg px-8 font-poppins"
                prepend-icon="mdi mdi-cellphone"
                append-icon="mdi mdi-arrow-right"
                @click="handleDownloadApp"
              >
                {{ t('landing.hero.actions.download') }}
              </VBtn>

              <VBtn
                size="x-large"
                variant="outlined"
                class="text-lg px-8 font-poppins"
                @click="() => scrollToSection('como-funciona')"
              >
                {{ t('landing.hero.actions.howItWorks') }}
              </VBtn>
            </div>

            <div class="d-flex align-center gap-8 pt-4 font-poppins">
              <div>
                <div class="text-h4 font-weight-bold text-primary">
                  {{ t('landing.hero.stats.users.value') }}
                </div>
                <div class="text-sm text-muted-foreground">
                  {{ t('landing.hero.stats.users.label') }}
                </div>
              </div>

              <VDivider
                vertical
                class="h-12"
              />

              <div>
                <div class="text-h4 font-weight-bold text-primary">
                  {{ t('landing.hero.stats.containers.value') }}
                </div>
                <div class="text-sm text-muted-foreground">
                  {{ t('landing.hero.stats.containers.label') }}
                </div>
              </div>
            </div>
          </div>
        </VCol>

        <VCol
          cols="12"
          lg="6"
        >
          <div class="relative">
            <VImg
              src="/images/contenedor.png"
              :alt="t('landing.hero.imageAlt')"
              class="relative z-10 w-full h-full object-contain drop-shadow-2xl"
            />
          </div>
        </VCol>
      </VRow>
    </VContainer>
  </section>
</template>


<style scoped>
.space-y-8 > * + * {
  margin-top: 32px;
}
.gap-4 {
  gap: 16px;
}
.gap-8 {
  gap: 32px;
}
.leading-tight {
  line-height: 1.25;
}
.leading-relaxed {
  line-height: 1.625;
}
.text-balance {
  text-wrap: balance;
}
.text-pretty {
  text-wrap: pretty;
}
.h-12 {
  height: 48px;
}
.aspect-square {
  aspect-ratio: 1;
}
.inset-0 {
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
.z-10 {
  z-index: 10;
}
.blur-circle {
  width: 128px;
  height: 128px;
  border-radius: 50%;
  filter: blur(64px);
}
.blur-circle-1 {
  bottom: -24px;
  right: -24px;
  background: rgba(5, 209, 110, 0.2);
}
.blur-circle-2 {
  top: -24px;
  left: -24px;
  background: rgba(2, 70, 83, 0.2);
}
</style>
