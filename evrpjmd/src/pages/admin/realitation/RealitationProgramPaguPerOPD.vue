<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        REALISASI PAGU PROGRAM PER OPD
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
          Halaman ini digunakan untuk mengelola realisasi pagu program RPJMD.  Data program, bidang urusan, urusan, opd penanggungjawab diperoleh dari SIMONEV Tahun Anggaran {{ this.userStore.PeriodeRPJMD.TA_AWAL }}.
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
        <template v-slot:item="{ index, item }">
          <tr class="bg-grey-lighten-5">
            <td>{{ (indexOffset + index) + 1 }}</td>
            <td colspan="12">
              [{{ item.Kd_Urusan }}] {{ item.Nm_Urusan }}
              <br>
              [{{ item.Kd_Urusan + '.' + item.Kd_Bidang }}] {{ item.Nm_Bidang }}
              <br>
              <strong>{{ item.Nm_Program }}</strong>
            </td>
            <td>
              <v-btn
                class="mr-2"
                v-tooltip:bottom="'Tambah Realisasi Pagu'"                
                color="primary"
                variant="outlined"
                prepend-icon="mdi-plus"
                density="compact"
                @click.stop="addItem(item)"                
                :disabled="item.pagu.length > 0"
              >
                Tambah
              </v-btn>
            </td>
          </tr>
          <template v-if="item.pagu.length > 0">
            <template v-for="(pagu, i) in item.pagu" :key="pagu.RpjmdRealisasiIndikatorID">              
              <tr class="text-center">
                <td colspan="2" class="bg-grey">&nbsp;</td>                                
                <td class="bg-blue">{{ $filters.formatUang(pagu.target_2) }}</td>
                <td class="bg-green">{{ $filters.formatUang(pagu.realisasi_2) }}</td>
                <td class="bg-blue">{{ $filters.formatUang(pagu.target_3) }}</td>
                <td class="bg-green">{{ $filters.formatUang(pagu.realisasi_3) }}</td>
                <td class="bg-blue">{{ $filters.formatUang(pagu.target_4) }}</td>
                <td class="bg-green">{{ $filters.formatUang(pagu.realisasi_4) }}</td>
                <td class="bg-blue">{{ $filters.formatUang(pagu.target_5) }}</td>
                <td class="bg-green">{{ $filters.formatUang(pagu.realisasi_5) }}</td>
                <td class="bg-blue">{{ $filters.formatUang(pagu.target_6) }}</td>
                <td class="bg-green">{{ $filters.formatUang(pagu.realisasi_6) }}</td>
                <td class="bg-blue">{{ $filters.formatUang(pagu.target_7) }} / {{ $filters.formatUang(pagu.realisasi_7) }}</td>                
                <td class="text-center">
                  <v-icon
                    class="mr-2"
                    v-tooltip:bottom="'Ubah Pagu'"
                    @click.stop="editItem(item, pagu)"
                    size="small"
                    color="primary"
                  >
                    mdi-pencil
                  </v-icon>
                  <v-icon
                    v-tooltip:bottom="'Hapus Pagu'"
                    @click.stop="deleteItem(pagu)"
                    size="small"
                    color="error"
                  >
                    mdi-delete
                  </v-icon>
                </td>
              </tr>
            </template>
          </template>
          <template v-else>
            <tr class="bg-green-lighten-5">
              <td colspan="15" class="text-center">Belum ada realisasi pagu. Silahkan tambah</td>
            </tr>
          </template>
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
    name: 'RealitationPaguProgramPerOPD',
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
        name: 'RealisasiPaguProgramPerOPD',
        OrgID: '',        
      });
    },
    mounted() {
      this.fetchOPD()
      var OrgID_Selected = this.pageStore.AtributeValueOfPage('RealisasiPaguProgramPerOPD', 'OrgID')
      if(OrgID_Selected.length > 0) {
        this.OrgID = OrgID_Selected;
      }      
    },
    data: () => ({
      btnLoading: false,
      //filter form
      daftar_opd: [],
      OrgID: null,
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
      async initialize() {  
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
            let payload = data.payload
            this.datatable = payload.data
            this.totalRecords = payload.totalRecords
            this.datatableLoading = false
          })
          .catch(() => {
            this.datatableLoading = false
          })
        }
      },
    },
    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'TAMBAH REALISASI PAGU PROGRAM' : 'UBAH REALISASI PAGU PROGRAM';
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
                class: 'font-weight-bold bg-blue',
              },
            },
            {
              title: 'REALISASI',
              value: 'data_' + next_i,
              headerProps: {
                class: 'font-weight-bold bg-green',
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
            title: 'NAMA PROGRAM / PAGU',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '200px',
            headerProps: {
              class: 'font-weight-bold',
            },
          },                    
          {
            title: 'CAPAIAN KINERJA PAGU PROGRAM',
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
            title: "AKSI",
            key: "actions",
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
        var page = this.pageStore.getPage('RealisasiPaguProgramPerOPD')        
        if (val.length > 0) {
          this.OrgID = val          
          page.OrgID = val
          this.pageStore.updatePage(page)
          this.initialize()
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
