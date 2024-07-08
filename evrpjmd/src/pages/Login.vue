<template>
  ini login, {{ userStore.getProfile }}
</template>
<script>
  import { usesUserStore } from '@/stores/UsersStore'
  export default {
    name: 'Login',
    setup() {
      const userStore = usesUserStore()

      return { userStore }
    },
  }
</script>