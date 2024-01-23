<template>
  <DAKMurniLayout :showrightsidebar="false" :temporaryleftsidebar="true">
    <v-container fluid>
      <v-row dense>
        <v-col cols="12">
          <v-alert type="info">
            Nilai persen realisasi keuangan tetap muncul 0% bila kurang dari
            0.01%
          </v-alert>
        </v-col>
      </v-row>
      <v-row dense class="mb-2">
        <v-col xs="12" sm="12" md="12">
          <v-card>
            <v-card-title class="headline">
              Progres Realisasi Fisik
            </v-card-title>
            <v-card-text>
              <chart-realisasi-fisik
                :labels="chart_daftar_sumber_dana"
                :target="chart_target_fisik"
                :realisasi="chart_realisasi_fisik"
                v-if="chartLoaded"
              />
            </v-card-text>
          </v-card>
        </v-col>
        <v-col xs="12" sm="12" md="12">
          <v-card>
            <v-card-title class="headline">
              Progres Realisasi Keuangan
            </v-card-title>
            <v-card-text>
              <chart-realisasi-keuangan
                :labels="chart_daftar_sumber_dana"
                :target="chart_target_keuangan"
                :realisasi="chart_realisasi_keuangan"
                v-if="chartLoaded"
              />
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DAKMurniLayout>
</template>
<script>
  import DAKMurniLayout from "@/views/layouts/DAKMurniLayout";
  import ChartTargetRealisasi from "@/components/chart/ChartBarSumberDanaTargetRealisasi";
  export default {
    name: "DAKMurni",
    created() {
      this.tahun_anggaran = this.$store.getters["auth/TahunSelected"];
      this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
      this.initialize();
    },
    data: () => ({
      tahun_anggaran: null,
      bulan_realisasi: null,      
      //chart
      chartLoaded: false,
      chart_daftar_sumber_dana: [],
      chart_target_fisik: [],
      chart_realisasi_fisik: [],
      chart_target_keuangan: [],
      chart_realisasi_keuangan: [],
    }),
    methods: {
      async initialize() {
        await this.$ajax
          .post(
            "/dakmurni",
            {
              ta: this.tahun_anggaran,
              bulan_realisasi: this.bulan_realisasi,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            // chart
            var daftar_sumber_dana = [];
            var target_fisik = [];
            var realisasi_fisik = [];
            var target_keuangan = [];
            var realisasi_kuangan = [];
            
            let list_sumber_dana = data.daftar_sumber_dana;
            list_sumber_dana.forEach(item => {              
              daftar_sumber_dana.push(item.Kd_SumberDana);
              target_fisik.push(item.target_fisik);
              realisasi_fisik.push(item.realisasi_fisik);
              target_keuangan.push(item.persen_target_keuangan);
              realisasi_kuangan.push(item.persen_keuangan);
            });
            this.chart_daftar_sumber_dana = daftar_sumber_dana;
            this.chart_target_fisik = target_fisik;
            this.chart_realisasi_fisik = realisasi_fisik;
            this.chart_target_keuangan = target_keuangan;
            this.chart_realisasi_keuangan = realisasi_kuangan;
            this.chartLoaded = true;
          });
      },
    },
    components: {
      DAKMurniLayout,
      "chart-realisasi-fisik": ChartTargetRealisasi,
      "chart-realisasi-keuangan": ChartTargetRealisasi,
    },
  };
</script>
