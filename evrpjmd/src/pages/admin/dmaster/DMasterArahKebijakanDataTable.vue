<template>
  <v-row class="mb-4" no-gutters>
    <v-col cols="12">          
      <v-card>
        <v-card-title class="d-flex align-center pe-2">
          <v-icon icon="mdi-format-list-bulleted"></v-icon> &nbsp;
          DAFTAR ARAH KEBIJAKAN
          <v-spacer></v-spacer>   
          <v-spacer></v-spacer>
          <v-text-field
            v-model="search"
            density="compact"
            label="Cari Arah Kebijakan"
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
          item-value="RpjmdArahKebijakanID"
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
                      <span class="headline">UBAH ARAH KEBIJAKAN</span>
                    </v-card-title>
                    <v-card-text>
                      <v-text-field
                        v-model="formdata.Kd_RpjmdArahKebijakan"                  
                        density="compact"        
                        label="KODE ARAH KEBIJAKAN"
                        variant="outlined"
                        prepend-inner-icon="mdi-graph"
                        hint="Masukan kode / nomor arah kebijakan dari rpjmd"
                        :rules="rule_kode_arah_kebijakan"
                        auto-grow
                      />    
                      <v-textarea
                        v-model="formdata.Nm_RpjmdArahKebijakan"
                        rows="1"
                        density="compact"        
                        label="NAMA ARAH KEBIJAKAN"
                        variant="outlined"
                        prepend-inner-icon="mdi-graph"
                        hint="Masukan arah kebijakan dari rpjmd"
                        :rules="rule_nama_arah_kebijakan"
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
              v-tooltip:bottom="'Tambah Program'"
              :to="'/admin/relations/programarahkebijakan/' + item.RpjmdArahKebijakanID + '/manage'"
              size="small"
              color="primary"
              variant="text"
              icon="mdi-plus"
              density="compact"
            />
            <v-icon
              class="mr-2"
              v-tooltip:bottom="'Ubah Arah Kebijakan'"
              @click.stop="editItem(item)"
              size="small"
              color="primary"
            >
              mdi-pencil
            </v-icon>
            <v-icon
              v-tooltip:bottom="'Hapus Arah Kebijakan'"
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
                <span class="font-weight-bold">ID: </span> {{ item.RpjmdArahKebijakanID }} 
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
		name: "DMasterArahKebijakanDataTable",
		created() {
      this.userStore = usesUserStore()
    },
    props: {
      RpjmdStrategiID: {
        type: String,
        default: null,
      },
    },
    data: () => ({
      btnLoading: false,
      datatableLoading: false,
      datatableLoaded: true,
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
          title: 'KODE ARAH KEBIJAKAN',
          key: 'kode_arah_kebijakan',
          align: 'start',
          width: 130,
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'NAMA ARAH KEBIJAKAN',
          key: 'Nm_RpjmdArahKebijakan',
          align: 'start',
          headerProps: {
            class: 'font-weight-bold',
          },
        },        
        {
          title: 'JUMLAH PROGRAM',
          key: 'jumlah_program',
          align: 'start',
          headerProps: {
            class: 'font-weight-bold',
          },
          width: 70,
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
        RpjmdArahKebijakanID: null,
        RpjmdStrategiID: null,
        PeriodeRPJMDID: null,
        Kd_RpjmdArahKebijakan: null,  
        Nm_RpjmdArahKebijakan: null,  
        created_at: null,
        updated_at: null,
      },
      formdefault: {
        RpjmdArahKebijakanID: null,
        RpjmdStrategiID: null,
        PeriodeRPJMDID: null,
        Kd_RpjmdArahKebijakan: null,  
        Nm_RpjmdArahKebijakan: null, 
        created_at: null,
        updated_at: null,
      },
      //form rules
      rule_kode_arah_kebijakan: [
        value => !!value || 'Mohon untuk di isi nama arah kebijakan dari RPJMD !!!',
      ],
      rule_nama_arah_kebijakan: [
        value => !!value || 'Mohon untuk di isi nama arah kebijakan dari RPJMD !!!',
      ],
      rule_nama_arah_kebijakan: [
        value => !!value || 'Mohon untuk di isi arah kebijakan arah kebijakan  dari RPJMD !!!',
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
              'key': 'kode_arah_kebijakan',
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

        if (this.RpjmdStrategiID === null || typeof this.RpjmdStrategiID === "undefined") {       
          await this.$ajax
            .post('/rpjmd/arahkebijakan', 
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
              this.datatableLoaded = false
            })
        } else {
          await this.$ajax
            .post(
              '/rpjmd/strategi/' + this.RpjmdStrategiID + '/arahkebijakan', 
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
              '/rpjmd/arahkebijakan/' + this.formdata.RpjmdArahKebijakanID,
              {
                _method: 'PUT',
                Kd_RpjmdArahKebijakan: this.formdata.Kd_RpjmdArahKebijakan,
                Nm_RpjmdArahKebijakan: this.formdata.Nm_RpjmdArahKebijakan,
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
            'Apakah Anda ingin menghapus data dengan ID ' + item.RpjmdArahKebijakanID + ' ?',
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
                  '/rpjmd/arahkebijakan/' + item.RpjmdArahKebijakanID,
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
            "/rpjmd/arahkebijakan/printcascading",
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
      searchTrigger () {
        if (this.search.length >= 3) {
          return this.search
        }
      },
    },
  }
</script>