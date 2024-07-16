<template>
  <v-main-layout>
    dashboard
  </v-main-layout>  
</template>
<script>
  import { usesUserStore } from '@/stores/UsersStore'
  import mainLayout from '@/layouts/MainLayout.vue'
  export default {
    name: 'DashboardAdmin',
    created() {
      this.userStore = usesUserStore()
    },
    data: () => ({
      //pinia
      userStore: null,
    }),
    components: {
      'v-main-layout': mainLayout,
    }
  }
</script>