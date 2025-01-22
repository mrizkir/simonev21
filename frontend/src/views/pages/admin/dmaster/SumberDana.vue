<template>
  <DataMasterLayout :showrightsidebar="false">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-cash-100
      </template>
      <template v-slot:name>
        SUMBER DANA
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
          Sumber Dana untuk referensi di RKA
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
            :search="search"
            item-key="SumberDanaID"
            sort-by="Nm_SumberDana"
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
                <v-toolbar-title>DAFTAR SUMBER DANA</v-toolbar-title>
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
                          'DMASTER-SUMBER-DANA_STORE'
                        )
                      "
                    >
                      <v-icon>mdi-reload</v-icon>
                    </v-btn>
                  </template>
                  <span>
                    Salin Sumber Dana ke T.A
                    {{ $store.getters["auth/TahunSelected"] }}
                  </span>
                </v-tooltip>
                <v-dialog v-model="dialogfrm" max-width="800px" persistent>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      v-on="on"
                      color="primary"
                      icon
                      outlined
                      small
                      class="ma-2"
                      :disabled="
                        btnLoading ||
                          !$store.getters['auth/can'](
                            'DMASTER-SUMBER-DANA_STORE'
                          )
                      "
                    >
                      <v-icon>mdi-plus</v-icon>
                    </v-btn>
                  </template>
                  <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                    <v-card>
                      <v-card-title>
                        <span class="headline">{{ formtitle }}</span>
                      </v-card-title>
                      <v-card-text>
                        <v-text-field
                          v-model="formdata.Kd_SumberDana"
                          label="KODE"
                          filled
                          outlined
                          :rules="rule_kd_sumberdana"
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="formdata.Nm_SumberDana"
                          label="SUMBER DANA"
                          filled
                          outlined
                          :rules="rule_name"
                        >
                        </v-text-field>
                        <v-textarea
                          v-model="formdata.Descr"
                          label="KETERANGAN"
                          filled
                          outlined
                        >
                        </v-textarea>
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
                          :loading="btnLoading"
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
                      <span class="headline">DETAIL DATA</span>
                    </v-card-title>
                    <v-card-text>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>
                              SUMBERDANAID
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.SumberDanaID }}
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
                              KODE
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.Kd_SumberDana }}
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
                              CREATED
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.Nm_SumberDana }}
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
                              SUMBER DANA
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.Nm_SumberDana }}
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
                              UPDATED
                            </v-card-title>
                            <v-card-subtitle>
                              {{
                                $date(formdata.updated_at).format(
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
                <v-dialog v-model="dialogcopyfrm" max-width="500px" persistent>
                  <v-form
                    ref="frmcopydata"
                    v-model="form_salin_valid"
                    lazy-validation
                  >
                    <v-card>
                      <v-card-title>
                        <span class="headline">
                          Salin OPD ke T.A
                          {{ $store.getters["auth/TahunSelected"] }}
                        </span>
                      </v-card-title>
                      <v-card-text>
                        <v-alert type="warning">
                          Menghindari duplikat proses salin, akan menghapus terlebih dahulu data Sumber Dana T.A {{ $store.getters["auth/TahunSelected"] }}
                        </v-alert>
                        <v-select
                          label="DARI TAHUN ANGGARAN"
                          v-model="tahunasal"
                          :items="daftar_ta"
                          :rules="rule_tahun_asal"
                          outlined
                          dense
                        />
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
                          @click.stop="salinsumberdana"
                          :disabled="!form_salin_valid || btnLoading"
                        >
                          SALIN
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-form>
                </v-dialog>
              </v-toolbar>
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
                <span>Detail Sumber Dana</span>
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
                      !$store.getters['auth/can']('DMASTER-SUMBER-DANA_UPDATE')
                    "
                  >
                    mdi-pencil
                  </v-icon>
                </template>
                <span>Ubah Sumber Dana</span>
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
                          'DMASTER-SUMBER-DESTROY_STORE'
                        )
                    "
                    @click.stop="deleteItem(item)"
                  >
                    mdi-delete
                  </v-icon>
                </template>
                <span>Hapus Sumber Dana</span>
              </v-tooltip>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <strong>ID:</strong>{{ item.SumberDanaID }}
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
    name: "SumberDana",
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
          text: "SUMBER DANA",
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
          { text: "KODE", value: "Kd_SumberDana", width: 150 },
          { text: "SUMBER DANA", value: "Nm_SumberDana" },
          { text: "KET.", value: "Descr" },
          { text: "AKSI", value: "actions", sortable: false, width: 100 },
        ],
        search: "",

        //dialog
        dialogfrm: false,
        dialogdetailitem: false,
        dialogcopyfrm: false,
        //form data
        form_valid: true,
        form_salin_valid: true,
        formdata: {
          SumberDanaID: "",
          Kd_SumberDana: "",
          Nm_SumberDana: "",
          Descr: "",
          created_at: "",
          updated_at: "",
        },
        formdefault: {
          SumberDanaID: "",
          Kd_SumberDana: "",
          Nm_SumberDana: "",
          Descr: "",
          created_at: "",
          updated_at: "",
        },
        editedIndex: -1,
        //salin opd
        tahunasal: null,
        daftar_ta: [],
        //form rules
        rule_kd_sumberdana: [
          value => !!value || "Mohon untuk di isi Kode Sumber Dana !!!",
          value =>
            /^[0-9]+$/.test(value) || "Kode Sumber Dana hanya boleh angka",
        ],
        rule_name: [
          value => !!value || "Mohon untuk di isi Nama Sumber Dana !!!",
          value =>
            /^[A-Za-z\s\\,\\.\\-]*$/.test(value) ||
            "Kode Sumber Dana hanya boleh string dan spasi",
        ],
        //form rules salin opd
        rule_tahun_asal: [
          value =>
            !!value || "Mohon untuk dipilih Tahun Anggaran sebelumnya!!!",
          value =>
            value < this.$store.getters["auth/TahunSelected"] ||
            "Tahun asal harus lebih kecil dari " +
              this.$store.getters["auth/TahunSelected"],
        ],
      };
    },
    methods: {
      initialize: async function() {
        this.datatableLoading = true;
        await this.$ajax
          .post(
            "/dmaster/sumberdana",
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
            this.datatable = data.sumberdana;
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
      editItem(item) {
        this.editedIndex = this.datatable.indexOf(item);
        this.formdata = Object.assign({}, item);
        this.dialogfrm = true;
      },
      copyItem() {
        this.daftar_ta = this.$store.getters["uifront/getDaftarTA"];
        this.dialogcopyfrm = true;
      },
      viewItem(item) {
        this.formdata = item;
        this.dialogdetailitem = true;
      },
      salinsumberdana() {
        if (this.$refs.frmcopydata.validate()) {
          this.$ajax
            .post(
              "/dmaster/sumberdana/salin",
              {
                tahun_asal: this.tahunasal,
                tahun_tujuan: this.$store.getters["auth/TahunSelected"],
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
          if (this.editedIndex > -1) {
            this.$ajax
              .post(
                "/dmaster/sumberdana/" + this.formdata.SumberDanaID,
                {
                  _method: "PUT",
                  Kd_SumberDana: this.formdata.Kd_SumberDana,
                  Nm_SumberDana: this.formdata.Nm_SumberDana,
                  Descr: this.formdata.Descr,
                },
                {
                  headers: {
                    Authorization: this.$store.getters["auth/Token"],
                  },
                }
              )
              .then(({ data }) => {
                Object.assign(
                  this.datatable[this.editedIndex],
                  data.sumberdana
                );
                this.closedialogfrm();
              })
              .catch(() => {
                this.btnLoading = false;
              });
          } else {
            this.$ajax
              .post(
                "/dmaster/sumberdana/store",
                {
                  Kd_SumberDana: this.formdata.Kd_SumberDana,
                  Nm_SumberDana: this.formdata.Nm_SumberDana,
                  Descr: this.formdata.Descr,
                  TA: this.$store.getters["auth/TahunSelected"],
                },
                {
                  headers: {
                    Authorization: this.$store.getters["auth/Token"],
                  },
                }
              )
              .then(({ data }) => {
                this.datatable.push(data.sumberdana);
                this.closedialogfrm();
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
            "Apakah Anda ingin menghapus data dengan ID " +
              item.SumberDanaID +
              " ?",
            { color: "red" }
          )
          .then(confirm => {
            if (confirm) {
              this.btnLoading = true;
              this.$ajax
                .post(
                  "/dmaster/sumberdana/" + item.SumberDanaID,
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
      closedialogcopyfrm() {
        this.btnLoading = false;
        this.dialogcopyfrm = false;
        setTimeout(() => {
          this.$refs.frmcopydata.reset();
        }, 300);
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
