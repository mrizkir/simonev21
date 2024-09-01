<template>
  <v-main-layout :token="userStore.AccessToken">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        PROGRAM STRATEGI
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
          Halaman ini digunakan untuk mengelola strategi RPJMD dengan program.
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
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      ARAH KEBIJAKAN
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_strategi.Nm_RpjmdArahKebijakan }}
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
                <span class="headline">TAMBAH PROGRAM</span>
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
                <v-textarea
                  v-model="formdata.Nm_RpjmdArahKebijakan"
                  rows="1"
                  density="compact"        
                  label="ARAH KEBIJAKAN"
                  variant="outlined"
                  prepend-inner-icon="mdi-graph"
                  hint="Masukan arah kebijakan strategi dari rpjmd"
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
      <v-strategi-data-table :RpjmdStrategiID="RpjmdStrategiID" />
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import dataTable from '@/pages/admin/relations/RelationProgramStrategiDataTable.vue'
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'RelationProgramStrategiManage',
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
        tujuan: {},
      },
      //form data
      form_valid: true,
      formdata: {
        RpjmdStrategiID: null,        
      },
      formdefault: {
        RpjmdStrategiID: null,        
      },
      //form rules
      rule_kode_strategi: [
        value => !!value || 'Mohon untuk di isi nama strategi dari RPJMD !!!',
      ],
      rule_nama_strategi: [
        value => !!value || 'Mohon untuk di isi nama strategi dari RPJMD !!!',
      ],
      rule_nama_arah_kebijakan: [
        value => !!value || 'Mohon untuk di isi arah kebijakan strategi  dari RPJMD !!!',
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
              '/rpjmd/strategi/store',
              {
                RpjmdStrategiID: this.RpjmdStrategiID,
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