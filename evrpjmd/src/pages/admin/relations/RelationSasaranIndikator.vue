<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        INDIKATOR SASARAN
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
          Halaman ini digunakan untuk mengelola relasi indikator sasaran RPJMD.
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
        item-value="RpjmdSasaranID"
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
                    <span class="headline">TAMBAH INDIKATOR SASARAN</span>
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
                    <v-autocomplete
                      v-model="formdata.IndikatorKinerja"  
                      label="INDIKATOR"
                      density="compact"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                      hint="Pilih indikator sasaran"
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
                      :label="'KONDISI AWAL ' + labeltahun[0]"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                      :disabled="disabledtarget"
                      :rules="rule_kondisi_awal"
                    />                    
                    <template v-if="formdata.Operasi == 'RANGE'">
                      <p class="mb-3">Kondisi Awal {{ labeltahun[1] }}:</p>                      
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_2"  
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_3"  
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>                      
                      </v-row>
                      <hr class="mb-3">
                      <p class="mb-3">Target Tahun {{ labeltahun[2] }}:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_4"  
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_5"  
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>                      
                      </v-row>
                      <p class="mb-3">Target Tahun {{ labeltahun[3] }}:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_6"  
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_7"  
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>
                      </v-row>
                      <p class="mb-3">Target Tahun {{ labeltahun[4] }}:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_8"  
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_9"  
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>
                      </v-row>
                      <p class="mb-3">Target Tahun {{ labeltahun[5] }}:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_10"  
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_11"  
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>
                      </v-row>
                      <p class="mb-3">Target Tahun {{ labeltahun[6] }}:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_12"  
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_13"  
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>
                      </v-row>
                      <p class="mb-3">Akhir RPJMD:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_14"  
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata.data_15"  
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>
                      </v-row>
                    </template>
                    <template v-else>
                      <v-row no-gutters>                      
                        <v-col cols="auto" md="12" lg="12">
                          <v-number-input
                            v-model="formdata.data_2"  
                            density="compact"
                            :label="'KONDISI AWAL ' + labeltahun[1]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget"
                            :rules="rule_kondisi_awal"
                          />
                        </v-col>
                      </v-row>
                      <v-row no-gutters>                      
                        <v-col cols="auto" md="12" lg="12">
                          <v-number-input
                            v-model="formdata.data_3"  
                            density="compact"
                            :label="'TARGET TAHUN ' + labeltahun[2]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget"
                            :rules="rule_target"
                          />    
                        </v-col>
                        <v-col cols="auto" md="12" lg="12">
                          <v-number-input
                            v-model="formdata.data_4"  
                            density="compact"
                            :label="'TARGET TAHUN ' + labeltahun[3]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget"
                            :rules="rule_target"
                          />
                        </v-col>                      
                        <v-col cols="auto" md="12" lg="12">
                          <v-number-input
                            v-model="formdata.data_5"  
                            density="compact"
                            :label="'TARGET TAHUN ' + labeltahun[4]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget"
                            :rules="rule_target"
                          />
                        </v-col>
                        <v-col cols="auto" md="12" lg="12">                        
                          <v-number-input
                            v-model="formdata.data_6"  
                            density="compact"
                            :label="'TARGET TAHUN ' + labeltahun[5]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget"
                            :rules="rule_target"
                          />              
                        </v-col>
                        <v-col cols="auto" md="12" lg="12">                        
                          <v-number-input
                            v-model="formdata.data_7"  
                            density="compact"
                            :label="'TARGET TAHUN ' + labeltahun[6]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget"
                            :rules="rule_target"
                          />     
                        </v-col>
                        <v-col cols="auto" md="12" lg="12">                        
                          <v-number-input
                            v-model="formdata.data_8"  
                            density="compact"
                            label="AKHIR RPJMD"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget"
                            :rules="rule_target"
                          />
                        </v-col>
                      </v-row>     
                    </template>
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
            <td colspan="10">[{{ item.kode_sasaran }}] {{ item.Nm_RpjmdSasaran }}</td>
            <td>
              <v-btn
                class="mr-2"
                v-tooltip:bottom="'Tambah Indikator'"                
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
            <tr v-for="(indikator, i) in item.indikator" :key="indikator.RpjmdRelasiIndikatorID" class="bg-green-lighten-5">
              <td>
                <v-icon icon="mdi-arrow-right" />
              </td>
              <td>{{ indikator.NamaIndikator }}</td>
              <td>{{ indikator.Satuan }}</td>
              <td>{{ indikator.data_1 }}</td>
              <template v-if="indikator.Operasi == 'RANGE'">
                <td>{{ indikator.data_2 + ' s.d ' + indikator.data_3 }}</td>
                <td>{{ indikator.data_4 + ' s.d ' + indikator.data_5 }}</td>
                <td>{{ indikator.data_6 + ' s.d ' + indikator.data_7 }}</td>
                <td>{{ indikator.data_8 + ' s.d ' + indikator.data_9 }}</td>
                <td>{{ indikator.data_10 + ' s.d ' + indikator.data_11 }}</td>
                <td>{{ indikator.data_12 + ' s.d ' + indikator.data_13 }}</td>
                <td>{{ indikator.data_14 + ' s.d ' + indikator.data_15 }}</td>
              </template>
              <template v-else>
                <td>{{ indikator.data_2 }}</td>
                <td>{{ indikator.data_3 }}</td>
                <td>{{ indikator.data_4 }}</td>
                <td>{{ indikator.data_5 }}</td>
                <td>{{ indikator.data_6 }}</td>
                <td>{{ indikator.data_7 }}</td>
                <td>{{ indikator.data_8 }}</td>
              </template>
              <td>
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
          </template>
          <template v-else>
            <tr class="bg-green-lighten-5">
              <td colspan="12" class="text-center">Belum ada indikator. Silahkan tambah</td>
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
  import { VNumberInput } from 'vuetify/labs/VNumberInput'
  export default {
    name: 'RelationSasaranIndikator',
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
          title: 'INDIKATOR - SASARAN',
          disabled: true,
          href: '#',
        },
      ]
    },
    data: () => ({
      btnLoading: false,
      datatableLoading: false,
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
        data_13: null,
        data_14: null,
        data_15: null,        
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
        data_13: null,
        data_14: null,
        data_15: null,
        Satuan: '-',
        Operasi: '-',
      }, 
      labeltahun: [],
      editedIndex: -1,
      //form rules range
      rule_kondisi_awal: [
        value => !!value || 'Mohon untuk diisi nilai kondisi awal !!!',
      ],
      rule_range_awal: [
        value => !!value || 'Mohon untuk diisi nilai awal target !!!',
      ],      
      rule_range_akhir: [
        value => !!value || 'Mohon untuk diisi nilai akhir target !!!',
      ],
      rule_target: [
        value => !!value || 'Mohon untuk diisi nilai target !!!',
      ],
      //pinia
      userStore: null,
    }),
    methods: {
      async initialize({ page, itemsPerPage }) {        
        this.datatableLoading = true
        const offset = (page - 1) * itemsPerPage
        this.indexOffset = offset

        await this.$ajax
          .post('/rpjmd/sasaran/indikatorsasaran', 
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
      async setLabelTahun() {
        let periode = this.userStore.PeriodeRPJMD;
        let TA_AWAL = periode.TA_AWAL
        let TA_AKHIR = periode.TA_AKHIR

        this.labeltahun.push(TA_AWAL - 1)
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
          .post('/rpjmd/indikatorkinerja/sasaran', 
            {
              PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
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
          .post('/rpjmd/indikatorkinerja/sasaran', 
            {
              PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
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
                '/rpjmd/relations/indikatorsasaran/' + this.formdata.RpjmdRelasiIndikatorID,
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
                  data_13: this.formdata.data_13,
                  data_14: this.formdata.data_14,
                  data_15: this.formdata.data_15,
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
                '/rpjmd/relations/indikatorsasaran/store',
                {
                  IndikatorKinerjaID: this.formdata.IndikatorKinerja.IndikatorKinerjaID,                
                  RpjmdCascadingID: this.datasasaran.RpjmdSasaranID,
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
                  data_13: this.formdata.data_13,
                  data_14: this.formdata.data_14,
                  data_15: this.formdata.data_15,
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
                  '/rpjmd/relations/indikatorsasaran/' + item.RpjmdRelasiIndikatorID,
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
      searchTrigger () {
        if (this.search.length >= 3) {
          return this.search
        }
      },
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
            width: 50,
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'NAMA SASARAN / INDIKATOR',
            key: 'Nm_RpjmdSasaran',
            align: 'start',
            headerProps: {
              class: 'font-weight-bold',
            },
          },          
          {
            title: 'SATUAN',
            key: 'Nm_RpjmdSasaran',
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
            key: 'Nm_RpjmdSasaran',
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
      'v-number-input': VNumberInput,
    },
  }
</script>