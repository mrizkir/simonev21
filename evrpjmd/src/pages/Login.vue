<template>
  <v-front-layout :showrightsidebar="false">
    
  </v-front-layout>
</template>
<script>
  import { usesUserStore } from '@/stores/UsersStore'
  import frontLayout from '@/layouts/FrontLayout.vue'
  export default {
    name: 'Login',
    setup() {
      const userStore = usesUserStore()

      return { userStore }
    },
    components: {
      'v-front-layout': frontLayout,
    }
  }
</script>