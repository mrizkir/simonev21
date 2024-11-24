<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        INDIKATOR PROGRAM
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
          Halaman ini digunakan untuk mengelola relasi indikator program RPJMD.  Data program, bidang urusan, urusan, opd penanggungjawab diperoleh dari SIMONEV Tahun Anggaran {{ this.userStore.PeriodeRPJMD.TA_AWAL }}.
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
                    <span class="headline">TAMBAH INDIKATOR PROGRAM</span>
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
                    <v-row class="mb-3" no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <v-alert title="Perhatian" type="info">
                          Nilai N.A (Not Available) diganti dengan -99999.
                        </v-alert>
                      </v-col>
                    </v-row>
                    <hr class="mb-3">
                    <v-autocomplete
                      v-model="formdata.IndikatorKinerja"  
                      label="INDIKATOR"
                      density="compact"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                      hint="Pilih indikator program"
                      :items="daftarindikator"
                      item-value="IndikatorKinerjaID"
                      item-title="NamaIndikator"
                      @update:modelValue="indikatorselected"
                      :disabled="editedIndex > -1"
                      clearable
                      return-object
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
                    <v-number-input
                      v-model="formdata.data_1"  
                      density="compact"
                      :label="'KONDISI KINERJA AWAL RPJMD ' + labeltahun[0]"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                      :disabled="disabledtarget"
                      :rules="rule_kondisi_awal"
                    />                    
                    <v-row no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-3">Target Indikator TA {{ labeltahun[1] }}:</p>
                        <v-number-input
                          v-model="formdata.data_2"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledtarget"
                          :rules="rule_target_indikator"
                        />    
                      </v-col>                      
                    </v-row>
                    <hr class="mb-3">                    
                    <v-row no-gutters>                      
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-3">Target Indikator TA {{ labeltahun[2] }}:</p>
                        <v-number-input
                          v-model="formdata.data_3"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledtarget"
                          :rules="rule_target_indikator"
                        />    
                      </v-col>                      
                    </v-row>                    
                    <v-row no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-3">Target Indikator TA {{ labeltahun[3] }}:</p>
                        <v-number-input
                          v-model="formdata.data_4"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledtarget"
                          :rules="rule_target_indikator"
                        />    
                      </v-col>                      
                    </v-row>                    
                    <v-row no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-3">Target Indikator TA {{ labeltahun[4] }}:</p>
                        <v-number-input
                          v-model="formdata.data_5"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledtarget"
                          :rules="rule_target_indikator"
                        />    
                      </v-col>                      
                    </v-row>                    
                    <v-row no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <p class="mb-3">Target Indikator TA {{ labeltahun[5] }}:</p>
                        <v-number-input
                          v-model="formdata.data_6"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledtarget"
                          :rules="rule_target_indikator"
                        />    
                      </v-col>                      
                    </v-row>
                    <p class="mb-3">Akhir RPJMD:</p>
                    <v-row no-gutters>                      
                      <v-col cols="auto" md="12" lg="12">
                        <v-number-input
                          v-model="formdata.data_7"  
                          density="compact"                          
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          class="mr-1"
                          :disabled="disabledtarget"
                          :rules="rule_kondisi_akhir"
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
            <td colspan="9">
              [{{ item.Kd_Urusan }}] {{ item.Nm_Urusan }}
              <br>
              [{{ item.Kd_Urusan + '.' + item.Kd_Bidang }}] {{ item.Nm_Bidang }}
              <br>
              <strong>[{{ item.kode_program }}] {{ item.Nm_Program }}</strong>
            </td>
            <td>
              <v-btn
                class="mr-2"
                v-tooltip:bottom="'Tambah Target Indikator'"                
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
            <template v-for="(indikator, i) in item.indikator" :key="indikator.RpjmdRelasiIndikatorID">
              <tr class="bg-green-lighten-5">
                <td>
                  <v-icon icon="mdi-arrow-right" />
                </td>
                <td colspan="9">{{ indikator.NamaIndikator }}</td>
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
                <td>{{ indikator.Satuan }}</td>
                <td>{{ indikator.data_1 == '-99999' ? 'n.a' :  indikator.data_1 }}</td>                              
                <td>{{ indikator.data_2 }}</td>                
                <td>{{ indikator.data_3 }}</td>                
                <td>{{ indikator.data_4 }}</td>                
                <td>{{ indikator.data_5 }}</td>                
                <td>{{ indikator.data_6 }}</td>
                <td>{{ indikator.data_7 }}</td>
                <td class="bg-grey">&nbsp;</td>
              </tr>
            </template>
          </template>
          <template v-else>
            <tr class="bg-green-lighten-5">
              <td colspan="16" class="text-center">Belum ada indikator. Silahkan tambah</td>
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
    name: 'RelationProgramIndikator',
    created() {
      this.userStore = usesUserStore()
      this.pageStore = usesPageStore()
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
          title: 'INDIKATOR - PROGRAM',
          disabled: true,
          href: '#',
        },
      ]
      this.pageStore.addToPages({
        name: "RelationProgramIndikator",
        BidangID_Selected: "",        
      });
    },
    mounted() {
      this.fetchBidangUrusan()
      var BidangID_Selected = this.pageStore.AtributeValueOfPage('RelationProgramIndikator', 'BidangID_Selected')
      if(BidangID_Selected.length > 0) {
        this.BidangID = BidangID_Selected;
      }      
    },
    data: () => ({
      breadcrumbs: [],
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
      disabledtarget: true,
      formdata: {
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
      formdefault: {
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
      rule_target_indikator: [
        value => !!value || 'Mohon untuk diisi nilai target indikator !!!',
      ],      
      rule_pagu_indikatif: [
        value => !!value || 'Mohon untuk diisi pagu indikatif program !!!',
      ],
      rule_kondisi_akhir: [
        value => !!value || 'Mohon untuk diisi nilai target !!!',
      ],
      //pinia
      userStore: null,
      pageStore: null,
    }),
    methods: {
      async fetchBidangUrusan() {
        await this.$ajax
          .post(
            "/dmaster/kodefikasi/bidangurusan",
            {
              TA: this.userStore.PeriodeRPJMD.TA_AWAL,
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
            pid: 'relasiprogram',
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
          .post('/rpjmd/indikatorkinerja/program', 
            {
              PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
              Listed: 1,
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

            this.formdata = Object.assign({}, item)
            this.formdata.IndikatorKinerja = {
              IndikatorKinerjaID: item.IndikatorKinerjaID,
              NamaIndikator: item.NamaIndikator,
              Satuan: item.Satuan,
              Operasi: item.Operasi,
            }
            this.dialogfrm = true
            this.disabledtarget = false
            this.btnLoading = false  
          })
      },
      indikatorselected() {
        if(this.formdata.IndikatorKinerja == null || typeof this.formdata.IndikatorKinerja == 'undefined') {
          this.formdata.Satuan = '-'
          this.formdata.Operasi = '-'
          this.disabledtarget = true
        } else {
          this.formdata.Satuan = this.formdata.IndikatorKinerja.Satuan
          this.formdata.Operasi = this.formdata.IndikatorKinerja.Operasi
          this.disabledtarget = false
        }
      },
      async save() {        
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          
          if (this.editedIndex > -1) {
            this.$ajax
              .post(
                '/rpjmd/relations/indikatorprogram/' + this.formdata.RpjmdRelasiIndikatorID,
                {
                  _method: 'PUT',
                  Operasi: this.formdata.Operasi,
                  data_1: this.formdata.data_1,
                  data_2: this.formdata.data_2,
                  data_3: this.formdata.data_3,
                  data_4: this.formdata.data_4,
                  data_5: this.formdata.data_5,
                  data_6: this.formdata.data_6,
                  data_7: this.formdata.data_7,
                  data_8: this.formdata.data_8,
                  data_9: this.formdata.data_9,
                  data_10: this.formdata.data_10,
                  data_11: this.formdata.data_11,
                  data_12: this.formdata.data_12,                  
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
            this.$ajax
              .post(
                '/rpjmd/relations/indikatorprogram/store',
                {
                  IndikatorKinerjaID: this.formdata.IndikatorKinerja.IndikatorKinerjaID,                
                  RpjmdCascadingID: this.dataprogram.PrgID,
                  PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
                  Operasi: this.formdata.Operasi,
                  data_1: this.formdata.data_1,
                  data_2: this.formdata.data_2,
                  data_3: this.formdata.data_3,
                  data_4: this.formdata.data_4,
                  data_5: this.formdata.data_5,
                  data_6: this.formdata.data_6,
                  data_7: this.formdata.data_7,
                  data_8: this.formdata.data_8,
                  data_9: this.formdata.data_9,
                  data_10: this.formdata.data_10,
                  data_11: this.formdata.data_11,
                  data_12: this.formdata.data_12,                  
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
            'Apakah Anda ingin menghapus data dengan ID ' + item.RpjmdRelasiIndikatorID + ' ?',
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
                  '/rpjmd/relations/indikatorprogram/' + item.RpjmdRelasiIndikatorID,
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
      fetchHeader() {
        let periode = this.userStore.PeriodeRPJMD;
        let TA_AWAL = periode.TA_AWAL
        let TA_AKHIR = periode.TA_AKHIR

        var children_target_tahun = []
        var i = 2        
        var next_i = 3
        for(var tahun = parseInt(TA_AWAL) + 1; tahun <= TA_AKHIR; tahun++) {
          children_target_tahun.push({
            title: tahun,            
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
            title: 'KONDISI AWAL ' + TA_AWAL,
            align: 'center',
            value: 'data_1',            
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'CAPAIAN KINERJA PROGRAM',
            align: 'center',
            children: children_target_tahun,
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
      BidangID(val) {
        var page = this.pageStore.getPage('RelationProgramIndikator')        
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