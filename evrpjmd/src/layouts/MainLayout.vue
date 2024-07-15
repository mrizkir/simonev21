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
        <v-list density="compact" nav>
          <v-list-item prepend-icon="mdi-home-city" title="Home" value="home" to="/admin"></v-list-item>
          <v-list-item prepend-icon="mdi-account" title="My Account" value="account"></v-list-item>
          <v-list-item prepend-icon="mdi-account-group-outline" title="Users" value="users"></v-list-item>
        </v-list>
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
        <v-btn to="/admin" class="mr-2" color="indigo darken-4" text large>
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
      <v-app-bar-nav-icon @click.stop="drawerRight = !drawerRight">
        <v-icon>mdi-menu-open</v-icon>
      </v-app-bar-nav-icon>
    </v-app-bar>

    <v-main>
      <slot />
    </v-main>
  </v-app>
</template>
<script>
  export default {
    name: 'MainLayout',
    props: {
      showrightsidebar: {
        type: Boolean,
        default: true,
      },
      temporaryleftsidebar: {
        type: Boolean,
        default: false,
      },
    },
    data: () => ({
      drawer: null,
      drawerRight: null,
    }),
  }
</script>