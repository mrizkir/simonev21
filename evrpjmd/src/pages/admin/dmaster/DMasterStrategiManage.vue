<template>
  <v-main-layout :token="userStore.AccessToken">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        STRATEGI
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
          Halaman ini digunakan untuk mengelola strategi.
        </v-alert>
      </template>      
    </v-page-header>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="auto" md="12" lg="12">
          <v-card>
            <v-card-title>
              <v-icon icon="mdi-eye"></v-icon> &nbsp;
              <span class="headline">DATA TUJUAN</span>
            </v-card-title>
            <v-card-text>
              <v-row justify="space-between">
                <v-col cols="auto" md="12" lg="12">
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      ID
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_sasaran.RpjmdSasaranID }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      MISI
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_sasaran.misi.Kd_RpjmdMisi }}. {{ data_sasaran.misi.Nm_RpjmdMisi }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      KODE TUJUAN
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_sasaran.Kd_RpjmdSasaran }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      NAMA TUJUAN
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_sasaran.Nm_RpjmdSasaran }}
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
                <span class="headline">TAMBAH STRATEGI</span>
              </v-card-title>
              <v-card-text>
                <v-text-field
                  v-model="formdata.Kd_RpjmdStrategi"                  
                  density="compact"        
                  label="KODE STRATEGI"
                  variant="outlined"
                  prepend-inner-icon="mdi-graph"
                  hint="Masukan kode / nomor strategi dari rpjmd"
                  :rules="rule_kode_strategi"
                  auto-grow
                />    
                <v-textarea
                  v-model="formdata.Nm_RpjmdStrategi"
                  rows="1"
                  density="compact"        
                  label="NAMA STRATEGI"
                  variant="outlined"
                  prepend-inner-icon="mdi-graph"
                  hint="Masukan strategi dari rpjmd"
                  :rules="rule_nama_strategi"
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
      <v-strategi-data-table :RpjmdSasaranID="RpjmdSasaranID" />
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import dataTable from '@/pages/admin/dmaster/DMasterStrategiDataTable.vue'
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'DMasterStrategiManage',
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
          title: 'STRATEGI',    
          href: '/admin/dmaster/strategi',
        },
        {
          title: 'KELOLA',
          disabled: true,
          href: '#',
        },
      ]
      this.RpjmdSasaranID = this.$route.params.RpjmdSasaranID;
    },
    mounted() {
      this.fetchSasaran()
    },
    data: () => ({
      RpjmdSasaranID: null,
      btnLoading: false,
      breadcrumbs: [],
      data_sasaran: {
        tujuan: {},
      },
      //form data
      form_valid: true,
      formdata: {
        RpjmdStrategiID: null,
        RpjmdSasaranID: null,
        PeriodeRPJMDID: null,
        Kd_RpjmdStrategi: null,  
        Nm_RpjmdStrategi: null,  
        created_at: null,
        updated_at: null,
      },
      formdefault: {
        RpjmdStrategiID: null,
        RpjmdSasaranID: null,
        PeriodeRPJMDID: null,
        Kd_RpjmdStrategi: null,  
        Nm_RpjmdStrategi: null,  
        created_at: null,
        updated_at: null,
      },
      //form rules
      rule_kode_strategi: [
        value => !!value || 'Mohon untuk di isi nama strategi dari RPJMD !!!',
      ],
      rule_nama_strategi: [
        value => !!value || 'Mohon untuk di isi nama strategi dari RPJMD !!!',
      ],
      //pinia
      userStore: null,
    }),
    methods: {      
      async fetchSasaran() {
        await this.$ajax
          .get('/rpjmd/sasaran/' + this.RpjmdSasaranID, {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            this.data_sasaran = data.payload            
          })
      },
      async save() {
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          this.$ajax
            .post(
              '/rpjmd/strategi/store',
              {
                RpjmdSasaranID: this.RpjmdSasaranID,                
                Kd_RpjmdStrategi: this.formdata.Kd_RpjmdStrategi,
                Nm_RpjmdStrategi: this.formdata.Nm_RpjmdStrategi,                  
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
      'v-strategi-data-table': dataTable,
    }
  }
</script>