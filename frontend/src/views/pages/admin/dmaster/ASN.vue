<template>
  <DataMasterLayout :showrightsidebar="false">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-account-circle-outline
      </template>
      <template v-slot:name>
        ASN
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
          ASN (APARAT SIPIL NEGARA) untuk nilai referensi di RKA (PA, KPA, PPK,
          dan PPTK)
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
            item-key="ASNID"
            sort-by="Nm_ASN"
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
                <v-toolbar-title>DAFTAR ASN</v-toolbar-title>
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
                          'DMASTER-ASN_STORE'
                        )
                      "
                    >
                      <v-icon>mdi-reload</v-icon>
                    </v-btn>
                  </template>
                  <span>
                    Salin Data ASN ke T.A
                    {{ $store.getters["auth/TahunSelected"] }}
                  </span>
                </v-tooltip>
                <v-dialog v-model="dialogfrm" max-width="800px" persistent>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      v-bind="attrs"
                      v-on="on"
                      color="primary"
                      icon
                      outlined
                      small
                      class="ma-2"
                      :disabled="
                        !$store.getters['auth/can']('DMASTER-ASN_STORE')
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
                          v-model="formdata.NIP_ASN"
                          label="NIP ASN"
                          filled
                          :rules="rule_nip_asn"
                          outlined
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="formdata.Nm_ASN"
                          label="NAMA ASN"
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
                      <span class="headline">DETAIL DATA</span>
                    </v-card-title>
                    <v-card-text>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>
                              ID
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.ASNID }}
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
                              NIP ASN
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.NIP_ASN }}
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
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>
                              NAMA ASN
                            </v-card-title>
                            <v-card-subtitle>
                              {{ formdata.Nm_ASN }}
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
                          Salin data ASN ke T.A
                          {{ $store.getters["auth/TahunSelected"] }}
                        </span>
                      </v-card-title>
                      <v-card-text>
                        <v-alert type="warning">
                          Proses ini akan menyalin data ASN dari T.A {{ $store.getters["auth/TahunSelected"] }} namun jabatan tidak ikut tersalin.
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
                          @click.stop="salinasn"
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
                <span>Detail ASN</span>
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
                      !$store.getters['auth/can']('DMASTER-ASN_UPDATE')
                    "
                  >
                    mdi-pencil
                  </v-icon>
                </template>
                <span>Ubah ASN</span>
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
                        !$store.getters['auth/can']('DMASTER-ASN_STORE')
                    "
                    @click.stop="deleteItem(item)"
                  >
                    mdi-delete
                  </v-icon>
                </template>
                <span>Hapus ASN</span>
              </v-tooltip>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <strong>ID:</strong>{{ item.ASNID }}
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
    name: "ASN",
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
          text: "ASN",
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
          { text: "NIP ASN", value: "NIP_ASN", width: 150 },
          { text: "NAMA ASN", value: "Nm_ASN" },
          { text: "AKSI", value: "actions", sortable: false, width: 100 },
        ],
        search: "",

        //dialog
        dialogfrm: false,
        dialogcopyfrm: false,
        dialogdetailitem: false,
        //form data
        form_valid: true,
        form_salin_valid: true,
        formdata: {
          ASNID: "",
          NIP_ASN: "",
          Nm_ASN: "",
          Descr: "",
          created_at: "",
          updated_at: "",
        },
        formdefault: {
          ASNID: "",
          NIP_ASN: "",
          Nm_ASN: "",
          Descr: "",
          created_at: "",
          updated_at: "",
        },
        editedIndex: -1,
        //salin opd
        tahunasal: null,
        daftar_ta: [],
        //form rules
        rule_nip_asn: [
          value => !!value || "Mohon untuk di isi NIP ASN !!!",
          value => /^[0-9]+$/.test(value) || "NIP ASN hanya boleh angka",
        ],
        rule_name: [
          value => !!value || "Mohon untuk di isi Nama ASN !!!",
          value =>
            /^[A-Za-z\s\\,\\.]*$/.test(value) ||
            "NIP ASN hanya boleh string dan spasi",
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
            "/dmaster/asn",
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
            this.datatable = data.asn;
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
      viewItem(item) {
        this.formdata = item;
        this.dialogdetailitem = true;
      },
      copyItem() {
        this.daftar_ta = this.$store.getters["uifront/getDaftarTA"];
        this.dialogcopyfrm = true;
      },
      salinasn() {
        if (this.$refs.frmcopydata.validate()) {
          this.$ajax
            .post(
              "/dmaster/asn/salin",
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
                "/dmaster/asn/" + this.formdata.ASNID,
                {
                  _method: "PUT",
                  NIP_ASN: this.formdata.NIP_ASN,
                  Nm_ASN: this.formdata.Nm_ASN,
                  Descr: this.formdata.Descr,
                },
                {
                  headers: {
                    Authorization: this.$store.getters["auth/Token"],
                  },
                }
              )
              .then(({ data }) => {
                Object.assign(this.datatable[this.editedIndex], data.asn);
                this.closedialogfrm();
              })
              .catch(() => {
                this.btnLoading = false;
              });
          } else {
            this.$ajax
              .post(
                "/dmaster/asn/store",
                {
                  NIP_ASN: this.formdata.NIP_ASN,
                  Nm_ASN: this.formdata.Nm_ASN,
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
                this.datatable.push(data.asn);
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
            "Apakah Anda ingin menghapus data dengan ID " + item.ASNID + " ?",
            { color: "red" }
          )
          .then(confirm => {
            if (confirm) {
              this.btnLoading = true;
              this.$ajax
                .post(
                  "/dmaster/asn/" + item.ASNID,
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
