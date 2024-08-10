<template>
  <v-main-layout :token="userStore.AccessToken">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        VISI
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
          Halaman ini digunakan untuk mengelola visi.
        </v-alert>
      </template>      
    </v-page-header>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">          
          <v-card>
            <v-card-title class="d-flex align-center pe-2">
              <v-icon icon="mdi-graph"></v-icon> &nbsp;
              DAFTAR VISI
              <v-spacer></v-spacer>              
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
              item-value="RpjmdVisiID"
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
                    <template v-slot:activator="{ props }">
                      <v-btn
                        v-bind="props"
                        color="primary"
                        rounded="sm"
                        prepend-icon="mdi-plus"
                      >
                        Tambah
                      </v-btn>
                    </template>
                    <v-form ref="frmdata" v-model="form_valid">
                      <v-card>
                        <v-card-title>
                          <v-icon icon="mdi-pencil"></v-icon> &nbsp;
                          <span class="headline">{{ formTitle }}</span>
                        </v-card-title>
                        <v-card-text>
                          <v-textarea
                            v-model="formdata.Nm_RpjmdVisi"
                            rows="1"
                            density="compact"        
                            label="NAMA VISI"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            hint="Masukan visi dalam rpjmd"
                            :rules="rule_nama_visi"
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
                  <v-dialog
                    v-model="dialogdetailitem"
                    max-width="700px"
                    persistent
                  >
                    <v-card>
                      <v-card-title>
                        <v-icon icon="mdi-eye"></v-icon> &nbsp;
                        <span class="headline">DETAIL DATA</span>
                      </v-card-title>
                      <v-card-text>
                        <v-row justify="space-between">
                          <v-col cols="auto" md="6" lg="6">
                            <v-row tag="dl" class="text-body-2" no-gutters>
                              <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                                ID
                              </v-col>
                              <v-col cols="auto" md="12" lg="12" tag="dt">
                                {{ formdata.RpjmdVisiID }}
                              </v-col>
                            </v-row>
                            <v-row tag="dl" class="text-body-2" no-gutters>
                              <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                                NAMA VISI
                              </v-col>
                              <v-col cols="auto" md="12" lg="12" tag="dt">
                                {{ formdata.Nm_RpjmdVisi }}
                              </v-col>
                            </v-row>
                          </v-col>
                        </v-row>                        
                      </v-card-text>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                          color="orange-darken-1"
                          @click.stop="closedialogdetailitem"
                          prepend-icon="mdi-close"
                          rounded="sm"
                        >
                          TUTUP
                        </v-btn>
                      </v-card-actions>                      
                    </v-card>
                  </v-dialog>
                </v-toolbar>
              </template>
              <template v-slot:item.no="{ index }">
                {{ (indexOffset + index) + 1 }}
              </template>              
              <template v-slot:item.actions="{ item }">
                <v-btn
                  class="mr-2"
                  v-tooltip:bottom="'Tambah Misi'"
                  :to="'/admin/dmaster/misi/' + item.RpjmdVisiID + '/manage'"
                  size="small"
                  color="primary"
                  variant="text"
                  icon="mdi-plus"
                  density="compact"
                />
                <v-icon                                
                  class="mr-2"
                  v-tooltip:bottom="'Detail Visi'"
                  @click.stop="viewItem(item)"
                  size="small"
                  color="primary"
                >
                  mdi-eye
                </v-icon>
                <v-icon
                  class="mr-2"
                  v-tooltip:bottom="'Ubah Visi'"
                  @click.stop="editItem(item)"
                  size="small"
                  color="primary"
                >
                  mdi-pencil
                </v-icon>
                <v-icon
                  v-tooltip:bottom="'Hapus Visi'"
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
                    <span class="font-weight-bold">ID: </span> {{ item.RpjmdVisiID }} 
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
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'DMasterVisi',
    created() {
      this.userStore = usesUserStore()
      this.breadcrumbs = [
        {
          title: 'HOME',
          href: '/admin/' + this.userStore.AccessToken,
        },
        {
          title: 'DATA MASTER',
          disabled: false,
          href: '#',
        },
        {
          title: 'VISI',
          disabled: true,
          href: '#',
        },
      ]
    },
    data: () => ({
      breadcrumbs: [],
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
          title: 'NAMA',
          key: 'Nm_RpjmdVisi',
          align: 'start',
          headerProps: {
            class: 'font-weight-bold',
          },
        },                
        {
          title: 'AKSI',
          key: 'actions',
          sortable: false,
          width: 130,
          headerProps: {
            class: 'font-weight-bold',
          },
        },
      ],
      //dialog
      dialogfrm: false,
      dialogdetailitem: false,
      //form data
      form_valid: true,      
      formdata: {
        RpjmdVisiID: null,
        PeriodeRPJMDID: null,
        Nm_RpjmdVisi: null,        
        TA_AWAL: null,
        TA_AKHIR: null,
        created_at: null,
        updated_at: null,
      },
      formdefault: {
        RpjmdVisiID: null,
        PeriodeRPJMDID: null,
        Nm_RpjmdVisi: null,        
        TA_AWAL: null,
        TA_AKHIR: null,
        created_at: null,
        updated_at: null,
      },
      //form rules
      rule_nama_visi: [
        value => !!value || 'Mohon untuk di isi nama visi dari RPJMD !!!',
      ],      
      editedIndex: -1,
      //pinia
      userStore: null,
    }),
    methods: {
      async initialize({ page, itemsPerPage, sortBy }) {
        this.datatableLoading = true
        const offset = (page - 1) * itemsPerPage
        this.indexOffset = offset

        if(sortBy.length == 0) {
          sortBy = [
            {
              'key': 'Nm_RpjmdVisi',
              'order': 'asc'
            },
          ]
        }

        await this.$ajax
          .post(
            '/rpjmd/visi',
            {
              PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
              sortBy: sortBy,
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
      viewItem(item) {
        this.formdata = item
        this.dialogdetailitem = true
      },
      editItem(item) {
        this.editedIndex = this.datatable.indexOf(item)
        this.formdata = Object.assign({}, item)
        this.dialogfrm = true        
      },
      async save() {
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          if (this.editedIndex > -1) {
            this.$ajax
              .post(
                '/rpjmd/visi/' + this.formdata.RpjmdVisiID,
                {
                  _method: 'PUT',
                  PeriodeRPJMDID: this.formdata.PeriodeRPJMDID,
                  Nm_RpjmdVisi: this.formdata.Nm_RpjmdVisi,                  
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
          } else {
            this.$ajax
              .post(
                '/rpjmd/visi/store',
                {
                  PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
                  Nm_RpjmdVisi: this.formdata.Nm_RpjmdVisi,                  
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
          this.closedialogfrm()
        }        
      },
      deleteItem(item) {
        this.$root.$confirm
          .open(
            'Delete',
            'Apakah Anda ingin menghapus data dengan ID ' + item.RpjmdVisiID + ' ?',
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
                  '/rpjmd/visi/' + item.RpjmdVisiID,
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
                  this.closedialogfrm()
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
      formTitle() {
        return this.editedIndex === -1 ? 'TAMBAH VISI' : 'UBAH VISI'
      },      
    },    
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,
    }
  }
</script>