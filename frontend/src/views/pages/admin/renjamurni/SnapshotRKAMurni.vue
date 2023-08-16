<template>
  <RenjaMurniLayout :showrightsidebar="true">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-source-fork
      </template>
      <template v-slot:name>
        SNAPSHOT RENJA MURNI
        
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
          Snapshot Rencana Kegiatan dan Anggaran (RKA) OPD / Unit Kerja APBD Murni <strong>bulan {{ nama_bulan }} T.A {{ $store.getters["auth/TahunSelected"] }}</strong>
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
              >
              </v-text-field>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="datatable"
            :search="search"
            item-key="RKAID"
            show-expand
            dense
            :expanded.sync="expanded"
            :single-expand="true"
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
            @click:row="dataTableRowClicked"
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>DAFTAR SUB KEGIATAN</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      v-bind="attrs"
                      v-on="on"
                      color="red"
                      icon                      
                      small
                      class="ma-2"
                      @click.stop="deleteSnapshot()"
                      :disabled="btnLoading || !(SOrgID_Selected.length > 0) || !(datatable.length > 0)"
                    >
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </template>
                  <span>HAPUS SNAPSHOT BULAN INI</span>
                </v-tooltip>
              </v-toolbar>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    small
                    v-bind="attrs"
                    v-on="on"
                    color="primary"
                    class="ma-1"
                    @click.stop="viewUraian(item)"
                  >
                    mdi-eye
                  </v-icon>
                </template>
                <span>detail uraian kegiatan</span>
              </v-tooltip>                           
            </template>
            <template v-slot:item.PaguDana1="{ item }">
              {{ item.PaguDana1 | formatUang }}
            </template>
            <template v-slot:item.RealisasiKeuangan1="{ item }">
              {{ item.RealisasiKeuangan1 | formatUang }}
            </template>
            <template v-slot:item.SisaAnggaran="{ item }">
              {{ (item.PaguDana1 - item.RealisasiKeuangan1) | formatUang }}
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <v-col cols="12" class="mb1">
                  <strong>ID:</strong>{{ item.RKAID }}
                  <strong>created_at:</strong>
                  {{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
                  <strong>updated_at:</strong>
                  {{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
                </v-col>
                <v-col
                  cols="12"
                  class="mb1 text-center"
                  v-if="item.Locked == 0"
                >
                  <v-btn
                    color="blue darken-1"
                    text
                    @click.stop="resetdatakegiatan(item)"
                    :disabled="btnLoading || item.Locked == 1"
                  >
                    RESET
                  </v-btn>
                </v-col>
              </td>
            </template>
            <template v-slot:body.append>
              <tr class="amber darken-1 font-weight-black">
                <td colspan="3" class="text-right">TOTAL</td>
                <td class="text-right">
                  {{ footers.pagukegiatan | formatUang }}
                </td>
                <td class="text-right">{{ footers.fisik }}</td>
                <td class="text-right">{{ footers.realisasi | formatUang }}</td>
                <td class="text-right">
                  {{ footers.persen_keuangan.toFixed(2) }}
                </td>
                <td class="text-right">
                  {{ footers.sisa | formatUang }}
                </td>
                <td></td>
              </tr>
            </template>
            <template v-slot:no-data>
              <v-btn
                class="ma-2"
                :loading="btnLoading"
                :disabled="showBtnLoadDataKegiatan || btnLoading"
                color="primary"
                @click.stop="loaddatakegiatanFirsttime"
              >
                BUAT SNAPSHOT
                <template v-slot:loader>
                  <span>BUAT SNAPSHOT ...</span>
                </template>
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
        <v-col cols="12">
          <strong>Total Realisasi Fisik</strong> : (Total Realisasi Fisik / Jumlah Sub Kegiatan)<br />
          <strong>Total Persen Realisasi Keuangan</strong> : (Total Realisasi Keuangan / Pagu Kegiatan) * 100<br />
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
    name: "SnapshotRKAMurni",
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
          text: "SNAPSHOT RKA MURNI",
          disabled: true,
          href: "#",
        },
      ];
      this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
      this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
        this.bulan_realisasi
      );

      this.$store.dispatch("uiadmin/addToPages", {
        name: "snapshotrkamurni",
        OrgID_Selected: "",
        SOrgID_Selected: "",
        datakegiatan: {
          RKAID: "",
        },
        datauraian: {
          RKARincID: "",
        },
        datarekening: {},
      });
    },
    mounted() {
      this.fetchOPD();
      var OrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
        "snapshotrkamurni",
        "OrgID_Selected"
      );
      var SOrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
        "snapshotrkamurni",
        "SOrgID_Selected"
      );
      if (OrgID_Selected.length > 0) {
        this.OrgID_Selected = OrgID_Selected;
        this.SOrgID_Selected = SOrgID_Selected;
      }
      if (SOrgID_Selected.length > 0) {
        this.OrgID_Selected = OrgID_Selected;
        this.SOrgID_Selected = SOrgID_Selected;
      }
      this.firstloading = false;
    },
    data() {
      return {
        firstloading: true,
        expanded: [],
        search: "",
        btnLoading: false,
        datatableLoading: false,
        datatableLoaded: false,
        datatable: [],
        headers: [
          { text: "KODE", value: "kode_sub_kegiatan", width: 80 },
          {
            text: "NAMA SUB KEGIATAN",
            value: "Nm_Sub_Kegiatan",
            width: 300,
          },
          {
            text: "PAGU KEGIATAN",
            value: "PaguDana1",
            align: "end",
            width: 100,
          },
          {
            text: "REALISASI FISIK",
            value: "RealisasiFisik1",
            align: "end",
            width: 100,
          },
          {
            text: "REALISASI KEUANGAN",
            value: "RealisasiKeuangan1",
            align: "end",
            width: 100,
          },
          { text: "%", align: "end", value: "persen_keuangan1", width: 50 },
          {
            text: "SISA PAGU",
            value: "SisaAnggaran",
            align: "end",
            width: 100,
          },
          { text: "AKSI", value: "actions", sortable: false, width: 110 },
        ],
        footers: {
          paguunitkerja: 0,
          pagukegiatan: 0,
          realisasi: 0,
          sisa: 0,
          persen_keuangan: 0,
          fisik: 0,
        },
        //filter form
        daftar_opd: [],
        OrgID_Selected: "",
        daftar_unitkerja: [],
        SOrgID_Selected: "",
        bulan_realisasi: null,
        nama_bulan: null,
      };
    },
    methods: {
      dataTableRowClicked(item) {
        if (item === this.expanded[0]) {
          this.expanded = [];
        } else {
          this.expanded = [item];
        }
      },
      changeBulanRealisasi(bulan_realisasi) {
        this.bulan_realisasi = bulan_realisasi;
        this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
          bulan_realisasi
        );
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
          .then(({ data, status }) => {
            if (status == 200) {
              this.daftar_opd = data.opd;
              this.datatableLoaded = false;
            }
          });
      },
      loadunitkerja: async function() {
        await this.$ajax
          .get("/dmaster/opd/" + this.OrgID_Selected + "/unitkerja", {
            headers: {
              Authorization: this.$store.getters["auth/Token"],
            },
          })
          .then(({ data }) => {
            this.DataOPD = data.organisasi;
            this.daftar_unitkerja = data.unitkerja;
            this.datatableLoaded = false;
          });
      },
      loaddatakegiatanFirsttime: async function() {
        this.btnLoading = true;
        await this.$ajax
          .post(
            "/snapshot/rkamurni/loaddatakegiatanfirsttime",
            {
              tahun: this.$store.getters["auth/TahunSelected"],
              bulan: this.$store.getters["uifront/getBulanRealisasi"],
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
            this.footersummary();
            this.btnLoading = false;
          })
          .catch(() => {
            this.btnLoading = false;
          });
      },
      loaddatakegiatan: async function() {
        this.datatableLoading = true;
        await this.$ajax
          .post(
            "/snapshot/rkamurni",
            {
              tahun: this.$store.getters["auth/TahunSelected"],
              bulan: this.$store.getters["uifront/getBulanRealisasi"],
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
            this.datatableLoaded = true;
            this.datatableLoading = false;
            this.footersummary();
          })
          .catch(() => {
            this.btnLoading = false;
          });
      },
      deleteSnapshot() {
        let tahun = this.$store.getters["auth/TahunSelected"];
        this.$root.$confirm
          .open(
            "Delete",
            "Apakah Anda ingin menghapus data snapshot bulan " +
            this.nama_bulan + ' T.A ' + tahun +
              " ?",
            { color: "red", width: "600px" }
          )
          .then(confirm => {
            if (confirm) {
              this.btnLoading = true;
              this.$ajax
                .post(
                  "/snapshot/rkamurni/" + tahun + this.$store.getters["uifront/getBulanRealisasi"],
                  {
                    _method: "DELETE",
                    pid: "all",                    
                    SOrgID: this.SOrgID_Selected,
                  },
                  {
                    headers: {
                      Authorization: this.$store.getters["auth/Token"],
                    },
                  }
                )
                .then(() => {
                  this.$router.go();
                })
                .catch(() => {
                  this.btnLoading = false;
                });
            }
          });
      },
      viewUraian(item) {
        var page = this.$store.getters["uiadmin/Page"]("snapshotrkamurni");
        if (page.datakegiatan.RKAID == "") {
          page.datakegiatan = item;
          this.$store.dispatch("uiadmin/updatePage", page);
          this.$router.push(
            "/renjamurni/snapshot/rka/uraian/" + page.datakegiatan.RKAID
          );
        } else {
          this.$root.$confirm
            .open(
              "INFO",
              "Kegiatan lain sedang dibuka, jadi tidak bisa membuka kegiatan ini",
              { color: "warning" }
            )
            .then(confirm => {
              if (confirm) {
                this.$router.push(
                  "/renjamurni/snapshot/rka/uraian/" + page.datakegiatan.RKAID
                );
              }
            });
        }
      },
    },
    computed: {
      showBtnLoadDataKegiatan() {
        var bool = true;
        if (this.SOrgID_Selected.length > 0 && this.datatableLoaded == true) {
          bool = this.datatable.length > 0;
        }
        return bool;
      },
    },
    watch: {
      OrgID_Selected(val) {
        var page = this.$store.getters["uiadmin/Page"]("snapshotrkamurni");
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
        var page = this.$store.getters["uiadmin/Page"]("snapshotrkamurni");
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
    },
  }
</script>