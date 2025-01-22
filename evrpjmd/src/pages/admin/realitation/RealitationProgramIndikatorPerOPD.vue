<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-graph
      </template>
      <template v-slot:name>
        REALISASI INDIKATOR PROGRAM PER OPD
      </template>
      <template v-slot:breadcrumbs>
        <v-breadcrumbs :items="breadcrumbs" class="pa-0">
          <template v-slot:divider>
            <v-icon>mdi-chevron-right</v-icon>
          </template>
        </v-breadcrumbs>
      </template>
      <template v-slot:desc>
        <v-alert color="cyan" border="start" colored-border type="info">
          Halaman ini digunakan untuk mengelola realisasi indikator program RPJMD.  Data program, bidang urusan, urusan, opd penanggungjawab diperoleh dari SIMONEV Tahun Anggaran {{ this.userStore.PeriodeRPJMD.TA_AWAL }}.
        </v-alert>
      </template>      
    </v-page-header>
  </v-main-layout>
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'  
  import { usesUserStore } from '@/stores/UsersStore'
  import { usesPageStore } from '@/stores/PageStore'
  import { VNumberInput } from 'vuetify/labs/VNumberInput'
  export default {
    name: 'RealitationIndikatorProgramPerOPD',
    created() {
      this.userStore = usesUserStore()
      this.pageStore = usesPageStore()
      this.breadcrumbs = [
        {
          title: 'HOME',
          href: '/admin/' + this.userStore.AccessToken,
        },
        {
          title: 'REALISASI',
          disabled: false,
          href: '#',
        },
        {
          title: 'INDIKATOR - PROGRAM',
          disabled: true,
          href: '#',
        },
      ]
      this.pageStore.addToPages({
        name: 'RealisasiIndikatorProgramPerOPD',
        BidangID_Selected: '',        
      });
    },
    data: () => ({
      btnLoading: false,
      datatableLoading: false,
      //pinia
      userStore: null,
      pageStore: null,
    }),
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,
      'v-number-input': VNumberInput,
    },
  }
</script>