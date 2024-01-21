<template>
  <RenjaMurniLayout :temporaryleftsidebar="true">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        FORM A MURNI
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
          Laporan Form A Rencana Kegiatan dan Anggaran (RKA) OPD / Unit Kerja
          APBD Murni s.d
          <strong>
            BULAN {{ nama_bulan }} T.A
            {{ $store.getters["auth/TahunSelected"] }}
          </strong>
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container fluid v-if="formadetail">
      <v-row class="mb-4" no-gutters>
        <v-col xs="12" sm="12" md="12">
          <v-card>
            <v-card-text>
              <v-row no-gutters>
                <v-col xs="12" sm="6" md="6">
                  <v-card flat>
                    <v-card-title>RKAID :</v-card-title>
                    <v-card-subtitle>
                      {{ datakegiatan.RKAID }}
                    </v-card-subtitle>
                  </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
              </v-row>
              <v-row no-gutters>
                <v-col xs="12" sm="6" md="6">
                  <v-card flat>
                    <v-card-title>KODE SUB KEGIATAN :</v-card-title>
                    <v-card-subtitle>
                      {{ datakegiatan.kode }}
                    </v-card-subtitle>
                  </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
              </v-row>
              <v-row no-gutters>
                <v-col xs="12" sm="6" md="6">
                  <v-card flat>
                    <v-card-title>NAMA SUB KEGIATAN :</v-card-title>
                    <v-card-subtitle>
                      {{ datakegiatan.nama_uraian }}
                    </v-card-subtitle>
                  </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col xs="12" sm="12" md="12">
          <v-bottom-navigation color="purple lighten-1">
            <v-btn @click.stop="printtoexcel" :disabled="btnLoading">
              <span>Cetak</span>
              <v-icon>mdi-printer</v-icon>
            </v-btn>
            <v-btn @click.stop="exitforma">
              <span>Keluar</span>
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-bottom-navigation>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col xs="12" sm="12" md="12">
          <v-alert type="info">
            Catatan: Nilai realisasi keuangan dan fisik dihitung akumulasi s.d
            <strong>
              BULAN {{ nama_bulan }} T.A
              {{ $store.getters["auth/TahunSelected"] }}
            </strong>
          </v-alert>
          <v-data-table
            :headers="headersdetail"
            :items="datatabledetail"
            :search="search"
            item-key="FormAMurniDetailID"
            dense
            :single-expand="true"
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
            :disable-pagination="true"
            :hide-default-footer="true"
          >
            <template v-slot:body="{ items }">
              <tbody>
                <tr v-for="item in items" v-bind:key="item.FormBMurniID">
                  <td>{{ item.kode }}</td>
                  <td>{{ item.nama_uraian }}</td>
                  <td class="text-right">
                    {{ item.totalPaguDana | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ item.persen_bobot | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{ item.persen_rata2_fisik | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{ item.persen_tertimbang_fisik | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{ item.total_target | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ item.total_realisasi | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ item.persen_realisasi }}
                  </td>
                  <td class="text-right">
                    {{ item.persen_tertimbang_realisasi | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{ item.sisa_anggaran | formatUang }}
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="orange font-weight-bold dark">
                  <td colspan="2" class="text-right">TOTAL</td>
                  <td class="text-right">
                    {{ total_forma.totalPaguDana | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ total_forma.totalPersenBobot | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{ total_forma.totalRealisasiFisik | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{ total_forma.totalPersenTertimbangFisikSatuKegiatan }}
                  </td>
                  <td class="text-right">
                    {{ total_forma.totalTargetSatuKegiatan | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ total_forma.totalRealisasiSatuKegiatan | formatUang }}
                  </td>
                  <td class="text-right">
                    {{
                      total_forma.total_persen_rata2_realisasi
                        | makeLookPrecision
                    }}
                  </td>
                  <td class="text-right">
                    {{
                      total_forma.totalPersenTertimbangRealisasiSatuKegiatan
                        | makeLookPrecision
                    }}
                  </td>
                  <td class="text-right">
                    {{ total_forma.sisa_anggaran | formatUang }}
                  </td>
                </tr>
              </tfoot>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
      <v-row class="mb-4" dense>
        <v-col xs="12" sm="12" md="6">
          <v-card class="mb-2">
            <v-card-title class="headline">
              Progres Realisasi Keuangan Murni
            </v-card-title>
            <v-card-text>
              <chart-realisasi-keuangan
                :datagrafik="chartrealisasifisik_murni"
                v-if="chartLoaded"
              />
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
         <v-col xs="12" sm="12" md="6">
          <v-card class="mb-2">
            <v-card-title class="headline">
              Progres Realisasi Fisik Murni
            </v-card-title>
            <v-card-text>
              <chart-realisasi-fisik
                :datagrafik="chartrealisasifisik_murni"
                v-if="chartLoaded"
              />
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
      </v-row>
    </v-container>
    <v-container fluid v-else>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-card>
            <v-card-title>
              FILTER
            </v-card-title>
            <v-card-text>
              <v-autocomplete
                :items="daftar_opd"
                v-model="OrgID_Selected"
                label="OPD / SKPD"
                item-text="Nm_Organisasi"
                item-value="OrgID"
              >
              </v-autocomplete>
              <v-autocomplete
                :items="daftar_unitkerja"
                v-model="SOrgID_Selected"
                label="UNIT KERJA"
                item-text="Nm_Sub_Organisasi"
                item-value="SOrgID"
              >
              </v-autocomplete>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-card>
            <v-card-text>
              <v-text-field
                v-model="search"
                append-icon="mdi-database-search"
                label="Search"
                single-line
                hide-details
              ></v-text-field>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-alert type="info">
            Catatan: Nilai realisasi keuangan dan fisik dihitung akumulasi s.d
            <strong>
              BULAN {{ nama_bulan }} T.A
              {{ $store.getters["auth/TahunSelected"] }}
            </strong>
          </v-alert>
          <v-data-table
            :headers="headers"
            :items="datatable"
            :search="search"
            item-key="FormAMurniID"
            dense
            :single-expand="true"
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
            :disable-pagination="true"
            :hide-default-footer="true"
            style="font-size: 11px"
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>DAFTAR SUB KEGIATAN</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
              </v-toolbar>
            </template>
            <template v-slot:body="{ items }">
              <tbody>
                <tr
                  v-for="item in items"
                  v-bind:key="item.FormBMurniID"
                  :class="[colorRowFormA(item), fontWeight(item)]"
                >
                  <td v-if="item.issubkegiatan">
                    <v-btn
                      color="primary"
                      @click.stop="viewItem(item)"
                      text
                      link
                      dense
                    >
                      {{ item.kode }}
                    </v-btn>
                  </td>
                  <td v-else>
                    {{ item.kode }}
                  </td>
                  <td>{{ item.nama_uraian }}</td>
                  <td class="text-right">
                    {{ item.pagu_dana1 | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ item.fisik_target1 | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{ item.fisik_realisasi1 | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{ item.keuangan_target1 | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ item.keuangan_realisasi1 | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ item.keuangan_realisasi_persen_1 | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{ item.sisa_anggaran | formatUang }}
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="orange font-weight-bold dark">
                  <td colspan="2" class="text-right">TOTAL</td>
                  <td class="text-right">
                    {{ total_data.totalPaguUnit | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ total_data.totalPersenTargetFisik | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{
                      total_data.totalPersenRealisasiFisik | makeLookPrecision
                    }}
                  </td>
                  <td class="text-right">
                    {{ total_data.totalTargetKeuanganKeseluruhan | formatUang }}
                  </td>
                  <td class="text-right">
                    {{
                      total_data.totalRealisasiKeuanganKeseluruhan | formatUang
                    }}
                  </td>
                  <td class="text-right">
                    {{
                      total_data.totalPersenTargetKeuangan | makeLookPrecision
                    }}
                  </td>
                  <td class="text-right">
                    {{ total_data.totalSisaAnggaran | formatUang }}
                  </td>
                </tr>
              </tfoot>
            </template>
          </v-data-table>
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
  //chart
  import ChartTargetRealisasi from "@/components/chart/ChartTargetRealisasi";
  export default {
    name: "FormAMurni",
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
          text: "FORM A",
          disabled: true,
          href: "#",
        },
      ];
      this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
      this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
        this.bulan_realisasi
      );

      this.$store.dispatch("uiadmin/addToPages", {
        name: "formamurni",
        OrgID_Selected: "",
        SOrgID_Selected: "",
        datakegiatan: [],
        formadetail: false,
      });
    },
    mounted() {
      this.formadetail = this.$store.getters["uiadmin/AtributeValueOfPage"](
        "formamurni",
        "formadetail"
      );
      if (this.formadetail) {
        this.initializeforma();
        this.firstloading = false;
        this.$refs.filter2.setFirstTimeLoading(this.firstloading);
      } else {
        this.fetchOPD();
        var OrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
          "formamurni",
          "OrgID_Selected"
        );
        var SOrgID_Selected = this.$store.getters[
          "uiadmin/AtributeValueOfPage"
        ]("formamurni", "SOrgID_Selected");
        if (OrgID_Selected.length > 0) {
          this.OrgID_Selected = OrgID_Selected;
          this.SOrgID_Selected = SOrgID_Selected;
        }
        if (SOrgID_Selected.length > 0) {
          this.OrgID_Selected = OrgID_Selected;
          this.SOrgID_Selected = SOrgID_Selected;
          this.firstloading = false;
          this.$refs.filter2.setFirstTimeLoading(this.firstloading);
        }
      }
    },
    data() {
      return {
        btnLoading: false,
        firstloading: true,
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

        //data table
        datatableLoaded: true,
        datatableLoading: false,
        expanded: [],
        datatable: [],
        headers: [
          {
            text: "KODE",
            value: "kode",
            width: 80,
            sortable: false,
          },
          {
            text: "PROGRAM/KEGIATAN/SUB KEGIATAN",
            value: "nama",
            width: 300,
            sortable: false,
          },
          {
            text: "PAGU DANA (RP)",
            value: "pagu_dana1",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "TARGET FISIK (%)",
            value: "fisik_target1",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "REALISASI FISIK (%)",
            value: "fisik_realisasi1",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "TARGET KEUANGAN (RP)",
            value: "keuangan_target1",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "REALISASI KEUANGAN (RP)",
            value: "keuangan_realisasi1",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "%",
            value: "keuangan_realisasi_persen_1",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "SISA ANGGARAN (RP)",
            value: "sisa_anggaran",
            align: "end",
            width: 100,
            sortable: false,
          },
        ],
        search: "",

        total_data: {
          totalPaguUnit: 0,
          totalTargetKeuanganKeseluruhan: 0,
          totalRealisasiKeuanganKeseluruhan: 0,
          totalPersenTargetFisik: 0,
          totalPersenRealisasiFisik: 0,
          totalSisaAnggaran: 0,
        },
        total_forma: {
          totalPaguDana: 0,
          totalPersenBobot: 0,
          totalRealisasiKeuanganKeseluruhan: 0,
          totalPersenTargetFisik: 0,
          totalPersenRealisasiFisik: 0,
          totalPersenTargetKeuangan: 0,
          totalPersenRealisasiKeuangan: 0,
          totalSisaAnggaran: 0,
          totalPersenSisaAnggaran: 0,
        },

        //form a detail
        datakegiatan: [],
        formadetail: false,

        //chart
        chartLoaded: false,
        chartrealisasikeuangan_murni: [[], []],        
        chartrealisasifisik_murni: [[], []],

        //headers detail form
        datatabledetail: [],
        headersdetail: [
          { text: "KODE REKENING", value: "kode", width: 80, sortable: false },
          { text: "URAIAN", value: "nama_uraian", width: 300, sortable: false },
          {
            text: "JUMLAH",
            value: "totalPaguDana",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "BOBOT (%)",
            value: "persen_bobot",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "REALISASI FISIK (%)",
            value: "persen_rata2_fisik",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "TTB FISIK (%)",
            value: "persen_tertimbang_fisik",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "TARGET KEUANGAN (RP)",
            value: "total_target",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "REALISASI KEUANGAN (RP)",
            value: "total_realisasi",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "(%)",
            value: "persen_realisasi",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "TTB KEUANGAN (%)",
            value: "persen_tertimbang_realisasi",
            align: "end",
            width: 100,
            sortable: false,
          },
          {
            text: "SISA ANGGARAN (RP)",
            value: "sisa_anggaran",
            align: "end",
            width: 100,
            sortable: false,
          },
        ],
      };
    },
    methods: {
      changeBulanRealisasi(bulan_realisasi) {
        this.bulan_realisasi = bulan_realisasi;
        this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
          bulan_realisasi
        );
        if (this.formadetail) {
          this.initializeforma();
        } else {
          this.loaddatakegiatan();
        }
      },
      fetchOPD: async function() {
        await this.$ajax
          .post(
            "/dmaster/opd",
            {
              tahun: this.$store.getters["auth/TahunSelected"],
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.daftar_opd = data.opd;
            this.datatableLoaded = true;
          });
      },
      async loadunitkerja() {
        await this.$ajax
          .get("/dmaster/opd/" + this.OrgID_Selected + "/unitkerja", {
            headers: {
              Authorization: this.$store.getters["auth/Token"],
            },
          })
          .then(({ data }) => {
            this.DataOPD = data.organisasi;
            this.daftar_unitkerja = data.unitkerja;
            this.datatableLoaded = true;
          });
      },
      async loaddatakegiatan() {
        this.datatableLoading = true;
        await this.$ajax
          .post(
            "/renjamurni/report/formbunitkerja",
            {
              tahun: this.$store.getters["auth/TahunSelected"],
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
            this.DataUnitKerja = data.unitkerja;
            this.datatable = data.rka;
            this.total_data = data.total_data;
            this.datatableLoaded = false;
            this.datatableLoading = false;
          });
      },
      viewItem(item) {
        this.formadetail = true;
        var page = this.$store.getters["uiadmin/Page"]("formamurni");
        page.formadetail = true;
        page.datakegiatan = item;
        this.$store.dispatch("uiadmin/updatePage", page);
        this.initializeforma();
        this.$router.replace("/renjamurni/report/forma/" + item.RKAID);
      },
      async initializeforma() {
        var page = this.$store.getters["uiadmin/Page"]("formamurni");
        this.datakegiatan = page.datakegiatan;
        let RKAID = this.datakegiatan.RKAID;

        await this.$ajax
          .post(
            "/renjamurni/report/forma",
            {
              RKAID: RKAID,
              no_bulan: this.bulan_realisasi,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.total_forma = data.total_data;
            this.datatabledetail = data.rka;
            this.chartrealisasikeuangan_murni[0] = data.chart_keuangan_murni[0];
            this.chartrealisasikeuangan_murni[1] = data.chart_keuangan_murni[1];
            this.chartrealisasifisik_murni[0] = data.chart_fisik_murni[0];
            this.chartrealisasifisik_murni[1] = data.chart_fisik_murni[1];
            this.chartLoaded = true;
          });
      },
      async printtoexcel() {
        var SOrgID_Selected = this.$store.getters[
          "uiadmin/AtributeValueOfPage"
        ]("formamurni", "SOrgID_Selected");
        this.btnLoading = true;
        await this.$ajax
          .post(
            "/renjamurni/report/forma/printtoexcel",
            {
              SOrgID: SOrgID_Selected,
              RKAID: this.datakegiatan.RKAID,
              no_bulan: this.bulan_realisasi,
              tahun: this.$store.getters["auth/TahunSelected"],
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
            link.setAttribute("download", "form_a_" + Date.now() + ".xlsx");
            document.body.appendChild(link);
            link.click();
            this.btnLoading = false;
          })
          .catch(() => {
            this.btnLoading = false;
          });
      },
      colorRowFormA(item) {
        var color = "";
        if (item.isprogram == 1) {
          color = "lime lighten-3";
        } else if (item.iskegiatan == 1) {
          color = "lime lighten-4";
        } else if (item.issubkegiatan == 1) {
          color = "white";
        } else {
          color = "white";
        }
        return color;
      },
      fontWeight(item) {
        var weight = "";
        if (item.isprogram == 1) {
          weight = "font-weight-bold";
        } else if (item.iskegiatan == 1) {
          weight = "font-weight-medium";
        } else if (item.issubkegiatan == 1) {
          weight = "Normal weight text";
        } else {
          weight = "Normal weight text";
        }
        return weight;
      },
      exitforma() {
        var page = this.$store.getters["uiadmin/Page"]("formamurni");
        page.formadetail = false;
        page.datakegiatan = [];
        this.$store.dispatch("uiadmin/updatePage", page);
        this.$router.go();
      },
    },
    watch: {
      OrgID_Selected(val) {
        var page = this.$store.getters["uiadmin/Page"]("formamurni");
        if (this.firstloading == true && val.length > 0) {
          page.OrgID_Selected = val;
          this.$store.dispatch("uiadmin/updatePage", page);
          this.loadunitkerja();
        } else if (this.firstloading == false && val.length > 0) {
          page.OrgID_Selected = val;
          this.$store.dispatch("uiadmin/updatePage", page);
          this.loadunitkerja();

          this.datatableLoaded = false;
          this.datatable = [];
        }
      },
      SOrgID_Selected(val) {
        var page = this.$store.getters["uiadmin/Page"]("formamurni");
        if (this.firstloading == false && val.length > 0) {
          this.datatableLoaded = false;
        }
        page.SOrgID_Selected = val;
        this.$store.dispatch("uiadmin/updatePage", page);
        this.loaddatakegiatan();
      },
    },
    components: {
      RenjaMurniLayout,
      ModuleHeader,
      Filter2,
      "chart-realisasi-keuangan": ChartTargetRealisasi,
      "chart-realisasi-fisik": ChartTargetRealisasi,
    },
  };
</script>
