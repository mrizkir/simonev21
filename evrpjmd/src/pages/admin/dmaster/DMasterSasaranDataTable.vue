<template>
  <v-row class="mb-4" no-gutters>
    <v-col cols="12">          
      <v-card>
        <v-card-title class="d-flex align-center pe-2">
          <v-icon icon="mdi-format-list-bulleted"></v-icon> &nbsp;
          DAFTAR SASARAN
          <v-spacer></v-spacer>   
          <v-spacer></v-spacer>
          <v-text-field
            v-model="search"
            density="compact"
            label="Cari Sasaran"
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
          item-value="RpjmdSasaranID"
          @update:options="initialize"
          :expand-on-click="true"
          items-per-page-text="Jumlah record per halaman"
        >
          <template v-slot:loading>
            <v-skeleton-loader :type="'table-row@' + itemsPerPage"></v-skeleton-loader>
          </template>
          <template v-slot:top>
            <v-toolbar flat>                  
              <v-spacer></v-spacer>
              <v-divider class="mx-4" inset vertical></v-divider>
              <v-dialog
                v-model="dialogfrm"
                max-width="600px"
                persistent
              >
                <v-form ref="frmdata" v-model="form_valid">
                  <v-card>
                    <v-card-title>
                      <v-icon icon="mdi-pencil"></v-icon> &nbsp;
                      <span class="headline">UBAH SASARAN</span>
                    </v-card-title>
                    <v-card-text>
                      <v-text-field
                        v-model="formdata.Kd_RpjmdSasaran"                  
                        density="compact"        
                        label="KODE SASARAN"
                        variant="outlined"
                        prepend-inner-icon="mdi-graph"
                        hint="Masukan kode / nomor sasaran dari rpjmd"
                        :rules="rule_kode_sasaran"
                        auto-grow
                      />    
                      <v-textarea
                        v-model="formdata.Nm_RpjmdSasaran"
                        rows="1"
                        density="compact"        
                        label="NAMA SASARAN"
                        variant="outlined"
                        prepend-inner-icon="mdi-graph"
                        hint="Masukan sasaran dari rpjmd"
                        :rules="rule_nama_sasaran"
                        auto-grow
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
          <template v-slot:item.no="{ index }">
            {{ (indexOffset + index) + 1 }}
          </template>
          <template v-slot:item.actions="{ item }">
            <v-btn
              class="mr-2"
              v-tooltip:bottom="'Tambah Strategi'"
              :to="'/admin/dmaster/strategi/' + item.RpjmdSasaranID + '/manage'"
              size="small"
              color="primary"
              variant="text"
              icon="mdi-plus"
              density="compact"
            />
            <v-icon
              class="mr-2"
              v-tooltip:bottom="'Ubah Sasaran'"
              @click.stop="editItem(item)"
              size="small"
              color="primary"
            >
              mdi-pencil
            </v-icon>
            <v-icon
              v-tooltip:bottom="'Hapus Sasaran'"
              @click.stop="deleteItem(item)"
              size="small"
              color="error"
            >
              mdi-delete
            </v-icon>
          </template>
          <template v-slot:expanded-row="{ columns, item }">
            <tr class="bg-grey-lighten-4">
              <td :colspan="columns.length" class="text-center">
                <span class="font-weight-bold">ID: </span> {{ item.RpjmdSasaranID }} 
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
		name: "DMasterSasaranDataTable",
		created() {
      this.userStore = usesUserStore()
    },
    props: {
      RpjmdTujuanID: {
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
          title: 'KODE SASARAN',
          key: 'kode_sasaran',
          align: 'start',
          width: 130,
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'NAMA SASARAN',
          key: 'Nm_RpjmdSasaran',
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
      formdata: {
        RpjmdSasaranID: null,
        RpjmdTujuanID: null,
        PeriodeRPJMDID: null,
        Kd_RpjmdSasaran: null,  
        Nm_RpjmdSasaran: null,  
        created_at: null,
        updated_at: null,
      },
      formdefault: {
        RpjmdSasaranID: null,
        RpjmdTujuanID: null,
        PeriodeRPJMDID: null,
        Kd_RpjmdSasaran: null,  
        Nm_RpjmdSasaran: null,  
        created_at: null,
        updated_at: null,
      },
      //form rules
      rule_kode_sasaran: [
        value => !!value || 'Mohon untuk di isi nama sasaran dari RPJMD !!!',
      ],
      rule_nama_sasaran: [
        value => !!value || 'Mohon untuk di isi nama sasaran dari RPJMD !!!',
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
              'key': 'kode_sasaran',
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

        if (this.RpjmdTujuanID === null || typeof this.RpjmdTujuanID === "undefined") {       
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
              this.datatable = payload.data
              this.totalRecords = payload.totalRecords
              this.datatableLoading = false    
            })
        } else {
          await this.$ajax
            .post(
              '/rpjmd/tujuan/' + this.RpjmdTujuanID + '/sasaran', 
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
      editItem(item) {        
        this.formdata = Object.assign({}, item)
        this.dialogfrm = true        
      },
      async save() {
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          this.$ajax
            .post(
              '/rpjmd/sasaran/' + this.formdata.RpjmdSasaranID,
              {
                _method: 'PUT',
                Kd_RpjmdSasaran: this.formdata.Kd_RpjmdSasaran,
                Nm_RpjmdSasaran: this.formdata.Nm_RpjmdSasaran,                  
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
            'Apakah Anda ingin menghapus data dengan ID ' + item.RpjmdSasaranID + ' ?',
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
                  '/rpjmd/sasaran/' + item.RpjmdSasaranID,
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
    },
    computed: {
      searchTrigger () {
        if (this.search.length >= 3) {
          return this.search
        }
      },
    },
  }
</script>