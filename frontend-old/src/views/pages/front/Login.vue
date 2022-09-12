<template>
	<FrontLayout :showrightsidebar="false">
		<template v-slot:system-bar>
			Tahun Anggaran: {{ formlogin.tahun_anggaran }} | Bulan Realisasi:
			{{
				$store.getters["uifront/getNamaBulan"](
					$store.getters["uifront/getBulanRealisasi"]
				)
			}}
		</template>
		<v-parallax
			dark
			src="https://cdn.vuetifyjs.com/images/backgrounds/vbanner.jpg"
		>
			<v-container hei class="fill-height" fluid>
				<v-row align="center" justify="center" no-gutters>
					<v-col xs="12" sm="6" md="4">
						<v-form
							ref="frmlogin"
							@keyup.native.enter="doLogin"
							lazy-validation
						>
							<v-card outlined>
								<v-card-title>
									<h1
										class="text-center display-1 font-weight-black primary--text"
									>
										LOGIN
									</h1>
								</v-card-title>
								<v-card-text>
									<v-alert
										outlined
										dense
										type="error"
										:value="form_error"
										icon="mdi-close-octagon-outline"
									>
										Username atau Password tidak dikenal !.
									</v-alert>
									<v-text-field
										v-model="formlogin.username"
										label="Username"
										:rules="rule_username"
										outlined
										dense
									/>
									<v-text-field
										v-model="formlogin.password"
										label="Password"
										type="password"
										:rules="rule_password"
										outlined
										dense
									/>
									<v-select
										v-model="formlogin.tahun_anggaran"
										:items="daftar_ta"
										label="TAHUN ANGGARAN"
										:rules="rule_tahun_anggaran"
										dense
										outlined
									/>
								</v-card-text>
								<v-card-actions class="justify-center">
									<v-btn
										color="primary"
										@click="doLogin"
										:disabled="btnLoading"
										block
									>
										Login
									</v-btn>
								</v-card-actions>
							</v-card>
						</v-form>
					</v-col>
				</v-row>
			</v-container>
		</v-parallax>
	</FrontLayout>
</template>
<script>
	import FrontLayout from "@/views/layouts/FrontLayout";
	export default {
		name: "Login",
		created() {
			this.$store.dispatch("uifront/init", this.$ajax);
			if (this.$store.getters["auth/Authenticated"]) {
				this.$router.push(
					"/dashboard/" + this.$store.getters["auth/AccessToken"]
				);
			}
		},
		mounted() {
			this.formlogin.tahun_anggaran = this.$store.getters[
				"uifront/getTahunAnggaran"
			];
			this.daftar_ta = this.$store.getters["uifront/getDaftarTA"];
			if (!(this.daftar_ta.length > 0)) {
				this.$ajax.get("/system/setting/uifront").then(({ data }) => {
					this.daftar_ta = data.daftar_ta;
				});
			}
		},
		data: () => ({
			btnLoading: false,
			//form
			form_error: false,
			daftar_ta: [],
			formlogin: {
				username: "",
				password: "",
				tahun_anggaran: "",
			},
			rule_username: [
				value => !!value || "Kolom Username mohon untuk diisi !!!",
			],
			rule_password: [
				value => !!value || "Kolom Password mohon untuk diisi !!!",
			],
			rule_tahun_anggaran: [
				value => !!value || "Tahun Anggaran mohon untuk dipilih !!!",
			],
		}),
		methods: {
			doLogin: async function() {
				if (this.$refs.frmlogin.validate()) {
					this.btnLoading = true;
					await this.$ajax
						.post("/auth/login", {
							username: this.formlogin.username,
							password: this.formlogin.password,
						})
						.then(({ data }) => {
							this.$ajax
								.get("/auth/me", {
									headers: {
										Authorization: data.token_type + " " + data.access_token,
									},
								})
								.then(response => {
									let user = response.data;
									Object.assign(user, {
										tahun_selected: this.formlogin.tahun_anggaran,
									});
									var data_user = {
										token: data,
										user: user,
									};
									this.$store.dispatch("auth/afterLoginSuccess", data_user);
								});
							this.btnLoading = false;
							this.form_error = false;
							this.$router.push("/dashboard/" + data.access_token);
						})
						.catch(() => {
							this.form_error = true;
							this.btnLoading = false;
						});
				}
			},
		},
		components: {
			FrontLayout,
		},
	};
</script>
