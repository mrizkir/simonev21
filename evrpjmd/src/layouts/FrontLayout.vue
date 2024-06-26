<template>
  <v-system-bar
    app
    color="blue"
    window
  >
    <strong><slot name="system-bar"/></strong> 
  </v-system-bar>
  <v-app-bar color="#000F61" elevation="0" dark app>
    <v-app-bar-nav-icon
      @click.stop="drawer = !drawer"        
    >
    </v-app-bar-nav-icon>
    <v-toolbar-title
      class="headline clickable"
      @click.stop="$router.push('/').catch(err => {})"
    >
      <span class="hidden-sm-and-down">
        EVALUASI RPJMD
      </span>
    </v-toolbar-title>    
    <v-spacer />
    <v-toolbar-items class="hidden-sm-and-down">
      <v-btn to="/" color="white" text large>
        DASHBOARD
      </v-btn>
      <!-- <v-btn to="/pelaporanopd" color="white" text large>
        PELAPORAN OPD
      </v-btn> -->
      <v-btn to="/login" color="white" text large>
        LOGIN
      </v-btn>
    </v-toolbar-items>
  </v-app-bar>
  <v-main>
    <slot />
  </v-main>  
</template>
<script>
  export default {
    name: 'FrontLayout',
    data: () => ({
      drawer: null,
      drawerRight: null,
    }),
  }
</script>