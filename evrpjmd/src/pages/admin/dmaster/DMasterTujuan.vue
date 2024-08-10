<template>
  <v-main-layout :token="userStore.AccessToken">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        TUJUAN
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
          Halaman ini digunakan untuk mengelola tujuan.
        </v-alert>
      </template>      
    </v-page-header>
    <v-container fluid>
      <v-tujuan-data-table />
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import dataTable from '@/pages/admin/dmaster/DMasterTujuanDataTable.vue'
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'DMasterTujuan',
    created() {
      this.userStore = usesUserStore()
      this.breadcrumbs = [
        {
          title: 'HOME',
          href: '/admin/' + this.userStore.AccessToken,
        },
        {
          title: 'DATA MASTER',
          disabled: false,
          href: '#',
        },
        {
          title: 'TUJUAN',
          disabled: true,
          href: '#',
        },
      ]
    },   
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,
      'v-tujuan-data-table': dataTable,
    }
  }
</script>