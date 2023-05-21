<template>
  <AdminGalleryLayout :showrightsidebar="false" :showleftsidebar="false">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-image
      </template>
      <template v-slot:name>
        GALLERY PEMBANGUNAN
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
          Gallery pembangunan
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container>
      <v-row v-if="datatable.length > 0">
        <v-col
          v-for="media in datatable"
          :key="media.id"
          class="d-flex child-flex"
          cols="4"
        >
          <CardFotoInfo :media="media" />
        </v-col>
      </v-row>
      <v-row v-else>
        <v-col>
          <v-alert dense type="info">
            Saat ini belum ada foto / video di gallery pembangunan.
          </v-alert>
        </v-col>
      </v-row>
    </v-container>
  </AdminGalleryLayout>
</template>
<script>
  import AdminGalleryLayout from "@/views/layouts/AdminGalleryLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  import CardFotoInfo from "@/components/gallery/CardFotoInfo";
  export default {
    name: "GalleryPembangunanAdmin",
    created() {
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
        },
        {
          text: "GALLERY PEMBANGUNAN",
          disabled: true,
          href: "#",
        },
      ];
      this.initialize();
    },
    data: () => ({
      //modul
      breadcrumbs: [],
      datatableLoading: false,
      datatable: [],
       //form data
      form_valid: true,
      daftar_bulan: [],
    }),
    methods: {
      initialize: async function() {
        await this.$ajax
          .post(
            "/renja/gallery",
            {
              pid: "gallery",
              TA: this.$store.getters["uifront/getTahunAnggaran"],
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.datatable = data.media;
            this.daftar_bulan = data.daftar_bulan;
            this.datatableLoading = false;
          })
          .catch(() => {
            this.datatableLoading = false;
          });
      }
    },
    components: {
      AdminGalleryLayout,
      ModuleHeader,
      CardFotoInfo,
    },
  };
</script>