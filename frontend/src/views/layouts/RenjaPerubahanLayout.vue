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
			{{ $store.getters["auth/TahunSelected"] }} |
			<strong>Bulan Realisasi:</strong>
			{{
				$store.getters["uifront/getNamaBulan"](
					$store.getters["uifront/getBulanRealisasi"]
				)
			}}
			<strong><slot name="system-bar"/></strong>
		</v-system-bar>
		<v-app-bar
			elevation="0"
			app
			:class="$store.getters['uifront/getTheme']('V-APP-BAR-CSS-CLASS')"
		>
			<v-app-bar-nav-icon
				@click.stop="drawer = !drawer"
				:class="
					this.$store.getters['uifront/getTheme'](
						'V-APP-BAR-NAV-ICON-CSS-CLASS'
					)
				"
			>
			</v-app-bar-nav-icon>
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
			<v-app-bar-nav-icon @click.stop="drawerRight = !drawerRight">
				<v-icon color="white">mdi-menu-open</v-icon>
			</v-app-bar-nav-icon>
		</v-app-bar>
		<v-navigation-drawer
			v-model="drawer"
			width="300"
			dark
			:class="
				$store.getters['uifront/getTheme']('V-NAVIGATION-DRAWER-CSS-CLASS')
			"
			:temporary="temporaryleftsidebar"
			app
		>
			<v-list-item>
				<v-list-item-avatar>
					<v-img :src="photoUser" @click.stop="toProfile"></v-img>
				</v-list-item-avatar>
				<v-list-item-content>
					<v-list-item-title class="title white--text">
						{{ ATTRIBUTE_USER("username") }}
					</v-list-item-title>
					<v-list-item-subtitle class="white--text">
						[{{ DEFAULT_ROLE }}]
					</v-list-item-subtitle>
				</v-list-item-content>
			</v-list-item>
			<v-list-item
				to="/renjaperubahan"
				v-if="CAN_ACCESS('RENJA-GROUP')"
				link
				:active-class="
					$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-CSS-CLASS')
				"
				:color="$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-COLOR')"
			>
				<v-list-item-icon class="mr-2">
					<v-icon>mdi-monitor-multiple</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>BOARD DATA RENJA</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
			<v-list-item
				link
				to="/renjaperubahan/datamentah"
				:active-class="
					$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-CSS-CLASS')
				"
				:color="$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-COLOR')"
				v-if="CAN_ACCESS('RENJA-RKA-PERUBAHAN_BROWSE')"
			>
				<v-list-item-icon class="mr-2">
					<v-icon>mdi-database</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>
						DATA MENTAH
					</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
			<v-subheader class="purple accent-5 white--text">TRANSAKSI</v-subheader>
			<v-list-item
				link
				to="/renjaperubahan/targetkinerja"
				:active-class="
					$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-CSS-CLASS')
				"
				:color="$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-COLOR')"
				v-if="CAN_ACCESS('RENJA-RKA-PERUBAHAN_BROWSE')"
			>
				<v-list-item-icon class="mr-2">
					<v-icon>mdi-arrow-collapse-right</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>
						TARGET KINERJA
					</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
			<v-list-item
				link
				to="/renjaperubahan/rka"
				:active-class="
					$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-CSS-CLASS')
				"
				:color="$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-COLOR')"
				v-if="CAN_ACCESS('RENJA-RKA-PERUBAHAN_BROWSE')"
			>
				<v-list-item-icon class="mr-2">
					<v-icon>mdi-arrow-collapse-right</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>
						RKA PERUBAHAN
					</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
			<v-subheader class="purple accent-5 white--text">LAPORAN</v-subheader>
			<v-list-item
				link
				to="/renjaperubahan/report/forma"
				:active-class="
					$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-CSS-CLASS')
				"
				:color="$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-COLOR')"
				v-if="CAN_ACCESS('RENJA-FORM-A-PERUBAHAN_BROWSE')"
			>
				<v-list-item-icon class="mr-2">
					<v-icon>mdi-arrow-collapse-right</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>
						FORM A
					</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
			<v-list-item
				link
				to="/renjaperubahan/report/formbopd"
				:active-class="
					$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-CSS-CLASS')
				"
				:color="$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-COLOR')"
				v-if="CAN_ACCESS('RENJA-FORM-B-PERUBAHAN_BROWSE')"
			>
				<v-list-item-icon class="mr-2">
					<v-icon>mdi-arrow-collapse-right</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>
						FORM B OPD
					</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
			<v-list-item
				link
				to="/renjaperubahan/report/formbunitkerja"
				:active-class="
					$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-CSS-CLASS')
				"
				:color="$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-COLOR')"
				v-if="CAN_ACCESS('RENJA-FORM-B-PERUBAHAN_BROWSE')"
			>
				<v-list-item-icon class="mr-2">
					<v-icon>mdi-arrow-collapse-right</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>
						FORM B UNIT KERJA
					</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
			<v-subheader class="purple accent-5 white--text">STATISTIK</v-subheader>
			<v-list-item
				link
				to="/renjaperubahan/statistik/peringkatopd"
				:active-class="
					$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-CSS-CLASS')
				"
				:color="$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-COLOR')"
				v-if="CAN_ACCESS('RENJA-STATISTIK-PERINGKAT-OPD_BROWSE')"
			>
				<v-list-item-icon class="mr-2">
					<v-icon>mdi-arrow-collapse-right</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>
						PERINGKAT OPD
					</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
		</v-navigation-drawer>
		<v-navigation-drawer
			v-model="drawerRight"
			width="300"
			app
			fixed
			right
			temporary
			v-if="showrightsidebar"
		>
			<v-list dense>
				<v-list-item>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-menu-open</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title class="title">
							OPTIONS
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-divider></v-divider>
				<v-list-item
					:class="
						$store.getters['uifront/getTheme']('V_LIST_ITEM_ACTIVE_CSS_CLASS')
					"
				>
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-filter</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>FILTER</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<slot name="filtersidebar" />
			</v-list>
		</v-navigation-drawer>
		<v-main class="mx-4 mb-4">
			<slot />
		</v-main>
		<v-footer app padless fixed dark>
			<v-card class="flex" flat tile>
				<v-divider></v-divider>
				<v-card-text class="py-2 white--text text-center">
					<strong>
						{{ $store.getters["uifront/getNamaAPP"] }} (2022)
					</strong>
					dikembangkan oleh TIM IT BAPELITBANG KAB. Bintan.
					<v-btn dark icon href="https://github.com/mrizkir/simonev21">
						<v-icon>mdi-github</v-icon>
					</v-btn>
				</v-card-text>
			</v-card>
		</v-footer>
	</div>
</template>
<script>
	import { mapGetters } from "vuex";
	export default {
		name: "RenjaPerubahanLayout",
		props: {
			showrightsidebar: {
				type: Boolean,
				default: true,
			},
			temporaryleftsidebar: {
				type: Boolean,
				default: false,
			},
		},
		data: () => ({
			loginTime: 0,
			drawer: null,
			drawerRight: null,
		}),
		methods: {
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
						this.$router.push("/login");
					})
					.catch(() => {
						this.$store.dispatch("auth/logout");
						this.$router.push("/login");
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
					photo = this.$api.storageURL + "/images/users/no_photo.png";
				} else {
					photo = this.$api.url + "/" + img;
				}
				return photo;
			},
		},
	};
</script>
