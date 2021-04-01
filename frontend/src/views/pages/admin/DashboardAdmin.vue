<template>
	<div>
		<v-system-bar
			app
			dark
			:class="$store.getters['uifront/getTheme']('V-SYSTEM-BAR-CSS-CLASS')"
		>
			<v-spacer></v-spacer>
			<strong>Hak Akses Sebagai:</strong> {{ ROLE }} |
			<strong>Tahun Anggaran:</strong>
			{{ $store.getters["uifront/getTahunAnggaran"] }} |
			<strong>Bulan Realisasi:</strong>
			{{
				$store.getters["uifront/getNamaBulan"](
					$store.getters["uifront/getBulanRealisasi"]
				)
			}}
		</v-system-bar>
		<v-app-bar app color="#E6E9ED" elevation="0">
			<v-toolbar-title
				class="headline clickable"
				@click.stop="
					$router
						.push('/dashboard/' + $store.getters['auth/AccessToken'])
						.catch(err => {})
				"
			>
				<span class="hidden-sm-and-down">
					{{ $store.getters["uifront/getNamaAPPAlias"] }}
				</span>
			</v-toolbar-title>
			<v-spacer></v-spacer>
			<v-divider class="mx-4" inset vertical></v-divider>
			<v-menu
				:close-on-content-click="true"
				origin="center center"
				transition="scale-transition"
				:offset-y="true"
				bottom
				left
			>
				<template v-slot:activator="{ on }">
					<v-avatar size="30">
						<v-img :src="photoUser" v-on="on" />
					</v-avatar>
				</template>
				<v-list>
					<v-list-item>
						<v-list-item-avatar>
							<v-img :src="photoUser"></v-img>
						</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title class="title">
								{{ ATTRIBUTE_USER("username") }}
							</v-list-item-title>
							<v-list-item-subtitle>
								[{{ DEFAULT_ROLE }}]
							</v-list-item-subtitle>
						</v-list-item-content>
					</v-list-item>
					<v-divider />
					<v-list-item to="/system-users/profil">
						<v-list-item-icon class="mr-2">
							<v-icon>mdi-account</v-icon>
						</v-list-item-icon>
						<v-list-item-title>Profil</v-list-item-title>
					</v-list-item>
					<v-divider />
					<v-list-item @click.prevent="logout">
						<v-list-item-icon class="mr-2">
							<v-icon>mdi-power</v-icon>
						</v-list-item-icon>
						<v-list-item-title>Logout</v-list-item-title>
					</v-list-item>
				</v-list>
			</v-menu>
		</v-app-bar>
		<v-main>
			<v-container fluid>
				<v-row>
					<v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('DMASTER-GROUP')">
							<v-card 
								elevation="0"
								class="mx-auto clickable deep-purple darken-1"
								max-width="344"
								min-height="230"
								color="#385F73"
								@click.native="$router.push('/dmaster')">
								<div class="text-center pt-4">
									<v-btn
										class="mx-2"
										fab
										dark
										large
										elevation ="0"
										color="white">
										<v-icon 
											color="#DA4453">
											mdi-monitor-multiple
										</v-icon>
									</v-btn>
								</div>
								<v-card-title class="white--text font-weight-bold justify-center">
										DATA MASTER
								</v-card-title>    
								<v-card-subtitle class="white--text font-weight-medium text-center">
										Pengaturan berbagai parameter sebagai referensi dari modul-modul lain dalam sistem.
								</v-card-subtitle>
						</v-card>
					</v-col>
				</v-row>
			</v-container>
		</v-main>
	</div>
</template>
<script>
	import { mapGetters } from "vuex";
	export default {
		name: "Dashboard",
		created() {
			this.TOKEN = this.$route.params.token;
			this.tahun_pendaftaran = this.$store.getters["uifront/getTahunAnggaran"];
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.TOKEN,
				},
				{
					text: "DASHBOARD",
					disabled: true,
					href: "#",
				},
			];
			this.initialize();
		},
		data: () => ({
			breadcrumbs: [],
			TOKEN: null,
			dashboard: null,
			tahun_pendaftaran: "",
		}),
		methods: {
			initialize: async function() {
				await this.$ajax
					.get("/auth/me", {
						headers: {
							Authorization: "Bearer " + this.TOKEN,
						},
					})
					.then(({ data }) => {
						this.dashboard = data.role[0];
						this.$store.dispatch("uiadmin/changeDashboard", this.dashboard);
					})
					.catch(error => {
						if (error.response.status == 401) {
							this.$router.push("/login");
						}
					});
				this.$store.dispatch("uiadmin/init", this.$ajax);
			},
			logout() {
				this.loginTime = 0;
				this.$ajax
					.post(
						"/auth/logout",
						{},
						{
							headers: {
								Authorization: "Bearer " + this.TOKEN,
							},
						}
					)
					.then(() => {
						this.$store.dispatch("auth/logout");
						this.$store.dispatch("uifront/reinit");
						this.$store.dispatch("uiadmin/reinit");
						this.$router.push("/");
					})
					.catch(() => {
						this.$store.dispatch("auth/logout");
						this.$store.dispatch("uifront/reinit");
						this.$store.dispatch("uiadmin/reinit");
						this.$router.push("/");
					});
			},
		},
		computed: {
			...mapGetters("auth", {
				DEFAULT_ROLE: "DefaultRole",
				ROLE: "Role",
				CAN_ACCESS: "can",
				ATTRIBUTE_USER: "AttributeUser",
			}),
			photoUser() {
				let img = this.ATTRIBUTE_USER("foto");
				var photo;
				if (img == "") {
					photo = this.$api.storageURL + "/storage/images/users/no_photo.png";
				} else {
					photo = this.$api.storageURL + "/" + img;
				}
				return photo;
			},
		},
	};
</script>
