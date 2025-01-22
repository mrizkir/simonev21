<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        REALISASI INDIKATOR TUJUAN
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
          Halaman ini digunakan untuk mengelola realisasi indikator tujuan RPJMD.  Data program, bidang urusan, urusan, opd penanggungjawab diperoleh dari SIMONEV Tahun Anggaran {{ this.userStore.PeriodeRPJMD.TA_AWAL }}.
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
        item-value="RpjmdTujuanID"
        @update:options="initialize"
        items-per-page-text="Jumlah record per halaman"
        disable-sort
      >
        <template v-slot:loading>
          <v-skeleton-loader :type="'table-row@' + itemsPerPage"></v-skeleton-loader>
        </template>
        <template v-slot:top>
          <v-toolbar flat>
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
                        ID TUJUAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ datatujuan.RpjmdTujuanID }}
                      </v-col>
                    </v-row>
                    <v-row tag="dl" class="text-body-2" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        KODE TUJUAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ datatujuan.kode_tujuan }}
                      </v-col>
                    </v-row>                    
                    <v-row tag="dl" class="text-body-2 mb-3" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        NAMA TUJUAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ datatujuan.Nm_RpjmdTujuan }}
                      </v-col>
                    </v-row>                    
                    <hr class="mb-3">
                    <v-select
                      v-model="formdata.IndikatorKinerja"
                      density="compact"
                      :items="daftarindikator"
                      label="DAFTAR INDIKATOR TUJUAN"
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
                        <p class="mb-1 text-info" v-if="formdata.Operasi == 'RANGE'">
                          Target RPJMD TA {{ labeltahun[1] }}: {{ data_target.data_2 }} s.d {{ data_target.data_3 }}
                        </p>
                        <p class="mb-1 text-info" v-else>
                          Target RPJMD TA {{ labeltahun[1] }}: {{ data_target.data_2 }}
                        </p>
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
                        <p class="mb-1 text-info" v-if="formdata.Operasi == 'RANGE'">
                          Target RPJMD TA {{ labeltahun[2] }}: {{ data_target.data_4 }} s.d {{ data_target.data_5 }}
                        </p>
                        <p class="mb-1 text-info" v-else>
                          Target RPJMD TA {{ labeltahun[2] }}: {{ data_target.data_3 }}
                        </p>
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
                        <p class="mb-1 text-info" v-if="formdata.Operasi == 'RANGE'">
                          Target RPJMD TA {{ labeltahun[3] }}: {{ data_target.data_5 }} s.d {{ data_target.data_6 }}
                        </p>
                        <p class="mb-1 text-info" v-else>
                          Target RPJMD TA {{ labeltahun[3] }}: {{ data_target.data_4 }}
                        </p>
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
                        <p class="mb-1 text-info" v-if="formdata.Operasi == 'RANGE'">
                          Target RPJMD TA {{ labeltahun[4] }}: {{ data_target.data_7 }} s.d {{ data_target.data_8 }}
                        </p>
                        <p class="mb-1 text-info" v-else>
                          Target RPJMD TA {{ labeltahun[4] }}: {{ data_target.data_5 }}
                        </p>
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
                        <p class="mb-1 text-info" v-if="formdata.Operasi == 'RANGE'">
                          Target RPJMD TA {{ labeltahun[5] }}: {{ data_target.data_9 }} s.d {{ data_target.data_10 }}
                        </p>
                        <p class="mb-1 text-info" v-else>
                          Target RPJMD TA {{ labeltahun[5] }}: {{ data_target.data_6 }}
                        </p>
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
                        <p class="mb-1" v-if="formdata.Operasi == 'RANGE'">
                          Target AKhir RPJMD: {{ data_target.data_11 }} s.d {{ data_target.data_12 }}
                        </p>
                        <p class="mb-1" v-else>
                          Target AKhir RPJMD: {{ data_target.data_7 }}
                        </p>
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
            <td colspan="13">[{{ item.kode_tujuan }}] {{ item.Nm_RpjmdTujuan }}</td>
            <td>
              <v-btn
                class="mr-2"
                v-tooltip:bottom="'Tambah Realisasi Tujuan'"                
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
    name: 'RealitationIndikatorTujuan',
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
          title: 'INDIKATOR - TUJUAN',
          disabled: true,
          href: '#',
        },
      ]      
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
      datatujuan: {},
      dialogfrm: false,
      //form data
      form_valid: true,
      daftarindikator: [],      
      disabledrealisasi: true,
      data_target: {
        Operasi: '-',
        data_2: '-',
        data_3: '-',
        data_4: '-',
        data_5: '-',
        data_6: '-',
        data_7: '-',
        data_8: '-',
        data_9: '-',
        data_10: '-',
        data_11: '-',
        data_12: '-',
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
      async initialize({ page, itemsPerPage }) {        
        this.datatableLoading = true
        
        var request_param = {
          PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
          pid: 'realisasi',
        }

        if(itemsPerPage > 0) {
          const offset = (page - 1) * itemsPerPage
          this.indexOffset = offset

          request_param.offset = offset
          request_param.limit = itemsPerPage
        }
        
        await this.$ajax
          .post('/rpjmd/relations/indikatortujuan', 
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
        this.datatujuan = item
        
        this.setLabelTahun()
        
        await this.$ajax
          .post('/rpjmd/tujuan/' + item.RpjmdTujuanID + '/indikator',
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
      async editItem(datatujuan, item) {
        this.editedIndex = this.datatable.indexOf(datatujuan)
        this.setLabelTahun()
        this.datatujuan = datatujuan
        
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
            this.data_target.data_8 = item.target_8
            this.data_target.data_9 = item.target_9
            this.data_target.data_10 = item.target_10
            this.data_target.data_11 = item.target_11
            this.data_target.data_12 = item.target_12

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
            data_8: '-',
            data_9: '-',
            data_10: '-',
            data_11: '-',
            data_12: '-',
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
          this.data_target.data_8 = this.formdata.IndikatorKinerja.data_8
          this.data_target.data_9 = this.formdata.IndikatorKinerja.data_9
          this.data_target.data_10 = this.formdata.IndikatorKinerja.data_10
          this.data_target.data_11 = this.formdata.IndikatorKinerja.data_11
          this.data_target.data_12 = this.formdata.IndikatorKinerja.data_12
          this.formdata.data_2 = 0
          this.formdata.data_3 = 0
          this.formdata.data_4 = 0
          this.formdata.data_5 = 0
          this.formdata.data_6 = 0
          this.formdata.data_7 = 0
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
                '/rpjmd/realitations/indikatortujuan/store',
                {
                  RpjmdRelasiIndikatorID: RpjmdRelasiIndikatorID,                
                  RpjmdCascadingID: this.datatujuan.RpjmdTujuanID,
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
        return this.editedIndex === -1 ? 'TAMBAH REALISASI INDIKATOR TUJUAN' : 'UBAH REALISASI INDIKATOR TUJUAN';
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
            title: 'NAMA TUJUAN / INDIKATOR',
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
            title: 'CAPAIAN KINERJA INDIKATOR TUJUAN',
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
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,
      'v-number-input': VNumberInput,
    },
  }
</script>