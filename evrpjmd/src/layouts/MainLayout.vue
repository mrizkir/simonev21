<template>
  <v-app id="evarpjmd">
    <v-navigation-drawer
      v-model="drawer"
      :temporary="temporaryleftsidebar"
    >
      <template v-slot:prepend>
        <v-list-item
          lines="two"
          prepend-avatar="https://randomuser.me/api/portraits/women/81.jpg"
          subtitle="Logged in"
          title="Jane Smith"
        >
        </v-list-item>
        <v-divider></v-divider>
        <slot name="leftsidebar" />
      </template>
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
      <v-toolbar-items class="hidden-sm-and-down">
        <v-btn :to="'/admin/' + token" class="mr-2" color="indigo darken-4" text large>
          DASHBOARD
        </v-btn>
        <v-btn to="/admin/dmaster" class="mr-2" color="indigo darken-4" text large>
          DATA MASTER
        </v-btn>
      </v-toolbar-items>
      <v-menu class="hidden-md-and-up" v-if="$vuetify.display.mobile">
        <template v-slot:activator="{ on }">
          <v-btn icon v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item to="/admin">
            <v-list-item-title>DASHBOARD</v-list-item-title>
          </v-list-item>
          <v-list-item to="/dmaster">
            <v-list-item-title>DATA MASTER</v-list-item-title>
          </v-list-item>
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
        <template v-slot:activator="{ on }">
          <v-avatar size="30">
            <v-img :src="photoUser" v-on="on" />
          </v-avatar>
        </template>
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
            console.log(data);
            // this.dashboard = data.role[0];
            // this.$store.dispatch("uiadmin/changeDashboard", this.dashboard);
          })
          .catch(error => {
            if (error.response.status == 401) {
              this.$router.push("/login");
            }
          });
      }
    },
    computed: {     
      photoUser() {
        let photo = '';
        // let img = this.userStore.AttributeUser("foto");
        // var photo;
        // if (img == "") {
        //   photo = this.$api.storageURL + "/images/users/no_photo.png";
        // } else {
        //   photo = this.$api.storageURL + "/" + img;
        // }
        return photo;
      },
    },
  }
</script>