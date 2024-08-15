<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        INDIKATOR TUJUAN
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
          Halaman ini digunakan untuk mengelola relasi indikator tujuan RPJMD.
        </v-alert>
      </template>      
    </v-page-header>
    <v-container fluid>
      <v-data-table-server
        density="compact"
        v-model:items-per-page="itemsPerPage"            
        :headers="fetchHeader"
        :items="datatable"
        :items-length="totalRecords"
        :loading="datatableLoading"
        :search="searchTrigger"
        item-value="IndikatorKinerjaID"
        @update:options="initialize"        
        items-per-page-text="Jumlah record per halaman"
      >
        
      </v-data-table-server>
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'  
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'RelationTujuanIndikator',
    created() {
      this.userStore = usesUserStore()
      this.breadcrumbs = [
        {
          title: 'HOME',
          href: '/admin/' + this.userStore.AccessToken,
        },
        {
          title: 'RELASI',
          disabled: false,
          href: '#',
        },
        {
          title: 'INDIKATOR - TUJUAN',
          disabled: true,
          href: '#',
        },
      ]
      this.initialize()
    },
    data: () => ({
      btnLoading: false,
      datatableLoading: false,
      //data table
      datatable: [],
      itemsPerPage: 10,
      totalRecords: 0,
      indexOffset: 0,      
      search: '',
    }),
    methods: {
      async initialize() {

      },
    },
    computed: {
      fetchHeader() {
        let periode = this.userStore.PeriodeRPJMD;
        let TA_AWAL = periode.TA_AWAL
        let TA_AKHIR = periode.TA_AKHIR

        var children_kondisi_awal = [
          {
            title: TA_AWAL - 1,
            value: 'height',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: TA_AWAL,
            value: 'base',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
        ]
        var children_target_tahun = []        
        for(var i = parseInt(TA_AWAL) + 1; i <= TA_AKHIR; i++) {
          console.log(i)
          children_target_tahun.push({
            title: i,
            value: 'height',
            headerProps: {
              class: 'font-weight-bold',
            },
          });   
        }
        
        var headers = [
          {
            title: 'NO',
            align: 'start',
            sortable: false,
            key: 'no',
            width: 70,
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'NAMA TUJUAN',
            key: 'Nm_RpjmdTujuan',
            align: 'start',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'INDIKATOR',
            key: 'Nm_RpjmdTujuan',
            align: 'start',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'SATUAN',
            key: 'Nm_RpjmdTujuan',
            align: 'start',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'KONDISI AWAL',
            align: 'center',
            children: children_kondisi_awal,
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'TARGET TAHUN',
            align: 'center',
            children: children_target_tahun,
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'AKHIR RPJMD',
            key: 'Nm_RpjmdTujuan',
            align: 'center',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
        ]
        return headers
      },
    },
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,      
    }
  }
</script>