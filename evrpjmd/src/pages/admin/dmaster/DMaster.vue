<template>
  <v-main-layout>
    <template v-slot:leftsidebar>
      <v-sidebar-left />
    </template>
    Data Master
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import sidebarLeft from '@/layouts/SidebarLeftDMasterAdmin.vue'
  export default {
    name: 'DMaster',
    components: {
      'v-main-layout': mainLayout,
      'v-sidebar-left': sidebarLeft,
    }
  }
</script>