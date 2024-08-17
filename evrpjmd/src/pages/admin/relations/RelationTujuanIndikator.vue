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
        item-value="RpjmdTujuanID"
        @update:options="initialize"        
        items-per-page-text="Jumlah record per halaman"
        disable-sort
      >
        <template v-slot:loading>
          <v-skeleton-loader :type="'table-row@' + itemsPerPage"></v-skeleton-loader>
        </template>
        <template v-slot:item="{ index, item }">
          <tr class="bg-grey-lighten-5">
            <td>{{ (indexOffset + index) + 1 }}</td>
            <td colspan="11">{{ item.Nm_RpjmdTujuan }}</td>
            <td>
              <v-btn
                class="mr-2"
                v-tooltip:bottom="'Tambah Sasaran'"
                :to="'/admin/dmaster/sasaran/' + item.RpjmdTujuanID + '/manage'"
                size="small"
                color="primary"
                variant="text"
                icon="mdi-plus"
                density="compact"
              />
            </td>
          </tr>
          
        </template>
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
    }),
    methods: {
      async initialize({ page, itemsPerPage }) {  
        this.datatableLoading = true
        const offset = (page - 1) * itemsPerPage
        this.indexOffset = offset

        await this.$ajax
          .post('/rpjmd/tujuan/indikatortujuan', 
            {
              PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,              
              offset: offset,
              limit: itemsPerPage,
            },
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            let payload = data.payload
            this.datatable = payload.data
            this.totalRecords = payload.totalRecords
            this.datatableLoading = false    
          })
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
            value: 'data_1',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: TA_AWAL,
            value: 'data_2',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
        ]
        var children_target_tahun = []
        var i = 3        
        for(var tahun = parseInt(TA_AWAL) + 1; tahun <= TA_AKHIR; tahun++) {
          children_target_tahun.push({
            title: tahun,
            value: 'data_' + i,
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
          {
            title: "AKSI",
            key: "actions",
            sortable: false,
            width: 110,
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