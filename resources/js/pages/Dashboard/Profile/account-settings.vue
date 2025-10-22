<script setup>
import AccountSettingsAccount from '@/views/pages/account-settings/AccountSettingsAccount.vue'
import AccountSettingsSecurity from '@/views/pages/account-settings/AccountSettingsSecurity.vue'

const route = useRoute()
</script>

<template>
  <div>
    <AccountSettingsAccount />
    <AccountSettingsSecurity />
  </div>
</template>
