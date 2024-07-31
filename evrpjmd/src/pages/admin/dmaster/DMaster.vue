<template>
  <v-main-layout :token="userStore.Token">
    <template v-slot:leftsidebar>
      <v-sidebar-left />
    </template>
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        Indikator Kinerja
      </template>
      <template v-slot:breadcrumbs>
        <v-breadcrumbs :items="breadcrumbs" class="pa-0">
          <template v-slot:divider>
            <v-icon>mdi-chevron-right</v-icon>
          </template>
        </v-breadcrumbs>
      </template>
      <template v-slot:desc>
        <v-alert color="cyan" border="start" colored-border type="info">
          Halaman ini digunakan untuk mengelola data master
        </v-alert>
      </template>      
    </v-page-header>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import sidebarLeft from '@/layouts/SidebarLeftDMasterAdmin.vue'
  import { usesUserStore } from '@/stores/UsersStore'
  export default {
    name: 'DMaster',
    created() {
      this.userStore = usesUserStore()
    },
    components: {
      'v-main-layout': mainLayout,
      'v-sidebar-left': sidebarLeft,
    },
  }
</script>