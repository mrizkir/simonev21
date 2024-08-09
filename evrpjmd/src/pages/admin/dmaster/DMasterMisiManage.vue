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
          Halaman ini digunakan untuk mengelola misi.
        </v-alert>
      </template>      
    </v-page-header>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="auto" md="12" lg="12">
          <v-card>
            <v-card-title>
              <v-icon icon="mdi-eye"></v-icon> &nbsp;
              <span class="headline">DATA VISI</span>
            </v-card-title>
            <v-card-text>
              <v-row justify="space-between">
                <v-col cols="auto" md="12" lg="12">
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      ID
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_visi.RpjmdVisiID }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      NAMA VISI
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_visi.Nm_RpjmdVisi }}
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
                <span class="headline">TAMBAH MISI</span>
              </v-card-title>
              <v-card-text>
                <v-text-field
                  v-model="formdata.Kd_RpjmdMisi"                  
                  density="compact"        
                  label="KODE MISI"
                  variant="outlined"
                  prepend-inner-icon="mdi-graph"
                  hint="Masukan kode / nomor misi dari rpjmd"
                  :rules="rule_kode_misi"
                  auto-grow
                />    
                <v-textarea
                  v-model="formdata.Nm_RpjmdMisi"
                  rows="1"
                  density="compact"        
                  label="NAMA VISI"
                  variant="outlined"
                  prepend-inner-icon="mdi-graph"
                  hint="Masukan misi dari rpjmd"
                  :rules="rule_nama_misi"
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
      <v-misi-data-table :RpjmdVisiID="RpjmdVisiID" />
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import dataTable from '@/pages/admin/dmaster/DMasterMisiDataTable.vue'
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'DMasterMisiManage',
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
          title: 'MISI',    
          href: '/admin/dmaster/misi',
        },
        {
          title: 'KELOLA',
          disabled: true,
          href: '#',
        },
      ]
      this.RpjmdVisiID = this.$route.params.RpjmdVisiID;
    },
    mounted() {
      this.fetchVisi()
    },
    data: () => ({
      RpjmdVisiID: null,
      btnLoading: false,
      breadcrumbs: [],
      data_visi: {},
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
      async fetchVisi() {
        await this.$ajax
          .get('/rpjmd/visi/' + this.RpjmdVisiID, {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            this.data_visi = data.payload            
          })
      },
      async save() {
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          this.$ajax
            .post(
              '/rpjmd/misi/store',
              {
                RpjmdVisiID: this.RpjmdVisiID,                
                Kd_RpjmdMisi: this.formdata.Kd_RpjmdMisi,
                Nm_RpjmdMisi: this.formdata.Nm_RpjmdMisi,                  
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
      'v-misi-data-table': dataTable,
    }
  }
</script>