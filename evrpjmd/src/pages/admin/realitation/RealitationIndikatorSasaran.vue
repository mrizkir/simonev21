<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-bullseye
      </template>
      <template v-slot:name>
        REALISASI INDIKATOR SASARAN
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
          Halaman ini digunakan untuk mengelola realisasi indikator sasaran RPJMD.  Data program, bidang urusan, urusan, opd penanggungjawab diperoleh dari SIMONEV Tahun Anggaran {{ this.userStore.PeriodeRPJMD.TA_AWAL }}.
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
        item-value="RpjmdSasaranID"
        @update:options="initialize"
        items-per-page-text="Jumlah record per halaman"
        disable-sort
        class="border-thin"
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
                        ID SASARAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ datasasaran.RpjmdSasaranID }}
                      </v-col>
                    </v-row>
                    <v-row tag="dl" class="text-body-2" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        KODE SASARAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ datasasaran.kode_sasaran }}
                      </v-col>
                    </v-row>                    
                    <v-row tag="dl" class="text-body-2 mb-3" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        NAMA SASARAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ datasasaran.Nm_RpjmdSasaran }}
                      </v-col>
                    </v-row>                    
                    <hr class="mb-3">
                    <v-select
                      v-model="formdata_IndikatorKinerja"
                      density="compact"
                      :items="daftarindikator"
                      label="DAFTAR INDIKATOR SASARAN"
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
                        {{ formdata_Satuan }}
                      </v-col>
                    </v-row>
                    <v-row tag="dl" class="text-body-2 mb-3" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        OPERASI
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ formdata_Operasi }}
                      </v-col>
                    </v-row>
                    <hr class="mb-3">                                        
                    <v-row no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-1 text-info" v-if="formdata_Operasi == 'RANGE'">
                          Target RPJMD TA {{ labeltahun[1] }}: {{ data_target_data_2 }} s.d {{ data_target_data_3 }}
                        </p>
                        <p class="mb-1 text-info" v-else>
                          Target RPJMD TA {{ labeltahun[1] }}: {{ data_target_data_3 }}
                        </p>
                        <p class="mb-3">Realisasi Indikator TA {{ labeltahun[1] }}:</p>
                        <v-number-input
                          v-model="formdata_data_2"  
                          :precision="2"
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
                        <p class="mb-1 text-info" v-if="formdata_Operasi == 'RANGE'">
                          Target RPJMD TA {{ labeltahun[2] }}: {{ data_target_data_4 }} s.d {{ data_target_data_5 }}
                        </p>
                        <p class="mb-1 text-info" v-else>
                          Target RPJMD TA {{ labeltahun[2] }}: {{ data_target_data_4 }}
                        </p>
                        <p class="mb-3">Realisasi Indikator TA {{ labeltahun[2] }}:</p>
                        <v-number-input
                          v-model="formdata_data_3"  
                          :precision="2"
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
                        <p class="mb-1 text-info" v-if="formdata_Operasi == 'RANGE'">
                          Target RPJMD TA {{ labeltahun[3] }}: {{ data_target_data_6 }} s.d {{ data_target_data_7 }}
                        </p>
                        <p class="mb-1 text-info" v-else>
                          Target RPJMD TA {{ labeltahun[3] }}: {{ data_target_data_5 }}
                        </p>
                        <p class="mb-3">Realisasi Indikator TA {{ labeltahun[3] }}:</p>
                        <v-number-input
                          v-model="formdata_data_4"  
                          :precision="2"
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
                        <p class="mb-1 text-info" v-if="formdata_Operasi == 'RANGE'">
                          Target RPJMD TA {{ labeltahun[4] }}: {{ data_target_data_8 }} s.d {{ data_target_data_9 }}
                        </p>
                        <p class="mb-1 text-info" v-else>
                          Target RPJMD TA {{ labeltahun[4] }}: {{ data_target_data_6 }}
                        </p>
                        <p class="mb-3">Realisasi Indikator TA {{ labeltahun[4] }}:</p>
                        <v-number-input
                          v-model="formdata_data_5"  
                          :precision="2"
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
                        <p class="mb-1 text-info" v-if="formdata_Operasi == 'RANGE'">
                          Target RPJMD TA {{ labeltahun[5] }}: {{ data_target_data_10 }} s.d {{ data_target_data_11 }}
                        </p>
                        <p class="mb-1 text-info" v-else>
                          Target RPJMD TA {{ labeltahun[5] }}: {{ data_target_data_7 }}
                        </p>
                        <p class="mb-3">Realisasi Indikator TA {{ labeltahun[5] }}:</p>
                        <v-number-input
                          v-model="formdata_data_6"  
                          :precision="2"
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
                        <p class="mb-1" v-if="formdata_Operasi == 'RANGE'">
                          Target AKhir RPJMD: {{ data_target_data_12 }} s.d {{ data_target_data_13 }}
                        </p>
                        <p class="mb-1" v-else>
                          Target AKhir RPJMD: {{ data_target_data_8 }}
                        </p>
                        <p class="mb-3">Realisasi Akhir RPJMD:</p>                    
                        <v-number-input
                          v-model="formdata_data_7"  
                          :precision="2"
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
          <tr class="bg-grey-lighten-5 border-thin">
            <td>{{ (indexOffset + index) + 1 }}</td>
            <td colspan="13" class="border-thin">[{{ item.kode_sasaran }}] {{ item.Nm_RpjmdSasaran }}</td>
            <td class="border-thin">
              <v-btn
                class="mr-2"
                v-tooltip:bottom="'Tambah Realisasi Sasaran'"                
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
              <tr class="bg-green-lighten-5 border-thin">
                <td class="border-thin">
                  <v-icon icon="mdi-arrow-right" />
                </td>
                <td colspan="13" class="border-thin">{{ indikator.NamaIndikator }}</td>
                <td class="text-center border-thin">
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
                <td class="bg-yellow border-thin">{{ indikator.Satuan }}</td>                        
                <td class="bg-blue border-thin">{{ indikator.target_3 }}</td>
                <td class="bg-green border-thin">{{ indikator.realisasi_2 }}</td>
                <td class="bg-blue border-thin">{{ indikator.target_4 }}</td>
                <td class="bg-green border-thin">{{ indikator.realisasi_3 }}</td>
                <td class="bg-blue border-thin">{{ indikator.target_5 }}</td>
                <td class="bg-gree border-thinn">{{ indikator.realisasi_4 }}</td>
                <td class="bg-blue border-thin">{{ indikator.target_6 }}</td>
                <td class="bg-green border-thin">{{ indikator.realisasi_5 }}</td>
                <td class="bg-blue border-thin">{{ indikator.target_7 }}</td>
                <td class="bg-green border-thin">{{ indikator.realisasi_6 }}</td>
                <td class="bg-lime-lighten-2 border-thin">{{ indikator.target_8 }} / {{ indikator.realisasi_7 }}</td>                
                <td class="bg-grey border-thin">&nbsp;</td>
              </tr>
            </template>
          </template>
          <template v-else>
            <tr class="bg-green-lighten-5 border-thin">
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
  export default {
    name: 'RealitationIndikatorSasaran',
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
          title: 'INDIKATOR - SASARAN',
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
      datasasaran: {},
      dialogfrm: false,
      //form data
      form_valid: true,
      daftarindikator: [],      
      disabledrealisasi: true,
      data_target_Operasi: null,
      data_target_data_2: null,
      data_target_data_3: null,
      data_target_data_4: null,
      data_target_data_5: null,
      data_target_data_6: null,
      data_target_data_7: null,
      data_target_data_8: null,
      data_target_data_9: null,
      data_target_data_10: null,
      data_target_data_11: null,
      data_target_data_12: null,
      formdata_RpjmdRealisasiIndikatorID: null,
      formdata_RpjmdRelasiIndikatorID: null,
      formdata_IndikatorKinerjaID: null,
      formdata_IndikatorKinerja: null,
      formdata_RpjmdCascadingID: null,
      formdata_PeriodeRPJMDID: null,
      formdata_data_1: null,
      formdata_data_2: null,
      formdata_data_3: null,
      formdata_data_4: null,
      formdata_data_5: null,
      formdata_data_6: null,
      formdata_data_7: null,
      formdata_data_8: null,
      formdata_data_9: null,
      formdata_data_10: null,
      formdata_data_11: null,
      formdata_data_12: null,
      formdata_Satuan: null,
      formdata_Operasi: null,
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
          .post('/rpjmd/relations/indikatorsasaran', 
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
        this.datasasaran = item
        
        this.setLabelTahun()
        
        await this.$ajax
          .post('/rpjmd/sasaran/' + item.RpjmdSasaranID + '/indikator',
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
      async editItem(datasasaran, item) {
        this.editedIndex = this.datatable.indexOf(datasasaran)
        this.setLabelTahun()
        this.datasasaran = datasasaran
        
        await this.$ajax
          .post('/rpjmd/sasaran/' + datasasaran.RpjmdSasaranID + '/indikator',
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
            
            this.formdata_RpjmdRealisasiIndikatorID = item.RpjmdRealisasiIndikatorID
            this.formdata_Satuan = item.Satuan
            this.formdata_Operasi = item.Operasi

            this.formdata_data_2 = parseFloat(item.realisasi_2)
            this.formdata_data_3 = parseFloat(item.realisasi_3)
            this.formdata_data_4 = parseFloat(item.realisasi_4)
            this.formdata_data_5 = parseFloat(item.realisasi_5)
            this.formdata_data_6 = parseFloat(item.realisasi_6)
            this.formdata_data_7 = parseFloat(item.realisasi_7)
            
            this.formdata_IndikatorKinerja = {
              IndikatorKinerjaID: item.IndikatorKinerjaID,
              NamaIndikator: item.NamaIndikator,
              Satuan: item.Satuan,
              Operasi: item.Operasi,
            }
            
            this.data_target_data_2 = item.target_2
            this.data_target_data_3 = item.target_3
            this.data_target_data_4 = item.target_4
            this.data_target_data_5 = item.target_5
            this.data_target_data_6 = item.target_6
            this.data_target_data_7 = item.target_7
            this.data_target_data_8 = item.target_8
            this.data_target_data_9 = item.target_9
            this.data_target_data_10 = item.target_10
            this.data_target_data_11 = item.target_11
            this.data_target_data_12 = item.target_12

            this.dialogfrm = true
            this.disabledrealisasi = false
            this.btnLoading = false  
          })
      },
      indikatorselected() {
        if(this.formdata_IndikatorKinerja == null || typeof this.formdata_IndikatorKinerja == 'undefined') {
          this.formdata_Satuan = '-'
          this.formdata_Operasi = '-'
          this.data_target_Operasi = '-'
          this.data_target_data_2 = '-'
          this.data_target_data_3 = '-'
          this.data_target_data_4 = '-'
          this.data_target_data_5 = '-'
          this.data_target_data_6 = '-'
          this.data_target_data_7 = '-'
          this.data_target_data_8 = '-'
          this.data_target_data_9 = '-'
          this.data_target_data_10 = '-'
          this.data_target_data_11 = '-'
          this.data_target_data_12 = '-'
          
          this.disabledrealisasi = true          
        } else {   
          this.formdata_Satuan = this.formdata_IndikatorKinerja.Satuan
          this.formdata_Operasi = this.formdata_IndikatorKinerja.Operasi          
          this.data_target_data_2 = this.formdata_IndikatorKinerja.data_2
          this.data_target_data_3 = this.formdata_IndikatorKinerja.data_3
          this.data_target_data_4 = this.formdata_IndikatorKinerja.data_4
          this.data_target_data_5 = this.formdata_IndikatorKinerja.data_5
          this.data_target_data_6 = this.formdata_IndikatorKinerja.data_6
          this.data_target_data_7 = this.formdata_IndikatorKinerja.data_7
          this.data_target_data_8 = this.formdata_IndikatorKinerja.data_8
          this.data_target_data_9 = this.formdata_IndikatorKinerja.data_9
          this.data_target_data_10 = this.formdata_IndikatorKinerja.data_10
          this.data_target_data_11 = this.formdata_IndikatorKinerja.data_11
          this.data_target_data_12 = this.formdata_IndikatorKinerja.data_12
          this.formdata_data_2 = 0
          this.formdata_data_3 = 0
          this.formdata_data_4 = 0
          this.formdata_data_5 = 0
          this.formdata_data_6 = 0
          this.formdata_data_7 = 0
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
                '/rpjmd/realitations/indikatorprogram/' + this.formdata_RpjmdRealisasiIndikatorID,
                {
                  _method: 'PUT',
                  data_2: this.formdata_data_2,
                  data_3: this.formdata_data_3,
                  data_4: this.formdata_data_4,
                  data_5: this.formdata_data_5,
                  data_6: this.formdata_data_6,
                  data_7: this.formdata_data_7,                  
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
            var RpjmdRelasiIndikatorID = this.formdata_IndikatorKinerja.RpjmdRelasiIndikatorID          
            var IndikatorKinerjaID = this.formdata_IndikatorKinerja.IndikatorKinerjaID               
            this.$ajax
              .post(
                '/rpjmd/realitations/indikatorsasaran/store',
                {
                  RpjmdRelasiIndikatorID: RpjmdRelasiIndikatorID,                
                  RpjmdCascadingID: this.datasasaran.RpjmdSasaranID,
                  IndikatorKinerjaID: IndikatorKinerjaID,
                  PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
                  Operasi: this.formdata_Operasi,                  
                  data_2: this.formdata_data_2,
                  data_3: this.formdata_data_3,
                  data_4: this.formdata_data_4,
                  data_5: this.formdata_data_5,
                  data_6: this.formdata_data_6,
                  data_7: this.formdata_data_7,                  
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
          this.editedIndex = -1        
          this.$refs.frmdata.reset()
          this.dialogfrm = false
        }, 300)
      },
    },
    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'TAMBAH REALISASI INDIKATOR SASARAN' : 'UBAH REALISASI INDIKATOR SASARAN';
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
                class: 'font-weight-bold bg-blue border-thin',
              },
            },
            {
              title: 'REALISASI',
              value: 'data_' + next_i,
              headerProps: {
                class: 'font-weight-bold bg-green border-thin',
              },
            },
          ]          
          children_realisasi_tahun.push({
            title: tahun,
            children: children,
            headerProps: {
              class: 'font-weight-bold border-thin',
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
              class: 'font-weight-bold border-thin',
            },
          },
          {
            title: 'NAMA SASARAN / INDIKATOR',
            key: 'Nm_RpjmdProgram',
            align: 'start',
            width: '200px',
            headerProps: {
              class: 'font-weight-bold border-thin',
            },
          },          
          {
            title: 'SATUAN',
            key: 'Nm_RpjmdProgram',
            align: 'start',            
            headerProps: {
              class: 'font-weight-bold border-thin',
            },
          },          
          {
            title: 'CAPAIAN KINERJA INDIKATOR SASARAN',
            align: 'center',
            children: children_realisasi_tahun,
            headerProps: {
              class: 'font-weight-bold border-thin',
            },
          },
          {
            title: 'AKHIR RPJMD',
            key: 'Nm_RpjmdProgram',
            align: 'center',
            headerProps: {
              class: 'font-weight-bold bg-lime-lighten-2 border-thin',
            },
          },
          {
            title: "AKSI",
            key: "actions",
            align: 'center',
            sortable: false,
            width: 110,
            headerProps: {
              class: 'font-weight-bold border-thin',
            },
          },
        ]
        return headers
      },
    },
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,
    },
  }
</script>