<template>
  <v-main-layout :token="userStore.AccessToken">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        ARAH KEBIJAKAN
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
          Halaman ini digunakan untuk mengelola arah kebijakan.
        </v-alert>
      </template>      
    </v-page-header>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="auto" md="12" lg="12">
          <v-card>
            <v-card-title>
              <v-icon icon="mdi-eye"></v-icon> &nbsp;
              <span class="headline">DATA STRATEGI</span>
            </v-card-title>
            <v-card-text>
              <v-row justify="space-between">
                <v-col cols="auto" md="12" lg="12">
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      ID
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_strategi.RpjmdStrategiID }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      SASARAN
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_strategi.sasaran.Kd_RpjmdSasaran }}. {{ data_strategi.sasaran.Nm_RpjmdSasaran }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      KODE STRATEGI
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_strategi.Kd_RpjmdStrategi }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      NAMA STRATEGI
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_strategi.Nm_RpjmdStrategi }}
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
                <span class="headline">TAMBAH ARAH KEBIJAKAN</span>
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
      <v-arah-kebijakan-data-table :RpjmdStrategiID="RpjmdStrategiID" />
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import dataTable from '@/pages/admin/dmaster/DMasterArahKebijakanDataTable.vue'
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'DMasterArahKebijakanManage',
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
          title: 'ARAH KEBIJAKAN',    
          href: '/admin/dmaster/arahkebijakan',
        },
        {
          title: 'KELOLA',
          disabled: true,
          href: '#',
        },
      ]
      this.RpjmdStrategiID = this.$route.params.RpjmdStrategiID;
    },
    mounted() {
      this.fetchStrategi()
    },
    data: () => ({
      RpjmdStrategiID: null,
      btnLoading: false,
      breadcrumbs: [],
      data_strategi: {
        sasaran: {},
      },
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
      //pinia
      userStore: null,
    }),
    methods: {      
      async fetchStrategi() {
        await this.$ajax
          .get('/rpjmd/strategi/' + this.RpjmdStrategiID, {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            this.data_strategi = data.payload
          })
      },
      async save() {
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          this.$ajax
            .post(
              '/rpjmd/arahkebijakan/store',
              {
                RpjmdStrategiID: this.RpjmdStrategiID,                
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
      'v-arah-kebijakan-data-table': dataTable,
    }
  }
</script>