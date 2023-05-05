<template>
  <SystemSettingLayout>
    <ModuleHeader>
      <template v-slot:icon>
        mdi-lock-check
      </template>
      <template v-slot:name>
        LOCK OPD
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
          Mengatur perizinan OPD menginput data per tahun anggaran pada masa pelaporan <strong>{{ $store.getters["uiadmin/getMasaPelaporan"].toUpperCase() }}</strong>. 
          Tahun Anggaran {{ $store.getters["uifront/getNamaBulan"](bulan_realisasi) }} {{ $store.getters["auth/TahunSelected"] }} 
        </v-alert>
      </template>
    </ModuleHeader>
    <template v-slot:filtersidebar>
      <Filter2 v-on:changeBulanRealisasi="changeBulanRealisasi" ref="filter2" />
    </template>
    <v-container fluid>
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
            item-key="OrgID"
            sort-by="kode_organisasi"
            show-expand
            :expanded.sync="expanded"
            dense
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
            :single-expand="true"
            class="elevation-1"
            @click:row="dataTableRowClicked"
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>
                  DAFTAR OPD
                </v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-switch
                      v-bind="attrs"
                      v-on="on"
                      v-model="toggleLockOpen"
                      @click.stop="lockAll()"
                      label="KUNCI"
                      :disabled="btnLoading"
                      class="mr-2"
                    />
                  </template>
                  <span>Kunci Seluru OPD</span>
                </v-tooltip>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-switch
                      v-bind="attrs"
                      v-on="on"
                      v-model="toggleLockClose"
                      @click.stop="lockAll()"
                      label="BUKA"
                      :disabled="btnLoading"
                    />
                  </template>
                  <span>Kunci Seluru OPD</span>
                </v-tooltip>
              </v-toolbar>
            </template>
            <template v-slot:item.Nm_Bidang_1="{ item }">
              {{ item.Nm_Bidang_1 }}
              {{ item.Nm_Bidang_2 }}
              {{ item.Nm_Bidang_3 }}
            </template>
            <template v-slot:item.actions="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-switch
                    v-bind="attrs"
                    v-on="on"
                    v-model="item.Locked"
                    :label="item.Locked == 1 ? 'BUKA' : 'KUNCI'"
                    @click.stop="changeLock(item)"
                    :disabled="btnLoading"
                  />
                </template>
                <span>Kunci</span>
              </v-tooltip>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <strong>ID:</strong>{{ item.OrgID }}
                <strong>created_at:</strong>
                {{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
                <strong>updated_at:</strong>
                {{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
              </td>
            </template>
            <template v-slot:no-data>
              <v-col cols="12">
                Belum ada data OPD
              </v-col>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
  </SystemSettingLayout>
</template>
<script>
  import SystemSettingLayout from "@/views/layouts/SystemSettingLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  import Filter2 from "@/components/sidebar/FilterMode2";
  export default {
    name: "LockOPD",
    created() {
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
        },
        {
          text: "KONFIGURASI SISTEM",
          disabled: false,
          href: "/system-setting",
        },
        {
          text: "INSTITUSI",
          disabled: false,
          href: "#",
        },
        {
          text: "LOCK OPD",
          disabled: true,
          href: "#",
        },
      ];
      this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
    },
    mounted() {
      this.initialize();
      this.firstloading = false;
      this.$refs.filter2.setFirstTimeLoading(this.firstloading);
    },
    data: () => ({
      breadcrumbs: [],
      firstloading: true,
      bulan_realisasi: null,
      datatableLoading: false,
      datatableLoaded: false,
      expanded: [],
      datatable: [],
      btnLoading: false,
      toggleLockOpen: 0,
      toggleLockClose: 0,
      headers: [
        { text: "KODE OPD", value: "kode_organisasi", width: 150 },
        { text: "NAMA OPD", value: "Nm_Organisasi", width: 300 },
        { text: "BIDANG URUSAN", value: "Nm_Bidang_1", width: 200 },
        { text: "KEPALA OPD", value: "NamaKepalaSKPD", width: 200 },
        { text: "AKSI", value: "actions", sortable: false, width: 100 },
      ],
      search: "",
    }),
    methods: {
      changeBulanRealisasi(bulan) {
        this.bulan_realisasi = bulan;
      },
      initialize: async function() {
        this.datatableLoading = true;
        await this.$ajax
          .post(
            "/dmaster/opd/lockedall",
            {
              tahun: this.$store.getters["auth/TahunSelected"],
              bulan: this.bulan_realisasi,
              masapelaporan: this.$store.getters["uiadmin/getMasaPelaporan"],
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            var opd = data.opd;
            for (var key in opd) {
              if (opd[key].Kd_Organisasi < 10) {
                opd[key].Kd_Organisasi = "0" + opd[key].Kd_Organisasi;
              }
            }
            this.datatable = data.opd;
            this.datatableLoaded = true;
            this.datatableLoading = false;
          });
      },
      dataTableRowClicked(item) {
        if (item === this.expanded[0]) {
          this.expanded = [];
        } else {
          this.expanded = [item];
        }
      },
      changeLock(item) {
        this.btnLoading = true;
        this.datatableLoading = true;
        this.$ajax
          .post(
            "/dmaster/opd/" + item.OrgID + "/lock",
            {
              _method: "PUT",
              tahun: this.$store.getters["auth/TahunSelected"],
              bulan: this.bulan_realisasi,
              status: item.Locked == true ? 1 : 0,
              masapelaporan: this.$store.getters["uiadmin/getMasaPelaporan"],
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(() => {
            this.datatableLoading = false;
            this.btnLoading = false;
          })
          .catch(() => {
            this.btnLoading = false;
            this.datatableLoading = false;
          });
      },
      lockAll() {
        this.btnLoading = true;
        this.$ajax
          .post(
            "/dmaster/opd/lockall",
            {
              _method: "PUT",
              tahun: this.$store.getters["auth/TahunSelected"],
              bulan: this.bulan_realisasi,
              status: this.toggleLockOpen,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(() => {
            this.initialize();
            this.btnLoading = false;
            this.toggleLockOpen = 0;
            this.toggleLockClose = 0;
          })
          .catch(() => {
            this.btnLoading = false;
          });
      },
    },
    watch: {
      bulan_realisasi() {
        if (!this.firstloading) {
          this.initialize();
        }
      },
    },
    components: {
      SystemSettingLayout,
      ModuleHeader,
      Filter2,
    },
  };
</script>
