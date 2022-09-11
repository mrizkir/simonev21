<template>
  <RenjaMurniLayout :showrightsidebar="false">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-chart-timeline-variant
      </template>
      <template v-slot:name>
        PERINGKAT OPD MURNI
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
          Berisi peringkat progres realisasi fisik dan keuangan terakhir OPD
        </v-alert>
      </template>
    </ModuleHeader>
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
              ></v-text-field>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="datatable"
            item-key="OrgID"
            :search="search"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
            class="elevation-1"
            :disable-pagination="true"
            :hide-default-footer="true"
            dense
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>PERINGKAT OPD</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
              </v-toolbar>
            </template>
            <template v-slot:item.RealisasiFisik1="{ item }">
              {{ item.RealisasiFisik1 | makeLookPrecision }}
            </template>
            <template v-slot:item.PersenRealisasiKeuangan1="{ item }">
              {{ item.PersenRealisasiKeuangan1 | makeLookPrecision }}
            </template>
            <template v-slot:no-data>
              data peringkat opd tidak tersedia.
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
  </RenjaMurniLayout>
</template>
<script>
  import RenjaMurniLayout from "@/views/layouts/RenjaMurniLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "StatistikPeringkatOPDMurni",
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
          text: "STATISTIK",
          disabled: false,
          href: "#",
        },
        {
          text: "PERINGKAT OPD",
          disabled: true,
          href: "#",
        },
      ];
    },
    mounted() {
      this.initialize();
    },
    data: () => ({
      search: "",
      datatableLoading: false,
      peringkat: [],			
      headers: [
        { text: "NOMOR", value: "index", width: 100 },
        { text: "KODE", value: "kode_organisasi", width: 160, sortable: false },
        { text: "NAMA OPD", value: "OrgNm", sortable: false },
        { text: "REALISASI FISIK", align: "end", value: "RealisasiFisik1" },
        {
          text: "REALISASI KEUANGAN",
          align: "end",
          value: "PersenRealisasiKeuangan1",
        },
      ],
    }),
    methods: {
      initialize: async function() {
        this.datatableLoading = true;
        await this.$ajax
          .post(
            "/renjamurni/statistik/peringkatopd",
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
            this.peringkat = data.peringkat;
            this.datatableLoading = false;
          });
      },
    },
    computed: {
      datatable() {
        return this.peringkat.map((items, index) => ({
          ...items,
          index: index + 1,
        }));
      },
    },
    components: {
      RenjaMurniLayout,
      ModuleHeader,
    },
  };
</script>
