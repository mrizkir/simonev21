<template>
  <FrontLayout>
    <template v-slot:system-bar>
      Tahun Anggaran: {{ tahun_anggaran }} | Bulan Realisasi:
      {{
        $store.getters["uifront/getNamaBulan"](
          $store.getters["uifront/getBulanRealisasi"]
        )
      }} | 
      APBD: {{ $store.getters["uifront/getMasaPelaporan"] }}
    </template>
    <ModuleHeader>
      <template v-slot:icon>
        mdi-chart-bar
      </template>
      <template v-slot:name>
        EVALUASI REALISASI PERUBAHAN PER T.A
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
          Evaluasi realisasi per OPD Perubahan datanya berasal dari FORM B OPD
          dengan posisi bulan
          {{
            $store.getters["uifront/getNamaBulan"](
              $store.getters["uifront/getBulanRealisasi"]
            )
          }}
          Tahun Anggaran {{ tahun_anggaran }}.
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container fluid>
      <v-row dense class="mb-2">
        <v-col xs="12" sm="6" md="3">
          <v-card color="#b71b1c" dark>
            <v-card-title class="headline">
              SANGAT RENDAH
            </v-card-title>
            <v-card-text>
              Tahun (x &lt;=50%)
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="2">
          <v-card color="#d49dc5" dark>
            <v-card-title class="headline">
              RENDAH
            </v-card-title>
            <v-card-text>
              Tahun (51% >= x &lt; 66%)
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="2">
          <v-card color="#e1c027" dark>
            <v-card-title class="headline">
              SEDANG
            </v-card-title>
            <v-card-text>
              Tahun (66% >= x &lt; 76%)
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="2">
          <v-card color="#8ec189" dark>
            <v-card-title class="headline">
              TINGGI
            </v-card-title>
            <v-card-text>
              Tahun (76% >= x &lt; 91%)
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="3">
          <v-card color="#29af28" dark>
            <v-card-title class="headline">
              SANGAT TINGGI
            </v-card-title>
            <v-card-text>
              Tahun (x >= 91%)
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
      </v-row>
      <v-row class="mb-2" no-gutters>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="datatable"
            item-key="kode_organisasi"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
            class="elevation-1"
            :disable-pagination="true"
            :hide-default-footer="true"
            dense
          >
            <template v-slot:item="{ item }">
              <tr>
                <td>{{ item.index }}</td>
                <td>{{ item.kode_organisasi }}</td>
                <td>{{ item.Nm_Organisasi }}</td>
                <td class="text-right">{{ item.pagu_dana | formatUang }}</td>
                <td class="text-center">{{ item.target_fisik }}</td>
                <td
                  v-bind:class="[
                    formatKodeWarna(0, item.realisasi_fisik),
                    'text-center',
                  ]"
                >
                  {{ item.realisasi_fisik }}
                </td>
                <td class="text-right">
                  {{ item.target_keuangan | formatUang }}
                </td>
                <td
                  v-bind:class="[
                    formatKodeWarna(0, item.persen_keuangan),
                    'text-right',
                  ]"
                >
                  {{ item.realisasi_keuangan | formatUang }}
                </td>
                <td class="text-center">{{ item.persen_keuangan }}</td>
              </tr>
            </template>
            <template v-slot:body.append>
              <tr class="amber darken-1 font-weight-black">
                <td colspan="3" class="text-right">TOTAL</td>
                <td class="text-right">
                  {{ footers.total_pagu_dana | formatUang }}
                </td>
                <td class="text-center">
                  {{ footers.total_target_fisik }}
                </td>
                <td
                  v-bind:class="[
                    formatKodeWarna(0, footers.total_realisasi_fisik),
                    'text-center',
                  ]"
                >
                  {{ footers.total_realisasi_fisik }}
                </td>
                <td class="text-right">
                  {{ footers.total_target_keuangan | formatUang }}
                </td>
                <td
                  v-bind:class="[
                    formatKodeWarna(0, footers.persen_keuangan),
                    'text-right',
                  ]"
                >
                  {{ footers.total_realisasi_keuangan | formatUang }}
                </td>
                <td class="text-center">
                  {{ footers.total_persen_keuangan }}
                </td>
              </tr>
            </template>
          </v-data-table>
        </v-col>
        <v-col cols="12" class="mt-4">
          <p>(<i>Buka Halaman Form B OPD untuk mengupdate halaman ini</i>)</p>
        </v-col>
      </v-row>
      <v-row dense class="mb-2">
        <v-col xs="12" sm="12" md="12">
          <v-card>
            <v-card-title class="headline">
              Progres Realisasi Fisik OPD
            </v-card-title>
            <v-card-text>
              <chart-realisasi-fisik
                :labels="chart_daftar_opd"
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
              Progres Realisasi Keuangan OPD
            </v-card-title>
            <v-card-text>
              <chart-realisasi-keuangan
                :labels="chart_daftar_opd"
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
      <Filter1
        :showrumustw="true"
        v-on:changeTahunAnggaran="changeTahunAnggaran"
        v-on:changeBulanRealisasi="changeBulanRealisasi"
        v-on:changeTWRumus="changeTWRumus"
        ref="filter1"
      />
    </template>
  </FrontLayout>
</template>
<script>
  import FrontLayout from "@/views/layouts/FrontLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  import Filter1 from "@/components/sidebar/FilterMode1";
  import ChartTargetRealisasi from "@/components/chart/ChartBarOPDTargetRealisasi";

  export default {
    name: "FrontEvaluasiRealisasiPerubahanTA",
    created() {
      this.$store.dispatch("uifront/init", this.$ajax);
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/",
        },
        {
          text: "EVALUASI APBD MURNI",
          disabled: false,
          href: "#",
        },
        {
          text: "REALISASI PER T.A",
          disabled: true,
          href: "#",
        },
      ];
      this.tahun_anggaran = this.$store.getters["uifront/getTahunAnggaran"];
      this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
      this.tw_rumus = this.$store.getters["uifront/getTWRumus"];
    },
    mounted() {
      this.initialize();
      this.firstloading = false;
      this.$refs.filter1.setFirstTimeLoading(this.firstloading);
    },
    data: () => ({
      firstloading: true,
      breadcrumbs: [],
      tahun_anggaran: null,
      tw_rumus: null,
      datatableLoading: false,

      //data table
      datatable: [],
      headers: [
        { text: "NO", value: "index", width: 70 },
        { text: "KODE", value: "kode_organisasi", width: 160, sortable: false },
        { text: "NAMA OPD", value: "Nm_Organisasi", sortable: false },
        {
          text: "PAGU DANA",
          value: "pagu_dana",
          sortable: false,
          align: "right",
        },
        {
          text: "TARGET FISIK (%)",
          value: "target_fisik",
          sortable: false,
          align: "center",
        },
        {
          text: "REALISASI FISIK (%)",
          value: "realisasi_fisik",
          sortable: false,
          align: "center",
        },
        {
          text: "TARGET KEUANGAN",
          value: "target_keuangan",
          sortable: false,
          align: "right",
        },
        {
          text: "REALISASI KEUANGAN",
          value: "realisasi_keuangan",
          sortable: false,
          align: "right",
        },
        {
          text: "% REALISASI KEUANGAN",
          value: "persen_keuangan",
          sortable: false,
          align: "center",
        },
      ],
      footers: {
        total_pagu_dana: 0,
        total_target_fisik: 0,
        total_realisasi_fisik: 0,
        total_target_keuangan: 0,
        total_realisasi_keuangan: 0,
        total_persen_keuangan: 0,
      },

      //chart
      chartLoaded: false,
      chart_daftar_opd: [],
      chart_target_fisik: [],
      chart_realisasi_fisik: [],
      chart_target_keuangan: [],
      chart_realisasi_keuangan: [],
    }),
    methods: {
      changeTahunAnggaran(ta) {
        this.tahun_anggaran = ta;
      },
      changeBulanRealisasi(bulan) {
        this.bulan_realisasi = bulan;
      },
      changeTWRumus(tw_rumus) {
        this.tw_rumus = tw_rumus;
      },
      initialize() {
        this.datatableLoading = true;
        this.$ajax
          .post("/evaluasiperubahan/realisasita", {
            tahun: this.tahun_anggaran,
            bulan: this.bulan_realisasi,
          })
          .then(({ data }) => {
            let laporan_realisasi = data.laporan_realisasi;
            this.datatableLoading = false;
            this.datatable = laporan_realisasi;
            this.footers = data.laporan_total;

            //chart
            var daftar_opd = [];
            var target_fisik = [];
            var realisasi_fisik = [];
            var target_keuangan = [];
            var realisasi_kuangan = [];
            laporan_realisasi.forEach(item => {
              daftar_opd.push(item.Alias_Organisasi);
              target_fisik.push(item.target_fisik);
              realisasi_fisik.push(item.realisasi_fisik);
              target_keuangan.push(item.persen_target_keuangan);
              realisasi_kuangan.push(item.persen_keuangan);
            });
            this.chart_daftar_opd = daftar_opd;
            this.chart_target_fisik = target_fisik;
            this.chart_realisasi_fisik = realisasi_fisik;
            this.chart_target_keuangan = target_keuangan;
            this.chart_realisasi_keuangan = realisasi_kuangan;
            this.chartLoaded = true;
          });
      },
    },
    watch: {
      tahun_anggaran() {
        if (!this.firstloading) {
          this.initialize();
        }
      },
      bulan_realisasi() {
        if (!this.firstloading) {
          this.initialize();
        }
      },
      tw_rumus() {
        if (!this.firstloading) {
          this.initialize();
        }
      },
    },
    components: {
      FrontLayout,
      ModuleHeader,
      Filter1,
      "chart-realisasi-fisik": ChartTargetRealisasi,
      "chart-realisasi-keuangan": ChartTargetRealisasi,
    },
  };
</script>
