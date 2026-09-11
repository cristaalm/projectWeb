<script setup>
import RedemptionsTable from './redemptions/RedemptionsTable.vue'
import RewardsTable from './RewardsTable.vue'
import { ref } from 'vue'

const tab = ref('catalog')
</script>

<template>
  <div>
    <VTabs
      v-model="tab"
      class="mb-6"
    >
      <VTab value="catalog">
        <VIcon
          icon="bx-gift"
          class="me-2"
        />
        Catálogo
      </VTab>
      <VTab value="redemptions">
        <VIcon
          icon="bx-receipt"
          class="me-2"
        />
        Canjes
      </VTab>
    </VTabs>

    <VWindow v-model="tab">
      <VWindowItem value="catalog">
        <RewardsTable />
      </VWindowItem>
      <VWindowItem value="redemptions">
        <RedemptionsTable />
      </VWindowItem>
    </VWindow>
  </div>
</template>
