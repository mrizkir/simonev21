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
          Ubah Rencana Kegiatan dan Anggaran (RKA) OPD / Unit Kerja APBD Perubahan
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-bottom-navigation color="purple lighten-1">
            <v-btn
              :to="{ path: '/renjaperubahan/rka/uraian/' + datakegiatan.RKAID }"
            >
              <span>Keluar</span>
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-bottom-navigation>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-form ref="frmeditkegiatan" v-model="form_valid" lazy-validation>
            <v-card>
              <v-card-title>
                <v-spacer />
                <v-icon small class="mr-2" v-if="datakegiatan.Locked == 1">
                  mdi-lock
                </v-icon>
              </v-card-title>
              <v-card-text>
                <h5>DATA KEGIATAN</h5>
                <v-divider class="mb-2"></v-divider>
                <v-text-field
                  label="URUSAN"
                  type="text"
                  filled
                  outlined
                  :value="
                    '[' +
                      datakegiatan.kode_urusan +
                      '] ' +
                      datakegiatan.Nm_Urusan
                  "
                  :disabled="true"
                />
                <v-text-field
                  label="BIDANG URUSAN"
                  type="text"
                  filled
                  outlined
                  :value="
                    '[' +
                      datakegiatan.kode_bidang +
                      '] ' +
                      datakegiatan.Nm_Bidang
                  "
                  :disabled="true"
                />
                <v-text-field
                  label="PROGRAM"
                  type="text"
                  filled
                  outlined
                  :value="
                    '[' + datakegiatan.kode_program + '] ' + datakegiatan.Nm_Program
                  "
                  :disabled="true"
                />
                <v-text-field
                  label="KEGIATAN"
                  type="text"
                  filled
                  outlined
                  :value="
                    '[' + datakegiatan.kode_kegiatan + '] ' + datakegiatan.Nm_Kegiatan
                  "
                  :disabled="true"
                />
                <v-text-field
                  label="SUB KEGIATAN"
                  type="text"
                  filled
                  outlined
                  :value="
                    '[' + datakegiatan.kode_sub_kegiatan + '] ' + datakegiatan.Nm_Sub_Kegiatan
                  "
                  :disabled="true"
                />			
                <v-currency-field
                  label="TARGET KINERJA MASUKAN"
                  :min="null"
                  :max="null"
                  outlined
                  filled
                  :decimal-length="2"
                  :value-as-integer="false"
                  v-model="formdata.PaguDana2"
                  :disabled="datakegiatan.Locked == 1"
                >
                </v-currency-field>
                <v-select
                  v-model="formdata.SumberDanaID"
                  label="SUMBER DANA"
                  :rules="rule_sumberdana"
                  :items="daftar_sumberdana"
                  item-text="Nm_SumberDana"
                  item-value="SumberDanaID"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-text-field
                  v-model="formdata.lokasi_kegiatan2"
                  label="LOKASI KEGIATAN"
                  :rules="rule_lokasikegiatan"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <h5>INDIKATOR DAN TOLAK UKUR KINERJA BELANJA LANGSUNG</h5>
                <v-divider class="mb-2"></v-divider>
                <v-text-field
                  v-model="formdata.capaian_program2"
                  label="CAPAIAN PROGRAM"
                  :rules="rule_capaianprogram"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-text-field
                  v-model="formdata.tk_capaian2"
                  label="TARGET KINERJA CAPAIAN (%)"
                  :rules="rule_tkcapaian"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-textarea
                  v-model="formdata.masukan2"
                  label="MASUKAN"
                  :rules="rule_masukan"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-textarea
                  v-model="formdata.keluaran2"
                  label="KELUARAN (OUTPUT)"
                  :rules="rule_keluaran"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-textarea
                  v-model="formdata.tk_keluaran2"
                  label="TARGET KINERJA KELUARAN (OUTPUT)"
                  :rules="rule_tkkeluaran"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-textarea
                  v-model="formdata.hasil2"
                  label="HASIL (OUTCOME)"
                  :rules="rule_hasil"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-textarea
                  v-model="formdata.tk_hasil2"
                  label="TARGET KINERJA HASIL (OUTCOME)"
                  :rules="rule_tkhasil"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-text-field
                  v-model="formdata.ksk2"
                  label="KELOMPOK SASARAN KEGIATAN"
                  :rules="rule_ksk"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-radio-group
                  v-model="formdata.sifat_kegiatan2"
                  row
                  :disabled="datakegiatan.Locked == 1"
                >
                  <v-radio label="BARU" value="baru"></v-radio>
                  <v-radio label="LANJUTAN" value="lanjutan">></v-radio>
                </v-radio-group>
                <v-text-field
                  v-model="formdata.waktu_pelaksanaan2"
                  label="WAKTU PELAKSANAAN"
                  :rules="rule_waktupelaksanaan"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <h5>PENGAMPU KEGIATAN</h5>
                <v-divider class="mb-2"></v-divider>
                <v-alert dense type="info">
                  Bila pejabat PA, KPA, PPK, dan PPTK kosong; silahkah isi dulu
                  di
                  <v-btn link text to="/dmaster/pejabat">
                    <strong>DMaster->Pejabat</strong>
                  </v-btn>
                </v-alert>
                <v-select
                  v-model="formdata.nip_pa2"
                  label="PENGGUNA ANGGARAN"
                  :rules="rule_pa"
                  :items="daftar_pa"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-select
                  v-model="formdata.nip_kpa2"
                  label="KUASA PENGGUNA ANGGARAN"
                  :rules="rule_kpa"
                  :items="daftar_kpa"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-select
                  label="PPK"
                  v-model="formdata.nip_ppk2"
                  :rules="rule_ppk"
                  :items="daftar_ppk"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <v-select
                  label="PPTK"
                  v-model="formdata.nip_pptk2"
                  :rules="rule_pptk"
                  :items="daftar_pptk"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
                <h5>LAIN-LAIN</h5>
                <v-divider class="mb-2"></v-divider>
                <v-textarea
                  v-model="formdata.Descr"
                  label="KETERANGAN"
                  type="text"
                  filled
                  outlined
                  :disabled="datakegiatan.Locked == 1"
                />
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="blue darken-1"
                  text
                  @click.stop="closeeditkegiatan"
                >
                  TUTUP
                </v-btn>
                <v-btn
                  color="blue darken-1"
                  text
                  @click.stop="updatekegiatan"
                  :loading="btnLoading"
                  :disabled="!form_valid || btnLoading || datakegiatan.Locked == 1"
                >
                  SIMPAN
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
        </v-col>
      </v-row>
    </v-container>
  </RenjaPerubahanLayout>
</template>
<script>
  import RenjaPerubahanLayout from "@/views/layouts/RenjaPerubahanLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "EditRKAPerubahan",
    created() {
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
        },
        {
          text: "BELANJA PERUBAHAN",
          disabled: false,
          href: "/renja",
        },
        {
          text: "RKA (RENCANA KEGIATAN DAN ANGGARAN)",
          disabled: false,
          href: "/renja/rka",
        },
        {
          text: "UBAH RKA",
          disabled: true,
          href: "#",
        },
      ];

      this.RKAID = this.$route.params.rkaid;
      var page = this.$store.getters["uiadmin/Page"]("rkaperubahan");
      this.datakegiatan = page.datakegiatan;

      if (Object.keys(this.datakegiatan).length > 1) {
        this.OrgID = page.OrgID_Selected;
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
        this.$router.push("/belanja/rkaperubahan");
      }
    },
    data() {
      return {
        //modul
        OrgID: "",
        RKAID: "",
        btnLoading: false,
        datakegiatan: [],
        //form data
        form_valid: true,
        daftar_sumberdana: [],
        daftar_pa: [],
        daftar_kpa: [],
        daftar_ppk: [],
        daftar_pptk: [],
        formdata: {
          RKAID: "",
          SumberDanaID: null,
          kode_urusan: "",
          kode_bidang: "",
          kode_organisasi: "",
          kode_suborganisasi: "",
          kode_program: "",
          kode_kegiatan: "",
          kode_sub_kegiatan: "",
          Nm_Urusan: "",
          Nm_Bidang: "",
          OrgNm: "",
          SOrgNm: "",
          Nm_Program: "",
          Nm_Kegiatan: "",
          Nm_Sub_Kegiatan: "",
          keluaran2: null,
          tk_keluaran2: "",
          hasil2: "",
          tk_hasil2: "",
          capaian_program2: "",
          tk_capaian2: "",
          masukan2: "",
          ksk2: "",
          sifat_kegiatan2: "baru",
          waktu_pelaksanaan2: "",
          lokasi_kegiatan2: "",
          PaguDana2: "",
          RealisasiKeuangan2: "",
          RealisasiFisik2: "",
          nip_pa2: "",
          nip_kpa2: "",
          nip_ppk2: "",
          nip_pptk2: "",
          Descr: "",
          TA: "",
          Locked: "",
          created_at: "",
          updated_at: "",
        },
        rule_sumberdana: [
          value => !!value || "Mohon untuk di pilih sumber dana !!!",
        ],
        rule_lokasikegiatan: [
          value => !!value || "Mohon untuk di isi lokasi kegiatan !!!",
        ],
        rule_capaianprogram: [
          value => !!value || "Mohon untuk di isi capaian program !!!",
        ],
        rule_tkcapaian: [
          value => !!value || "Mohon untuk di isi target kinerja capaian !!!",
          value =>
            /^(100(\.0{1,2})?|[1-9]?\d(\.\d{1,2})?)$/.test(value) ||
            "Isi dengan nilai persentase 0.00 s.d 100.00",
        ],
        rule_masukan: [value => !!value || "Mohon untuk di isi masuk !!!"],
        rule_keluaran: [value => !!value || "Mohon untuk di isi output !!!"],
        rule_tkkeluaran: [
          value =>
            !!value ||
            "Mohon untuk di isi target kinerja keluaran (OUTPUT) !!!",
        ],
        rule_hasil: [
          value => !!value || "Mohon untuk di isi hasil (OUTCOME) !!!",
        ],
        rule_tkhasil: [
          value =>
            !!value || "Mohon untuk di isi target kinerja hasil (OUTCOME) !!!",
        ],
        rule_ksk: [
          value =>
            !!value || "Mohon untuk di isi kelompok sasaran kegiatan !!!",
        ],
        rule_waktupelaksanaan: [
          value => !!value || "Mohon untuk di isi waktu pelaksanaan !!!",
        ],
        rule_pa: [value => !!value || "Mohon untuk di pilih namam PA !!!"],
        rule_kpa: [value => !!value || "Mohon untuk di pilih namam KPA !!!"],
        rule_ppk: [value => !!value || "Mohon untuk di pilih namam PPK !!!"],
        rule_pptk: [value => !!value || "Mohon untuk di pilih namam PPTK !!!"],
      };
    },
    methods: {
      initialize: async function() {
        this.formdata = Object.assign({}, this.datakegiatan);
        await this.$ajax
          .get("/dmaster/opd/" + this.OrgID + "/pejabat", {
            headers: {
              Authorization: this.$store.getters["auth/Token"],
            },
          })
          .then(({ data }) => {
            this.daftar_pa = data.pejabat.pa;
            this.daftar_kpa = data.pejabat.kpa;
            this.daftar_ppk = data.pejabat.ppk;
            this.daftar_pptk = data.pejabat.pptk;
          })
          .catch(() => {
            this.btnLoading = false;
          });

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
            this.daftar_sumberdana = data.sumberdana;
          })
          .catch(() => {
            this.btnLoading = false;
          });
      },
      updatekegiatan: async function() {
        if (this.$refs.frmeditkegiatan.validate()) {
          this.btnLoading = true;
          await this.$ajax
            .post(
              "/renja/rkaperubahan/updatekegiatan/" + this.formdata.RKAID,
              {
                _method: "PUT",
                PaguDana2: this.formdata.PaguDana2,
                SumberDanaID: this.formdata.SumberDanaID,
                keluaran2: this.formdata.keluaran2,
                tk_keluaran2: this.formdata.tk_keluaran2,
                hasil2: this.formdata.hasil2,
                tk_hasil2: this.formdata.tk_hasil2,
                capaian_program2: this.formdata.capaian_program2,
                tk_capaian2: this.formdata.tk_capaian2,
                masukan2: this.formdata.masukan2,
                ksk2: this.formdata.ksk2,
                sifat_kegiatan2: this.formdata.sifat_kegiatan2,
                waktu_pelaksanaan2: this.formdata.waktu_pelaksanaan2,
                lokasi_kegiatan2: this.formdata.lokasi_kegiatan2,
                nip_pa2: this.formdata.nip_pa2,
                nip_kpa2: this.formdata.nip_kpa2,
                nip_ppk2: this.formdata.nip_ppk2,
                nip_pptk2: this.formdata.nip_pptk2,
                Descr: this.formdata.Descr,
              },
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                },
              }
            )
            .then(() => {
              var page = this.$store.getters["uiadmin/Page"]("rkaperubahan");
              page.datakegiatan.PaguDana2 = this.formdata.PaguDana2;
              page.datakegiatan.SumberDanaID = this.formdata.SumberDanaID;
              page.datakegiatan.keluaran2 = this.formdata.keluaran2;
              page.datakegiatan.tk_keluaran2 = this.formdata.tk_keluaran2;
              page.datakegiatan.hasil2 = this.formdata.hasil2;
              page.datakegiatan.tk_hasil2 = this.formdata.tk_hasil2;
              page.datakegiatan.capaian_program2 = this.formdata.capaian_program2;
              page.datakegiatan.tk_capaian2 = this.formdata.tk_capaian2;
              page.datakegiatan.masukan2 = this.formdata.masukan2;
              page.datakegiatan.ksk2 = this.formdata.ksk2;
              page.datakegiatan.sifat_kegiatan2 = this.formdata.sifat_kegiatan2;
              page.datakegiatan.waktu_pelaksanaan2 = this.formdata.waktu_pelaksanaan2;
              page.datakegiatan.lokasi_kegiatan2 = this.formdata.lokasi_kegiatan2;
              page.datakegiatan.nip_pa2 = this.formdata.nip_pa2;
              page.datakegiatan.nip_kpa2 = this.formdata.nip_kpa2;
              page.datakegiatan.nip_ppk2 = this.formdata.nip_ppk2;
              page.datakegiatan.nip_pptk2 = this.formdata.nip_pptk2;
              page.datakegiatan.Descr = this.formdata.Descr;
              this.$store.dispatch("uiadmin/updatePage", page);
              this.closeeditkegiatan();
            })
            .catch(() => {
              this.btnLoading = false;
            });
        }
      },
      closeeditkegiatan() {
        this.$router.push(
          "/renjaperubahan/rka/uraian/" + this.datakegiatan.RKAID
        );
      },
    },
    components: {
      RenjaPerubahanLayout,
      ModuleHeader,
    },
  };
</script>
