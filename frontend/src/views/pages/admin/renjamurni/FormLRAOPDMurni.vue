<template>
  <RenjaMurniLayout :temporaryleftsidebar="true">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        LAPORAN REALISASI ANGGARAN
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
          Laporan Realisasi Anggaran OPD APBD Murni
          s.d
          <strong>
            BULAN {{ nama_bulan }} T.A
            {{ $store.getters["auth/TahunSelected"] }}
          </strong>
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container fluid>
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
            Catatan: Nilai realisasi anggaran dihitung akumulasi s.d
            <strong>
              BULAN {{ nama_bulan }} T.A
              {{ $store.getters["auth/TahunSelected"] }}
            </strong>
          </v-alert>
        </v-col>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="datatable"
            :search="search"
            item-key="FormLRAMurniDetailID"
            dense
            :single-expand="true"
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
            :disable-pagination="true"
            :hide-default-footer="true"
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
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
            <template v-slot:body="{ items }">
              <tbody>
                <tr
                  v-for="item in items"
                  v-bind:key="item.FormLRAMurniDetailID"
                  :class="color_tingkat(item.tingkat)"
                  @click="expand(item)"
                >
                  <td>{{ item.kode }}</td>
                  <td>{{ item.nama_uraian }}</td>
                  <td class="text-right">
                    {{ item.pagu_uraian | formatUang }}
                  </td>
                  <td class="text-right">{{ item.realisasi | formatUang }}</td>
                  <td class="text-center">{{ item.persen_realisasi }}</td>
                </tr>
              </tbody>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-card>
            <v-card-title>
              DAFTAR KODE REKENING YANG TIDAK ADA DI DATA MASTER
            </v-card-title>
            <v-card-text>
              {{ daftar_rek_tidak_terdaftar }}
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
  export default {
    name: "FormLRAOPDMurni",
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
          href: "/belanjamurni",
        },
        {
          text: "LAPORAN",
          disabled: false,
          href: "#",
        },
        {
          text: "LAPORAN REALISASI ANGGARAN OPD",
          disabled: true,
          href: "#",
        },
      ];
      this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
      this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
        this.bulan_realisasi
      );

      this.$store.dispatch("uiadmin/addToPages", {
        name: "lraopdmurni",
        OrgID_Selected: "",
      });
    },
    mounted() {
      this.fetchOPD();
      var OrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
        "lraopdmurni",
        "OrgID_Selected"
      );
      if (OrgID_Selected.length > 0) {
        this.OrgID_Selected = OrgID_Selected;
        this.firstloading = false;
        this.$refs.filter2.setFirstTimeLoading(this.firstloading);
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
        //Organisasi
        DataOPD: null,

        //data table
        datatableLoaded: true,
        datatableLoading: false,
        expanded: [],
        datatable: [],
        headers: [
          { text: "KODE REKENING", value: "kode", width: 80, sortable: false },
          {
            text: "URAIAN",
            value: "nama_uraian",
            sortable: false,
          },
          {
            text: "ANGGARAN",
            value: "pagu_uraian",
            width: 150,
            align: "right",
            sortable: false,
          },
          {
            text: "REALISASI",
            value: "realisasi",
            width: 150,
            align: "right",
            sortable: false,
          },
          {
            text: "%",
            value: "persen_realisasi",
            width: 150,
            align: "center",
            sortable: false,
          },
        ],
        daftar_rek_tidak_terdaftar: [],
        search: "",
      };
    },
    methods: {
      color_tingkat: function(tingkat) {
        if (tingkat == 1) {
          return "tingkat-1";
        } else if (tingkat == 2) {
          return "tingkat-2";
        } else if (tingkat == 3) {
          return "tingkat-3";
        } else if (tingkat == 4) {
          return "tingkat-4";
        } else if (tingkat == 5) {
          return "tingkat-5";
        } else if (tingkat == 6) {
          return "tingkat-6";
        }
      },
      changeBulanRealisasi(bulan_realisasi) {
        this.bulan_realisasi = bulan_realisasi;
        this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
          bulan_realisasi
        );
        this.loaddatakegiatan();
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
      loaddatakegiatan: async function() {
        this.datatableLoading = true;
        await this.$ajax
          .post(
            "/renjamurni/report/lraopd",
            {
              tahun: this.$store.getters["auth/TahunSelected"],
              no_bulan: this.bulan_realisasi,
              OrgID: this.OrgID_Selected,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.datatable = data.lra;
            this.daftar_rek_tidak_terdaftar = data.daftar_rek_tidak_terdaftar;
            this.datatableLoaded = false;
            this.datatableLoading = false;
          });
      },
      printtoexcel: async function() {
        this.btnLoading = true;
        await this.$ajax
          .post(
            "/renjamurni/report/lraopd/printtoexcel",
            {
              tahun: this.$store.getters["auth/TahunSelected"],
              no_bulan: this.bulan_realisasi,
              OrgID: this.OrgID_Selected,
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

            link.setAttribute(
              "download",
              "lra_opd_murni_" + Date.now() + ".xlsx"
            );
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
      OrgID_Selected(val) {
        var page = this.$store.getters["uiadmin/Page"]("lraopdmurni");
        if (this.firstloading == false && val.length > 0) {
          this.datatableLoaded = false;
        }
        page.OrgID_Selected = val;
        this.$store.dispatch("uiadmin/updatePage", page);
        this.loaddatakegiatan();
      },
    },
    components: {
      RenjaMurniLayout,
      ModuleHeader,
      Filter2,
    },
  };
</script>
