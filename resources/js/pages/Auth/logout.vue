<template>
  <div />
</template>

<script setup>
import useLogout from '@/hooks/Auth/useLogout'

useLogout().logoutUser()
</script>
