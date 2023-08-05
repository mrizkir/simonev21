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
        name: "snapshotmurni",
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
        "snapshotmurni",
        "OrgID_Selected"
      );
      var SOrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
        "snapshotmurni",
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
    },
    watch: {
      OrgID_Selected(val) {
        var page = this.$store.getters["uiadmin/Page"]("snapshotmurni");
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
        var page = this.$store.getters["uiadmin/Page"]("snapshotmurni");
        if (this.firstloading == false && val.length > 0) {
          this.datatableLoaded = false;
        }
        page.SOrgID_Selected = val;
        this.$store.dispatch("uiadmin/updatePage", page);
        // this.loaddatakegiatan();
      },
    },
    components: {
      RenjaMurniLayout,
      ModuleHeader,
      Filter2,
    },
  }
</script>