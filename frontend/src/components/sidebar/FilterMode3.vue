<template>
  <v-list-item>
    <v-list-item-content>
      <v-select
        v-model="tahun_anggaran"
        :items="daftar_ta"
        label="TAHUN ANGGARAN"
        outlined
      />
    </v-list-item-content>
  </v-list-item>
</template>
<script>
  export default {
    name: "FilterMode3",
    created() {
      this.daftar_ta = this.$store.getters["uifront/getDaftarTA"];
      this.tahun_anggaran = this.$store.getters["uifront/getTahunAnggaran"];
    },
    data: () => ({
      firstloading: true,
      daftar_ta: [],
      tahun_anggaran: null,
    }),
    methods: {
      setFirstTimeLoading(bool) {
        this.firstloading = bool;
      },
    },
    watch: {
      tahun_anggaran(val) {
        if (!this.firstloading) {
          this.$store.dispatch("uifront/updateTahunAnggaran", val);
          this.$emit("changeTahunAnggaran", val);
        }
      },
    },
  };
</script>
