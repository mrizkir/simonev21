<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        INDIKATOR TUJUAN
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
          Halaman ini digunakan untuk mengelola relasi indikator tujuan RPJMD.
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
                    <span class="headline">TAMBAH INDIKATOR TUJUAN</span>
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
                    <v-autocomplete
                      v-model="formdata.IndikatorKinerjaID"  
                      label="INDIKATOR"
                      density="compact"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                      hint="Pilih indikator tujuan"
                      :items="daftarindikator"
                      item-value="IndikatorKinerjaID"
                      item-title="NamaIndikator"
                      clearable
                    />
                    <v-number-input
                      v-model="formdata.data_1"  
                      density="compact"
                      :label="'KONDISI AWAL ' + labeltahun[0]"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                    />
                    <v-number-input
                      v-model="formdata.data_2"  
                      density="compact"
                      :label="'KONDISI AWAL ' + labeltahun[1]"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                    />
                    <v-number-input
                      v-model="formdata.data_3"  
                      density="compact"
                      :label="'TARGET TAHUN ' + labeltahun[2]"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                    />
                    <v-number-input
                      v-model="formdata.data_4"  
                      density="compact"
                      :label="'TARGET TAHUN ' + labeltahun[3]"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                    />
                    <v-number-input
                      v-model="formdata.data_5"  
                      density="compact"
                      :label="'TARGET TAHUN ' + labeltahun[4]"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                    />
                    <v-number-input
                      v-model="formdata.data_6"  
                      density="compact"
                      :label="'TARGET TAHUN ' + labeltahun[5]"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                    />                    
                    <v-number-input
                      v-model="formdata.data_7"  
                      density="compact"
                      :label="'TARGET TAHUN ' + labeltahun[6]"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                    />                    
                    <v-number-input
                      v-model="formdata.data_8"  
                      density="compact"
                      label="AKHIR RPJMD"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                    />
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
            <td colspan="10">{{ item.Nm_RpjmdTujuan }}</td>
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
              <td>{{ indikator.data_2 }}</td>
              <td>{{ indikator.data_3 }}</td>
              <td>{{ indikator.data_4 }}</td>
              <td>{{ indikator.data_5 }}</td>
              <td>{{ indikator.data_6 }}</td>
              <td>{{ indikator.data_7 }}</td>
              <td>{{ indikator.data_8 }}</td>
              <td>
                <v-icon
                  class="mr-2"
                  v-tooltip:bottom="'Ubah Indikator'"
                  @click.stop="editItem(indikator)"
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
            <tr>
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
    name: 'RelationTujuanIndikator',
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
          title: 'INDIKATOR - TUJUAN',
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
      datatujuan: {},
      dialogfrm: false,
      //form data
      form_valid: true,
      daftarindikator: [],
      formdata: {
        IndikatorKinerjaID: null,
        RpjmdCascadingID: null,
        PeriodeRPJMDID: null,
        data_1: 0,
        data_2: 0,
        data_3: 0,
        data_4: 0,
        data_5: 0,
        data_6: 0,
        data_7: 0,
        data_8: 0,
      }, 
      formdefault: {
        IndikatorKinerjaID: null,
        RpjmdCascadingID: null,
        PeriodeRPJMDID: null,
        data_1: 0,
        data_2: 0,
        data_3: 0,
        data_4: 0,
        data_5: 0,
        data_6: 0,
        data_7: 0,
        data_8: 0,
      }, 
      labeltahun: [],
    }),
    methods: {
      async initialize({ page, itemsPerPage }) {        
        this.datatableLoading = true
        const offset = (page - 1) * itemsPerPage
        this.indexOffset = offset

        await this.$ajax
          .post('/rpjmd/tujuan/indikatortujuan', 
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
      async addItem(item) {
        this.btnLoading = true
        this.dialogfrm = true        
        this.datatujuan = item

        let periode = this.userStore.PeriodeRPJMD;
        let TA_AWAL = periode.TA_AWAL
        let TA_AKHIR = periode.TA_AKHIR

        this.labeltahun.push(TA_AWAL - 1)        
        this.labeltahun.push(TA_AWAL)        
        
        for(var tahun = parseInt(TA_AWAL) + 1; tahun <= TA_AKHIR; tahun++) {
          this.labeltahun.push(tahun);   
        }

        await this.$ajax
          .post('/rpjmd/indikatorkinerja/tujuan', 
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
      async save() {        
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true

          this.$ajax
            .post(
              '/rpjmd/relations/indikatortujuan/store',
              {
                IndikatorKinerjaID: this.formdata.IndikatorKinerjaID,                
                RpjmdCascadingID: this.datatujuan.RpjmdTujuanID,
                PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
                data_1: this.formdata.data_1,                  
                data_2: this.formdata.data_2,                  
                data_3: this.formdata.data_3,                  
                data_4: this.formdata.data_4,                  
                data_5: this.formdata.data_5,                  
                data_6: this.formdata.data_6,                  
                data_7: this.formdata.data_7,                  
                data_8: this.formdata.data_8,                  
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
      },      
      closedialogfrm() {
        this.btnLoading = false
        setTimeout(() => {
          this.formdata = Object.assign({}, this.formdefault)          
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
            title: 'NAMA TUJUAN / INDIKATOR',
            key: 'Nm_RpjmdTujuan',
            align: 'start',
            headerProps: {
              class: 'font-weight-bold',
            },
          },          
          {
            title: 'SATUAN',
            key: 'Nm_RpjmdTujuan',
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
            key: 'Nm_RpjmdTujuan',
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
    }
  }
</script>