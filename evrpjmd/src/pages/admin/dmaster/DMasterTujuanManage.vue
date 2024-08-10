<template>
  <v-main-layout :token="userStore.AccessToken">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        MISI
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
          Halaman ini digunakan untuk mengelola tujuan.
        </v-alert>
      </template>      
    </v-page-header>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="auto" md="12" lg="12">
          <v-card>
            <v-card-title>
              <v-icon icon="mdi-eye"></v-icon> &nbsp;
              <span class="headline">DATA MISI</span>
            </v-card-title>
            <v-card-text>
              <v-row justify="space-between">
                <v-col cols="auto" md="12" lg="12">
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      ID
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_misi.RpjmdMisiID }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      VISI
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_misi.visi.Nm_RpjmdVisi }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      KODE MISI
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_misi.Kd_RpjmdMisi }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      NAMA MISI
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_misi.Nm_RpjmdMisi }}
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>                
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="auto" md="12" lg="12">
          <v-form ref="frmdata" v-model="form_valid">
            <v-card>
              <v-card-title>
                <v-icon icon="mdi-pencil"></v-icon> &nbsp;
                <span class="headline">TAMBAH TUJUAN</span>
              </v-card-title>
              <v-card-text>
                <v-text-field
                  v-model="formdata.Kd_RpjmdTujuan"                  
                  density="compact"        
                  label="KODE TUJUAN"
                  variant="outlined"
                  prepend-inner-icon="mdi-graph"
                  hint="Masukan kode / nomor tujuan dari rpjmd"
                  :rules="rule_kode_tujuan"
                  auto-grow
                />    
                <v-textarea
                  v-model="formdata.Nm_RpjmdTujuan"
                  rows="1"
                  density="compact"        
                  label="NAMA TUJUAN"
                  variant="outlined"
                  prepend-inner-icon="mdi-graph"
                  hint="Masukan tujuan dari rpjmd"
                  :rules="rule_nama_tujuan"
                  auto-grow
                />
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
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
        </v-col>
      </v-row>
      <v-tujuan-data-table :RpjmdMisiID="RpjmdMisiID" />
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import dataTable from '@/pages/admin/dmaster/DMasterTujuanDataTable.vue'
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'DMasterTujuanManage',
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
          title: 'TUJUAN',    
          href: '/admin/dmaster/tujuan',
        },
        {
          title: 'KELOLA',
          disabled: true,
          href: '#',
        },
      ]
      this.RpjmdMisiID = this.$route.params.RpjmdMisiID;
    },
    mounted() {
      this.fetchMisi()
    },
    data: () => ({
      RpjmdMisiID: null,
      btnLoading: false,
      breadcrumbs: [],
      data_misi: {
        visi: {},
      },
      //form data
      form_valid: true,
      formdata: {
        RpjmdMisiID: null,
        RpjmdMisiID: null,
        PeriodeRPJMDID: null,
        Kd_RpjmdTujuan: null,  
        Nm_RpjmdTujuan: null,  
        created_at: null,
        updated_at: null,
      },
      formdefault: {
        RpjmdMisiID: null,
        RpjmdMisiID: null,
        PeriodeRPJMDID: null,
        Kd_RpjmdTujuan: null,  
        Nm_RpjmdTujuan: null,  
        created_at: null,
        updated_at: null,
      },
      //form rules
      rule_kode_tujuan: [
        value => !!value || 'Mohon untuk di isi nama tujuan dari RPJMD !!!',
      ],
      rule_nama_tujuan: [
        value => !!value || 'Mohon untuk di isi nama tujuan dari RPJMD !!!',
      ],
      //pinia
      userStore: null,
    }),
    methods: {      
      async fetchMisi() {
        await this.$ajax
          .get('/rpjmd/misi/' + this.RpjmdMisiID, {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            this.data_misi = data.payload            
          })
      },
      async save() {
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          this.$ajax
            .post(
              '/rpjmd/tujuan/store',
              {
                RpjmdMisiID: this.RpjmdMisiID,                
                Kd_RpjmdTujuan: this.formdata.Kd_RpjmdTujuan,
                Nm_RpjmdTujuan: this.formdata.Nm_RpjmdTujuan,                  
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
    },
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,
      'v-tujuan-data-table': dataTable,
    }
  }
</script>