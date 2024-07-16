<template>
  <v-front-layout :showrightsidebar="false" :classmain="'mx-0 mb-0'">
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
        >
        </v-text-field>
        <v-text-field
          v-model="formlogin.password"
          density="compact"
          label="Password"
          variant="outlined"
          prepend-inner-icon="mdi-lock-outline"
          hint="Masukan password sama dengan simonev21"
          type="password"
          :rules="rule_password"
        >
        </v-text-field>
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
  </v-front-layout>
</template>
<script>
  import { usesUserStore } from '@/stores/UsersStore'
  import frontLayout from '@/layouts/FrontLayout.vue'
  export default {
    name: 'Login',
    created() {
      this.userStore = usesUserStore()
    },
    data: () => ({      
      btnLoading: false,
      form_error: false,
      //pinia
      userStore: null,
      //form
      formlogin: {
        username: null,
        password: null,
        tahun_evaluasi: 2021,
      },
      rule_username: [
        value => !!value || "Kolom Username mohon untuk diisi !!!",
      ],
      rule_password: [
        value => !!value || "Kolom Password mohon untuk diisi !!!",
      ],
      rule_tahun_evaluasi: [
        value => !!value || "Tahun Evaluasi RPJMD mohon untuk dipilih !!!",
      ],
    }),
    methods: {
      async doLogin() {
        if (this.$refs.frmlogin.validate()) {
          this.btnLoading = true
          await this.$ajax
            .post("/auth/login", {
              username: this.formlogin.username,
              password: this.formlogin.password,
            })
            .then(({ data }) => {
              this.$ajax
                .get("/auth/me", {
                  headers: {
                    Authorization: data.token_type + " " + data.access_token,
                  },
                })
                .then(response => {
                  let user = response.data;
                  Object.assign(user, {
                    tahun_evaluasi: this.formlogin.tahun_evaluasi,
                  });
                  var data_user = {
                    token: data,
                    user: user,
                  };
                  this.userStore.afterLoginSuccess(data_user)
                });
              this.btnLoading = false;
              this.form_error = false;
              this.$router.push("/admin/" + data.access_token);
            })
            .catch(() => {              
              this.form_error = true;
              this.btnLoading = false;
            });          
        }
      },
    },
    components: {
      'v-front-layout': frontLayout,
    }
  }
</script>