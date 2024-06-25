<template>
  <v-front-layout>
    <template v-slot:system-bar>
      test
    </template>
    test
  </v-front-layout>  
</template>
<script>
  import frontLayout from '@/layouts/FrontLayout.vue'
  export default {
    name: 'DashboardFront',
    components: {
      'v-front-layout': frontLayout,
    }
  }
</script>