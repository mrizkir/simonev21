<template>
  <v-main-layout :token="userStore.Token">
    <template v-slot:leftsidebar>
      <v-sidebar-left />
    </template>
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        Indikator Kinerja
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
          Halaman ini digunakan untuk mengelola indikator kinerja untuk IKK dan IKU.
        </v-alert>
      </template>      
    </v-page-header>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">          
          <v-card>
            <v-card-title class="d-flex align-center pe-2">
              <v-icon icon="mdi-graph"></v-icon> &nbsp;
              DAFTAR INDIKATOR KINERJA
              <v-spacer></v-spacer>
              <v-text-field
                v-model="search"
                density="compact"
                label="Cari Indikator"
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
              :search="search"
              item-value="name"
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
                    <v-card>
                      <v-card-title>
                        <v-icon icon="mdi-pencil"></v-icon> &nbsp;
                        <span class="text-h5">{{ formTitle }}</span>
                      </v-card-title>
                      <v-card-text>
                        <v-textarea
                          v-model="formdata.NamaIndikator"
                          rows="1"
                          density="compact"        
                          label="NAMA INDIKATOR"
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          hint="Masukan indikator kinerja rpjmd"
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
                        >
                          SIMPAN
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </v-toolbar>
              </template>
              <template v-slot:item.actions="{ item }">
                <v-icon                                
                  class="mr-2"
                  v-tooltip:bottom="'Detail Indikator Kinerja'"
                  @click.stop="viewItem(item)"
                  size="small"
                  color="primary"
                >
                  mdi-eye
                </v-icon>
                <v-icon
                  class="mr-2"
                  v-tooltip:bottom="'Ubah Indikator Kinerja'"
                  @click.stop="editItem(item)"
                  size="small"
                  color="primary"
                >
                  mdi-pencil
                </v-icon>
                <v-icon
                  v-tooltip:bottom="'Hapus Indikator Kinerja'"
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
                    More info about {{ item.name }}
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
  import sidebarLeft from '@/layouts/SidebarLeftDMasterAdmin.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import { usesUserStore } from '@/stores/UsersStore'
  const desserts = [
    {
      name: 'Frozen Yogurt',
      calories: 159,
      fat: 6.0,
      carbs: 24,
      protein: 4.0,
      iron: '1',
    },
    {
      name: 'Jelly bean',
      calories: 375,
      fat: 0.0,
      carbs: 94,
      protein: 0.0,
      iron: '0',
    },
    {
      name: 'KitKat',
      calories: 518,
      fat: 26.0,
      carbs: 65,
      protein: 7,
      iron: '6',
    },
    {
      name: 'Eclair',
      calories: 262,
      fat: 16.0,
      carbs: 23,
      protein: 6.0,
      iron: '7',
    },
    {
      name: 'Gingerbread',
      calories: 356,
      fat: 16.0,
      carbs: 49,
      protein: 3.9,
      iron: '16',
    },
    {
      name: 'Ice cream sandwich',
      calories: 237,
      fat: 9.0,
      carbs: 37,
      protein: 4.3,
      iron: '1',
    },
    {
      name: 'Lollipop',
      calories: 392,
      fat: 0.2,
      carbs: 98,
      protein: 0,
      iron: '2',
    },
    {
      name: 'Cupcake',
      calories: 305,
      fat: 3.7,
      carbs: 67,
      protein: 4.3,
      iron: '8',
    },
    {
      name: 'Honeycomb',
      calories: 408,
      fat: 3.2,
      carbs: 87,
      protein: 6.5,
      iron: '45',
    },
    {
      name: 'Donut',
      calories: 452,
      fat: 25.0,
      carbs: 51,
      protein: 4.9,
      iron: '22',
    },
  ]

  const FakeAPI = {
    async fetch ({ page, itemsPerPage, sortBy, search }) {
      return new Promise(resolve => {
        setTimeout(() => {
          const start = (page - 1) * itemsPerPage
          const end = start + itemsPerPage
          const items = desserts.slice().filter(item => {
            if (search.name && !item.name.toLowerCase().includes(search.name.toLowerCase())) {
              return false
            }

            // eslint-disable-next-line sonarjs/prefer-single-boolean-return
            if (search.calories && !(item.calories >= Number(search.calories))) {
              return false
            }

            return true
          })

          if (sortBy.length) {
            const sortKey = sortBy[0].key
            const sortOrder = sortBy[0].order
            items.sort((a, b) => {
              const aValue = a[sortKey]
              const bValue = b[sortKey]
              return sortOrder === 'desc' ? bValue - aValue : aValue - bValue
            })
          }

          const paginated = items.slice(start, end)

          resolve({ items: paginated, total: items.length })
        }, 500)
      })
    },
  }

  export default {
    name: 'DMasterIndikatorKinerja',
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
          title: 'INDIKATOR KINERJA',
          disabled: true,
          href: '#',
        },        
      ]      
    },
    data: () => ({
      breadcrumbs: [],
      btnLoading: true,
      datatableLoading: false,
      //data table
      expanded: [],
      datatable: [],
      itemsPerPage: 5,
      headers: [
        {
          title: 'NO',
          align: 'start',
          sortable: false,
          key: 'name',
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'NAMA INDIKATOR',
          key: 'calories',
          align: 'start',
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'IKU',
          key: 'fat',
          align: 'start',
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'IKK',
          key: 'carbs',
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
      totalRecords: 0,
      name: '',
      calories: '',
      search: '',
      //form data
      form_valid: true,
      form_salin_valid: true,
      daftar_bidang_urusan: [],
      formdata: {
        IndikatorKinerjaID: null,
        NamaIndikator: null,
        is_iku: false,
        is_ikk: false,        
        created_at: null,
        updated_at: null,
      },
      formdefault: {
        IndikatorKinerjaID: null,
        NamaIndikator: null,
        is_iku: false,
        is_ikk: false,        
        created_at: null,
        updated_at: null,
      },
      //dialog
      dialogfrm: false,
      editedIndex: -1,
      //pinia
      userStore: null,
    }),
    methods: {
      async initialize({ page, itemsPerPage, sortBy }) {
        this.datatableLoading = true

        await this.$ajax
          .post(
            "/dmaster/kodefikasi/indikatorkinerja",
            {
              sortBy: sortBy,
              page: page,
              itemsPerPage: itemsPerPage,
            },
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            this.datatable = data.payload;
            this.datatableLoading = false;
          });
        // FakeAPI.fetch({ page, itemsPerPage, sortBy, search: { name: this.name, calories: this.calories } }).then(({ items, total }) => {
        //   this.datatable = items
        //   this.totalRecords = total
          this.datatableLoading = false
        // })
        
      },
      async save() {
        
      },
      closedialogfrm() {
        this.btnLoading = false;
        this.dialogfrm = false;
        setTimeout(() => {
          this.formdata = Object.assign({}, this.formdefault);
          this.editedIndex = -1;
          this.$refs.frmdata.reset();
        }, 300);
      },
    },
    computed: {
      formTitle() {
        return this.editedIndex === -1 ? "TAMBAH INDIKATOR" : "UBAH INDIKATOR";
      },
    },
    watch: {
      name () {
        this.search = String(Date.now())
      },
      calories () {
        this.search = String(Date.now())
      },
    },
    components: {
      'v-main-layout': mainLayout,
      'v-sidebar-left': sidebarLeft,
      'v-page-header': pageHeader,
    }
  }
</script>