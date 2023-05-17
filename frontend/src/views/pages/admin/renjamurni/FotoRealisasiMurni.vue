<template>
  <RenjaMurniLayout :showrightsidebar="false">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-image-album
      </template>
      <template v-slot:name>
        FOTO REALISASI
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
          Foto - foto realisasi yang nanti akan muncul di gallery pembangunan
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
                    <td width="150" class="font-weight-bold">
                      KODE SUB KEGIATAN
                    </td>
                    <td width="400">{{ datakegiatan.kode_sub_kegiatan }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">PAGU URAIAN</td>
                    <td width="400">
                      {{ datauraian.PaguUraian1 | formatUang }}
                    </td>
                    <td width="150" class="font-weight-bold">
                      NAMA SUB KEGIATAN
                    </td>
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
                    <td width="150" class="font-weight-bold">
                      KODE OPD / SKPD
                    </td>
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
            <v-btn :to="{ path: '/renjamurni/rka/realisasi/' + RKARincID }">
              <span>Keluar</span>
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-bottom-navigation>
        </v-col>
      </v-row>
      <v-row>
        <v-col v-for="media in datatable" :key="media.id" class="d-flex child-flex" cols="4">
          <v-img
            :src="media.publicFullUrl"
            :lazy-src="`https://picsum.photos/10/6?image=${media.id * 5 + 10}`"
            aspect-ratio="1"
            class="grey lighten-2"
          >
            <template v-slot:placeholder>
              <v-row class="fill-height ma-0" align="center" justify="center">
                <v-progress-circular
                  indeterminate
                  color="grey lighten-5"
                ></v-progress-circular>
              </v-row>
            </template>
          </v-img>
        </v-col>
      </v-row>
    </v-container>
  </RenjaMurniLayout>
</template>
<script>
  import RenjaMurniLayout from "@/views/layouts/RenjaMurniLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "RealisasiRKAMurni",
    created() {
      this.RKARincID = this.$route.params.rkarincid;
      var page = this.$store.getters["uiadmin/Page"]("rkamurni");
      this.datakegiatan = page.datakegiatan;
      this.datauraian = page.datauraian;

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
          text: "RKA (RENCANA KEGIATAN DAN ANGGARAN)",
          disabled: false,
          href: "/renjamurni/rka",
        },
        {
          text: "URAIAN",
          disabled: false,
          href: "/renjamurni/rka/uraian/" + page.datakegiatan.RKAID,
        },
        {
          text: "FOTO REALISASI",
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
        this.$router.push("/renjamurni/rka");
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
        this.$ajax
          .post(
            "/renja/gallery",
            {
              RKARincID: this.datauraian.RKARincID,
              pid: "realisasirincian",
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.datatable = data.media;
            this.datatableLoading = false;
          })
          .catch(() => {
            this.datatableLoading = false;
          });
      },
    },
    components: {
      RenjaMurniLayout,
      ModuleHeader,
    },
  };
</script>
