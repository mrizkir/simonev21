<template>
	<RenjaPerubahanLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-graph
			</template>
			<template v-slot:name>
				TARGET KINERJA
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
					Halaman ini digunakan untuk memperbaiki bila Target Kinerja FISIK atau KEUANGAN terdapat kesalahan
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container v-if="Object.keys(datakegiatan).length > 1">
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-card>
						<v-card-title>
							DATA KEGIATAN
							<v-spacer />
							<v-icon small class="mr-2" v-if="datakegiatan.Locked">
								mdi-lock
							</v-icon>
						</v-card-title>
						<v-card-text>
							<table class="table">
								<tbody>
									<tr>
										<td width="150">RKAID</td>
										<td width="400">{{ datakegiatan.RKAID }}</td>
										<td width="150">PAGU DANA</td>
										<td width="400">
											{{ datakegiatan.PaguDana1 | formatUang }}
										</td>
									</tr>
									<tr>
										<td width="150">KODE PROGRAM</td>
										<td width="400">{{ datakegiatan.kode_program }}</td>
										<td width="150">NAMA BIDANG URUSAN</td>
										<td width="400">{{ datakegiatan.Nm_Bidang }}</td>
									</tr>
									<tr>
										<td width="150">PROGRAM</td>
										<td width="400">{{ datakegiatan.Nm_Program }}</td>
										<td width="150">KODE OPD / SKPD</td>
										<td width="400">{{ datakegiatan.kode_organisasi }}</td>
									</tr>
									<tr>
										<td width="150">KODE KEGIATAN</td>
										<td width="400">{{ datakegiatan.kode_kegiatan }}</td>
										<td width="150">OPD / SKPD</td>
										<td width="400">{{ datakegiatan.Nm_Organisasi }}</td>
									</tr>
									<tr>
										<td width="150">NAMA KEGIATAN</td>
										<td width="400">{{ datakegiatan.Nm_Kegiatan }}</td>
										<td width="150">KODE UNIT KERJA</td>
										<td width="400">{{ datakegiatan.kode_sub_organisasi }}</td>
									</tr>
									<tr>
										<td width="150">KODE SUB KEGIATAN</td>
										<td width="400">
											{{ datakegiatan.kode_sub_kegiatan }}
										</td>
										<td width="150">UNIT KERJA</td>
										<td width="400">{{ datakegiatan.Nm_Sub_Organisasi }}</td>
									</tr>
									<tr>
										<td width="150">NAMA SUB KEGIATAN</td>
										<td width="400">
											{{ datakegiatan.Nm_Sub_Kegiatan }}
										</td>
										<td width="150">DIBUAT/DIUBAH</td>
										<td width="400">
											{{
												$date(datakegiatan.created_at).format(
													"DD/MM/YYYY HH:mm"
												)
											}}
											/
											{{
												$date(datakegiatan.updated_at).format(
													"DD/MM/YYYY HH:mm"
												)
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
						<v-btn @click.stop="exituraiantargetkinerja">
							<span>Keluar</span>
							<v-icon>mdi-close</v-icon>
						</v-btn>
					</v-bottom-navigation>
				</v-col>
			</v-row>
      <v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-card>
						<v-card-title>
							PILIH URAIAN SUB KEGIATAN
						</v-card-title>
						<v-card-text>
							<v-autocomplete
								:items="daftar_uraian"
								v-model="RKARincID_Selected"
								label="URAIAN"
							>
							</v-autocomplete>
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
						item-key="RKATargetRincID"
						sort-by="bulan2"						
						:loading="datatableLoading"
						loading-text="Loading... Please wait"						
						class="elevation-1"
						:disable-pagination="true"
						:hide-default-footer="true"
						dense						
					>
						<template v-slot:top>							
							<v-toolbar flat color="white">
								<v-toolbar-title>DAFTAR TARGET</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
								<v-spacer></v-spacer>
							</v-toolbar>
							<v-alert type="info">
								Untuk merubah silahkan klik pada angka yang akan diubah
							</v-alert>
						</template>
						<template v-slot:item.actions="{ item }">
							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon
										small
										v-bind="attrs"
										v-on="on"
										class="ma-1"
										color="red"										
										:disabled="btnLoading || item.Locked == 1 || datatable.length <= 12"
										@click.stop="deleteItem(item)"
									>
										mdi-delete
									</v-icon>
								</template>
								<span>Hapus Uraian</span>
							</v-tooltip>
						</template>
						<template v-slot:item.target2="props">
							<v-edit-dialog
								:return-value.sync="props.item.target2"
								large
								@save="savetargetanggarankas(props.item)"
							> 
									{{ props.item.target2 | formatUang }}
									<template v-slot:input>
										<div class="mt-4 title">Update Target</div>
										<v-currency-field
											label="ANGGARAN KAS"
											:min="null"
											:max="null"
											outlined
											autofocus
											v-model="props.item.target2"
										>
										</v-currency-field>
									</template>
							</v-edit-dialog>
						</template>
						<template v-slot:item.fisik2="props">
							<v-edit-dialog
								:return-value.sync="props.item.fisik2"
								large
								@save="savetargetfisik(props.item)"
							> 
									{{ props.item.fisik2 }}
									<template v-slot:input>
										<div class="mt-4 title">Update Target</div>
										<v-currency-field
											label="FISIK"
											:min="0"
											:max="100"
											outlined
											autofocus
											:auto-decimal-mode="false"
											:allow-negative="false"
											:value-as-integer="true"
											v-model="props.item.fisik2"
										>
										</v-currency-field>
									</template>
							</v-edit-dialog>
						</template>
						<template v-slot:body.append>
							<tr class="amber darken-1 font-weight-black">
								<td class="text-right">TOTAL</td>
								<td class="text-right">
									{{ footers.target2 | formatUang }}
								</td>								
								<td class="text-right">
									{{ footers.fisik2 }}
								</td>
								<td></td>
							</tr>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<strong>ID:</strong>
								{{ item.RKATargetRincID }}								
								<strong>created_at:</strong>
								{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
								<strong>updated_at:</strong>
								{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
							</td>
						</template>
						<template v-slot:no-data>
							<span v-if="RKARincID_Selected">
								BELUM ADA DATA SILAHKAN TAMBAH
							</span>
							<span v-else>
								BELUM ADA DATA SILAHKAN PILIH URAIAN
							</span>
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
		name: "TargetKinerjaPerubahanDetail",
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
					href: "/renjaperubahan",
				},
				{
					text: "TARGET KINERJA",
					disabled: false,
					href: "/renjaperubahan/targetkinerja",
				},
				{
					text: "DETAIL",
					disabled: true,
					href: "#",
				},
			];
			this.RKAID = this.$route.params.rkaid;
			var page = this.$store.getters["uiadmin/Page"]("targetkinerjaperubahan");
			this.datakegiatan = page.datakegiatan;
			if (Object.keys(this.datakegiatan).length > 1) {
				this.initialize();
				if (page.datauraian.RKARincID) {
					this.RKARincID_Selected = page.datauraian.RKARincID;
				}
			} else {
				this.exituraiantargetkinerja();
			}
		},
		data() {
			return {
				//modul
				RKAID: "",
				datakegiatan: [],
				btnLoading: false,
				datatableLoading: false,
				datatableLoaded: false,
				expanded: [],
				datatable: [],
				headers: [
					{ text: "BULAN KE", value: "bulan2", sortable: false, width: 100 },					
					{
						text: "ANGGARAN KAS",
						align: "end",
						value: "target2",						
					},
					{
						text: "FISIK",
						align: "end",
						value: "fisik2",						
					},					
					{ text: "AKSI", value: "actions", sortable: false, width: 70 },
				],
				footers: {
					target2: 0,
					fisik2: 0,
				},
				search: "",

				//dialog
				dialogedituraian: false,
				dialogtargetfisik: false,
				dialogtargetanggarankas: false,
				dialogdetailitem: false,

				//form data
				form_valid: true,
				daftar_uraian: [],
				RKARincID_Selected: null,
				//form rules
				rule_volume: [
					value => !!value || "Mohon untuk di isi Volume kegiatan !!!",
					value =>
						/^[0-9]+$/.test(value) || "Volume kegiatan hanya boleh angka",
				],
				rule_angka: [
					value =>
						/^(100(\.0{1,2})?|[1-9]?\d(\.\d{1,2})?)$/.test(value) ||
						"Isi dengan nilai persentase 0.00 s.d 100.00",
				],
			};
		},
		methods: {
			initialize: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.get("/renja/rkaperubahan/" + this.datakegiatan.RKAID, {
						headers: {
							Authorization: this.$store.getters["auth/Token"],
						},
					})
					.then(({ data }) => {				
						var uraian = [];
						var du = data.uraian;
						du.forEach(item => {
							uraian.push({
								text: "[" + item.kode_uraian + "] " + item.nama_uraian,
								value: item.RKARincID,
							});
						});				
						this.daftar_uraian = uraian;
						this.datatableLoaded = true;
						this.datatableLoading = false;						
					})
					.catch(() => {
						this.datatableLoaded = true;
						this.datatableLoading = false;
					});
			},
			fetchTargetKinerja(RKARincID_Selected) {
				this.$ajax
					.get(
						"/renja/targetkinerjaperubahan/" + RKARincID_Selected + "/uraian",							
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.datatable = data.targetkinerja;
						this.footersummary();
					});
			},
			savetargetanggarankas(item) {				
				this.datatableLoading = true;
				this.$ajax
					.post(
						"/renja/targetkinerjaperubahan/updatetargetanggarankas",
						{
							_method: "put",
							RKATargetRincID: item.RKATargetRincID,
							target2: item.target2,							
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(() => {
						this.datatableLoading = false;
						this.footersummary();
					})
					.catch(() => {
						this.datatableLoading = false;						
						this.fetchTargetKinerja(this.RKARincID_Selected);
					});
			},
			savetargetfisik(item) {
				this.datatableLoading = true;
				this.$ajax
					.post(
						"/renja/targetkinerjaperubahan/updatetargetfisik",
						{
							_method: "put",
							RKATargetRincID: item.RKATargetRincID,
							fisik2: item.fisik2,							
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(() => {
						this.datatableLoading = false;						
						this.footersummary();
					})
					.catch(() => {
						this.datatableLoading = false;						
						this.fetchTargetKinerja(this.RKARincID_Selected);
					});
			},
			footersummary() {
				let data = this.datatable;
				var summary = {
					target2: 0,
					fisik2: 0,
				};
				if (data.length > 0) {
					var totaltarget = 0;
					var totalfisik = 0;
					for (var i = 0; i < data.length; i++) {
						var num = new Number(data[i].target2);
						totaltarget += num;												
						num = new Number(data[i].fisik2);
						totalfisik += num;
					}					
					summary.target2 = totaltarget;
					summary.fisik2 = totalfisik;
				}
				this.footers = summary;
			},
			exituraiantargetkinerja() {
				this.btnLoading = false;
				var page = this.$store.getters["uiadmin/Page"]("targetkinerjaperubahan");
				page.datakegiatan = {
					RKAID: "",
				};
				page.datauraian = {
					RKARincID: null,
				};
				page.datarekening = {};
				this.$store.dispatch("uiadmin/updatePage", page);
				this.$router.push("/renjaperubahan/targetkinerja");
			},
			deleteItem(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menghapus data target kinerja dengan kode " +
							item.RKATargetRincID +
							" ?",
						{ color: "red", width: "600px" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/renja/targetkinerjaperubahan/" + item.RKATargetRincID,
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
									this.$router.go();
								})
								.catch(() => {
									this.btnLoading = false;
								});
						}
					});
			},
		},
		watch: {
			RKARincID_Selected: async function(val) {
				if (val !== null && typeof val !== "undefined") {
					var page = this.$store.getters["uiadmin/Page"]("targetkinerjaperubahan");
					page.datauraian= {
						RKARincID: val,
					};
					this.$store.dispatch("uiadmin/updatePage", page);
					this.fetchTargetKinerja(val);
				}
			},
		},
		components: {
			RenjaPerubahanLayout,
			ModuleHeader,
		},
	};
</script>
