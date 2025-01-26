<template>
  <v-main-layout :token="userStore.AccessToken">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        PROGRAM ARAH KEBIJAKAN
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
          Halaman ini digunakan untuk mengelola relasi arah kebijakan RPJMD dengan program.
        </v-alert>
      </template>      
    </v-page-header>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="auto" md="12" lg="12">
          <v-card>
            <v-card-title>
              <v-icon icon="mdi-eye"></v-icon> &nbsp;
              <span class="headline">DATA ARAH KEBIJAKAN</span>
            </v-card-title>
            <v-card-text>
              <v-row justify="space-between">
                <v-col cols="auto" md="12" lg="12">
                  <v-row tag="dl" class="text-body-2 mb-2" no-gutters>
                    <v-col cols="auto" md="12" lg="2" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      ID ARAH KEBIJAKAN
                    </v-col>
                    <v-col cols="auto" md="12" lg="4" tag="dt">
                      {{ data_arah_kebijakan.RpjmdArahKebijakanID }}
                    </v-col>
                    <v-col cols="auto" md="12" lg="2" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      ID STRATEGI
                    </v-col>
                    <v-col cols="auto" md="12" lg="4" tag="dt">
                      <a href="">{{ data_arah_kebijakan.RpjmdStrategiID }}</a>
                    </v-col>
                  </v-row>                 
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      NAMA STRATEGI
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_arah_kebijakan.Nm_RpjmdStrategi }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      KODE ARAH KEBIJAKAN
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_arah_kebijakan.Kd_RpjmdArahKebijakan }}
                    </v-col>
                  </v-row>
                  <v-row tag="dl" class="text-body-2" no-gutters>
                    <v-col cols="auto" md="12" lg="12" tag="dt" class="font-weight-bold bg-deep-purple-lighten-5">
                      NAMA ARAH KEBIJAKAN
                    </v-col>
                    <v-col cols="auto" md="12" lg="12" tag="dt">
                      {{ data_arah_kebijakan.Nm_RpjmdArahKebijakan }}
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
                <span class="headline">TAMBAH PROGRAM UNTUK ARAH KEBIJAKAN INI</span>
              </v-card-title>
              <v-card-text>
                <v-text-field
                  v-model="formdata.Kd_ProgramRPJMD"                  
                  density="compact"        
                  label="KODE PROGRAM"
                  variant="outlined"
                  prepend-inner-icon="mdi-graph"
                  hint="Masukan kode / nomor program dari rpjmd"
                  :rules="rule_kode_program"
                  auto-grow
                />    
                <v-textarea
                  v-model="formdata.Nm_ProgramRPJMD"
                  rows="1"
                  density="compact"        
                  label="NAMA PROGRAM"
                  variant="outlined"
                  prepend-inner-icon="mdi-graph"
                  hint="Masukan nama program dari rpjmd"
                  :rules="rule_nama_program"
                  auto-grow
                />
                <v-autocomplete
                  :items="daftar_program"
                  density="compact"
                  variant="outlined"
                  v-model="formdata.PrgID"
                  prepend-inner-icon="mdi-graph"
                  label="PROGRAM DI SIMONEV"              
                  item-title="nama_program"
                  item-value="PrgID"
                  :rules="rule_program"
                  clearable                  
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
      <v-arah-kebijakan-data-table :RpjmdArahKebijakanID="RpjmdArahKebijakanID" />
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'
  import dataTable from '@/pages/admin/relations/RelationProgramArahKebijakanDataTable.vue'
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'RelationProgramArahKebijakanManage',
    created() {
      this.userStore = usesUserStore()
      this.breadcrumbs = [
        {
          title: 'HOME',
          href: '/admin/' + this.userStore.AccessToken,
        },
        {
          title: 'RELASI',
          disabled: false,
          href: '#',
        },
        {
          title: 'ARAH KEBIJAKAN - PROGRAM',
          href: '/admin/relations/programarahkebijakan',
        },
        {
          title: 'KELOLA',
          disabled: true,
          href: '#',
        },
      ]
      this.RpjmdArahKebijakanID = this.$route.params.RpjmdArahKebijakanID;
    },
    mounted() {
      this.fetchArahKebijakan()
      this.fetchProgram()
    },
    data: () => ({
      RpjmdArahKebijakanID: null,
      btnLoading: false,
      breadcrumbs: [],
      data_arah_kebijakan: {
        strategi: {},
      },
      //form data
      daftar_program: [],
      form_valid: true,
      formdata: {
        Program: null,       
      },
      formdefault: {
        Program: null,        
      },
      //form rules
      rule_kode_program: [
        value => !!value || 'Mohon untuk di isi kode program dari RPJMD !!!',
      ],
      rule_nama_program: [
        value => !!value || 'Mohon untuk di isi nama program dari RPJMD !!!',
      ],
      rule_program: [
        value => !!value || 'Mohon untuk dipilih nama program yang berelasi dengan permendagri 90 TAHUN 2019 !!!',
      ],
      //pinia
      userStore: null,
    }),
    methods: {      
      async fetchArahKebijakan() {
        await this.$ajax
          .get('/rpjmd/arahkebijakan/' + this.RpjmdArahKebijakanID, {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            this.data_arah_kebijakan = data.payload
          })
      },
      async fetchProgram() {
        await this.$ajax
        .post('/dmaster/kodefikasi/program', 
          {
            TA: this.userStore.PeriodeRPJMD.TA_AWAL,            
          },
          {
            headers: {
              Authorization: this.userStore.Token,
            },
          }
        )
        .then(({ data }) => {
          this.daftar_program = data.kodefikasiprogram
        })
      },
      async save() {
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          this.$ajax
            .post(
              '/rpjmd/relations/arahkebijakanprogram/store',
              {
                RpjmdArahKebijakanID: this.RpjmdArahKebijakanID,
                PrgID: this.formdata.PrgID,
                Kd_ProgramRPJMD: this.formdata.Kd_ProgramRPJMD,          
                Nm_ProgramRPJMD: this.formdata.Nm_ProgramRPJMD,          
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