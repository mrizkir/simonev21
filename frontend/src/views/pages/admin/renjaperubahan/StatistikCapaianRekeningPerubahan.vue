<template>
  <RenjaPerubahanLayout :showrightsidebar="true" :temporaryleftsidebar="true">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-chart-timeline-variant
      </template>
      <template v-slot:name>
        CAPAIAN REKENING PERUBAHAN
      </template>
      <template v-slot:breadcrumbs>
        <v-breadcrumbs :items="breadcrumbs" class="pa-0">
          <template v-slot:divider>
            <v-icon>mdi-chevron-right</v-icon>
          </template>
        </v-breadcrumbs>
      </template>
      <template v-slot:desc>
        <v-alert color="cyan" border="left" colored-border type="info">
          Berisi progres realisasi fisik dan keuangan per rekening
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container fluid>
      <v-row dense>
        <v-col xs="12" sm="6" md="6">
          <card-capaian-rekening :tw="1" v-bind:tahun_anggaran="tahun_anggaran" :mode="'keuangan'" :entryLvl="2" />
        </v-col>
        <v-col xs="12" sm="6" md="6">
          <card-capaian-rekening :tw="1" v-bind:tahun_anggaran="tahun_anggaran" :mode="'fisik'" :entryLvl="2" />
        </v-col>
      </v-row>
      <v-row dense>
        <v-col xs="12" sm="6" md="6">
          <card-capaian-rekening :tw="2" v-bind:tahun_anggaran="tahun_anggaran" :mode="'keuangan'" :entryLvl="2" />
        </v-col>
        <v-col xs="12" sm="6" md="6">
          <card-capaian-rekening :tw="2" v-bind:tahun_anggaran="tahun_anggaran" :mode="'fisik'" :entryLvl="2" />
        </v-col>
      </v-row>
      <v-row dense>
        <v-col xs="12" sm="6" md="6">
          <card-capaian-rekening :tw="3" v-bind:tahun_anggaran="tahun_anggaran" :mode="'keuangan'" :entryLvl="2" />
        </v-col>
        <v-col xs="12" sm="6" md="6">
          <card-capaian-rekening :tw="3" v-bind:tahun_anggaran="tahun_anggaran" :mode="'fisik'" :entryLvl="2" />
        </v-col>
      </v-row>
      <v-row dense>
        <v-col xs="12" sm="6" md="6">
          <card-capaian-rekening :tw="4" v-bind:tahun_anggaran="tahun_anggaran" :mode="'keuangan'" :entryLvl="2" />
        </v-col>
        <v-col xs="12" sm="6" md="6">
          <card-capaian-rekening :tw="4" v-bind:tahun_anggaran="tahun_anggaran" :mode="'fisik'" :entryLvl="2" />
        </v-col>
      </v-row>
    </v-container>
    <template v-slot:filtersidebar>
      <Filter5
        v-on:changeTWRealisasi="changeTWRealisasi"        
        ref="filter5"
      />
    </template>
  </RenjaPerubahanLayout>
</template>
<script>
  import RenjaPerubahanLayout from "@/views/layouts/RenjaPerubahanLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  import CardCapaianRekening from "@/components/rekening/CardCapaianRekening";
  import Filter5 from "@/components/sidebar/FilterMode5";
  export default {
    name: "StatistikCapaianRekeningPerubahan",
    created() {      
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
        },
        {
          text: "RENCANA KERJA PERUBAHAN",
          disabled: false,
          href: "/renjaperubahan",
        },
        {
          text: "STATISTIK",
          disabled: false,
          href: "#",
        },
        {
          text: "CAPAIAN REKENING",
          disabled: true,
          href: "#",
        },
      ];
    
      this.tahun_anggaran = this.$store.getters["auth/TahunSelected"];
      this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
      this.tw_realisasi = this.$store.getters["uifront/getTWRealisasi"];
    },
    mounted() {
      this.firstloading = false;
      this.$refs.filter5.setFirstTimeLoading(this.firstloading);
    },
    data: () => ({
      firstloading: true,
      breadcrumbs: [],
      tahun_anggaran: null,
      bulan_realisasi: null,
      tw_realisasi: null,
    }),
    methods: {
      changeTWRealisasi(tw_realisasi) {
        this.tw_realisasi = tw_realisasi;
      },
    },
    components: {
      RenjaPerubahanLayout,
      ModuleHeader,
      "card-capaian-rekening": CardCapaianRekening,
      Filter5,
    },
  }
</script>
