<template>
  <v-front-layout :showrightsidebar="false" :classmain="'mx-0 mb-0'">
    <v-parallax
      dark
      :src="$api.storageURL +  '/storages/images/banners/back_1.jpg'"
      height="700"
    >
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center" no-gutters>
          <v-col xs="12" sm="6" md="4">
            <v-form
              ref="frmlogin"
              @keyup.native.enter="doLogin"
            >
              <v-card
                class="mx-auto pa-8 pb-8"
                elevation="8"
                max-width="448"
                rounded="lg"
                :loading="btnLoading"
              >

                <v-card-text class="mb-0">
                  <v-alert
                    variant="outlined"
                    density="compact"
                    type="error"
                    :model-value="form_error"
                    icon="mdi-close-octagon-outline"
                    class="mb-3"
                  >
                    Username atau Password tidak dikenal !.
                  </v-alert>
                  <v-text-field
                    v-model="formlogin.username"
                    density="compact"        
                    label="Username"
                    variant="outlined"
                    prepend-inner-icon="mdi-account-outline"
                    hint="Masukan username password sama dengan simonev21"
                    :rules="rule_username"
                  />          
                  <v-text-field
                    v-model="formlogin.password"
                    density="compact"
                    label="Password"
                    variant="outlined"
                    prepend-inner-icon="mdi-lock-outline"
                    hint="Masukan password sama dengan simonev21"
                    type="password"
                    :rules="rule_password"
                  />          
                  <v-select
                    v-model="formlogin.periode_rpjmd"
                    density="compact"
                    :items="daftar_periode"
                    label="Tahun Evaluasi"
                    variant="outlined"
                    prepend-inner-icon="mdi-calendar"
                    item-value="PeriodeRPJMDID"
                    item-title="NamaPeriode"
                    :rules="rule_periode_rpjmd"
                    return-object
                  />
                </v-card-text>
                <v-card-actions>
                  <v-btn
                    color="blue"
                    size="large"
                    variant="tonal"
                    @click="doLogin"
                    :disabled="btnLoading"
                    block
                  >
                    Log In
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-form>
          </v-col>
        </v-row>
      </v-container>
    </v-parallax>
    
  </v-front-layout>
</template>
<script>
  import { usesUserStore } from '@/stores/UsersStore'
  import frontLayout from '@/layouts/FrontLayout.vue'
  export default {
    name: 'Login',
    created() {
      this.userStore = usesUserStore()
      this.initialize()
    },
    data: () => ({      
      btnLoading: false,
      form_error: false,
      //pinia
      userStore: null,
      //form
      daftar_periode: [],
      formlogin: {
        username: null,
        password: null,
        periode_rpjmd: null,
      },
      rule_username: [
        value => !!value || 'Kolom Username mohon untuk diisi !!!',
      ],
      rule_password: [
        value => !!value || 'Kolom Password mohon untuk diisi !!!',
      ],
      rule_periode_rpjmd: [
        value => !!value || 'Periode RPJMD mohon untuk dipilih !!!',
      ],
    }),
    methods: {
      async initialize() {
        await this.$ajax
          .post("/rpjmd/periode")
          .then(({ data }) => {            
            this.daftar_periode = data.payload.data
          })
      },
      async doLogin() {

        const { valid } = await this.$refs.frmlogin.validate()

        if(valid) {
          this.btnLoading = true
          await this.$ajax
            .post('/auth/login', {
              username: this.formlogin.username,
              password: this.formlogin.password,
            })
            .then(({ data }) => {
              this.$ajax
                .get('/auth/me', {
                  headers: {
                    Authorization: data.token_type + ' ' + data.access_token,
                  },
                })
                .then(response => {
                  var user = response.data
                  user.tahun_selected = this.formlogin.periode_rpjmd.TA_AWAL
                  Object.assign(user, {
                    periode_rpjmd: this.formlogin.periode_rpjmd,
                  })
                  var data_user = {
                    token: data,
                    user: user,
                  }
                  this.userStore.afterLoginSuccess(data_user)
                })
              this.btnLoading = false
              this.form_error = false
              this.$router.push('/admin/' + data.access_token)
            })
            .catch(() => {              
              this.form_error = true
              this.btnLoading = false
            })          
        }
      },
    },
    components: {
      'v-front-layout': frontLayout,
    }
  }
</script>
