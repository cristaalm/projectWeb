<template>
  <section id="tecnico" class="pt-20 px-4 bg-muted/30">
    <VContainer class="max-w-7xl">
      <div class="text-center mb-12 space-y-4">
        <h2 class="text-4xl md:text-5xl font-weight-bold text-balance">Tecnología de vanguardia</h2>
        <p class="text-xl text-muted-foreground max-w-2xl mx-auto text-pretty">
          Un ecosistema completo que integra hardware, software y servicios en la nube.
        </p>
      </div>

      <div class="text-center mb-8">
        <VBtn
          size="large"
          variant="outlined"
          @click="isExpanded = !isExpanded"
          append-icon="mdi mdi-chevron-down"
          :class="{ 'rotate-icon': isExpanded }"
        >
          {{ isExpanded ? 'Ver menos detalles' : 'Ver detalles técnicos' }}
        </VBtn>
      </div>

      <VExpandTransition>
        <div v-show="isExpanded" class="space-y-8">
          <VRow>
            <VCol
              v-for="(tech, index) in technologies"
              :key="index"
              cols="12"
              md="6"
              lg="4"
            >
              <VCard class="pa-6 hover-shadow">
                <VSheet class="w-12 h-12 rounded-lg mb-4 d-flex align-center justify-center !bg-opacity-10 !bg-primary">
                  <VIcon :icon="tech.icon" size="24" color="primary" />
                </VSheet>
                <h3 class="text-lg font-weight-bold mb-2">{{ tech.title }}</h3>
                <p class="text-sm text-muted-foreground leading-relaxed">{{ tech.description }}</p>
              </VCard>
            </VCol>
          </VRow>

          <VCard class="pa-8">
            <h3 class="text-h5 font-weight-bold mb-6">Arquitectura del Sistema</h3>
            <div class="space-y-4">
              <div v-for="(arch, index) in architecture" :key="index">
                <h4 class="font-weight-semibold mb-2">{{ arch.title }}</h4>
                <p class="text-sm text-muted-foreground leading-relaxed">{{ arch.description }}</p>
              </div>
            </div>
          </VCard>

          <VCard class="pa-8 differentiators-card">
            <h3 class="text-h5 font-weight-bold mb-4">Diferenciadores Clave</h3>
            <div class="space-y-3">
              <div
                v-for="(diff, index) in differentiators"
                :key="index"
                class="flex items-center w-full gap-1"
              >
                <div class="flex items-center gap-[2px]">
                  <div class="flex items-center justify-center w-[18px] h-[18px] bg-primary rounded-full">
                    <VIcon size="12" color="white">mdi mdi-check</VIcon>
                  </div>
                  <div class="w-[12px] h-[12px] bg-primary rounded-full" />
                  <div class="w-[8px] h-[8px] mt-[1px] bg-primary rounded-full" />
                </div>
                <span class="text-muted-foreground">
                  <strong class="text-foreground">{{ diff.label }}:</strong> {{ diff.description }}
                </span>
              </div>
            </div>
          </VCard>
        </div>
      </VExpandTransition>
    </VContainer>
  </section>
</template>

<script setup>
import { ref } from 'vue'

const isExpanded = ref(false)

const technologies = [
  {
    icon: 'mdi mdi-chip',
    title: 'IoT + Inteligencia Artificial',
    description: 'Contenedores inteligentes con Raspberry Pi 4 y clasificación automática mediante GPT-4o.',
  },
  {
    icon: 'mdi mdi-cellphone',
    title: 'App Móvil Nativa',
    description: 'Desarrollada en Android Studio con Java. Interfaz intuitiva diseñada en Figma.',
  },
  {
    icon: 'mdi mdi-cloud',
    title: 'API REST Robusta',
    description: 'Backend en Laravel con arquitectura cliente-servidor escalable y segura.',
  },
  {
    icon: 'mdi mdi-database',
    title: 'Base de Datos PostgreSQL',
    description: 'Almacenamiento estructurado de usuarios, puntos, recompensas y métricas ambientales.',
  },
  {
    icon: 'mdi mdi-lightning-bolt',
    title: 'Validación Rápida',
    description: 'Clasificación de materiales en ≤5 segundos por depósito con alta precisión.',
  },
  {
    icon: 'mdi mdi-shield-check',
    title: 'Seguridad y Privacidad',
    description: 'Encriptación de datos, autenticación por tokens y eliminación automática de imágenes.',
  },
]

const architecture = [
  {
    title: 'Aplicación Móvil (Ciudadano y Comerciante)',
    description: 'Desarrollada en Android Studio con Java. Permite registro mediante INE y selfie, generación de código QR único, visualización de puntos e historial, y exploración de negocios aliados. Los comerciantes pueden escanear QR para validar canjes.',
  },
  {
    title: 'Panel Web Administrativo',
    description: 'Construido con Vue.js y Laravel. Gestión de usuarios, validación de documentos, administración de negocios aliados, categorías de comercios, y visualización de métricas ambientales. Desplegado en Render.',
  },
  {
    title: 'Sistema Embebido (Contenedor Inteligente)',
    description: 'Raspberry Pi 4 con cámara y lector QR. Identifica al usuario, captura imagen del material, envía a la API para clasificación mediante IA, y ejecuta acciones físicas según la respuesta (apertura de compuertas).',
  },
  {
    title: 'Inteligencia Artificial',
    description: 'Modelo multimodal GPT-4o con prompt estructurado para análisis de imágenes. Devuelve clasificación en JSON (plástico, aluminio, no reciclable) y puntos asignados. Optimiza tiempos de desarrollo con alta precisión.',
  },
]

const differentiators = [
  {
    label: 'Multi-material',
    description: 'Procesamiento de PET y aluminio en un solo sistema',
  },
  {
    label: 'Validación rápida',
    description: '≤5 segundos por depósito',
  },
  {
    label: 'Ecosistema completo',
    description: 'Apps móviles y web con sistema de recompensas gamificado',
  },
  {
    label: 'Trazabilidad IoT',
    description: 'Registro detallado vinculado al usuario',
  },
]
</script>

<style scoped>
.space-y-4 > * + * {
  margin-top: 16px;
}
.space-y-3 > * + * {
  margin-top: 12px;
}
.space-y-8 > * + * {
  margin-top: 32px;
}
.mb-12 {
  margin-bottom: 48px;
}
.mb-8 {
  margin-bottom: 32px;
}
.max-w-2xl {
  max-width: 672px;
}
.mx-auto {
  margin-left: auto;
  margin-right: auto;
}
.text-balance {
  text-wrap: balance;
}
.text-pretty {
  text-wrap: pretty;
}
.leading-relaxed {
  line-height: 1.625;
}
.w-12 {
  width: 48px;
}
.h-12 {
  height: 48px;
}
.mb-4 {
  margin-bottom: 16px;
}
.gap-3 {
  gap: 12px;
}
.mt-1 {
  margin-top: 4px;
}
.rotate-icon :deep(.v-icon) {
  transform: rotate(180deg);
  transition: transform 0.3s;
}
.hover-shadow {
  transition: box-shadow 0.3s;
}
.hover-shadow:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
.differentiators-card {
  background: rgba(5, 209, 110, 0.05) !important;
  border: 1px solid rgba(5, 209, 110, 0.2) !important;
}
</style>
