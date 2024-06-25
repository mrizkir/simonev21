<template>
  <v-system-bar
    app
    color="primary"
    window
  >
    <strong><slot name="system-bar"/></strong> 
  </v-system-bar>
  <v-main>
    <slot />
  </v-main>  
</template>
<script>
  export default {
    name: 'FrontLayout'
  }
</script>