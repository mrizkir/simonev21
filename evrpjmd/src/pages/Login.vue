<template>
  <v-front-layout :showrightsidebar="false">
    <v-card
      class="mx-auto pa-12 pb-8"
      elevation="8"
      max-width="448"
      rounded="lg"
    >
      <v-text-field
        density="compact"        
        label="Username"
        variant="outlined"
        prepend-inner-icon="mdi-email-outline"
        hint="Masukan username password sama dengan simonev21"
      >
      </v-text-field>
      <v-text-field
        density="compact"        
        label="Password"
        variant="outlined"
        prepend-inner-icon="mdi-lock-outline"
        hint="Masukan password sama dengan simonev21"
        type="password"
      >
      </v-text-field>
    </v-card>
  </v-front-layout>
</template>
<script>
  import { usesUserStore } from '@/stores/UsersStore'
  import frontLayout from '@/layouts/FrontLayout.vue'
  export default {
    name: 'Login',
    setup() {
      const userStore = usesUserStore()

      return { userStore }
    },
    components: {
      'v-front-layout': frontLayout,
    }
  }
</script>