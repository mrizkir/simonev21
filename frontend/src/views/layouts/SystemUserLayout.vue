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
				:to="{ path: '/system-users' }"
				v-if="CAN_ACCESS('SYSTEM-USERS-GROUP')"
				link
				:active-class="
					$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-CSS-CLASS')
				"
				:color="$store.getters['uifront/getTheme']('V-LIST-ITEM-BOARD-COLOR')"
			>
				<v-list-item-icon class="mr-2">
					<v-icon>mdi-account-key</v-icon>
				</v-list-item-icon>
				<v-list-item-content>
					<v-list-item-title>BOARD USER SISTEM</v-list-item-title>
				</v-list-item-content>
			</v-list-item>
		</v-navigation-drawer>
	</div>
</template>
<script>
	import { mapGetters } from "vuex";
	export default {
		name: "SystemUserLayout",
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
					photo = this.$api.storageURL + "/storage/images/users/no_photo.png";
				} else {
					photo = this.$api.storageURL + "/" + img;
				}
				return photo;
			},
		},
	};
</script>
