<template>
  <RenjaPerubahanLayout :showrightsidebar="false">
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
                    <td width="150" class="font-weight-bold">OPD / SKPD</td>
                    <td width="400">{{ datakegiatan.Nm_Organisasi }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">KODE URAIAN</td>
                    <td width="400">{{ datauraian.kode_uraian }}</td>
                    <td width="150" class="font-weight-bold">KODE UNIT</td>
                    <td width="400">{{ datakegiatan.kode_sub_organisasi }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">NAMA URAIAN</td>
                    <td width="400">{{ datauraian.nama_uraian }}</td>
                    <td width="150" class="font-weight-bold">UNIT KERJA</td>
                    <td width="400">{{ datakegiatan.Nm_Sub_Organisasi }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">VOLUME</td>
                    <td width="400">{{ datauraian.volume }}</td>
                    <td width="150" class="font-weight-bold">KODE KEGIATAN</td>
                    <td width="400">{{ datakegiatan.kode_kegiatan }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">HARGA SATUAN</td>
                    <td width="400">
                      {{ datauraian.harga_satuan2 | formatUang }}
                    </td>
                    <td width="150" class="font-weight-bold">NAMA KEGIATAN</td>
                    <td width="400">{{ datakegiatan.Nm_Kegiatan }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">PAGU URAIAN</td>
                    <td width="400">
                      {{ datauraian.PaguUraian2 | formatUang }}
                    </td>
                    <td width="150" class="font-weight-bold">
                      KODE SUB KEGIATAN
                    </td>
                    <td width="400">{{ datakegiatan.kode_sub_kegiatan }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">PROGRAM</td>
                    <td width="400">{{ datakegiatan.Nm_Program }}</td>
                    <td width="150" class="font-weight-bold">
                      NAMA SUB KEGIATAN
                    </td>
                    <td width="400">{{ datakegiatan.Nm_Sub_Kegiatan }}</td>
                  </tr>
                  <tr>
                    <td width="150" class="font-weight-bold">
                      KODE OPD / SKPD
                    </td>
                    <td width="400">{{ datakegiatan.kode_organisasi }}</td>
                    <td width="150" class="font-weight-bold">DIBUAT/DIUBAH</td>
                    <td width="400">
                      {{
                        $date(datauraian.created_at).format("DD/MM/YYYY HH:mm")
                      }}
                      ~
                      {{
                        $date(datauraian.updated_at).format("DD/MM/YYYY HH:mm")
                      }}
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
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn v-bind="attrs" v-on="on" @click.stop="tambahFoto">
                  <span>Tambah Foto</span>
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </template>
              <span>Tambah Foto</span>
            </v-tooltip>
            <v-btn :to="{ path: '/renjaperubahan/rka/realisasi/' + RKARincID }">
              <span>Keluar</span>
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-bottom-navigation>
        </v-col>
      </v-row>
      <v-row v-if="datatable.length > 0">
        <v-col
          v-for="media in datatable"
          :key="media.id"
          class="d-flex child-flex"
          cols="4"
        >
          <v-card class="mx-auto">
            <v-img
              :src="media.publicFullUrl"
              :lazy-src="
                `https://picsum.photos/10/6?image=${media.id * 5 + 10}`
              "
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
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn icon @click.stop="deleteItem(media)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
      <v-row v-else>
        <v-col>
          <v-alert dense type="info">
            Belum ada foto realisasi untuk uraian ini.
          </v-alert>
        </v-col>
      </v-row>
      <v-dialog v-model="dialogfrm" max-width="800px" persistent>
        <v-form ref="frmdata" v-model="form_valid" lazy-validation>
          <v-card>
            <v-card-title>
              <span class="headline">TAMBAH FOTO</span>
            </v-card-title>
            <v-card-text>
              <v-simple-table dense dark>
                <template v-slot:default>
                  <tbody>
                    <tr>
                      <td width="150">ID :</td>
                      <td>{{ datauraian.RKARincID }}</td>
                    </tr>
                    <tr>
                      <td width="150">NAMA URAIAN :</td>
                      <td>{{ datauraian.nama_uraian }}</td>
                    </tr>
                    <tr>
                      <td width="150">PAGU URAIAN :</td>
                      <td>
                        {{ datauraian.PaguUraian1 | formatUang }}
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
              <v-container fluid>
                <v-row>
                  <v-col cols="12" sm="12" md="12">
                    <v-select
                      :items="daftar_bulan"
                      v-model="bulan1"
                      label="BULAN"
                      :rules="rule_bulan"
                      outlined
                      dense
                      class="mb-1"
                    />
                    <v-file-input
                      accept="image/jpeg,image/png"
                      label="Foto Realisasi (MAKS. 2MB)"
                      outlined
                      dense
                      v-model="formdata.foto"
                      prepend-icon=""
                      chips
                      show-size
                      :rules="rule_foto"
                      @change="handleFileSelect"
                    />
                    <v-progress-linear v-model="percentCompleted" height="25">
                      <strong>
                        {{ fileSize }} ({{ Math.ceil(percentCompleted) }}%)
                      </strong>
                    </v-progress-linear>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closedialogfrm">
                TUTUP
              </v-btn>
              <v-btn
                color="blue darken-1"
                text
                @click="save"
                :loading="btnLoading"
                :disabled="!form_valid || btnLoading"
              >
                SIMPAN
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-form>
      </v-dialog>
    </v-container>
  </RenjaPerubahanLayout>
</template>
<script>
  import RenjaPerubahanLayout from "@/views/layouts/RenjaPerubahanLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "RealisasiRKAPerubahan",
    created() {
      this.RKARincID = this.$route.params.rkarincid;
      var page = this.$store.getters["uiadmin/Page"]("rkaperubahan");
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
          href: "/renjaperubahan/rka/uraian/" + page.datakegiatan.RKAID,
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
        percentCompleted: 0,
        fileSize: "0 KB",
        //dialog
        dialogfrm: false,

        //form data
        form_valid: true,
        daftar_bulan: [],
        bulan1: null,
        formdata: {
          foto: null,
        },
        formdefault: {
          foto: null,
        },
        //form rules
        rule_bulan: [
          value => !!value || "Mohon untuk di pilih bulan realisasi !!!",
        ],
        rule_foto: [
          value => !!value || "Mohon foto realisasi untuk diisi !!!",
          value => {
            if (value && typeof value !== "undefined") {
              if (value.size <= 2000000) {
                return true;
              } else {
                return "Mohon foto realisasi dan ukuran foto kurang atau sama 2MB !!!";
              }
            } else {
              return "Mohon foto realisasi untuk diisi !!!";
            }
          },
        ],
      };
    },
    methods: {
      initialize: async function() {
        this.$ajax
          .post(
            "/renja/gallery",
            {
              RKARincID: this.datauraian.RKARincID,
              pid: "rincian",
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.datatable = data.media;
            this.daftar_bulan = data.daftar_bulan;
            this.datatableLoading = false;
          })
          .catch(() => {
            this.datatableLoading = false;
          });
      },
      tambahFoto() {
        this.dialogfrm = true;
      },
      handleFileSelect() {
        this.percentCompleted = 0;
        this.fileSize = "0 KB";
      },
      save() {
        if (this.$refs.frmdata.validate()) {
          this.btnLoading = true;

          var data = new FormData();
          data.append("RKARealisasiRincID", this.bulan1);
          data.append("foto", this.formdata.foto);
          data.append("pid", "realisasirincian");

          this.$ajax
            .post("/renja/gallery/store", data, {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
                "Content-Type": "multipart/form-data",
              },
              onUploadProgress: function(progressEvent) {
                let total = progressEvent.total;
                let loaded = progressEvent.loaded;
                this.percentCompleted = Math.round((loaded * 100) / total);
                this.fileSize =
                  total < 1024
                    ? total + " KB"
                    : (loaded / (1024 * 1024)).toFixed(2) + " MB";
              }.bind(this),
            })
            .then(() => {
              this.initialize();
              this.btnLoading = false;
            })
            .catch(() => {
              this.btnLoading = false;
            });
        }
      },
      closedialogfrm() {
        this.btnLoading = false;
        this.bulan1 = null;
        this.percentCompleted = 0;
        this.fileSize = "0 KB";
        setTimeout(() => {
          this.$refs.frmdata.reset();
          this.formdata = Object.assign({}, this.formdefault);
          this.dialogfrm = false;
        }, 300);
      },
      deleteItem(item) {
        this.$root.$confirm
          .open(
            "Delete",
            "Apakah Anda ingin menghapus foto realisasi dengan ID " +
              item.id +
              " ?",
            { color: "red" }
          )
          .then(confirm => {
            if (confirm) {
              this.btnLoading = true;
              this.$ajax
                .post(
                  "/renja/gallery/" + item.id,
                  {
                    _method: "DELETE",
                    RKARealisasiRincID: item.RKARealisasiRincID,
                    pid: "realisasirincian",
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
                })
                .catch(() => {
                  this.btnLoading = false;
                });
            }
          });
      },
    },
    components: {
      RenjaPerubahanLayout,
      ModuleHeader,
    },
  };
</script>
