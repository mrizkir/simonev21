<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        REALISASI INDIKATOR PROGRAM
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
          <v-toolbar flat>
            <v-autocomplete
              :items="daftar_bidang_urusan"
              density="compact"
              variant="outlined"
              v-model="BidangID"
              label="BIDANG URUSAN"              
              item-title="bidangurusan"
              item-value="BidangID"
              class="pa-3 mt-4"
              clearable              
            />
            <v-dialog
              v-model="dialogfrm"
              max-width="600px"
              persistent
            >
              <v-form ref="frmdata" v-model="form_valid">
                <v-card>
                  <v-card-title>
                    <v-icon icon="mdi-pencil"></v-icon> &nbsp;
                    <span class="headline">{{ formTitle }}</span>
                  </v-card-title>
                  <v-card-text>
                    <v-row tag="dl" class="text-body-2" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        ID PROGRAM
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ dataprogram.PrgID }}
                      </v-col>
                    </v-row>
                    <v-row tag="dl" class="text-body-2" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        URUSAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ dataprogram.Nm_Urusan }}
                      </v-col>
                    </v-row>                    
                    <v-row tag="dl" class="text-body-2" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        BIDANG URUSAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ dataprogram.Nm_Bidang }}
                      </v-col>
                    </v-row>                    
                    <v-row tag="dl" class="text-body-2" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        KODE PROGRAM
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ dataprogram.kode_program }}
                      </v-col>
                    </v-row>
                    <v-row tag="dl" class="text-body-2 mb-3" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        NAMA PROGRAM
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ dataprogram.Nm_Program }}
                      </v-col>
                    </v-row>
                    <hr class="mb-3">
                    <v-select
                      v-model="formdata.IndikatorKinerja"
                      density="compact"
                      :items="daftarindikator"
                      label="DAFTAR INDIKATOR PROGRAM"
                      variant="outlined"
                      prepend-inner-icon="mdi-calendar"
                      class="mr-1"
                      @update:modelValue="indikatorselected"
                      clearable
                      return-object
                      item-value="RpjmdRelasiIndikatorID"
                      item-title="NamaIndikator"
                      :disabled="editedIndex > -1"
                    />                    
                    <v-row tag="dl" class="text-body-2 mb-3" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        SATUAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ formdata.Satuan }}
                      </v-col>
                    </v-row>
                    <v-row tag="dl" class="text-body-2 mb-3" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        OPERASI
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ formdata.Operasi }}
                      </v-col>
                    </v-row>
                    <hr class="mb-3">                                        
                    <v-row no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-1">Target TA {{ labeltahun[1] }}: {{ data_target.data_2 }}</p>
                        <p class="mb-3">Realisasi Indikator TA {{ labeltahun[1] }}:</p>
                        <v-number-input
                          v-model="formdata.data_2"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledrealisasi"                          
                        />    
                      </v-col>                      
                    </v-row>
                    <hr class="mb-3">                    
                    <v-row no-gutters>                      
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-1">Target TA {{ labeltahun[2] }}: {{ data_target.data_3 }}</p>
                        <p class="mb-3">Realisasi Indikator TA {{ labeltahun[2] }}:</p>
                        <v-number-input
                          v-model="formdata.data_3"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledrealisasi"                          
                        />    
                      </v-col>                      
                    </v-row>                    
                    <v-row no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-1">Target TA {{ labeltahun[3] }}: {{ data_target.data_4 }}</p>
                        <p class="mb-3">Realisasi Indikator TA {{ labeltahun[3] }}:</p>
                        <v-number-input
                          v-model="formdata.data_4"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledrealisasi"                          
                        />                            
                      </v-col>                      
                    </v-row>                    
                    <v-row no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-1">Target TA {{ labeltahun[4] }}: {{ data_target.data_5 }}</p>
                        <p class="mb-3">Realisasi Indikator TA {{ labeltahun[4] }}:</p>
                        <v-number-input
                          v-model="formdata.data_5"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledrealisasi"                          
                        />    
                      </v-col>
                    </v-row>                    
                    <v-row no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-1">Target TA {{ labeltahun[5] }}: {{ data_target.data_6 }}</p>
                        <p class="mb-3">Realisasi Indikator TA {{ labeltahun[5] }}:</p>
                        <v-number-input
                          v-model="formdata.data_6"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledrealisasi"                          
                        />                            
                      </v-col>                      
                    </v-row>                         
                    <v-row no-gutters>                                         
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-1">Target AKhir RPJMD: {{ data_target.data_7 }}</p>
                        <p class="mb-3">Realisasi Akhir RPJMD:</p>                    
                        <v-number-input
                          v-model="formdata.data_7"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledrealisasi"                          
                        />                        
                      </v-col>
                    </v-row>               
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                      color="orange-darken-1"
                      @click.stop="closedialogfrm"
                      prepend-icon="mdi-close"
                      rounded="sm"
                    >
                      TUTUP
                    </v-btn>
                    <v-btn
                      color="primary"
                      @click.stop="save"
                      rounded="sm"
                      prepend-icon="mdi-content-save"
                      :disabled="!form_valid || btnLoading"
                    >
                      SIMPAN
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-form>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item="{ index, item }">
          <tr class="bg-grey-lighten-5">
            <td>{{ (indexOffset + index) + 1 }}</td>
            <td colspan="13">
              [{{ item.Kd_Urusan }}] {{ item.Nm_Urusan }}
              <br>
              [{{ item.Kd_Urusan + '.' + item.Kd_Bidang }}] {{ item.Nm_Bidang }}
              <br>
              <strong>[{{ item.kode_program }}] {{ item.Nm_Program }}</strong>
            </td>
            <td>
              <v-btn
                class="mr-2"
                v-tooltip:bottom="'Tambah Realisasi Indikator'"                
                color="primary"
                variant="outlined"
                prepend-icon="mdi-plus"
                density="compact"
                @click.stop="addItem(item)"                
              >
                Tambah
              </v-btn>
            </td>
          </tr>
          <template v-if="item.indikator.length > 0">
            <template v-for="(indikator, i) in item.indikator" :key="indikator.RpjmdRealisasiIndikatorID">
              <tr class="bg-green-lighten-5">
                <td>
                  <v-icon icon="mdi-arrow-right" />
                </td>
                <td colspan="13">{{ indikator.NamaIndikator }}</td>
                <td class="text-center">
                  <v-icon
                    class="mr-2"
                    v-tooltip:bottom="'Ubah Indikator'"
                    @click.stop="editItem(item, indikator)"
                    size="small"
                    color="primary"
                  >
                    mdi-pencil
                  </v-icon>
                  <v-icon
                    v-tooltip:bottom="'Hapus Indikator'"
                    @click.stop="deleteItem(indikator)"
                    size="small"
                    color="error"
                  >
                    mdi-delete
                  </v-icon>
                </td>
              </tr>
              <tr class="text-center">
                <td colspan="2" class="bg-grey">&nbsp;</td>                
                <td class="bg-blue">{{ indikator.Satuan }}</td>                        
                <td class="bg-blue">{{ indikator.target_2 }}</td>
                <td class="bg-blue">{{ indikator.realisasi_2 }}</td>
                <td class="bg-blue">{{ indikator.target_3 }}</td>
                <td class="bg-blue">{{ indikator.realisasi_3 }}</td>
                <td class="bg-blue">{{ indikator.target_4 }}</td>
                <td class="bg-blue">{{ indikator.realisasi_4 }}</td>
                <td class="bg-blue">{{ indikator.target_5 }}</td>
                <td class="bg-blue">{{ indikator.realisasi_5 }}</td>
                <td class="bg-blue">{{ indikator.target_6 }}</td>
                <td class="bg-blue">{{ indikator.realisasi_6 }}</td>
                <td class="bg-blue">{{ indikator.target_7 }} / {{ indikator.realisasi_7 }}</td>                
                <td class="bg-grey">&nbsp;</td>
              </tr>
            </template>
          </template>
          <template v-else>
            <tr class="bg-green-lighten-5">
              <td colspan="15" class="text-center">Belum ada realisasi indikator. Silahkan tambah</td>
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
    name: 'RealitationIndikatorProgram',
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
        name: 'RealisasiIndikatorProgram',
        BidangID_Selected: '',        
      });
    },
    mounted() {
      this.fetchBidangUrusan()
      var BidangID_Selected = this.pageStore.AtributeValueOfPage('RealisasiIndikatorProgram', 'BidangID_Selected')
      if(BidangID_Selected.length > 0) {
        this.BidangID = BidangID_Selected;
      }      
    },
    data: () => ({
      btnLoading: false,
      datatableLoading: false,
      //filter form
      daftar_bidang_urusan: [],
      BidangID: null,
      //data table
      datatable: [],
      itemsPerPage: 10,
      totalRecords: 0,
      indexOffset: 0,
      search: '',      
      //dialog
      dataprogram: {},
      dialogfrm: false,
      //form data
      form_valid: true,
      daftarindikator: [],      
      disabledrealisasi: true,
      data_target: {
        data_2: '-',
        data_3: '-',
        data_4: '-',
        data_5: '-',
        data_6: '-',
        data_7: '-',
      },
      formdata: {
        RpjmdRealisasiIndikatorID: null,
        RpjmdRelasiIndikatorID: null,
        IndikatorKinerjaID: null,
        IndikatorKinerja: null,
        RpjmdCascadingID: null,
        PeriodeRPJMDID: null,
        data_1: null,
        data_2: null,
        data_3: null,
        data_4: null,
        data_5: null,
        data_6: null,
        data_7: null,
        data_8: null,
        data_9: null,
        data_10: null,
        data_11: null,
        data_12: null,        
        Satuan: '-',
        Operasi: '-',
      }, 
      formdefault: {
        RpjmdRealisasiIndikatorID: null,
        RpjmdRelasiIndikatorID: null,
        IndikatorKinerja: null,
        RpjmdCascadingID: null,
        PeriodeRPJMDID: null,
        data_1: null,
        data_2: null,
        data_3: null,
        data_4: null,
        data_5: null,
        data_6: null,
        data_7: null,
        data_8: null,
        data_9: null,
        data_10: null,
        data_11: null,
        data_12: null,
        Satuan: '-',
        Operasi: '-',
      }, 
      labeltahun: [],
      editedIndex: -1,
      //form rules range
      rule_kondisi_awal: [
        value => !!value || 'Mohon untuk diisi nilai kondisi awal !!!',
      ],
      rule_realisasi_indikator: [
        value => !!value || 'Mohon untuk diisi nilai realisasi indikator !!!',
      ],      
      rule_pagu_indikatif: [
        value => !!value || 'Mohon untuk diisi pagu indikatif program !!!',
      ],
      rule_kondisi_akhir: [
        value => !!value || 'Mohon untuk diisi nilai realisasi !!!',
      ],
      //pinia
      userStore: null,
      pageStore: null,
    }),
    methods: {
      async fetchBidangUrusan() {
        await this.$ajax
          .post(
            '/dmaster/kodefikasi/bidangurusan',
            {
              TA: this.userStore.PeriodeRPJMD.TA_AWAL,
              pid: 'evrpjmd',
            },
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            this.daftar_bidang_urusan = data.kodefikasibidangurusan;
          });
      },
      async initialize({ page, itemsPerPage }) {  
        if (this.BidangID !== null || typeof  BidangID  !== 'undefined') {
          this.datatableLoading = true
         
          var request_param = {
            pid: 'realisasiprogram',
            PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,            
          }

          if(itemsPerPage > 0) {
            const offset = (page - 1) * itemsPerPage
            this.indexOffset = offset

            request_param.offset = offset
            request_param.limit = itemsPerPage
          }
          
          await this.$ajax
            .post('/dmaster/kodefikasi/bidangurusan/' + this.BidangID + '/programrpjmd', 
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
              this.totalRecords = payload.totalRecords
              this.datatableLoading = false
            })
            .catch(() => {
              this.datatableLoading = false
            })
        } else {
          this.datatableLoading = false    
        }
      },
      async setLabelTahun() {
        let periode = this.userStore.PeriodeRPJMD;
        let TA_AWAL = periode.TA_AWAL
        let TA_AKHIR = periode.TA_AKHIR

        this.labeltahun.push(TA_AWAL)
        
        for(var tahun = parseInt(TA_AWAL) + 1; tahun <= TA_AKHIR; tahun++) {
          this.labeltahun.push(tahun);   
        }
      },
      async addItem(item) {
        this.btnLoading = true
        this.dialogfrm = true        
        this.dataprogram = item
        
        this.setLabelTahun()
        
        await this.$ajax
          .post('/dmaster/kodefikasi/program/' + item.PrgID + '/indikator',
            {
              'pid': 'array',
            },
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            let payload = data.payload            
            this.daftarindikator = payload
            this.btnLoading = false  
          })
      },
      async editItem(dataprogram, item) {
        this.editedIndex = this.datatable.indexOf(dataprogram)
        this.setLabelTahun()
        this.dataprogram = dataprogram
        
        await this.$ajax
          .post('/rpjmd/indikatorkinerja/program', 
            {
              PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
              Listed: 0,
            },
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            let payload = data.payload
            this.daftarindikator = payload
            
            this.formdata.RpjmdRealisasiIndikatorID = item.RpjmdRealisasiIndikatorID
            this.formdata.Satuan = item.Satuan
            this.formdata.Operasi = item.Operasi

            this.formdata.data_2 = item.realisasi_2
            this.formdata.data_3 = item.realisasi_3
            this.formdata.data_4 = item.realisasi_4
            this.formdata.data_5 = item.realisasi_5
            this.formdata.data_6 = item.realisasi_6
            this.formdata.data_7 = item.realisasi_7
            
            this.formdata.IndikatorKinerja = {
              IndikatorKinerjaID: item.IndikatorKinerjaID,
              NamaIndikator: item.NamaIndikator,
              Satuan: item.Satuan,
              Operasi: item.Operasi,
            }
            
            this.data_target.data_2 = item.target_2
            this.data_target.data_3 = item.target_3
            this.data_target.data_4 = item.target_4
            this.data_target.data_5 = item.target_5
            this.data_target.data_6 = item.target_6
            this.data_target.data_7 = item.target_7

            this.dialogfrm = true
            this.disabledrealisasi = false
            this.btnLoading = false  
          })
      },
      indikatorselected() {
        if(this.formdata.IndikatorKinerja == null || typeof this.formdata.IndikatorKinerja == 'undefined') {
          this.formdata.Satuan = '-'
          this.formdata.Operasi = '-'
          this.data_target = {
            data_2: '-',
            data_3: '-',
            data_4: '-',
            data_5: '-',
            data_6: '-',
            data_7: '-',
          }
          this.disabledrealisasi = true          
        } else {   
          this.formdata.Satuan = this.formdata.IndikatorKinerja.Satuan
          this.formdata.Operasi = this.formdata.IndikatorKinerja.Operasi
          this.data_target.data_2 = this.formdata.IndikatorKinerja.data_2
          this.data_target.data_3 = this.formdata.IndikatorKinerja.data_3
          this.data_target.data_4 = this.formdata.IndikatorKinerja.data_4
          this.data_target.data_5 = this.formdata.IndikatorKinerja.data_5
          this.data_target.data_6 = this.formdata.IndikatorKinerja.data_6
          this.data_target.data_7 = this.formdata.IndikatorKinerja.data_7
          this.disabledrealisasi = false          
        }
      },
      async save() { 
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          
          if (this.editedIndex > -1) {
            this.$ajax
              .post(
                '/rpjmd/realitations/indikatorprogram/' + this.formdata.RpjmdRealisasiIndikatorID,
                {
                  _method: 'PUT',
                  data_2: this.formdata.data_2,
                  data_3: this.formdata.data_3,
                  data_4: this.formdata.data_4,
                  data_5: this.formdata.data_5,
                  data_6: this.formdata.data_6,
                  data_7: this.formdata.data_7,                  
                },
                {
                  headers: {
                    Authorization: this.userStore.Token,
                  },
                }
              )
              .then(() => {
                this.$router.go()
              })
              .catch(() => {
                this.btnLoading = false
              })
          } else {
            var RpjmdRelasiIndikatorID = this.formdata.IndikatorKinerja.RpjmdRelasiIndikatorID          
            var IndikatorKinerjaID = this.formdata.IndikatorKinerja.IndikatorKinerjaID               
            this.$ajax
              .post(
                '/rpjmd/realitations/indikatorprogram/store',
                {
                  RpjmdRelasiIndikatorID: RpjmdRelasiIndikatorID,                
                  RpjmdCascadingID: this.dataprogram.PrgID,
                  IndikatorKinerjaID: IndikatorKinerjaID,
                  PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
                  Operasi: this.formdata.Operasi,                  
                  data_2: this.formdata.data_2,
                  data_3: this.formdata.data_3,
                  data_4: this.formdata.data_4,
                  data_5: this.formdata.data_5,
                  data_6: this.formdata.data_6,
                  data_7: this.formdata.data_7,                  
                },
                {
                  headers: {
                    Authorization: this.userStore.Token,
                  },
                }
              )
              .then(() => {
                this.$router.go()
              })
              .catch(() => {
                this.btnLoading = false
              })
          }
        }
      },  
      deleteItem(item) {
        this.$root.$confirm
          .open(
            'Delete',
            'Apakah Anda ingin menghapus data realisasi dengan ID ' + item.RpjmdRealisasiIndikatorID + ' ?',
            {
              color: 'red',
              width: '400px',
            }
          )
          .then(confirm => {
            if (confirm) {
              this.btnLoading = true
              this.$ajax
                .post(
                  '/rpjmd/realitations/indikatorprogram/' + item.RpjmdRealisasiIndikatorID,
                  {
                    _method: 'DELETE',
                  },
                  {
                    headers: {
                      Authorization: this.userStore.Token,
                    },
                  }
                )
                .then(() => {
                  this.$router.go()
                })
                .catch(() => {
                  this.btnLoading = false
                })
            }
          })
      },
      closedialogfrm() {
        this.btnLoading = false
        setTimeout(() => {
          this.formdata = Object.assign({}, this.formdefault)  
          this.editedIndex = -1        
          this.$refs.frmdata.reset()
          this.dialogfrm = false
        }, 300)
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
      BidangID(val) {
        var page = this.pageStore.getPage('RealisasiIndikatorProgram')        
        if (val.length > 0) {
          this.BidangID = val          
          page.BidangID_Selected = val
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