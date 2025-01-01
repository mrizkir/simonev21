<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        LAPORAN FORMULIR E.78 PER TAHUN
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
        :headers="fetchHeader"
        :items="datatable"
        :loading="datatableLoading"        
        item-value="PrgID"
        @update:options="initialize"
        items-per-page-text="Jumlah record per halaman"
        hide-default-footer
        disable-sort
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-autocomplete
              :items="daftar_sasaran_rpjmd"
              density="compact"
              variant="outlined"
              v-model="RpjmdSasaranID"
              label="SASARAN RPJMD"              
              item-title="Nm_RpjmdSasaran"
              item-value="RpjmdSasaranID"
              class="pa-3 mt-4"
              clearable              
            />
          </v-toolbar>
        </template>
        <template v-slot:item="{ index, item }">
          <tr>
            <td>{{ index + 1 }}</td>
            <td>{{ item.Nm_ProgramRPJMD }}</td>
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
  import { usesPageStore } from '@/stores/PageStore'

  export default {
    name: 'ReportFormulirE78PerTahun',
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
          title: 'FORMULIR E.78 PER TAHUN',
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
            let payload = data.payload
            this.daftar_sasaran_rpjmd = payload.data
          })
      },
      async initialize() {  
        if (this.RpjmdSasaranID !== null || typeof  RpjmdSasaranID  !== 'undefined') {
          var request_param = {           
            PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,            
            RpjmdSasaranID: this.RpjmdSasaranID,            
          }

          await this.$ajax
            .post('/rpjmd/report/formulire78', 
              request_param,
              {
                headers: {
                  Authorization: this.userStore.Token,
                },
              }
            )
            .then(({ data }) => {
              let payload = data.payload
              this.datatable = payload.data
              this.datatableLoading = false
            })
            .catch(() => {
              this.datatableLoading = false
            })

        } else {
          this.datatableLoading = false    
        }
      },
    },
    computed: {
      fetchHeader() {
        let headers = [
          {
            title: 'NO',
            align: 'start',
            sortable: false,
            key: 'no',
            width: 50,
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'PROGRAM',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '200px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'INDIKATOR KINERJA',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '100px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'DATA CAPAIAN PADA AWAL TAHUN PERENCANAAN',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'TARGET PADA AKHIR TAHUN PERENCANAAN',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'TARGET RPJMD KAB/KOTA MELALUI PELAKSANAAN RKPD TAHUN KE 2',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'CAPAIAN TARGET RPJMD KAB/KOTA MELALUI PELAKSANAAN RKPD TAHUN KE 2',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'TINGKAT CAPAIAN TARGET RPJMD KAB/KOTA HASIL PELAKSANAAN RKPD TAHUN KE 2',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'CAPAIAN PADA AKHIR TAHUN PERENCANAAN',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'RASIO CAPAIAN AKHIR(%)',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
        ]
        return headers
      },
    },
    watch: {
      RpjmdSasaranID(val) {
        console.log(val)
        var page = this.pageStore.getPage('ReportFormulirE78')        
        if (val.length > 0) {
          this.RpjmdSasaranID = val          
          page.RpjmdSasaranID_Selected = val
          this.pageStore.updatePage(page)
          this.initialize()
        }
      },
    },
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,
    },
  }
</script>