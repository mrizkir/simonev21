<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        REALISASI INDIKATOR PROGRAM PER OPD
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
          Halaman ini digunakan untuk mengelola realisasi indikator program RPJMD.  Data program, bidang urusan, urusan, opd penanggungjawab diperoleh dari SIMONEV Tahun Anggaran {{ this.userStore.PeriodeRPJMD.TA_AWAL }}.
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
      <template v-slot:loading>
          <v-skeleton-loader :type="'table-row@' + itemsPerPage"></v-skeleton-loader>
        </template>
        <template v-slot:top>
          <v-autocomplete
            :items="daftar_opd"
            density="compact"
            variant="outlined"
            v-model="OrgID"
            label="OPD / SKPD"              
            item-title="Nm_Organisasi"
            item-value="OrgID"
            class="pa-3 mt-4"
            clearable              
          />
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
  import { VNumberInput } from 'vuetify/labs/VNumberInput'
  export default {
    name: 'RealitationIndikatorProgramPerOPD',
    created() {
      this.userStore = usesUserStore()
      this.pageStore = usesPageStore()
      this.breadcrumbs = [
        {
          title: 'HOME',
          href: '/admin/' + this.userStore.AccessToken,
        },
        {
          title: 'REALISASI',
          disabled: false,
          href: '#',
        },
        {
          title: 'INDIKATOR - PROGRAM',
          disabled: true,
          href: '#',
        },
      ]
      this.pageStore.addToPages({
        name: 'RealisasiIndikatorProgramPerOPD',
        OrgID: '',        
      });
    },
    mounted() {
      this.fetchOPD()
      var OrgID_Selected = this.pageStore.AtributeValueOfPage('RealisasiIndikatorProgramPerOPD', 'OrgID')
      if(OrgID_Selected.length > 0) {
        this.OrgID = OrgID_Selected;
      }      
    },
    data: () => ({
      btnLoading: false,      
      //filter form
      daftar_opd: [],
      OrgID: null,
      //Organisasi
      DataOPD: null,
      //data table
      datatableLoading: false,
      datatableLoaded: true,
      datatable: [],
      itemsPerPage: 10,
      totalRecords: 0,
      indexOffset: 0,
      search: '',
      //pinia
      userStore: null,
      pageStore: null,
    }),
    methods: {
      async fetchOPD() {
        let periode = this.userStore.PeriodeRPJMD;
        let TA_AWAL = periode.TA_AWAL

        await this.$ajax
          .post(
            "/dmaster/opd",
            {
              tahun: TA_AWAL,
            },
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            this.daftar_opd = data.opd;
            this.datatableLoaded = true;
          });
      },
      async initialize({ page, itemsPerPage }) {  
        if (this.OrgID !== null && typeof this.OrgID !== 'undefined') {
          
          this.datatableLoading = true

          await this.$ajax
          .get(
            '/dmaster/opd/' + this.OrgID + '/programrpjmd',
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            console.log(data)
            this.datatableLoaded = false;
          });
        }
      },
    },
    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'TAMBAH REALISASI INDIKATOR PROGRAM' : 'UBAH REALISASI INDIKATOR PROGRAM';
      },
      fetchHeader() {
        let periode = this.userStore.PeriodeRPJMD;
        let TA_AWAL = periode.TA_AWAL
        let TA_AKHIR = periode.TA_AKHIR

        var children_realisasi_tahun = []
        var i = 2        
        var next_i = 3
        for(var tahun = parseInt(TA_AWAL) + 1; tahun <= TA_AKHIR; tahun++) {

          var children = [
            {
              title: 'TARGET',
              value: 'data_' + i,
              headerProps: {
                class: 'font-weight-bold',
              },
            },
            {
              title: 'REALISASI',
              value: 'data_' + next_i,
              headerProps: {
                class: 'font-weight-bold',
              },
            },
          ]          
          children_realisasi_tahun.push({
            title: tahun,
            children: children,
            headerProps: {
              class: 'font-weight-bold',
            },
          });   
          i += 2
          next_i += 2
        }
        
        var headers = [
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
            title: 'NAMA PROGRAM / INDIKATOR',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '200px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },          
          {
            title: 'SATUAN',
            key: 'Nm_RpjmdProgram',
            align: 'start',            
            headerProps: {
              class: 'font-weight-bold',
            },
          },          
          {
            title: 'CAPAIAN KINERJA INDIKATOR PROGRAM',
            align: 'center',
            children: children_realisasi_tahun,
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'AKHIR RPJMD',
            key: 'Nm_RpjmdProgram',
            align: 'center',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'AKSI',
            key: 'actions',
            align: 'center',
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
    watch: {
      OrgID(val) {
        var page = this.pageStore.getPage('RealisasiIndikatorProgramPerOPD')        
        if (val.length > 0) {
          this.OrgID = val          
          page.OrgID_Selected = val
          this.pageStore.updatePage(page)
          this.initialize({page: 1, itemsPerPage: this.itemsPerPage})
        }        
      },
    },
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,
      'v-number-input': VNumberInput,
    },
  }
</script>