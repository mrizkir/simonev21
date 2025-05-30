<template>
  <RenjaPerubahanLayout :showrightsidebar="false">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        RKA PERUBAHAN
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
          Realisasi Uraian Rencana Kegiatan dan Anggaran (RKA) OPD / Unit Kerja
          APBD Perubahan
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container
      v-if="
        Object.keys(this.datakegiatan).length > 2 &&
          Object.keys(this.datauraian).length > 2
      "
    >
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-card>
            <v-card-title>
              DATA URAIAN
            </v-card-title>
            <v-card-text>
              <table class="table">
                <tbody>
                  <tr>
                    <td width="150" class="font-weight-bold">RKARincID</td>
                    <td width="400">{{ datauraian.RKARincID }}</td>
                    <td width="150" class="font-weight-bold">KODE UNIT</td>
                    <td width="400">{{ datakegiatan.kode_sub_organisasi }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">KODE URAIAN</td>
                    <td width="400">{{ datauraian.kode_uraian }}</td>
                    <td width="150" class="font-weight-bold">UNIT KERJA</td>
                    <td width="400">{{ datakegiatan.Nm_Sub_Organisasi }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">NAMA URAIAN</td>
                    <td width="400">{{ datauraian.nama_uraian }}</td>
                    <td width="150" class="font-weight-bold">KODE KEGIATAN</td>
                    <td width="400">{{ datakegiatan.kode_kegiatan }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">VOLUME</td>
                    <td width="400">{{ datauraian.volume }}</td>
                    <td width="150" class="font-weight-bold">NAMA KEGIATAN</td>
                    <td width="400">{{ datakegiatan.Nm_Kegiatan }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">HARGA SATUAN</td>
                    <td width="400">
                      {{ datauraian.harga_satuan1 | formatUang }}
                    </td>
                    <td width="150" class="font-weight-bold">KODE SUB KEGIATAN</td>
                    <td width="400">{{ datakegiatan.kode_sub_kegiatan }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">PAGU URAIAN</td>
                    <td width="400">
                      {{ datauraian.PaguUraian1 | formatUang }}
                    </td>
                    <td width="150" class="font-weight-bold">NAMA SUB KEGIATAN</td>
                    <td width="400">{{ datakegiatan.Nm_Sub_Kegiatan }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">PROGRAM</td>
                    <td width="400">{{ datakegiatan.Nm_Program }}</td>
                    <td width="150" class="font-weight-bold">DIBUAT</td>
                    <td width="400">
                      {{
                        $date(datauraian.created_at).format("DD/MM/YYYY HH:mm")
                      }}
                    </td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">KODE OPD / SKPD</td>
                    <td width="400">{{ datakegiatan.kode_organisasi }}</td>
                    <td width="150" class="font-weight-bold">DIUBAH</td>
                    <td width="400">
                      {{
                        $date(datauraian.updated_at).format("DD/MM/YYYY HH:mm")
                      }}
                    </td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">OPD / SKPD</td>
                    <td width="400">{{ datakegiatan.Nm_Organisasi }}</td>
                    <td width="150" class="font-weight-bold">SISA</td>
                    <td width="400">
                      {{ footers.sisa | formatUang }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-bottom-navigation color="purple lighten-1">
            <v-btn @click.stop="exitrealisasi">
              <span>Keluar</span>
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-bottom-navigation>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="datatable"
            item-key="RKARealisasiRincID"
            sort-by="bulan2"
            show-expand
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
            :expanded.sync="expanded"
            :single-expand="true"
            class="elevation-1"
            :disable-pagination="true"
            :hide-default-footer="true"
            dense
            @click:row="dataTableRowClicked"
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>DAFTAR REALISASI</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
              </v-toolbar>
            </template>
            <template v-slot:item.actions>
              N.A
            </template>
            <template v-slot:item.target2="{ item }">
              {{ item.target2 | formatUang }}
            </template>
            <template v-slot:item.realisasi2="{ item }">
              {{ item.realisasi2 | formatUang }}
            </template>
            <template v-slot:item.sisa_anggaran="{ item }">
              {{ item.sisa_anggaran | formatUang }}
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <strong>ID:</strong>{{ item.RKARealisasiRincID }}
                <strong>created_at:</strong>
                {{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
                <strong>updated_at:</strong>
                {{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
              </td>
            </template>
            <template v-slot:body.append>
              <tr class="amber darken-1 font-weight-black">
                <td colspan="2" class="text-right">TOTAL</td>
                <td class="text-right">
                  {{ footers.anggarankas | formatUang }}
                </td>
                <td class="text-right">
                  {{ footers.realisasi | formatUang }}
                </td>
                <td class="text-right">
                  {{ footers.targetfisik }}
                </td>
                <td class="text-right">
                  {{ footers.fisik }}
                </td>
                <td class="text-right">
                  {{ footers.sisa | formatUang }}
                </td>
                <td></td>
              </tr>
            </template>
            <template v-slot:no-data>
              data realisasi tidak tersedia.
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
  </RenjaPerubahanLayout>
</template>
<script>
  import RenjaPerubahanLayout from "@/views/layouts/RenjaPerubahanLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "SnapshotRealisasiRKAPerubahan",
    created() {
      this.RKARincID = this.$route.params.rkarincid;
      var page = this.$store.getters["uiadmin/Page"]("snapshotrkaperubahan");
      this.datakegiatan = page.datakegiatan;
      this.datauraian = page.datauraian;

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
          text: "RKA (RENCANA KEGIATAN DAN ANGGARAN)",
          disabled: false,
          href: "/renjaperubahan/rka",
        },
        {
          text: "URAIAN",
          disabled: false,
          href: "/renjaperubahan/snapshot/rka/uraian/" + page.datakegiatan.RKAID,
        },
        {
          text: "REALISASI",
          disabled: true,
          href: "#",
        },
      ];
      if (
        Object.keys(this.datakegiatan).length > 2 &&
        Object.keys(this.datauraian).length > 2
      ) {
        this.initialize();
      } else {
        page.datakegiatan = {
          RKAID: "",
        };
        page.datauraian = {
          RKARincID: "",
        };
        page.datarekening = {};

        this.$store.dispatch("uiadmin/updatePage", page);
        this.$router.push("/renjaperubahan/rka");
      }
    },
    data() {
      return {
        //modul
        RKARincID: "",
        btnLoading: false,
        datakegiatan: [],
        datauraian: [],
        datatableLoading: false,
        datatableLoaded: false,
        expanded: [],
        datatable: [],
        headers: [
          { text: "BULAN", value: "NamaBulan", width: 70 },
          { text: "ANGGARAN KAS", align: "end", value: "target2", width: 100 },
          {
            text: "REALISASI/SP2D",
            align: "end",
            value: "realisasi2",
            width: 100,
          },
          {
            text: "TARGET FISIK",
            align: "end",
            value: "target_fisik2",
            width: 100,
          },
          { text: "FISIK (%)", align: "end", value: "fisik2", width: 100 },
          {
            text: "SISA ANGGARAN",
            align: "end",
            value: "sisa_anggaran",
            width: 100,
          },
          { text: "AKSI", value: "actions", sortable: false, width: 70 },
        ],
        footers: {
          anggarankas: 0,
          realisasi: 0,
          sisa: 0,
          persen_sisa: 0,
          targetfisik: 0,
          fisik: 0,
        },
      };
    },
    methods: {
      initialize: async function() {
        this.datatableLoading = true;
        this.$ajax
          .post(
            "/snapshot/rkaperubahan/realisasi",
            {
              RKARincID: this.datauraian.RKARincID,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.datatable = data.realisasi;
            this.footers.anggarankas = data.totalanggarankas;
            this.footers.realisasi = data.totalrealisasi;
            this.footers.sisa = data.sisa_anggaran;
            this.footers.targetfisik = data.totaltargetfisik;
            this.footers.fisik = data.totalfisik;
            this.datatableLoading = false;
          })
          .catch(() => {
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
      exitrealisasi() {
        this.btnLoading = false;
        var page = this.$store.getters["uiadmin/Page"]("snapshotrkaperubahan");
        page.datauraian = {
          RKARincID: "",
        };
        page.datarekening = {};
        this.$store.dispatch("uiadmin/updatePage", page);
        this.$router.push("/renjaperubahan/snapshot/rka/uraian/" + page.datakegiatan.RKAID);
      },
    },
    components: {
      RenjaPerubahanLayout,
      ModuleHeader,
    },
  };
</script>
