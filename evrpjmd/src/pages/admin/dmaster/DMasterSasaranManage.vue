<template>
  <v-main-layout :token="userStore.AccessToken">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        SASARAN
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
          Halaman ini digunakan untuk mengelola sasaran.
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
                      {{ data_tujuan.RpjmdTujuanID }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      MISI
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_tujuan.misi.Kd_RpjmdMisi }}. {{ data_tujuan.misi.Nm_RpjmdMisi }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      KODE TUJUAN
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_tujuan.Kd_RpjmdTujuan }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      NAMA TUJUAN
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_tujuan.Nm_RpjmdTujuan }}
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
                <span class="headline">TAMBAH SASARAN</span>
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
      <v-sasaran-data-table :RpjmdTujuanID="RpjmdTujuanID" />
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import dataTable from '@/pages/admin/dmaster/DMasterSasaranDataTable.vue'
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'DMasterSasaranManage',
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
          title: 'SASARAN',    
          href: '/admin/dmaster/sasaran',
        },
        {
          title: 'KELOLA',
          disabled: true,
          href: '#',
        },
      ]
      this.RpjmdTujuanID = this.$route.params.RpjmdTujuanID;
    },
    mounted() {
      this.fetchTujuan()
    },
    data: () => ({
      RpjmdTujuanID: null,
      btnLoading: false,
      breadcrumbs: [],
      data_tujuan: {
        misi: {},
      },
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
      async fetchTujuan() {
        await this.$ajax
          .get('/rpjmd/tujuan/' + this.RpjmdTujuanID, {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            this.data_tujuan = data.payload            
          })
      },
      async save() {
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          this.$ajax
            .post(
              '/rpjmd/sasaran/store',
              {
                RpjmdTujuanID: this.RpjmdTujuanID,                
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
      'v-sasaran-data-table': dataTable,
    }
  }
</script>