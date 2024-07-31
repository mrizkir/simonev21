<template>
  <v-main-layout :token="token">
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
      this.token = this.$route.params.token;
    },
    data: () => ({
      //pinia
      userStore: null,
      token: null,
    }),
    components: {
      'v-main-layout': mainLayout,
    }
  }
</script>