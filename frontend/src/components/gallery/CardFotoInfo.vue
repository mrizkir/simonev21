<template>
  <v-card class="mx-auto">
    <v-img
      :src="media.publicFullUrl"
      :lazy-src="
        `https://picsum.photos/10/6?image=${media.id * 5 + 10}`
      "
      aspect-ratio="1"
      class="grey lighten-2"
    >
      <template v-slot:placeholder>
        <v-row class="fill-height ma-0" align="center" justify="center">
          <v-progress-circular
            indeterminate
            color="grey lighten-5"
          ></v-progress-circular>
        </v-row>
      </template>
    </v-img>   
  </v-card>
</template>

<script>
	export default {
    name: "CardFotoInfo",
    props: {
      media: {
        type: Object,
      },
    },
  };
</script>
