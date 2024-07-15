<template>
  <v-main-layout>
    dashboard
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  export default {
    name: 'DashboardAdmin',
    components: {
      'v-main-layout': mainLayout,
    }
  }
</script>