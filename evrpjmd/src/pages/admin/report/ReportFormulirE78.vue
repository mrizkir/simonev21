<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        LAPORAN FORMULIR E.78
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
          Halaman ini digunakan untuk mencetak formulir E.78 per tahun anggaran
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
        item-value="PrgID"
        @update:options="initialize"
        items-per-page-text="Jumlah record per halaman"
        disable-sort
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-autocomplete
              :items="daftar_sasaran_rpjmd"
              density="compact"
              variant="outlined"
              v-model="BidangID"
              label="SASARAN RPJMD"              
              item-title="Nm_RpjmdSasaran"
              item-value="RpjmdSasaranID"
              class="pa-3 mt-4"
              clearable              
            />
          </v-toolbar>
        </template>
      </v-data-table-server>
    </v-container>
  </v-main-layout>
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'  
  import { usesUserStore } from '@/stores/UsersStore'
  import { usesPageStore } from '@/stores/PageStore'

  export default {
    name: 'ReportFormulirE78',
    created() {
      this.userStore = usesUserStore()
      this.pageStore = usesPageStore()
      this.breadcrumbs = [
        {
          title: 'HOME',
          href: '/admin/' + this.userStore.AccessToken,
        },
        {
          title: 'REPORT',
          disabled: false,
          href: '#',
        },
        {
          title: 'FORMULIR E.78',
          disabled: true,
          href: '#',
        },
      ]
      this.pageStore.addToPages({
        name: "ReportFormulirE78",
        RpjmdSasaranID_Selected: "",        
      });      
    },
    mounted() {
      this.fetchSasaranRPJMD()
      var RpjmdSasaranID_Selected = this.pageStore.AtributeValueOfPage('ReportFormulirE78', 'RpjmdSasaranID_Selected')
      if(RpjmdSasaranID_Selected.length > 0) {
        this.RpjmdSasaranID = RpjmdSasaranID_Selected;
      }      
    },
    data: () => ({
      breadcrumbs: [],
      btnLoading: false,
      datatableLoading: false,
      //filter form
      daftar_sasaran_rpjmd: [],
      RpjmdSasaranID: null,
      //data table
      datatable: [],
      //pinia
      userStore: null,
      pageStore: null,
    }),
    methods: {
      async fetchSasaranRPJMD() {
        var request_param = {          
          PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,            
        }
        await this.$ajax
          .post('/rpjmd/sasaran', 
            request_param,
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            console.log(data)
          })
      },
    },
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,
    },
  }
</script>