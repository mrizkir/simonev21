<template>
  <RenjaPerubahanLayout :temporaryleftsidebar="true">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        LAPORAN REKAP LRA BELANJA
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
          Laporan Rekapitulasi LRA Belanja APBD Perubahan
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
            item-key="FormLRAPerubahanDetailID"
            dense
            :single-expand="true"
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
            :disable-pagination="true"
            :hide-default-footer="true"
          >
            <template v-slot:body.append>
              <tr class="amber darken-1 font-weight-black">
                <td colspan="2" class="text-right">TOTAL</td>
                <td class="text-right">
                  {{ total_belanja_pegawai | formatUang }}
                </td>
                <td class="text-right">
                  {{ total_belanja_barang_jasa | formatUang }}
                </td>                
                <td class="text-right">
                  {{ total_belanja_modal | formatUang }}
                </td>                                
              </tr>
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
    name: "FormRekapLRAPerubahan",
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
          href: "/belanjaperubahan",
        },
        {
          text: "LAPORAN",
          disabled: false,
          href: "#",
        },
        {
          text: "LAPORAN REKAP LRA BELANJA",
          disabled: true,
          href: "#",
        },
      ];
      this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
      this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
        this.bulan_realisasi
      );
      
    },
    mounted() {
      this.initialize();

      this.firstloading = false;
      this.$refs.filter2.setFirstTimeLoading(this.firstloading);
    },
    data() {
      return {
        btnLoading: false,
        firstloading: true,
        bulan_realisasi: null,
        nama_bulan: null,

        //total
        total_belanja_pegawai: 0,
        total_belanja_barang_jasa: 0,
        total_belanja_modal: 0,

        //data table
        datatableLoaded: true,
        datatableLoading: false,
        expanded: [],
        datatable: [],
        headers: [
          { text: "KODE OPD", value: "kode_organisasi", width: 200, sortable: false },
          {
            text: "NAMA OPD",
            value: "Nm_Organisasi",
            sortable: false,
          },
          {
            text: "BELANJA PEGAWAI",
            value: "rp_belanja_pegawai",
            width: 150,
            align: "right",
            sortable: false,
          },
          {
            text: "BELANJA BARANG & JASA",
            value: "rp_belanja_barang_jasa",
            width: 200,
            align: "right",
            sortable: false,
          },
          {
            text: "BELANJA MODAL",
            value: "rp_belanja_modal",
            width: 150,
            align: "center",
            sortable: false,
          },
        ],
        search: "",
      };
    },
    methods: {      
      changeBulanRealisasi(bulan_realisasi) {
        this.bulan_realisasi = bulan_realisasi;
        this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
          bulan_realisasi
        );
        console.log(this.bulan_realisasi);
        this.initialize();
      },      
      initialize: async function() {
        this.datatableLoading = true;        
        await this.$ajax
          .post(
            "/renjaperubahan/report/rekaplra",
            {
              tahun: this.$store.getters["auth/TahunSelected"],
              no_bulan: this.bulan_realisasi,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.datatable = data.rekap_lra;            
            this.datatableLoaded = false;
            this.datatableLoading = false;

            this.total_belanja_pegawai = data.total_belanja_pegawai;
            this.total_belanja_barang_jasa = data.total_belanja_barang_jasa;
            this.total_belanja_modal = data.total_belanja_modal;
          });
      },
      printtoexcel: async function() {
        this.btnLoading = true;
        await this.$ajax
          .post(
            "/renjaperubahan/report/rekaplra/printtoexcel",
            {
              tahun: this.$store.getters["auth/TahunSelected"],
              no_bulan: this.bulan_realisasi,
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
              "lra_opd_perubahan_" + Date.now() + ".xlsx"
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
    components: {
      RenjaPerubahanLayout,
      ModuleHeader,
      Filter2,
    },
  };
</script>
