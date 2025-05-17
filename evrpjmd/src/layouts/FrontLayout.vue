<template>
  <v-app id="evarpjmd">
    <v-navigation-drawer
      v-model="drawer"
      :temporary="temporaryleftsidebar"
      width="300"
      v-if="!isLoginPage"
    >
    &nbsp;
    </v-navigation-drawer>
    <v-navigation-drawer
      v-model="drawerRight"
      width="300"
      location="right"
      temporary
      v-if="showrightsidebar && !isLoginPage"
    >
      &nbsp;
    </v-navigation-drawer>
    <v-app-bar class="white" elevation="0">
      <template v-slot:prepend>
        <v-app-bar-nav-icon @click="drawer = !drawer" v-if="!isLoginPage"></v-app-bar-nav-icon>
      </template>
      <v-app-bar-title>Evaluasi RPJMD</v-app-bar-title>
      <v-spacer />      
      <v-toolbar-items class="hidden-sm-and-down">
        <v-btn to="/" class="mr-2" color="indigo darken-4" text large>
          DASHBOARD
        </v-btn>
        <v-btn to="/login" class="mr-2" color="indigo darken-4" text large>
          LOGIN
        </v-btn>
      </v-toolbar-items>
      <v-menu class="hidden-md-and-up" v-if="$vuetify.display.mobile">
        <template v-slot:activator="{ on }">
          <v-btn icon v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item to="/">
            <v-list-item-title>DASHBOARD</v-list-item-title>
          </v-list-item>
          <v-list-item to="/login">
            <v-list-item-title>LOGIN</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <v-divider class="mx-4" inset vertical v-if="!isLoginPage"></v-divider>
      <v-app-bar-nav-icon @click.stop="drawerRight = !drawerRight" v-if="!isLoginPage">
        <v-icon>mdi-menu-open</v-icon>
      </v-app-bar-nav-icon>
    </v-app-bar>

    <v-main :class="classmain">
      <slot />
    </v-main>
  </v-app>
</template>
<script>
  export default {
    name: 'FrontLayout',
    props: {
      showrightsidebar: {
        type: Boolean,
        default: true,
      },
      temporaryleftsidebar: {
        type: Boolean,
        default: true,
      },
      classmain: {
        type: String,
        default: "mx-4 mb-4",
      },
    },
    data: () => ({
      drawer: null,
      drawerRight: null,
    }),
    computed: {
      isLoginPage() {
        return this.$route.path === '/login'
      }
    }
  }
</script>