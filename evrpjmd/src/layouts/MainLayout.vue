<template>  
  <v-app id="evarpjmd">
    <v-system-bar color="primary">
      <v-icon class="ms-2" icon="mdi-wifi-strength-4"></v-icon>      
      <span class="ms-2">
        {{ userStore.PeriodeRPJMD.NamaPeriode }}
      </span>
    </v-system-bar>
    <v-navigation-drawer
      v-model="drawer"
      :temporary="temporaryleftsidebar"
    >
      <template v-slot:prepend>
        <v-list-item
          lines="two"
          :prepend-avatar="photoUser"
          subtitle="Logged in"
          :title="dataUser.username"
        >
        </v-list-item>
        <v-divider />
      </template>
      <v-list density="compact" nav>
        <v-list-item prepend-icon="mdi-home-city" title="DASHBOARD" value="dmaster" :to="'/admin/' + token" link slim />
        <v-list-subheader title="DATA MASTER" color="purple accent-5 bg-red-lighten-5 text-red-ligthen-5" />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="PERIODE RPJMD" value="periode_rpjmd" to="/admin/dmaster/perioderpjmd" link slim />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="INDIKATOR KINERJA" value="indikator_kinerja" to="/admin/dmaster/indikatorkinerja" link slim />
        <v-divider color="blue-lighten-3" />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="VISI" value="visi" to="/admin/dmaster/visi" link slim />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="MISI" value="misi" to="/admin/dmaster/misi" link slim />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="TUJUAN" value="tujuan" to="/admin/dmaster/tujuan" link slim />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="SASARAN" value="sasaran" to="/admin/dmaster/sasaran" link slim />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="STRATEGI" value="strategi" to="/admin/dmaster/strategi" link slim />
        <v-list-subheader title="RELASI" color="purple accent-5 bg-red-lighten-5 text-red-ligthen-5" />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="INDIKATOR TUJUAN" value="indikator-tujuan" to="/admin/relations/indikatortujuan" link slim />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="INDIKATOR SASARAN" value="indikator-sasaran" to="/admin/relations/indikatorsasaran" link slim />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="INDIKATOR PROGRAM" value="indikator-program" to="/admin/relations/indikatorprogram" link slim />
        <v-list-item prepend-icon="mdi-arrow-collapse-right" title="PROGRAM STRATEGI" value="program-strategi" to="/admin/relations/programstrategi" link slim />
      </v-list>
    </v-navigation-drawer>
    <v-navigation-drawer
      v-model="drawerRight"
      width="300"
      location="right"
      temporary
      v-if="showrightsidebar"
    >
      test
    </v-navigation-drawer>
    <v-app-bar class="white" elevation="0">
      <template v-slot:prepend>
        <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
      </template>
      <v-app-bar-title>Evaluasi RPJMD</v-app-bar-title>
      <v-spacer />
      <v-menu class="hidden-md-and-up" v-if="$vuetify.display.mobile">
        <template v-slot:activator="{ on }">
          <v-btn icon v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list density="compact" nav>
          <v-list-item prepend-icon="mdi-calendar" title="PERIODE RPJMD" value="indikator_kinerja" to="/admin/dmaster/perioderpjmd" link slim></v-list-item>    
        </v-list>
      </v-menu>
      <v-divider class="mx-4" inset vertical></v-divider>
      <v-menu
        :close-on-content-click="true"
        origin="center center"
        transition="scale-transition"
        :offset-y="true"
        bottom
        left
      >
        <template v-slot:activator="{ props }">
          <v-avatar size="30">
            <v-img :src="photoUser" v-bind="props" />
          </v-avatar>
        </template>
        <v-list density="compact" nav>
          <v-list-item
            prepend-icon="mdi-power"
            title="Logout"
            @click.prevent="logout"
            link
            slim
          />
        </v-list>
      </v-menu>
      <v-app-bar-nav-icon @click.stop="drawerRight = !drawerRight">
        <v-icon>mdi-menu-open</v-icon>
      </v-app-bar-nav-icon>
    </v-app-bar>

    <v-main :class="classmain">
      <slot />
    </v-main>
    <v-footer app padless fixed dark>
      <div class="px-4 py-2 bg-black text-center w-100">
        Evaluasi RPJMD
      </div>
    </v-footer>
  </v-app>
</template>
<script>
  import { usesUserStore } from '@/stores/UsersStore'
  export default {
    name: 'MainLayout',
    created() {
      this.userStore = usesUserStore()
      this.initialize()
    },
    props: {
      showrightsidebar: {
        type: Boolean,
        default: true,
      },
      temporaryleftsidebar: {
        type: Boolean,
        default: false,
      },
      classmain: {
        type: String,
        default: "mx-4 mb-4",
      },
      token: {
        type: String,
        default: null,
      },
    },
    data: () => ({
      drawer: null,
      drawerRight: null,
      dataUser: {},
      //pinia
      userStore: null,
    }),
    methods: {
      async initialize() {        
        await this.$ajax
          .get("/auth/me", {
            headers: {
              Authorization: 'Bearer ' + this.token,
            },
          })
          .then(({ data }) => {            
            this.dataUser = data            
            // this.dashboard = data.role[0];
            // this.$store.dispatch("uiadmin/changeDashboard", this.dashboard);
          })
          .catch(error => {
            console.log(error)
            if (error.response.status == 401) {
              this.$router.push("/login");
            }
          });
      },
      logout() {        
        this.$ajax
          .post(
            "/auth/logout",
            {},
            {
              headers: {
                Authorization: 'Bearer ' + this.token,
              },
            }
          )
          .then(() => {
            this.userStore.logout()
            this.$router.push("/login");
          })
          .catch(() => {
            this.userStore.logout()
            this.$router.push("/login");
          });
      },
    },
    computed: {     
      photoUser() {        
        let img = this.dataUser.foto;
        var photo;
        if (img == "") {
          photo = this.$api.storageURL + "/images/users/no_photo.png";
        } else {
          photo = this.$api.storageURL + "/" + img;
        }
        return photo;
      },
    },
  }
</script>