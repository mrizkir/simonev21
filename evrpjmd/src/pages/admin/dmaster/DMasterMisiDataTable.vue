<template>
  <v-row class="mb-4" no-gutters>
    <v-col cols="12">          
      <v-card>
        <v-card-title class="d-flex align-center pe-2">
          <v-icon icon="mdi-graph"></v-icon> &nbsp;
          DAFTAR MISI
          <v-spacer></v-spacer>   
          <v-spacer></v-spacer>
          <v-text-field
            v-model="search"
            density="compact"
            label="Cari Misi"
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
          item-value="RpjmdMisiID"
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

              </v-dialog>
            </v-toolbar>
          </template>
          <template v-slot:item.no="{ index }">
            {{ (indexOffset + index) + 1 }}
          </template>
          <template v-slot:item.actions="{ item }">
            <v-icon
              class="mr-2"
              v-tooltip:bottom="'Ubah Misi'"
              @click.stop="editItem(item)"
              size="small"
              color="primary"
            >
              mdi-pencil
            </v-icon>
            <v-icon
              v-tooltip:bottom="'Hapus Misi'"
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
                <span class="font-weight-bold">ID: </span> {{ item.RpjmdMisiID }} 
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
		name: "DMasterMisiDataTable",
		created() {
      this.userStore = usesUserStore()
    },
    props: {
      RpjmdVisiID: {
        type: String,
        RpjmdVisiID: null,
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
          title: 'KODE MISI',
          key: 'Kd_RpjmdMisi',
          align: 'start',
          width: 130,
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'NAMA MISI',
          key: 'Nm_RpjmdMisi',
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
        RpjmdMisiID: null,
        RpjmdVisiID: null,
        PeriodeRPJMDID: null,
        Kd_RpjmdMisi: null,  
        Nm_RpjmdMisi: null,  
        created_at: null,
        updated_at: null,
      },
      formdefault: {
        RpjmdMisiID: null,
        RpjmdVisiID: null,
        PeriodeRPJMDID: null,
        Kd_RpjmdMisi: null,  
        Nm_RpjmdMisi: null,  
        created_at: null,
        updated_at: null,
      },
      //form rules
      rule_kode_misi: [
        value => !!value || 'Mohon untuk di isi nama misi dari RPJMD !!!',
      ],
      rule_nama_misi: [
        value => !!value || 'Mohon untuk di isi nama misi dari RPJMD !!!',
      ],
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
              'key': 'Kd_RpjmdMisi',
              'order': 'asc'
            },
          ]
        }
        
        if (this.RpjmdVisiID === null || typeof this.RpjmdVisiID === "undefined") {       
          await this.$ajax
            .post('/rpjmd/misi', 
              {
                sortBy: sortBy,
                offset: offset,
                limit: itemsPerPage,
                search: this.search,
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
        } else {
          await this.$ajax
            .post(
              '/rpjmd/visi/' + this.RpjmdVisiID + '/misi', 
              {
                sortBy: sortBy,
                offset: offset,
                limit: itemsPerPage,
                search: this.search,
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
        }
      },
      editItem(item) {        
        this.formdata = Object.assign({}, item)
        this.dialogfrm = true        
      },
      deleteItem(item) {
        this.$root.$confirm
          .open(
            'Delete',
            'Apakah Anda ingin menghapus data dengan ID ' + item.RpjmdMisiID + ' ?',
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
                  '/rpjmd/misi/' + item.RpjmdMisiID,
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