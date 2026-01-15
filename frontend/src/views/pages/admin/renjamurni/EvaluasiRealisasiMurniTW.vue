<template>
  <RenjaMurniLayout :temporaryleftsidebar="true">
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
        EVALUASI REALISASI MURNI PER T.W
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
          Evaluasi realisasi per OPD Murni datanya berasal dari FORM B OPD
          dengan posisi triwulan
          {{ $store.getters["uifront/getTWRealisasi"] }} Tahun Anggaran
          {{ tahun_anggaran }}. Interval nilai berdasarkan PERMENDAGRI Nomor 18 Tahun 2016
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
              Triwulan I (x &lt;= 13%)<br />Triwulan II (x &lt;= 22.50%)
              <br />Triwulan III (x &lt;= 37%) <br />Triwulan IV (x &lt;=
              50%)
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="3">
          <v-card color="#d49dc5" dark>
            <v-card-title class="headline">
              RENDAH
            </v-card-title>
            <v-card-text>
              Triwulan I (13.01% >= x &lt;= 17.99%)<br />Triwulan II (22.51% >= x &lt;=
              31.25%) <br />Triwulan III (38% >= x &lt;= 49%) <br />Triwulan IV (51%
              >= x &lt;= 65%)
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="3">
          <v-card color="#e1c027" dark>
            <v-card-title class="headline">
              SEDANG
            </v-card-title>
            <v-card-text>
              Triwulan I (18% >= x &lt;= 31.26%)<br />Triwulan II (31.26% >= x &lt;=
              38.75%) <br />Triwulan III (50% >= x &lt;= 56%) <br />Triwulan IV (66%
              >= x &lt;= 75%)
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="3">
          <v-card color="#8ec189" dark>
            <v-card-title class="headline">
              TINGGI
            </v-card-title>
            <v-card-text>
              Triwulan I (31.27% >= x &lt;= 38.76%)<br />Triwulan II (38.76% >= x &lt;=
              47.50%) <br />Triwulan III (57% >= x &lt;= 68%) <br />Triwulan IV (76%
              >= x &lt;= 90%)
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
      </v-row>
      <v-row class="mb-2">
        <v-col xs="12" sm="6" md="3" offset="4">
          <v-card color="#29af28" dark>
            <v-card-title class="headline">
              SANGAT TINGGI
            </v-card-title>
            <v-card-text>
              Triwulan I (x >= 38.77%)<br />Triwulan II (x >= 47.51%) <br />Triwulan
              III (69% >= x &lt;= 75%) <br />Triwulan IV (x >= 91% &lt;= 100%)
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
      </v-row>
      <v-row class="mb-4" no-gutters>
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
          <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>DAFTAR OPD</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>                
                <v-btn
                  color="primary"
                  fab
                  small
                  @click.stop="printtoexcel"
                  :disabled="btnLoading || datatableLoaded"
                >
                  <v-icon>mdi-printer</v-icon>
                </v-btn>
              </v-toolbar>
            </template>
            <template v-slot:item="{ item }">
              <tr>
                <td>{{ item.index }}</td>
                <td>{{ item.kode_organisasi }}</td>
                <td>{{ item.Nm_Organisasi }}</td>
                <td class="text-right">{{ item.pagu_dana | formatUang }}</td>
                <td class="text-center">{{ item.target_fisik }}</td>
                <td
                  v-bind:class="[
                    formatKodeWarna(tw_rumus, item.realisasi_fisik),
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
                    formatKodeWarna(tw_rumus, item.persen_keuangan),
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
                    formatKodeWarna(tw_rumus, footers.total_realisasi_fisik),
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
                    formatKodeWarna(tw_rumus, footers.total_persen_keuangan),
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
    </v-container>
    <template v-slot:filtersidebar>
      <Filter4
        :showrumustw="true"
        v-on:changeTahunAnggaran="changeTahunAnggaran"
        v-on:changeTWRealisasi="changeTWRealisasi"
        v-on:changeTWRumus="changeTWRumus"
        ref="filter4"
      />
    </template>
  </RenjaMurniLayout>
</template>
<script>
  import RenjaMurniLayout from "@/views/layouts/RenjaMurniLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  import Filter4 from "@/components/sidebar/FilterMode4";
  export default {
    name: "EvaluasiRealisasiMurniTW",
    created() {
      this.$store.dispatch("uifront/init", this.$ajax);
      this.breadcrumbs = [
      {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
        },
        {
          text: "RENCANA KERJA MURNI",
          disabled: false,
          href: "/belanjamurni",
        },
        {
          text: "LAPORAN",
          disabled: false,
          href: "#",
        },
        {
          text: "REALISASI PER T.W",
          disabled: true,
          href: "#",
        },
      ];
      this.tahun_anggaran = this.$store.getters["uifront/getTahunAnggaran"];
      this.tw_realisasi = this.$store.getters["uifront/getTWRealisasi"];
      this.tw_rumus = this.$store.getters["uifront/getTWRumus"];
    },
    mounted() {
      this.initialize();
      this.firstloading = false;
      this.$refs.filter4.setFirstTimeLoading(this.firstloading);
    },
    data: () => ({
      firstloading: true,
      breadcrumbs: [],
      tahun_anggaran: null,
      tw_realisasi: null,
      tw_rumus: null,

      btnLoading: false,
      datatableLoaded: true,
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
    }),
    methods: {
      changeTahunAnggaran(ta) {
        this.tahun_anggaran = ta;
      },
      changeTWRealisasi(tw_realisasi) {
        this.tw_realisasi = tw_realisasi;
      },
      changeTWRumus(tw_rumus) {
        this.tw_rumus = tw_rumus;
      },
      initialize() {
        this.datatableLoading = true;
        this.$ajax
          .post("/evaluasimurni/realisasitw", {
            tahun: this.tahun_anggaran,
            tw_realisasi: this.tw_realisasi,
          })
          .then(({ data }) => {
            this.datatableLoading = false;
            this.datatable = data.evaluasi_realisasi;
            this.footers = data.laporan_total;
            this.datatableLoaded = false;
          });
      },
      printtoexcel: async function() {
        this.btnLoading = true;
        await this.$ajax
          .post(
            "/evaluasimurni/realisasitw/printtoexcel",
            {
              tahun: this.tahun_anggaran,
              tw_realisasi: this.tw_realisasi,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
              responseType: "arraybuffer",
            }
          )
          .then(({ data }) => {
            const url = window.URL.createObjectURL(new Blob([data]));
            const link = document.createElement("a");
            link.href = url;

            link.setAttribute("download", "evaluasi_realisasi_tw_" + Date.now() + ".xlsx");
            document.body.appendChild(link);
            link.click();
            this.btnLoading = false;
          })
          .catch(() => {
            this.btnLoading = false;
          });
      },
    },
    watch: {
      tahun_anggaran() {
        if (!this.firstloading) {
          this.initialize();
        }
      },
      tw_realisasi() {
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
      RenjaMurniLayout,
      ModuleHeader,
      Filter4,
    },
  };
</script>
