<template>
  <v-card class="mx-auto">
    <v-img
      :src="media.publicFullUrl"
      :lazy-src="
        `https://picsum.photos/10/6?image=${media.id * 5 + 10}`
      "
      class="grey lighten-2 white--text align-end"
      max-height="300"
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
    <v-card-title class="text-size-14">
      Rp. {{ media.pagu_uraian|formatUang }}
      <v-spacer></v-spacer>
      {{ media.bulan }} {{ media.TA }}
    </v-card-title>
    <v-card-subtitle>
      {{ media.kode_uraian }} - {{ media.nama_uraian }}
    </v-card-subtitle>
    <v-card-text class="text-size-12">
      <div>
        <span class="font-weight-medium">target keuangan: </span> {{ media.target_keuangan|formatUang }}                      
      </div>
      <div>
        <span class="font-weight-medium">realisasi keuangan: </span> {{ media.realisasi_keuangan|formatUang }}      
      </div>
      <div>
        <span class="font-weight-medium">target fisik: </span> {{ media.target_fisik }}              
        <span class="font-weight-medium">realisasi fisik: </span> {{ media.realisasi_fisik }}      
      </div>
    </v-card-text>
    <v-card-actions>  
      <v-spacer></v-spacer>
      <v-btn icon @click.stop="deleteItem(media)" :disabled="btnLoading" v-if="showbuttondelete">
        <v-icon style="color: red;">mdi-delete</v-icon>
      </v-btn>
    </v-card-actions>
  </v-card>
</template>
<script>
  export default {
    name: "CardFotoInfoRealisasi",
    props: {
      media: {
        type: Object,
        required: true,
      },
      showbuttondelete: {
        type: Boolean,
        default: false,
      },
    },
    data: () => ({
      btnLoading: false,
      show: false,
    }),
    methods: {
      async deleteItem(media) {
        this.$root.$confirm
          .open(
            "Delete",
            "Apakah Anda ingin menghapus foto realisasi dengan ID " +
              media.id +
              " ?",
            { color: "red" }
          )
          .then(confirm => {
            if (confirm) {
              this.btnLoading = true;
              this.$ajax
                .post(
                  "/renja/gallery/" + media.id,
                  {
                    _method: "DELETE",
                    RKARealisasiRincID: media.RKARealisasiRincID,
                    pid: "realisasirincian",
                  },
                  {
                    headers: {
                      Authorization: this.$store.getters["auth/Token"],
                    },
                  }
                )
                .then(() => {
                  this.$emit("callbackafterdelete");
                  this.btnLoading = false;
                })
                .catch(() => {
                  this.btnLoading = false;
                });
            }
          });
      },
    },
  };
</script>
