<template>
  <DataMasterLayout :showrightsidebar="false">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-group
      </template>
      <template v-slot:name>
        SUB KEGIATAN
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
          Daftar "sub kegiatan" sesuai dengan Keputusan Menteri Dalam Negeri No.
          050-3708 tentang pemutakhiran, klasifikasi, kodefikasi, perencanaan,
          dan pembangunan daerah.
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
                filled
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
            item-key="SubKgtID"
            show-expand
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
                      color="primary"
                      icon
                      outlined
                      small
                      class="ma-2"
                      @click.stop="copyItem"
                      :disabled="
                        !$store.getters['auth/can'](
                          'DMASTER-KODEFIKASI-SUB-KEGIATAN_STORE'
                        )
                      "
                    >
                      <v-icon>mdi-reload</v-icon>
                    </v-btn>
                  </template>
                  <span>
                    Salin sub kegiatan ke T.A
                    {{ $store.getters["auth/TahunSelected"] }}
                  </span>
                </v-tooltip>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      v-bind="attrs"
                      v-on="on"
                      color="primary"
                      icon
                      outlined
                      small
                      class="ma-2"
                      @click.stop="addItem"
                      :disabled="
                        !$store.getters['auth/can'](
                          'DMASTER-KODEFIKASI-SUB-KEGIATAN_STORE'
                        )
                      "
                    >
                      <v-icon>mdi-plus</v-icon>
                    </v-btn>
                  </template>
                  <span>Tambah Kegiatan</span>
                </v-tooltip>
                <v-dialog v-model="dialogcopyfrm" max-width="500px" persistent>
                  <v-form
                    ref="frmcopydata"
                    v-model="form_salin_valid"
                    lazy-validation
                  >
                    <v-card>
                      <v-card-title>
                        <span class="headline">
                          Salin Sub Kegiatan ke T.A
                          {{ $store.getters["auth/TahunSelected"] }}
                        </span>
                      </v-card-title>
                      <v-card-text>
                        <v-alert type="warning">
                          Menghindari duplikat proses salin, akan menghapus terlebih dahulu data sub kegiatan T.A {{ $store.getters["auth/TahunSelected"] }}
                        </v-alert>
                        <v-select
                          label="DARI TAHUN ANGGARAN"
                          v-model="tahunasal"
                          :items="daftar_ta"
                          :rules="rule_tahun_asal"
                          outlined
                          dense
                        />
                        <v-text-field
                          v-model="lpad"
                          label="LEFT PAD"
                          filled
                          :rules="rule_lpad"
                          outlined
                        >
                        </v-text-field>
                      </v-card-text>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                          color="blue darken-1"
                          text
                          @click.stop="closedialogcopyfrm"
                        >
                          TUTUP
                        </v-btn>
                        <v-btn
                          color="blue darken-1"
                          text
                          @click.stop="salinsubkegiatan"
                          :disabled="!form_salin_valid || btnLoading"
                        >
                          SALIN
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-form>
                </v-dialog>
                <v-dialog v-model="dialogfrm" max-width="800px" persistent>
                  <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                    <v-card>
                      <v-card-title>
                        <span class="headline">
                          {{ formtitle }}
                        </span>
                      </v-card-title>
                      <v-card-text>
                        <v-select
                          v-model="formdata.KgtID"
                          :items="daftar_kegiatan"
                          item-text="nama_kegiatan"
                          item-value="KgtID"
                          label="KEGIATAN"
                          :rules="rule_kegiatan"
                          single-line
                          filled
                          outlined
                        >
                        </v-select>
                        <v-text-field
                          v-model="formdata.Kd_SubKegiatan"
                          label="KODE SUB KEGIATAN"
                          filled
                          :rules="rule_kode"
                          outlined
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="formdata.Nm_SubKegiatan"
                          label="NAMA SUB KEGIATAN"
                          filled
                          :rules="rule_name"
                          outlined
                        >
                        </v-text-field>
                        <v-textarea
                          v-model="formdata.Descr"
                          label="KETERANGAN"
                          filled
                          outlined
                        >
                        </v-textarea>
                        <v-switch v-model="formdata.Locked" label="AKTIF" />
                      </v-card-text>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                          color="blue darken-1"
                          text
                          @click.stop="closedialogfrm"
                        >
                          TUTUP
                        </v-btn>
                        <v-btn
                          color="blue darken-1"
                          text
                          @click.stop="save"
                          :disabled="!form_valid || btnLoading"
                        >
                          SIMPAN
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-form>
                </v-dialog>
                <v-dialog
                  v-model="dialogdetailitem"
                  max-width="800px"
                  persistent
                >
                  <v-card>
                    <v-card-title>
                      <span class="headline">
                        DETAIL DATA
                      </span>
                    </v-card-title>
                    <v-card-text>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>
                              ID
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.SubKgtID }}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive
                          width="100%"
                          v-if="$vuetify.breakpoint.xsOnly"
                        />
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>
                              KEGIATAN
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.Nm_Kegiatan }}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive
                          width="100%"
                          v-if="$vuetify.breakpoint.xsOnly"
                        />
                      </v-row>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>
                              KODE SUB KEGIATAN
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.kode_sub_kegiatan }}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive
                          width="100%"
                          v-if="$vuetify.breakpoint.xsOnly"
                        />
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>
                              KETERANGAN
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.Descr }}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive
                          width="100%"
                          v-if="$vuetify.breakpoint.xsOnly"
                        />
                      </v-row>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>
                              NAMA SUB KEGIATAN
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.Nm_SubKegiatan }}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive
                          width="100%"
                          v-if="$vuetify.breakpoint.xsOnly"
                        />
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>
                              CREATED/UPDATED
                            </v-card-title>
                            <v-card-subtitle>
                              {{
                                $date(formdata.updated_at).format(
                                  "DD/MM/YYYY HH:mm"
                                )
                              }}/
                              {{
                                $date(formdata.created_at).format(
                                  "DD/MM/YYYY HH:mm"
                                )
                              }}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive
                          width="100%"
                          v-if="$vuetify.breakpoint.xsOnly"
                        />
                      </v-row>
                    </v-card-text>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn
                        color="blue darken-1"
                        text
                        @click.stop="closedialogdetailitem"
                      >
                        KELUAR
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </v-toolbar>
            </template>
            <template v-slot:item.Locked="{ item }">
              <v-chip class="ma-2" :color="item.Locked == 1 ? 'red' : 'success'" outlined small>
                {{ item.Locked == 1 ? "TIDAK AKTIF" : "AKTIF" }}
              </v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    v-bind="attrs"
                    v-on="on"
                    small
                    class="mr-2"
                    @click.stop="viewItem(item)"
                  >
                    mdi-eye
                  </v-icon>
                </template>
                <span>Detail Kegiatan</span>
              </v-tooltip>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    v-bind="attrs"
                    v-on="on"
                    small
                    class="mr-2"
                    @click.stop="editItem(item)"
                    :disabled="
                      !$store.getters['auth/can'](
                        'DMASTER-KODEFIKASI-SUB-KEGIATAN_UPDATE'
                      )
                    "
                  >
                    mdi-pencil
                  </v-icon>
                </template>
                <span>Ubah Kegiatan</span>
              </v-tooltip>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    v-bind="attrs"
                    v-on="on"
                    small
                    color="red darken-1"
                    :disabled="
                      btnLoading ||
                        !$store.getters['auth/can'](
                          'DMASTER-KODEFIKASI-SUB-KEGIATAN_STORE'
                        )
                    "
                    @click.stop="deleteItem(item)"
                  >
                    mdi-delete
                  </v-icon>
                </template>
                <span>Hapus Kegiatan</span>
              </v-tooltip>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <strong>ID:</strong>{{ item.SubKgtID }}
                <strong>created_at:</strong>
                {{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
                <strong>updated_at:</strong>
                {{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
              </td>
            </template>
            <template v-slot:no-data>
              Data belum tersedia
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
  </DataMasterLayout>
</template>
<script>
  import DataMasterLayout from "@/views/layouts/DataMasterLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "KodefikasiSubKegiatan",
    created() {
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
        },
        {
          text: "DATA MASTER",
          disabled: false,
          href: "#",
        },
        {
          text: "KODEFIKASI",
          disabled: true,
          href: "#",
        },
        {
          text: "SUB KEGIATAN",
          disabled: true,
          href: "#",
        },
      ];
      this.initialize();
    },
    data() {
      return {
        btnLoading: false,
        datatableLoading: false,
        expanded: [],
        datatable: [],
        headers: [
          { text: "KODE SUB KEGIATAN", value: "kode_sub_kegiatan", width: 100 },
          { text: "NAMA SUB KEGIATAN", value: "Nm_SubKegiatan", width: 250 },
          { text: "KEGIATAN", value: "Nm_Kegiatan", width: 170 },
          { text: "KET", value: "Descr", width: 140 },
          { text: "TA", value: "TA", width: 70 },
          { text: "STATUS", value: "Locked", align: "center", width: 120 },
          { text: "AKSI", value: "actions", sortable: false, width: 100 },
        ],
        search: "",
        //dialog
        dialogcopyfrm: false,
        dialogfrm: false,
        dialogdetailitem: false,
        //form data
        form_valid: true,
        form_salin_valid: true,
        daftar_kegiatan: [],
        formdata: {
          SubKgtID: "",
          KgtID: "",
          Kd_SubKegiatan: "",
          Nm_SubKegiatan: "",
          Descr: "",
          TA: "",
          Locked: 0,
          created_at: "",
          updated_at: "",
        },
        formdefault: {
          SubKgtID: "",
          KgtID: "",
          Kd_SubKegiatan: "",
          Nm_SubKegiatan: "",
          Descr: "",
          TA: "",
          Locked: 0,
          created_at: "",
          updated_at: "",
        },
        editedIndex: -1,
        //salin sub kegiatan
        tahunasal: null,
        daftar_ta: [],
        lpad: 2,
        //form rules
        rule_kegiatan: [
          value => !!value || "Mohon untuk di pilih Kegiatan !!!",
        ],
        rule_kode: [
          value => !!value || "Mohon untuk di isi Kode Sub Kegiatan!!!",
          value =>
            /^[0-9]+$/.test(value) || "Kode Sub Kegiatan hanya boleh angka",
          value => value.length > 1 || "Kode Sub Kegiatan minimal 2 angka",
        ],
        rule_name: [
          value => !!value || "Mohon untuk di isi Nama Sub Kegiatan !!!",
        ],
        //form rules salin sub kegiatan
        rule_tahun_asal: [
          value =>
            !!value || "Mohon untuk dipilih Tahun Anggaran sebelumnya!!!",
          value =>
            value < this.$store.getters["auth/TahunSelected"] ||
            "Tahun asal harus lebih kecil dari " +
              this.$store.getters["auth/TahunSelected"],
        ],
        rule_lpad: [
          value => !!value || "Mohon untuk di isi left pad!!!",
          value =>
            /^[0-9]+$/.test(value) || "Left pad hanya boleh angka",
          value => parseInt(value) >= 2 || "Left pad minimal 2 angka",
        ],
      };
    },
    methods: {
      initialize: async function() {
        this.datatableLoading = true;
        await this.$ajax
          .post(
            "/dmaster/kodefikasi/subkegiatan",
            {
              TA: this.$store.getters["auth/TahunSelected"],
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.datatable = data.kodefikasisubkegiatan;
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
      copyItem() {
        this.daftar_ta = this.$store.getters["uifront/getDaftarTA"];
        this.dialogcopyfrm = true;
      },
      async addItem() {
        await this.$ajax
          .post(
            "/dmaster/kodefikasi/kegiatan",
            {
              TA: this.$store.getters["auth/TahunSelected"],
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.daftar_kegiatan = data.kodefikasikegiatan;
            this.dialogfrm = true;
            this.formdata.Locked = 1;
          });
      },
      async editItem(item) {
        this.editedIndex = this.datatable.indexOf(item);
        await this.$ajax
          .post(
            "/dmaster/kodefikasi/kegiatan",
            {
              TA: this.$store.getters["auth/TahunSelected"],
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.daftar_kegiatan = data.kodefikasikegiatan;
            this.formdata = Object.assign({}, item);
            this.formdata.Locked = item.Locked == 0 ? 1 : 0;
            this.dialogfrm = true;
          });
      },
      viewItem(item) {
        this.formdata = item;
        this.dialogdetailitem = true;
      },
      salinsubkegiatan() {
        if (this.$refs.frmcopydata.validate()) {
          this.$ajax
            .post(
              "/dmaster/kodefikasi/subkegiatan/salin",
              {
                tahun_asal: this.tahunasal,
                tahun_tujuan: this.$store.getters["auth/TahunSelected"],
                lpad: this.lpad,
              },
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                },
              }
            )
            .then(() => {
              this.$router.go();
              this.closedialogcopyfrm();
            })
            .catch(() => {
              this.btnLoading = false;
            });
        }
      },
      save() {
        if (this.$refs.frmdata.validate()) {
          this.btnLoading = true;
          var locked = this.formdata.Locked == 1 ? 0 : 1;
          if (this.editedIndex > -1) {
            this.$ajax
              .post(
                "/dmaster/kodefikasi/subkegiatan/" + this.formdata.SubKgtID,
                {
                  _method: "PUT",
                  KgtID: this.formdata.KgtID,
                  Kd_SubKegiatan: this.formdata.Kd_SubKegiatan,
                  Nm_SubKegiatan: this.formdata.Nm_SubKegiatan,
                  Descr: this.formdata.Descr,
                  Locked: locked,
                },
                {
                  headers: {
                    Authorization: this.$store.getters["auth/Token"],
                  },
                }
              )
              .then(() => {
                this.closedialogfrm();
                this.$router.go();
              })
              .catch(() => {
                this.btnLoading = false;
              });
          } else {
            this.$ajax
              .post(
                "/dmaster/kodefikasi/subkegiatan/store",
                {
                  KgtID: this.formdata.KgtID,
                  Kd_SubKegiatan: this.formdata.Kd_SubKegiatan,
                  Nm_SubKegiatan: this.formdata.Nm_SubKegiatan,
                  Descr: this.formdata.Descr,
                  TA: this.$store.getters["auth/TahunSelected"],
                  Locked: locked,
                },
                {
                  headers: {
                    Authorization: this.$store.getters["auth/Token"],
                  },
                }
              )
              .then(() => {
                this.closedialogfrm();
                this.$router.go();
              })
              .catch(() => {
                this.btnLoading = false;
              });
          }
        }
      },
      deleteItem(item) {
        this.$root.$confirm
          .open(
            "Delete",
            "Apakah Anda ingin menghapus data sub kegiatan dengan ID " +
              item.SubKgtID +
              " ?",
            { color: "red", width: "500px" }
          )
          .then(confirm => {
            if (confirm) {
              this.btnLoading = true;
              this.$ajax
                .post(
                  "/dmaster/kodefikasi/subkegiatan/" + item.SubKgtID,
                  {
                    _method: "DELETE",
                  },
                  {
                    headers: {
                      Authorization: this.$store.getters["auth/Token"],
                    },
                  }
                )
                .then(() => {
                  const index = this.datatable.indexOf(item);
                  this.datatable.splice(index, 1);
                  this.btnLoading = false;
                })
                .catch(() => {
                  this.btnLoading = false;
                });
            }
          });
      },
      closedialogfrm() {
        this.btnLoading = false;
        this.dialogfrm = false;
        setTimeout(() => {
          this.formdata = Object.assign({}, this.formdefault);
          this.editedIndex = -1;
          this.$refs.frmdata.reset();
        }, 300);
      },
      closedialogcopyfrm() {
        this.btnLoading = false;
        this.dialogcopyfrm = false;
        setTimeout(() => {
          this.$refs.frmcopydata.reset();
        }, 300);
      },
      closedialogdetailitem() {
        this.dialogdetailitem = false;
        setTimeout(() => {
          this.formdata = Object.assign({}, this.formdefault);
          this.editedIndex = -1;
        }, 300);
      },
    },
    computed: {
      formtitle() {
        return this.editedIndex === -1 ? "TAMBAH DATA" : "UBAH DATA";
      },
    },
    components: {
      DataMasterLayout,
      ModuleHeader,
    },
  };
</script>
