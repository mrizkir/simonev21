<template>
  <v-row class="mb-4" no-gutters>
    <v-col cols="12">          
      <v-card>
        <v-card-title class="d-flex align-center pe-2">
          <v-icon icon="mdi-format-list-bulleted"></v-icon> &nbsp;
          {{ datatableTitle }}
          <v-spacer></v-spacer>   
          <v-spacer></v-spacer>
          <v-text-field
            v-model="search"
            density="compact"
            label="Cari Program"
            prepend-inner-icon="mdi-magnify"
            variant="solo-filled"
            flat
            hide-details
            single-line
          />           
          <template v-slot:append>
            <v-btn icon="mdi-dots-vertical"></v-btn>
          </template>
        </v-card-title>
        <v-divider></v-divider>            
        <v-data-table-server
          density="compact"
          v-model:items-per-page="itemsPerPage"            
          :headers="headers"
          :items="datatable"
          :items-length="totalRecords"
          :loading="datatableLoading"
          :search="searchTrigger"
          item-value="ArahKebijakanProgramID"
          @update:options="initialize"
          :expand-on-click="true"
          items-per-page-text="Jumlah record per halaman"
          class="border-thin"
        >
          <template v-slot:loading>
            <v-skeleton-loader :type="'table-row@' + itemsPerPage"></v-skeleton-loader>
          </template>
          <template v-slot:top>
            <v-toolbar flat>                  
              <v-spacer></v-spacer>
              <v-divider class="mx-4" inset vertical></v-divider>
              <v-btn
                color="primary"
                rounded="sm"
                prepend-icon="mdi-printer"
                @click.stop="printcascading"
                :disabled="btnLoading || datatableLoaded"
              >
                Cetak Cascading
              </v-btn>
              <v-dialog
                v-model="dialogfrm"
                max-width="600px"
                persistent
              >
                <v-form ref="frmdata" v-model="form_valid">
                  <v-card>
                    <v-card-title>
                      <v-icon icon="mdi-pencil"></v-icon> &nbsp;
                      <span class="headline">UBAH PROGRAM</span>
                    </v-card-title>
                    <v-card-text>                      
                      <v-text-field
                        v-model="formdata.Kd_ProgramRPJMD"                  
                        density="compact"        
                        label="KODE PROGRAM"
                        variant="outlined"
                        prepend-inner-icon="mdi-graph"
                        hint="Masukan kode / nomor sasaran dari rpjmd"
                        :rules="rule_kode_sasaran"
                        auto-grow
                      />    
                      <v-textarea
                        v-model="formdata.Nm_ProgramRPJMD"
                        rows="1"
                        density="compact"        
                        label="NAMA PROGRAM"
                        variant="outlined"
                        prepend-inner-icon="mdi-graph"
                        hint="Masukan sasaran dari rpjmd"
                        :rules="rule_nama_sasaran"
                        auto-grow
                      />
                      <v-autocomplete
                        :items="daftar_program"
                        density="compact"
                        variant="outlined"
                        prepend-inner-icon="mdi-graph"
                        v-model="formdata.PrgID"
                        label="PROGRAM DI SIMONEV"              
                        item-title="nama_program"
                        item-value="PrgID"
                        :rules="rule_program"
                        clearable                  
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
            <tr class="border-thin">
              <td rowspan="2" class="border-thin">
                {{ (indexOffset + index) + 1 }}
              </td>
              <td colspan="5" class="border-thin">
                <span class="font-weight-bold">Sasaran:</span> <i>{{ (typeof item.Nm_RpjmdSasaran === 'undefined' || item.Nm_RpjmdSasaran === null) ? '-' :  item.Nm_RpjmdSasaran }}</i><br>
                <span class="font-weight-bold">Strategi:</span> <i>{{ (typeof item.Nm_RpjmdStrategi === 'undefined' || item.Nm_RpjmdStrategi === null) ? '-' :  item.Nm_RpjmdStrategi }}</i><br>
                <span class="font-weight-bold">Arah Kebijakan:</span> <i>{{ (typeof item.Nm_RpjmdArahKebijakan === 'undefined' || item.Nm_RpjmdArahKebijakan === null) ? '-' :  item.Nm_RpjmdArahKebijakan }}</i>
              </td>
              <td class="border-thin" rowspan="2">
                <v-icon
                  class="mr-2"
                  v-tooltip:bottom="'Ubah Program'"
                  @click.stop="editItem(item)"
                  size="small"
                  color="primary"
                >
                  mdi-pencil
                </v-icon>
                <v-icon
                  v-tooltip:bottom="'Hapus Program'"
                  @click.stop="deleteItem(item)"
                  size="small"
                  color="error"
                >
                  mdi-delete
                </v-icon>
              </td>
            </tr>
            <tr>
              <td class="border-thin">{{ item.Kd_ProgramRPJMD }}</td>
              <td class="border-thin">{{ item.Nm_ProgramRPJMD }}</td>
              <td class="border-thin">{{ item.Nm_Bidang }}</td>
              <td class="border-thin">{{ item.Nm_Urusan }}</td>
              <td class="border-thin">{{ item.nama_program }}</td>                            
            </tr>
          </template>                    
          <template v-slot:expanded-row="{ columns, item }">
            <tr class="bg-grey-lighten-4">
              <td :colspan="columns.length" class="text-center">
                <span class="font-weight-bold">ID: </span> {{ item.ArahKebijakanProgramID }} 
                <span class="font-weight-bold">UPDATED_AT: </span> {{ $dayjs(item.updated_at).format("DD/MM/YYYY HH:mm") }}
              </td>
            </tr>
          </template>
          <template v-slot:no-data>
            Data belum tersedia
          </template>
        </v-data-table-server>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
  import { usesUserStore } from '@/stores/UsersStore'
  export default {
		name: "RelationProgramArahKebijakanDataTable",
		created() {
      this.userStore = usesUserStore()
    },
    props: {
      RpjmdArahKebijakanID: {
        type: String,
        default: null,
      },
    },
    data: () => ({
      btnLoading: false,
      datatableLoading: false,
      //data table
      datatable: [],
      itemsPerPage: 10,
      totalRecords: 0,
      indexOffset: 0,
      headers: [
        {
          title: 'NO',
          align: 'start',
          sortable: false,
          key: 'no',
          width: 70,
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'KODE PROGRAM RPJMD',
          key: 'Kd_ProgramRPJMD',
          align: 'start',
          width: 130,
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'NAMA PROGRAM RPJMD',
          key: 'Nm_ProgramRPJMD',
          align: 'start',
          headerProps: {
            class: 'font-weight-bold',
          },
        },        
        {
          title: 'NAMA BIDANG DI SIMONEV',
          key: 'Nm_Bidang',
          align: 'start',
          headerProps: {
            class: 'font-weight-bold',
          },
        },        
        {
          title: 'NAMA URUSAN DI SIMONEV',
          key: 'Nm_Urusan',
          align: 'start',
          headerProps: {
            class: 'font-weight-bold',
          },
        },        
        {
          title: 'NAMA PROGRAM DI SIMONEV',
          key: 'nama_program',
          align: 'start',
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
      ],
      search: '',
      //dialog
      dialogfrm: false,
      dialogdetailitem: false,
      //form data
      form_valid: true, 
      daftar_program: [],     
      formdata: {
        ArahKebijakanProgramID: null,
        PeriodeRPJMDID: null,
        RpjmdArahKebijakanID: null,        
        PrgID: null,          
        Kd_ProgramRPJMD: null,          
        Nm_ProgramRPJMD: null,          
        created_at: null,
        updated_at: null,
      },
      formdefault: {
        ArahKebijakanProgramID: null,
        PeriodeRPJMDID: null,
        RpjmdArahKebijakanID: null,        
        PrgID: null,          
        Kd_ProgramRPJMD: null,          
        Nm_ProgramRPJMD: null,          
        created_at: null,
        updated_at: null,
      },
      //form rules
      rule_kode_program: [
        value => !!value || 'Mohon untuk di isi nama sasaran dari RPJMD !!!',
      ],
      rule_nama_program: [
        value => !!value || 'Mohon untuk di isi nama sasaran dari RPJMD !!!',
      ],
      rule_program: [
        value => !!value || 'Mohon untuk dipilih nama program yang berelasi dengan permendagri 90 TAHUN 2019 !!!',
      ],
      //pinia
      userStore: null,
    }),
    methods: {
      async initialize({ page, itemsPerPage, sortBy }) {  
        this.datatableLoading = true
        
        if(sortBy.length == 0) {
          sortBy = [
            {
              'key': 'kode_program',
              'order': 'asc'
            },
          ]
        }
        
        var request_param = {
          PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
          sortBy: sortBy,
          search: this.search,
        }

        if(itemsPerPage > 0) {
          const offset = (page - 1) * itemsPerPage
          this.indexOffset = offset

          request_param.offset = offset
          request_param.limit = itemsPerPage
        }

        if (this.RpjmdArahKebijakanID === null || typeof this.RpjmdArahKebijakanID === "undefined") {       
          await this.$ajax
            .post('/rpjmd/relations/arahkebijakanprogram',
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
          await this.$ajax
            .post(
              '/rpjmd/arahkebijakan/' + this.RpjmdArahKebijakanID + '/program', 
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
        }
      },
      async editItem(item) {        
        await this.$ajax
          .post('/dmaster/kodefikasi/program', 
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
            this.daftar_program = data.kodefikasiprogram
            this.formdata = Object.assign({}, item)
            this.dialogfrm = true        
          })        
      },
      async save() {
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          this.$ajax
            .post(
              '/rpjmd/relations/arahkebijakanprogram/' + this.formdata.ArahKebijakanProgramID,
              {
                _method: 'PUT',
                PrgID: this.formdata.PrgID,
                Kd_ProgramRPJMD: this.formdata.Kd_ProgramRPJMD,          
                Nm_ProgramRPJMD: this.formdata.Nm_ProgramRPJMD,                          
              },
              {
                headers: {
                  Authorization: this.userStore.Token,
                },
              }
            )
            .then(() => {
              this.closedialogfrm()
              this.$router.go()
            })
            .catch(() => {
              this.btnLoading = false
            })
        }
      },
      deleteItem(item) {
        this.$root.$confirm
          .open(
            'Delete',
            'Apakah Anda ingin menghapus data dengan ID ' + item.ArahKebijakanProgramID + ' ?',
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
                  '/rpjmd/relations/arahkebijakanprogram/' + item.ArahKebijakanProgramID,
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
      closedialogdetailitem() {
        this.dialogdetailitem = false;
        setTimeout(() => {
          this.formdata = Object.assign({}, this.formdefault);
          this.editedIndex = -1;
        }, 300);
      },
      async printcascading() {
        this.btnLoading = true
        
        var request_param = {
          PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,          
        }

        await this.$ajax
          .post(
            "/rpjmd/relations/arahkebijakanprogram/printcascading",
            request_param,
            {
              headers: {
                Authorization: this.userStore.Token,
              },
              responseType: "arraybuffer",
            }
          )
          .then(({ data }) => {
            const url = window.URL.createObjectURL(new Blob([data]));
            const link = document.createElement("a");
            link.href = url;

            link.setAttribute("download", "arah_kebijakan_cascading_" + Date.now() + ".xlsx");
            document.body.appendChild(link);
            link.click();
            this.btnLoading = false;
          })
          .catch(() => {
            this.btnLoading = false;
          });      
      },
    },
    computed: {
      datatableTitle() {
        return (this.RpjmdArahKebijakanID === null || typeof this.RpjmdArahKebijakanID === "undefined") ? 'DAFTAR RELASI PROGRAM DENGAN ARAH KEBIJAKAN' : 'DAFTAR PROGRAM UNTUK ARAH KEBIJAKAN INI'
      },
      searchTrigger () {
        if (this.search.length >= 3) {
          return this.search
        }
      },
    },
  }
</script>