<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        LAPORAN FORMULIR E.78 PER TAHUN ({{ userStore.TahunSelected }})
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
          Halaman ini digunakan untuk mencetak formulir E.78 per tahun anggaran. Pada pilihan sasaran RPJMD hanya menampilkan sasaran yang memiliki jumlah program lebih dari 1.
          Silahkan merujuk pada Permendagri No. 86 Tahun 2017 Hal. 618
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
        items-per-page="-1"
        itemsLength="-1"
        hide-default-footer
        disable-sort
        class="report-table"
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
            <td colspan="3">{{ item.Nm_ProgramRPJMD }}</td>                        
            <td>{{ $filters.formatUang(item.target_pagu_7) }}</td>
            <td></td>            
            <td>{{ getTargetPagujmdTahunKe(item) == 'N.A' ? 'N.A' : $filters.formatUang(getTargetPagujmdTahunKe(item)) }}</td>
            <td></td>
            <td>{{ getRealisasiPagujmdTahunKe(item) == 'N.A' ? 'N.A' : $filters.formatUang(getRealisasiPagujmdTahunKe(item)) }}</td>
            <td></td>
            <td>{{ item.tingkat_capaian_target_pagu }} %</td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <template v-if="item.indikator_kinerja.length > 0">
            <template v-for="(indikator, i) in item.indikator_kinerja" :key="indikator.RpjmdRelasiIndikatorID">
              <tr class="bg-green-lighten-5">
                <td colspan="1">
                  <v-icon icon="mdi-arrow-right" />
                </td>
                <td>{{ indikator.NamaIndikator }}</td>
                <td>{{ indikator.target_fisik_1 === '-99999' ? 'N.A' : indikator.target_fisik_1 + ' ' + indikator.Satuan }}</td>                
                <td>{{ indikator.target_fisik_7 === '-99999' ? 'N.A' : indikator.target_fisik_7 + ' ' + indikator.Satuan }}</td>
                <td></td>                
                <td>{{ getTargetFisikRpjmdTahunKe(indikator) }}</td>
                <td></td>
                <td>{{ getRealisasiFisikRpjmdTahunKe(indikator) }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>                
                <td></td>                
              </tr>
            </template>
          </template>
        </template>       
      </v-data-table-server>
    </v-container>
    <template v-slot:filtersidebar>
      <v-filter-3 v-on:changeTahunAnggaran="changeTahunAnggaran" ref="filter3" />      
    </template>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import filter3 from '@/components/sidebar/FilterMode3.vue'
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
      this.$refs.filter3.setFirstTimeLoading(this.firstloading);
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
      tahun_anggaran: null,
    }),
    methods: {
      changeTahunAnggaran(ta) {
				this.tahun_anggaran = ta
        console.log(this.tahun_anggaran)
				// var page = this.$store.getters["uiadmin/Page"]("rkpdmurni");
				// page.tahun_anggaran = ta;
				// this.$store.dispatch("uiadmin/updatePage", page);

				// this.initialize();
			},
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
            let sasaran = payload.data

            sasaran.forEach(data_s => {
              if(data_s.jumlah_program > 0) {
                this.daftar_sasaran_rpjmd.push({
                  'RpjmdSasaranID': data_s.RpjmdSasaranID,
                  'Nm_RpjmdSasaran': data_s.Nm_RpjmdSasaran + ' (' + data_s.jumlah_program + ' Program)',
                })
              }
            })
            // 
          })
      },
      async initialize() {  
        if (this.RpjmdSasaranID !== null && typeof this.RpjmdSasaranID !== 'undefined') {
          var request_param = {           
            PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,            
            RpjmdSasaranID: this.RpjmdSasaranID,
            ta: this.userStore.TahunSelected,
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
      getTargetFisikRpjmdTahunKe(item) {
        let tahun_ke = this.userStore.TahunSelected - this.userStore.TahunAwalPeriodeRPMJD
        const value = item['target_fisik_' + tahun_ke]
        return value !== null && value !== undefined && value !== '-99999' ? value + ' ' + item.Satuan : 'N.A'
      },
      getTargetPagujmdTahunKe(item) {
        let tahun_ke = (this.userStore.TahunSelected - this.userStore.TahunAwalPeriodeRPMJD) + 1
        const value = item['target_pagu_' + tahun_ke]        
        return value !== null && value !== undefined ? value : 'N.A'
      },
      getRealisasiFisikRpjmdTahunKe(item) {
        let tahun_ke = this.userStore.TahunSelected - this.userStore.TahunAwalPeriodeRPMJD
        const value = item['realisasi_fisik_' + tahun_ke]
        return value !== null && value !== undefined ? value + ' ' + item.Satuan : 'N.A'
      },
      getRealisasiPagujmdTahunKe(item) {
        let tahun_ke = (this.userStore.TahunSelected - this.userStore.TahunAwalPeriodeRPMJD) + 1
        const value = item['realisasi_pagu_' + tahun_ke]
        return value !== null && value !== undefined ? value : 'N.A'
      },
      calculateTotal(column) {
        let total = 0;
        this.datatable.forEach(item => {
          if (item[column] !== null && item[column] !== undefined && item[column] !== '-99999') {
            total += parseFloat(item[column]);
          }
        });
        return total;
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
            title: 'PROGRAM / INDIKATOR KINERJA',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '240px',
            headerProps: {
              class: 'font-weight-bold border-thin',
            },
          },          
          {
            title: 'DATA CAPAIAN AWAL TAHUN PERENCANAAN ' + this.userStore.TahunAwalPeriodeRPMJD,
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            headerProps: {
              class: 'font-weight-bold border-thin',
            },
          },
          {
            title: 'TARGET AKHIR TAHUN PERENCANAAN ' + this.userStore.TahunAkhirPeriodeRPMJD,
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            children: [
              {
                title: 'K',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
              {
                title: 'Rp. ',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
            ],
            headerProps: {
              class: 'font-weight-bold border-thin',
            },
          },
          {
            title: 'TARGET RPJMD TAHUN KE - ' + (this.userStore.TahunSelected - this.userStore.TahunAwalPeriodeRPMJD),
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            children: [
              {
                title: 'K',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
              {
                title: 'Rp. ',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
            ],
            headerProps: {
              class: 'font-weight-bold border-thin',
            },
          },
          {
            title: 'CAPAIAN TARGET',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            children: [
              {
                title: 'K',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
              {
                title: 'Rp. ',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
            ],
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'TINGKAT CAPAIAN TARGET',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            children: [
              {
                title: 'K',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
              {
                title: 'Rp. ',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
            ],
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'CAPAIAN PADA AKHIR TAHUN PERENCANAAN',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            children: [
              {
                title: 'K',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
              {
                title: 'Rp. ',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
            ],
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'RASIO CAPAIAN AKHIR(%)',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '70px',
            children: [
              {
                title: 'K',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
              {
                title: 'Rp. ',                
                headerProps: {
                  class: 'font-weight-bold border-thin',
                },
              },
            ],
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
      'v-filter-3': filter3,
    },
  }
</script>