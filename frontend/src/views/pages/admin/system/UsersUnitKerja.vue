<template>
  <SystemUserLayout>
    <ModuleHeader>
      <template v-slot:icon>
        mdi-account
      </template>
      <template v-slot:name>
        USERS UNIT KERJA
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
          User dengan role Unit Kerja bertanggungjawab terhadap proses
          pengolahan data renja.
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
            :items="daftar_users"
            :search="search"
            item-key="id"
            sort-by="name"
            show-expand
            :expanded.sync="expanded"
            :single-expand="true"
            @click:row="dataTableRowClicked"
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>DAFTAR USERS UNIT KERJA</v-toolbar-title>
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
                      :disabled="!$store.getters['auth/can']('SYSTEM-USERS-UNIT-KERJA_STORE')"
                    >
                      <v-icon>mdi-database-refresh</v-icon>
                    </v-btn>
                  </template>
                  <span>SALIN DATA USER OPD</span>
                </v-tooltip>
                <v-tooltip
                  bottom
                  v-if="$store.getters['auth/can']('USER_STOREPERMISSIONS')"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      v-bind="attrs"
                      v-on="on"
                      color="warning"
                      icon
                      outlined
                      small
                      class="ma-2"
                      :disabled="btnLoading"
                      @click.stop="syncPermission"
                    >
                      <v-icon>mdi-head-sync-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>Sinkronisasi Permission</span>
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
                      :disabled="btnLoading || !$store.getters['auth/can']('SYSTEM-USERS-UNIT-KERJA_STORE')"
                      @click.stop="showDialogTambahUserUnitKerja"
                    >
                      <v-icon>mdi-plus</v-icon>
                    </v-btn>
                  </template>
                  <span>Tambah User Unit Kerja</span>
                </v-tooltip>
                <v-dialog v-model="dialog" max-width="500px" persistent>
                  <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                    <v-card>
                      <v-card-title>
                        <span class="headline">{{ formTitle }}</span>
                      </v-card-title>
                      <v-card-text>
                        <v-text-field
                          v-model="editedItem.name"
                          label="NAMA USER"
                          outlined
                          :rules="rule_user_name"
                          filled
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="editedItem.email"
                          label="EMAIL"
                          outlined
                          :rules="rule_user_email"
                          filled
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="editedItem.nomor_hp"
                          label="NOMOR HP"
                          outlined
                          :rules="rule_user_nomorhp"
                          filled
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="editedItem.username"
                          label="USERNAME"
                          outlined
                          :rules="rule_user_username"
                          filled
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="editedItem.password"
                          label="PASSWORD"
                          type="password"
                          outlined
                          :rules="rule_user_password"
                          filled
                        >
                        </v-text-field>
                        <v-autocomplete
                          :items="daftar_opd"
                          v-model="org_id"
                          label="OPD"
                          item-text="Nm_Organisasi"
                          item-value="OrgID"
                          outlined
                          :rules="rule_user_opd"
                          filled
                        >
                        </v-autocomplete>
                        <v-autocomplete
                          :items="daftar_unitkerja"
                          v-model="editedItem.sorg_id"
                          label="UNIT KERJA"
                          item-text="Nm_Sub_Organisasi"
                          item-value="SOrgID"
                          multiple
                          small-chips
                          outlined
                          :rules="rule_user_unitkerja"
                          filled
                        >
                        </v-autocomplete>
                        <v-autocomplete
                          :items="daftar_roles"
                          v-model="editedItem.role_id"
                          label="ROLES"
                          multiple
                          small-chips
                          outlined
                          filled
                        >
                        </v-autocomplete>
                      </v-card-text>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click.stop="close">
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
                <v-dialog v-model="dialogEdit" max-width="500px" persistent>
                  <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                    <v-card>
                      <v-card-title>
                        <span class="headline">{{ formTitle }}</span>
                      </v-card-title>
                      <v-card-text>
                        <v-text-field
                          v-model="editedItem.name"
                          label="NAMA USER"
                          outlined
                          :rules="rule_user_name"
                          filled
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="editedItem.email"
                          label="EMAIL"
                          outlined
                          :rules="rule_user_email"
                          filled
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="editedItem.nomor_hp"
                          label="NOMOR HP"
                          outlined
                          :rules="rule_user_nomorhp"
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="editedItem.username"
                          label="USERNAME"
                          outlined
                          :rules="rule_user_username"
                          filled
                        >
                        </v-text-field>
                        <v-text-field
                          v-model="editedItem.password"
                          label="PASSWORD"
                          type="password"
                          outlined
                          :rules="rule_user_passwordEdit"
                          filled
                        >
                        </v-text-field>
                        <v-autocomplete
                          :items="daftar_opd"
                          v-model="org_id"
                          label="OPD"
                          item-text="Nm_Organisasi"
                          item-value="OrgID"
                          outlined
                          :rules="rule_user_opd"
                          filled
                        >
                        </v-autocomplete>
                        <v-autocomplete
                          :items="daftar_unitkerja"
                          v-model="editedItem.sorg_id"
                          label="UNIT KERJA"
                          item-text="Nm_Sub_Organisasi"
                          item-value="SOrgID"
                          multiple
                          small-chips
                          outlined
                          :rules="rule_user_unitkerja"
                          filled
                        >
                        </v-autocomplete>
                        <v-autocomplete
                          :items="daftar_roles"
                          v-model="editedItem.role_id"
                          label="ROLES"
                          multiple
                          small-chips
                          outlined
                          filled
                        >
                        </v-autocomplete>
                      </v-card-text>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click.stop="close">
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
                  v-if="dialogUserPermission"
                  v-model="dialogUserPermission"
                  max-width="800px"
                  persistent
                >
                  <UserPermissions
                    :user="editedItem"
                    v-on:closeUserPermissions="closeUserPermissions"
                    role_default="unitkerja"
                  />
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
                          Salin data relasi user ke UNIT KERJA ke T.A
                          {{ TAHUN_SELECTED }}
                        </span>
                      </v-card-title>
                      <v-card-text>
                        <v-alert type="warning">
                          Menghindari duplikat proses salin, akan menghapus
                          terlebih dahulu data relasi user ke UNIT KERJA T.A
                          {{ TAHUN_SELECTED }}
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
                          @click.stop="salinuserunitkerja"
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
                    :disabled="btnLoading"
                    @click.stop="setPermission(item)"
                  >
                    mdi-axis-arrow-lock
                  </v-icon>
                </template>
                <span>Setting Permission</span>
              </v-tooltip>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    v-bind="attrs"
                    v-on="on"
                    small
                    class="mr-2"
                    :disabled="btnLoading"
                    @click.stop="editItem(item)"
                  >
                    mdi-pencil
                  </v-icon>
                </template>
                <span>Ubah User</span>
              </v-tooltip>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    v-bind="attrs"
                    v-on="on"
                    small
                    class="mr-2"
                    color="red darken-1"
                    :disabled="btnLoading || item.isdeleted == 0"
                    @click.stop="deleteItem(item)"
                  >
                    mdi-delete
                  </v-icon>
                </template>
                <span>Hapus User</span>
              </v-tooltip>
            </template>
            <template v-slot:item.foto="{ item }">
              <v-avatar size="30">
                <v-img :src="$api.url + '/' + item.foto" />
              </v-avatar>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <v-col cols="12">
                  <strong>ID:</strong>{{ item.id }}
                  <strong>created_at:</strong>
                  {{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
                  <strong>updated_at:</strong>
                  {{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
                </v-col>
                <v-col cols="12">
                  <strong>Daftar OPD yang dikelola:</strong> <br />
                  <span v-for="opd in item.opd" v-bind:key="opd.OrgID">
                    [{{ opd.Nm_Organisasi }}]
                  </span>
                </v-col>
              </td>
            </template>
            <template v-slot:no-data>
              Data belum tersedia
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
  </SystemUserLayout>
</template>
<script>
  import { mapGetters } from "vuex";
  import SystemUserLayout from "@/views/layouts/SystemUserLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  import UserPermissions from "@/views/pages/admin/system/UserPermissions";
  export default {
    name: "UsersUnitKerja",
    created() {
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.ACCESS_TOKEN,
        },
        {
          text: "USER SISTEM",
          disabled: false,
          href: "/system-users",
        },
        {
          text: "USERS UNIT KERJA",
          disabled: true,
          href: "#",
        },
      ];
      this.initialize();
    },
    data() {
      return {
        role_id: 0,
        datatableLoading: false,
        btnLoading: false,
        //tables
        headers: [
          { text: "", value: "foto" },
          { text: "USERNAME", value: "username", sortable: true },
          { text: "NAME", value: "name", sortable: true },
          { text: "EMAIL", value: "email", sortable: true },
          { text: "NOMOR HP", value: "nomor_hp", sortable: true },
          { text: "AKSI", value: "actions", sortable: false, width: 120 },
        ],
        expanded: [],
        search: "",
        daftar_users: [],
        //form
        form_valid: true,
        form_salin_valid: true,
        daftar_roles: [],
        dialog: false,
        dialogEdit: false,
        dialogcopyfrm: false,
        dialogUserPermission: false,
        editedIndex: -1,
        org_id: "",
        daftar_opd: [],
        daftar_unitkerja: [],
        editedItem: {
          id: 0,
          username: "",
          password: "",
          name: "",
          email: "",
          nomor_hp: "",
          org_id: "",
          sorg_id: [],
          role_id: ["unitkerja"],
          created_at: "",
          updated_at: "",
        },
        defaultItem: {
          id: 0,
          username: "",
          password: "",
          name: "",
          email: "",
          nomor_hp: "",
          org_id: "",
          sorg_id: [],
          role_id: ["unitkerja"],
          created_at: "",
          updated_at: "",
        },
        //salin opd
        tahunasal: null,
        daftar_ta: [],
        //form rules
        rule_user_name: [
          value => !!value || "Mohon untuk di isi nama User !!!",
        ],
        rule_user_email: [
          value => !!value || "Mohon untuk di isi email User !!!",
          value => /.+@.+\..+/.test(value) || "Format E-mail harus benar",
        ],
        rule_user_nomorhp: [
          value => !!value || "Nomor HP mohon untuk diisi !!!",
          value =>
            /^\+[1-9]{1}[0-9]{1,14}$/.test(value) ||
            "Nomor HP hanya boleh angka dan gunakan kode negara didepan seperti +6281214553388",
        ],
        rule_user_username: [
          value => !!value || "Mohon untuk di isi username User !!!",
          value =>
            /^[A-Za-z_]*$/.test(value) ||
            "Username hanya boleh string dan underscore",
        ],
        rule_user_opd: [
          value =>
            !!value || "Mohon untuk di pilih OPD / SKPD dari User ini !!!",
        ],
        rule_user_unitkerja: [
          value => !!value || "Mohon untuk di pilih Unit Kerja dari User ini !!!",
        ],
        rule_user_password: [
          value => !!value || "Mohon untuk di isi password User !!!",
          value => {
            if (value && typeof value !== "undefined" && value.length > 0) {
              return value.length >= 8 || "Minimial Password 8 Karakter";
            } else {
              return true;
            }
          },
        ],
        rule_user_passwordEdit: [
          value => {
            if (value && typeof value !== "undefined" && value.length > 0) {
              return value.length >= 8 || "Minimial Password 8 Karakter";
            } else {
              return true;
            }
          },
        ],
        //form rules salin urusan
        rule_tahun_asal: [
          value =>
            !!value || "Mohon untuk dipilih Tahun Anggaran sebelumnya!!!",
          value =>
            value < this.TAHUN_SELECTED ||
            "Tahun asal harus lebih kecil dari " + this.TAHUN_SELECTED,
        ],
      };
    },
    methods: {
      initialize: async function() {
        this.datatableLoading = true;
        await this.$ajax
          .post(
            "/system/usersunitkerja",
            {
              TA: this.TAHUN_SELECTED,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.daftar_users = data.users;
            this.role_id = data.role.id;
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
      syncPermission() {
        this.$root.$confirm
          .open(
            "Konfirmasi Sinkronisasi",
            "Sinkronisasi hanya untuk user dalam role UNIT KERJA, bila user memiliki role lain akan terhapus permission-nya ?",
            { color: "warning", width: 500 }
          )
          .then(async confirm => {
            if (confirm) {
              this.btnLoading = true;
              await this.$ajax
                .post(
                  "/system/users/syncallpermissions",
                  {
                    role_name: "unitkerja",
                  },
                  {
                    headers: {
                      Authorization: this.$store.getters["auth/Token"],
                    },
                  }
                )
                .then(() => {
                  this.btnLoading = false;
                })
                .catch(() => {
                  this.btnLoading = false;
                });
            }
          });
      },
      showDialogTambahUserUnitKerja: async function() {
        await this.$ajax
          .get("/system/setting/roles", {
            headers: {
              Authorization: this.TOKEN,
            },
          })
          .then(({ data }) => {
            let roles = data.roles;
            var daftar_roles = [];
            roles.forEach(element => {
              if (element.name == "unitkerja") {
                daftar_roles.push({
                  text: element.name,
                  disabled: true,
                });
              }
            });
            this.daftar_roles = daftar_roles;
          });
        await this.$ajax
          .post(
            "/dmaster/opd",
            {
              tahun: this.TAHUN_SELECTED,
            },
            {
              headers: {
                Authorization: this.TOKEN,
              },
            }
          )
          .then(({ data }) => {
            this.daftar_opd = data.opd;
            this.btnLoading = false;
            this.dialog = true;
          })
          .catch(() => {
            this.btnLoading = false;
          });
      },
      editItem: async function(item) {
        this.editedIndex = this.daftar_users.indexOf(item);
        item.password = "";
        this.editedItem = Object.assign({}, item);
        this.btnLoading = true;
        await this.$ajax
          .post(
            "/dmaster/opd",
            {
              tahun: this.TAHUN_SELECTED,
            },
            {
              headers: {
                Authorization: this.TOKEN,
              },
            }
          )
          .then(({ data }) => {
            this.daftar_opd = data.opd;
          })
          .catch(() => {
            this.btnLoading = false;
          });
        await this.$ajax
          .get("/system/users/" + item.id + "/unitkerja", {
            headers: {
              Authorization: this.TOKEN,
            },
          })
          .then(({ data }) => {
            this.org_id = data.OrgID;
            let daftar_unitkerja = data.daftar_unitkerja;
            var unitkerja = [];
            daftar_unitkerja.forEach(element => {
              unitkerja.push(element.SOrgID);
            });
            this.editedItem.sorg_id = unitkerja;
          });
        await this.$ajax
          .get("/system/setting/roles", {
            headers: {
              Authorization: this.TOKEN,
            },
          })
          .then(({ data }) => {
            let roles = data.roles;
            var daftar_roles = [];
            roles.forEach(element => {
              if (element.name == "unitkerja") {
                daftar_roles.push({
                  text: element.name,
                  disabled: true,
                });
              }
            });
            this.daftar_roles = daftar_roles;
          });
        await this.$ajax
          .get("/system/users/" + item.id + "/roles", {
            headers: {
              Authorization: this.TOKEN,
            },
          })
          .then(({ data }) => {
            this.editedItem.role_id = data.roles;
            this.btnLoading = false;
            this.dialogEdit = true;
          });
      },
      setPermission: async function(item) {
        this.dialogUserPermission = true;
        this.editedItem = item;
      },
      close() {
        this.btnLoading = false;
        this.dialog = false;
        this.dialogEdit = false;
        setTimeout(() => {
          this.$refs.frmdata.reset();
          this.editedItem = Object.assign({}, this.defaultItem);
          this.editedIndex = -1;
        }, 300);
      },
      closeUserPermissions() {
        this.btnLoading = false;
        this.dialogUserPermission = false;
      },
      salinuserunitkerja() {
        if (this.$refs.frmcopydata.validate()) {
          this.$ajax
            .post(
              "/system/usersunitkerja/salin",
              {
                tahun_asal: this.tahunasal,
                tahun_tujuan: this.TAHUN_SELECTED,
              },
              {
                headers: {
                  Authorization: this.TOKEN,
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
                "/system/usersunitkerja/" + this.editedItem.id,
                {
                  _method: "PUT",
                  name: this.editedItem.name,
                  email: this.editedItem.email,
                  nomor_hp: this.editedItem.nomor_hp,
                  username: this.editedItem.username,
                  password: this.editedItem.password,
                  org_id: this.org_id,
                  sorg_id: JSON.stringify(
                    Object.assign({}, this.editedItem.sorg_id)
                  ),
                  role_id: JSON.stringify(
                    Object.assign({}, this.editedItem.role_id)
                  ),
                },
                {
                  headers: {
                    Authorization: this.TOKEN,
                  },
                }
              )
              .then(({ data }) => {
                Object.assign(this.daftar_users[this.editedIndex], data.user);
                this.close();
              })
              .catch(() => {
                this.btnLoading = false;
              });
          } else {
            this.$ajax
              .post(
                "/system/usersunitkerja/store",
                {
                  name: this.editedItem.name,
                  email: this.editedItem.email,
                  ta: this.TAHUN_SELECTED,
                  nomor_hp: this.editedItem.nomor_hp,
                  username: this.editedItem.username,
                  password: this.editedItem.password,
                  org_id: this.org_id,
                  sorg_id: JSON.stringify(
                    Object.assign({}, this.editedItem.sorg_id)
                  ),
                  role_id: JSON.stringify(
                    Object.assign({}, this.editedItem.role_id)
                  ),
                },
                {
                  headers: {
                    Authorization: this.TOKEN,
                  },
                }
              )
              .then(({ data }) => {
                this.daftar_users.push(data.user);
                this.close();
              })
              .catch(() => {
                this.btnLoading = false;
              });
          }
        }
      },
      closedialogcopyfrm() {
        this.btnLoading = false;
        this.dialogcopyfrm = false;
        setTimeout(() => {
          this.$refs.frmcopydata.reset();
        }, 300);
      },
      deleteItem(item) {
        this.$root.$confirm
          .open(
            "Delete",
            "Apakah Anda ingin menghapus username " + item.username + " ?",
            { color: "red" }
          )
          .then(confirm => {
            if (confirm) {
              this.btnLoading = true;
              this.$ajax
                .post(
                  "/system/usersunitkerja/" + item.id,
                  {
                    _method: "DELETE",
                  },
                  {
                    headers: {
                      Authorization: this.TOKEN,
                    },
                  }
                )
                .then(() => {
                  const index = this.daftar_users.indexOf(item);
                  this.daftar_users.splice(index, 1);
                  this.btnLoading = false;
                })
                .catch(() => {
                  this.btnLoading = false;
                });
            }
          });
      },    
    },
    computed: {
      formTitle() {
        return this.editedIndex === -1
          ? "TAMBAH USER UNIT KERJA"
          : "EDIT USER UNIT KERJA";
      },
      ...mapGetters("auth", {
        ACCESS_TOKEN: "AccessToken",
        TOKEN: "Token",
        TAHUN_SELECTED: "TahunSelected",
      }),
    },
    watch: {
      dialog(val) {
        val || this.close();
        this.editedItem.sorg_id = [];
      },
      dialogEdit(val) {
        val || this.close();
      },
      async org_id(val) {
        if (val) {
          await this.$ajax
            .get("/dmaster/opd/" + val + "/unitkerja", {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            })
            .then(({ data }) => {
              this.daftar_unitkerja = data.unitkerja;
            });
        }
      },
    },
    components: {
      SystemUserLayout,
      ModuleHeader,
      UserPermissions,
    },
  };
</script>
