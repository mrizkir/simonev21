<template>
  <v-select
    v-model="tahun_anggaran"
    density="compact"
    :items="daftar_ta"
    label="Tahun Evaluasi"
    variant="outlined"    
  />
</template>

<script>
  import { usesUserStore } from '@/stores/UsersStore'

  export default {
    name: 'FilterMode3',
    created() {
      this.userStore = usesUserStore()      
      this.daftar_ta = this.userStore.DaftarTahunAnggaran;
      this.tahun_anggaran = this.userStore.TahunSelected;
    },
    data: () => ({
      firstloading: true,
      daftar_ta: [],
      tahun_anggaran: null,
      //pinia
      userStore: null,
    }),
    methods: {
      setFirstTimeLoading(bool) {
        this.firstloading = bool
      },
    },
    watch: {
      tahun_anggaran(val) {
        if (!this.firstloading) {
          this.userStore.updateTahunAnggaran(val)
          // this.$store.dispatch('uifront/updateTahunAnggaran', val);
          // this.$emit('changeTahunAnggaran', val);
        }
      },
    },
  }
</script>