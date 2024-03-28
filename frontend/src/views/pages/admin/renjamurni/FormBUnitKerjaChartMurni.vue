<template>
  <RenjaMurniLayout :temporaryleftsidebar="true">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        CHART FORM B UNIT KERJA MURNI
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
          Laporan Chart Form B Unit Kerja Rencana Kegiatan dan Anggaran (RKA) OPD APBD
          Murni s.d
          <strong>
            BULAN {{ nama_bulan }} T.A
            {{ $store.getters["auth/TahunSelected"] }}
          </strong>
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container fluid>
      <v-row dense class="mb-2">
        <v-col xs="12" sm="12" md="12">
          <v-card>
            <v-card-title class="headline">
              Progres Realisasi Fisik
            </v-card-title>
            <v-card-text>
              <chart-realisasi-fisik
                :labels="chart_daftar_sub_kegiatan"
                :target="chart_target_fisik"
                :realisasi="chart_realisasi_fisik"
                v-if="chartLoaded"
              />
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row dense class="mb-2">
        <v-col xs="12" sm="12" md="12">
          <v-card>
            <v-card-title class="headline">
              Progres Realisasi Keuangan
            </v-card-title>
            <v-card-text>
              <chart-realisasi-keuangan
                :labels="chart_daftar_sub_kegiatan"
                :target="chart_target_keuangan"
                :realisasi="chart_realisasi_keuangan"
                v-if="chartLoaded"
              />
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <template v-slot:filtersidebar>
      <Filter2 v-on:changeBulanRealisasi="changeBulanRealisasi" ref="filter2" />
    </template>
  </RenjaMurniLayout>
</template>
<script>
  import RenjaMurniLayout from "@/views/layouts/RenjaMurniLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  import Filter2 from "@/components/sidebar/FilterMode2";
  import ChartTargetRealisasi from "@/components/chart/ChartBarOPDTargetRealisasi";
  export default {
    name: "FormBUnitKerjaChartMurni",
    created() {
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
        },
        {
          text: "RENCANA KERJA MURNI",
          disabled: false,
          href: "/renjamurni",
        },
        {
          text: "LAPORAN",
          disabled: false,
          href: "#",
        },
        {
          text: "FORM B UNIT KERJA",
          disabled: false,
          href: "/renjamurni/report/formbunitkerja",
        },
        {
          text: "CHART",
          disabled: true,
          href: "#",
        },
      ];
      this.tahun_anggaran = this.$store.getters["auth/TahunSelected"];
      this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
      this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
        this.bulan_realisasi
      );
    },
    mounted() {
      var OrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
        "formbunitkerjamurni",
        "OrgID_Selected"
      );
      var SOrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
        "formbunitkerjamurni",
        "SOrgID_Selected"
      );
      if (OrgID_Selected.length > 0) {
        this.OrgID_Selected = OrgID_Selected;
        this.SOrgID_Selected = SOrgID_Selected;
      }
      if (SOrgID_Selected.length > 0) {
        this.OrgID_Selected = OrgID_Selected;
        this.SOrgID_Selected = SOrgID_Selected;
        this.firstloading = false;
        this.$refs.filter2.setFirstTimeLoading(this.firstloading);

        this.initialize();
      }
    },
    data() {
      return {
        btnLoading: false,
        firstloading: true,
        tahun_anggaran: null,
        bulan_realisasi: null,
        nama_bulan: null,

        //filter form
        daftar_opd: [],
        OrgID_Selected: "",
        daftar_unitkerja: [],
        SOrgID_Selected: "",
        //Organisasi
        DataOPD: null,
        DataUnitKerja: null,

        //chart
        chartLoaded: false,
        chart_daftar_sub_kegiatan: [],
        chart_target_fisik: [],
        chart_realisasi_fisik: [],
        chart_target_keuangan: [],
        chart_realisasi_keuangan: [],
      };
    },
    methods: {
      changeBulanRealisasi(bulan_realisasi) {
        this.bulan_realisasi = bulan_realisasi;
        this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
          bulan_realisasi
        );
        this.initialize();
      },
      async initialize() {
        await this.$ajax
          .post(
            "/renjamurni/report/formbunitkerjamurni/chart",
            {
              tahun: this.tahun_anggaran,
              no_bulan: this.bulan_realisasi,
              SOrgID: this.SOrgID_Selected,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            let chart = data.chart;
            //chart
            var chart_daftar_sub_kegiatan = [];
            var target_fisik = [];
            var realisasi_fisik = [];
            var target_keuangan = [];
            var realisasi_kuangan = [];

            chart.forEach(item => {
              chart_daftar_sub_kegiatan.push(item.kode_sub_kegiatan);
              target_fisik.push(item.target_fisik);
              realisasi_fisik.push(item.realisasi_fisik);
              target_keuangan.push(item.target_keuangan);
              realisasi_kuangan.push(item.realisasi_keuangan);
            });
            this.chart_daftar_sub_kegiatan = chart_daftar_sub_kegiatan;
            this.chart_target_fisik = target_fisik;
            this.chart_realisasi_fisik = realisasi_fisik;
            this.chart_target_keuangan = target_keuangan;
            this.chart_realisasi_keuangan = realisasi_kuangan;
            this.chartLoaded = true;
          });
      },
    },
    components: {
      RenjaMurniLayout,
      ModuleHeader,
      Filter2,
      "chart-realisasi-fisik": ChartTargetRealisasi,
      "chart-realisasi-keuangan": ChartTargetRealisasi,
    },
  };
</script>
