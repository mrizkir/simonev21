<template>
	<RenjaMurniLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-graph
			</template>
			<template v-slot:name>
				RKA MURNI
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
					Ubah Rencana Kegiatan dan Anggaran (RKA) OPD / Unit Kerja APBD Murni
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-bottom-navigation color="purple lighten-1">
						<v-btn
							:to="{ path: '/renjamurni/rka/uraian/' + datakegiatan.RKAID }"
						>
							<span>Keluar</span>
							<v-icon>mdi-close</v-icon>
						</v-btn>
					</v-bottom-navigation>
				</v-col>
			</v-row>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-form ref="frmdata" v-model="form_valid" lazy-validation>
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
								<h5>DATA REKENING</h5>
								<v-divider class="mb-2"></v-divider>
								<v-autocomplete
									:items="daftar_jenis"
									v-model="rekening_JnsID"
									label="REKENING JENIS"
									item-text="nama_rek3"
									item-value="JnsID"
									filled
									outlined
									:disabled="datakegiatan.Locked == 1 || btnLoading"
								>					
								</v-autocomplete>
								<v-autocomplete
									:items="daftar_objek"
									v-model="rekening_ObyID"
									label="REKENING OBJEK"
									item-text="nama_rek4"
									item-value="ObyID"
									filled
									outlined
									:disabled="datakegiatan.Locked == 1 || btnLoading"
								>
								</v-autocomplete>
								<v-autocomplete
									:items="daftar_rincian_objek"
									v-model="rekening_RObyID"
									label="REKENING RINCIAN OBJEK"
									item-text="nama_rek5"
									item-value="RObyID"
									filled
									outlined
									:disabled="datakegiatan.Locked == 1 || btnLoading"
								>
								</v-autocomplete>
								<v-autocomplete
									:items="daftar_sub_rincian_objek"
									v-model="rekening_SubRObyID"
									label="REKENING SUB RINCIAN OBJEK"
									item-text="nama_rek6"
									item-value="SubRObyID"
									filled
									outlined
									return-object
									:rules="rule_sub_rincian_objek"
									:disabled="datakegiatan.Locked == 1 || btnLoading"
								>
								</v-autocomplete>
								<h5>DATA URAIAN</h5>
								<v-divider class="mb-2"></v-divider>
								<v-text-field
									v-model="formdata.nama_uraian"
									label="NAMA URAIAN"
									:rules="rule_uraian"
									type="text"
									filled
									outlined
									:disabled="datakegiatan.Locked == 1 || btnLoading"
								/>
								<v-text-field
									v-model="formdata.volume"
									label="VOLUME"
									:rules="rule_volume"
									type="text"
									filled
									outlined
									:disabled="datakegiatan.Locked == 1 || btnLoading"
								/>
								<v-text-field
									v-model="formdata.satuan"
									label="SATUAN"
									:rules="rule_satuan"
									type="text"
									filled
									outlined
									:disabled="datakegiatan.Locked == 1 || btnLoading"
								/>
								<v-currency-field
									label="HARGA PER SATUAN"
									:min="null"
									:max="null"
									filled
									outlined
									v-model="formdata.harga_satuan"
									:disabled="datakegiatan.Locked == 1 || btnLoading"
								/>
								<v-currency-field
									label="JUMLAH PAGU URAIAN"
									:min="null"
									:max="null"
									filled
									outlined
									v-model="PaguUraian"
									:disabled="true"
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
									@click.stop="closeadduraian"
								>
									TUTUP
								</v-btn>
								<v-btn
									color="blue darken-1"
									text
									@click.stop="save"
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
	</RenjaMurniLayout>
</template>
<script>
	import RenjaMurniLayout from "@/views/layouts/RenjaMurniLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	export default {
		name: "AddUraianRKAMurni",
		created() {
			this.RKAID = this.$route.params.rkaid;
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
					href: "/renjamurni/rka/uraian/" + this.RKAID,
				},
				{
					text: "TAMBAH",
					disabled: true,
					href: "#",
				},
			];

			var page = this.$store.getters["uiadmin/Page"]("rkamurni");
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
				this.$router.push("/belanja/rkamurni");
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
				daftar_jenis: [],
				rekening_JnsID: null,

				daftar_objek: [],
				rekening_ObyID: null,
				
				daftar_rincian_objek: [],
				rekening_RObyID: null,
				
				daftar_sub_rincian_objek: [],
				rekening_SubRObyID: null,

				formdata: {
					RKAID: "",
					SubRObyID: "",
					kode_uraian: "",
					nama_uraian: "",
					volume: 1,
					satuan: "",
					harga_satuan: 0,
					Descr: "",
				},
				rule_sub_rincian_objek: [
					value => !!value || "Mohon untuk di pilih rekening sub rincian objek !!!",
				],
				rule_uraian: [
					value => !!value || "Mohon untuk di isi nama uraian !!!",
				],
				rule_volume: [
					value => {
						if (value !== null && value !== "" && value.length > 0) {
							return /^[0-9]+$/.test(value) || "Volume uraian harus angka";
						} else {
							return true;
						}
					},
				],
				rule_satuan: [
					value => !!value || "Mohon untuk di isi satuan uraian !!!",
				],
			};
		},
		methods: {
			initialize: async function() {
				await this.$ajax
					.post(
						"/dmaster/rekening/jenis",
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
						this.daftar_jenis = data.jenis;
					});				
			},
			save() {
				if (this.$refs.frmdata.validate()) {					
					this.$ajax
						.post(
							"/renja/rkamurni/storeuraian",
							{
								RKAID: this.RKAID,
								SubRObyID: this.rekening_SubRObyID.SubRObyID,
								kode_uraian1: this.rekening_SubRObyID.kode_uraian,
								nama_uraian1: this.formdata.nama_uraian,
								volume1: this.formdata.volume,
								satuan1: this.formdata.satuan,
								harga_satuan1: this.formdata.harga_satuan,
								PaguUraian1: this.PaguUraian,
								Descr: this.formdata.Descr,
							},
							{
								headers: {
									Authorization: this.$store.getters["auth/Token"],
								},
							}
						)
						.then(() => {	
							this.$router.push(
								"/renjamurni/rka/uraian/" + this.datakegiatan.RKAID
							);
						})
						.catch(() => {
							this.btnLoading = false;
						});
				}
			},
			closeadduraian() {
				this.$router.push(
					"/renjamurni/rka/uraian/" + this.datakegiatan.RKAID
				);
			},
		},
		watch: {
			rekening_JnsID(val) {
				this.btnLoading = true;
				
				this.daftar_objek = [];
				this.rekening_ObyID = null;
				
				this.daftar_rincian_objek = [];
				this.rekening_RObyID = null;
				
				this.daftar_sub_rincian_objek = [];
				this.rekening_SubRObyID = null;

				this.$ajax
					.get(
						"/dmaster/rekening/jenis/" + val + "/objekrka",
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.daftar_objek = data.objek;
						this.btnLoading = false;
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			rekening_ObyID(val) {
				this.btnLoading = true;
				
				this.daftar_rincian_objek = [];
				this.rekening_RObyID = null;
				
				this.daftar_sub_rincian_objek = [];
				this.rekening_SubRObyID = null;

				this.$ajax
					.get(
						"/dmaster/rekening/objek/" + val + "/rincianobjekrka",
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.daftar_rincian_objek = data.rincianobjek;
						this.btnLoading = false;
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			rekening_RObyID(val) {
				this.btnLoading = true;
				
				this.daftar_sub_rincian_objek = [];
				this.rekening_SubRObyID = null;

				this.$ajax
					.get(
						"/dmaster/rekening/rincianobjek/" + val + "/subrincianobjekrka",
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.daftar_sub_rincian_objek = data.subrincianobjek;
						this.btnLoading = false;
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			rekening_SubRObyID(val) {
				if (val) {
					this.formdata.nama_uraian = val.SubRObyNm;			
				} else {
					this.formdata.nama_uraian = "";
				}
			}
		},
		computed: {
			PaguUraian() {
				return this.formdata.volume * this.formdata.harga_satuan;
			},
		},
		components: {
			RenjaMurniLayout,
			ModuleHeader,
		},
	};
</script>
