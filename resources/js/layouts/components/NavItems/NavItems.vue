<script setup>
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue'

import useMenuStore from '@/store/userMenu'
import VerticalNavGroup from '@layouts/components/VerticalNavGroup.vue'
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue'

const user = JSON.parse(localStorage.getItem('user'))
const menuStore = useMenuStore()
const menu = menuStore

menuStore.loadMenu(user)
</script>

<template>
  <template v-for="(item, index) in menu.value">
    <VerticalNavSectionTitle
      v-if="typeof item === 'string'"
      :key="'separator' + index"
      :item="{
        heading: item,
      }"
    />

    <!-- si tiene la pripiedad menu y esta no esta vacia -->
    <VerticalNavGroup
      v-else-if="item.menu && item.menu.length > 0"
      :key="'GroupMenu' + index"
      :item="{
        title: item.title,
        icon: item.icon,
      }"
    >
      <VerticalNavLink
        v-for="(subItem, subIndex) in item.menu"
        :key="'SubOption' + subIndex"
        :item="{
          title: subItem.title,
          to: subItem.to,
        }"
      />
    </VerticalNavGroup>

    <VerticalNavLink
      v-else
      :key="'Option' + index"
      :item="{
        title: item.title,
        to: item.to,
        icon: item.icon,
      }"
    />
  </template>
</template>
