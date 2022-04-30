<template>
	<SystemSettingLayout>
    <ModuleHeader>
			<template v-slot:icon>
				mdi-variable
			</template>
			<template v-slot:name>
				VARIABLES
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
					Mengatur berbagai macam variable default sistem. Perubahan berlaku pada Login selanjutnya.
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container fluid>  
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-form ref="frmdata" v-model="form_valid" lazy-validation>
						<v-card>
							<v-card-title>
								WAKTU
							</v-card-title>
							<v-card-text>
								<v-row>
									<v-col xs="12" sm="12" md="4">
										<v-select
											v-model="formdata.default_ta" 
											:items="daftar_ta"
											label="TAHUN ANGGARAN"
											outlined
											:rules="rule_default_ta"
										/>
									</v-col>
									<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly" />
									<v-col xs="12" sm="12" md="5">
										<p>Masa pelaporan SIMONEV</p>
										<v-radio-group
											v-model="formdata.default_masa_pelaporan"
											row
										>
											<v-radio
												label="APBD MURNI"
												value="murni"
											></v-radio>
											<v-radio
												label="APBD PERUBAHAN"
												value="perubahan"
											></v-radio>
										</v-radio-group>
									</v-col>
									<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly" />
								</v-row>
							</v-card-text>
							<v-card-actions>
								<v-spacer></v-spacer>
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
				</v-col>
			</v-row>
		</v-container>												
  </SystemSettingLayout>
</template>
<script>
import SystemSettingLayout from "@/views/layouts/SystemSettingLayout";
import ModuleHeader from "@/components/ModuleHeader";
export default {
		name: "Variables",
		created() {
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "KONFIGURASI SISTEM",
					disabled: false,
					href: "/system-setting"
				},
				{
					text: "INSTITUSI",
					disabled: false,
					href: "#",
				},
				{
					text: "VARIABLES",
					disabled: true,
					href: "#",
				},
			];
			this.daftar_ta = this.$store.getters["uifront/getDaftarTA"];
			this.initialize();
		},
		data: () => ({
			breadcrumbs: [], 
			btnLoading: false,
			//form
			form_valid: true, 
			daftar_ta: [],
			formdata: {
				default_ta: null,
				default_masa_pelaporan: null,
			}, 
		}),
		methods: {
			async initialize() {
				await this.$ajax.get("/system/setting/variables",
				{
					headers: {
						Authorization: this.$store.getters["auth/Token"],
					},
				})
				.then(({ data }) => {
					let setting = data.setting; 
					this.formdata.default_ta = setting.DEFAULT_TA;					
					this.formdata.default_masa_pelaporan = setting.DEFAULT_MASA_PELAPORAN;					
				});
			},
			save() {
				if (this.$refs.frmdata.validate()) {
					this.btnLoading = true;
					this.$ajax.post("/system/setting/variables",
						{
							_method: "PUT", 
							pid: "Variable default sistem",
							setting: JSON.stringify({
								201: this.formdata.default_ta,
								203: this.formdata.default_masa_pelaporan,
							}),                       
						},
							{
								headers: {
									Authorization: this.$store.getters["auth/Token"],
								}
							}
						)
						.then(() => { 
							this.btnLoading = false;
						})
						.catch(() => {
							this.btnLoading = false;
						});
				}
			},
		},
		components: {
			SystemSettingLayout,
			ModuleHeader,
	 },
}
</script>