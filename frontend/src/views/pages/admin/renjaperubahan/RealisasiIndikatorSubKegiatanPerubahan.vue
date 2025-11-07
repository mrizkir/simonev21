<template>
  <RenjaPerubahanLayout :temporaryleftsidebar="true">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-chart-line
      </template>
      <template v-slot:name>
        REALISASI INDIKATOR SUB KEGIATAN PERUBAHAN
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
          Laporan Realisasi Indikator Sub Kegiatan Rencana Kegiatan dan Anggaran (RKA) OPD APBD
          Perubahan s.d
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
            Catatan: Nilai realisasi dihitung akumulasi s.d
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
            item-key="no"
            dense
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
            :disable-pagination="true"
            :hide-default-footer="true"
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>DAFTAR REALISASI INDIKATOR SUB KEGIATAN</v-toolbar-title>
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
            <template v-slot:item.realisasi="{ item }">
              {{ item.realisasi }}
            </template>
            <template v-slot:item.pencapaian="{ item }">
              {{ item.pencapaian | makeLookPrecision }}
            </template>
            <template v-slot:item.rasio_realisasi="{ item }">
              {{ item.rasio_realisasi | makeLookPrecision }}
            </template>
            <template v-slot:body="{ items }">
              <tbody>
                <tr
                  v-for="item in items"
                  v-bind:key="item.no"
                >
                  <td class="text-center">{{ item.no }}</td>
                  <td>{{ item.nama_sub_kegiatan }}</td>
                  <td>{{ item.indikator_kegiatan }}</td>
                  <td>{{ item.komponen }}</td>
                  <td class="text-right">
                    {{ item.realisasi }}
                  </td>
                  <td class="text-right">
                    {{ item.pencapaian | makeLookPrecision }}
                  </td>
                  <td class="text-right">
                    {{ item.pagu | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ item.realisasi_pagu | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ item.rasio_realisasi | makeLookPrecision }}
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="orange font-weight-bold dark">
                  <td colspan="4" class="text-right">TOTAL</td>
                  <td class="text-right">
                    {{ total_data.total_pagu | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ total_data.total_realisasi_pagu | formatUang }}
                  </td>
                  <td class="text-right">
                    {{ total_data.total_rasio_realisasi | makeLookPrecision }}
                  </td>
                </tr>
              </tfoot>
            </template>
            <template v-slot:no-data>
              Belum ada data realisasi indikator sub kegiatan.
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
    <template v-slot:filtersidebar>
      <Filter2 v-on:changeBulanRealisasi="changeBulanRealisasi" ref="filter2" />
    </template>
  </RenjaPerubahanLayout>
</template>
<script>
  import RenjaPerubahanLayout from "@/views/layouts/RenjaPerubahanLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  import Filter2 from "@/components/sidebar/FilterMode2";
  export default {
    name: "RealisasiIndikatorSubKegiatanPerubahan",
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
          text: "LAPORAN",
          disabled: false,
          href: "#",
        },
        {
          text: "REALISASI INDIKATOR SUB KEGIATAN",
          disabled: true,
          href: "#",
        },
      ];
      this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
      this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
        this.bulan_realisasi
      );
      this.$store.dispatch("uiadmin/addToPages", {
        name: "realisasiindikatorsubkegiatanperubahan",
        OrgID_Selected: "",
        SOrgID_Selected: "",
      });
    },
    mounted() {
      this.fetchOPD();
      var OrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
        "realisasiindikatorsubkegiatanperubahan",
        "OrgID_Selected"
      );
      var SOrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
        "realisasiindikatorsubkegiatanperubahan",
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
        datatable: [],
        headers: [
          { 
            text: "NO", 
            value: "no", 
            width: 60,
            align: "center",
            sortable: false 
          },
          {
            text: "NAMA SUB KEGIATAN",
            value: "nama_sub_kegiatan",
            width: 300,
            sortable: false,
          },
          {
            text: "INDIKATOR KEGIATAN",
            value: "indikator_kegiatan",
            width: 250,
            sortable: false,
          },
          {
            text: "KOMPONEN",
            value: "komponen",
            width: 120,
            sortable: false,
          },
          {
            text: "REALISASI",
            value: "realisasi",
            align: "end",
            width: 120,
            sortable: false,
          },
          {
            text: "PENCAPAIAN (%)",
            value: "pencapaian",
            align: "end",
            width: 120,
            sortable: false,
          },
          {
            text: "PAGU",
            value: "pagu",
            align: "end",
            width: 120,
            sortable: false,
          },
          {
            text: "REALISASI PAGU",
            value: "realisasi_pagu",
            align: "end",
            width: 120,
            sortable: false,
          },
          {
            text: "RASIO REALISASI (%)",
            value: "rasio_realisasi",
            align: "end",
            width: 120,
            sortable: false,
          },
        ],
        search: "",
        total_data: {
          total_pagu: 0,
          total_realisasi_pagu: 0,
          total_rasio_realisasi: 0,
        },
      };
    },
    methods: {
      changeBulanRealisasi(bulan_realisasi) {
        this.bulan_realisasi = bulan_realisasi;
        this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
          bulan_realisasi
        );
        this.loaddata();
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
      async loaddata() {
        this.datatableLoading = true;
        await this.$ajax
          .post(
            "/renjaperubahan/report/realisasiindikatorsubkegiatan",
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
            this.datatable = data.data;
            this.total_data = data.total_data;
            this.datatableLoaded = false;
            this.datatableLoading = false;
          });
      },
      printtoexcel: async function() {
        this.btnLoading = true;
        await this.$ajax
          .post(
            "/renjaperubahan/report/realisasiindikatorsubkegiatan/printtoexcel",
            {
              tahun: this.$store.getters["auth/TahunSelected"],
              no_bulan: this.bulan_realisasi,
              SOrgID: this.SOrgID_Selected,
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
            link.setAttribute("download", "realisasi_indikator_sub_kegiatan_perubahan_" + Date.now() + ".xlsx");
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
        var page = this.$store.getters["uiadmin/Page"]("realisasiindikatorsubkegiatanperubahan");
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
        var page = this.$store.getters["uiadmin/Page"]("realisasiindikatorsubkegiatanperubahan");
        if (this.firstloading == false && val.length > 0) {
          this.datatableLoaded = false;
        }
        page.SOrgID_Selected = val;
        this.$store.dispatch("uiadmin/updatePage", page);
        this.loaddata();
      },
    },
    components: {
      RenjaPerubahanLayout,
      ModuleHeader,
      Filter2,
    },
  };
</script>

