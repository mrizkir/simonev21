<template>
  <v-front-layout>
    <v-container fluid>
      <v-row dense>
        <v-col xs="12" sm="6" md="4">
          <v-card color="primary" dark>
            <v-card-title class="text-h5">
              Jumlah Indikator Tujuan
            </v-card-title>
            <v-card-subtitle>Tujuan RPJMD: {{ statistik.jumlah_tujuan }}</v-card-subtitle>
            <v-card-text>
              <v-icon size="100">mdi-target</v-icon>
              <span class="text-h3">{{ statistik.jumlah_indikator_tujuan }}</span>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col xs="12" sm="6" md="4">
          <v-card color="primary" dark>
            <v-card-title class="text-h5">
              Jumlah Indikator Sasaran
            </v-card-title>
            <v-card-subtitle>Sasaran RPJMD: {{ statistik.jumlah_sasaran }}</v-card-subtitle>
            <v-card-text>
              <v-icon size="100">mdi-target</v-icon>
              <span class="text-h3">{{ statistik.jumlah_indikator_sasaran }}</span>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col xs="12" sm="6" md="4">
          <v-card color="primary" dark>
            <v-card-title class="text-h5">
              Jumlah Indikator Program
            </v-card-title>
            <v-card-subtitle>&nbsp;</v-card-subtitle>
            <v-card-text>
              <v-icon size="100">mdi-target</v-icon>
              <span class="text-h3">{{ statistik.jumlah_indikator_program }}</span>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col xs="12" sm="6" md="4">
          <v-card color="primary" dark>
            <v-card-title class="text-h5">
              Jumlah IKU
            </v-card-title>
            <v-card-subtitle>
              Indikator Kinerja Utama
            </v-card-subtitle>
            <v-card-text>
              <v-icon size="100">mdi-target</v-icon>
              <span class="text-h3">{{ statistik.jumlah_iku }}</span>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col xs="12" sm="6" md="4">
          <v-card color="primary" dark>
            <v-card-title class="text-h5">
              Jumlah IKK
            </v-card-title>
            <v-card-subtitle>
              Indikator Kinerja Kunci
            </v-card-subtitle>
            <v-card-text>
              <v-icon size="100">mdi-target</v-icon>
              <span class="text-h3">{{ statistik.jumlah_ikk }}</span>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col xs="12" sm="6" md="4">
          <v-card color="primary" dark>
            <v-card-title class="text-h5">
              Jumlah Program RPJMD
            </v-card-title>
            <v-card-subtitle>&nbsp;</v-card-subtitle>
            <v-card-text>
              <v-icon size="100">mdi-target</v-icon>
              <span class="text-h3">{{ statistik.jumlah_program }}</span>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row dense>
        <v-col xs="12" sm="12" md="12">
          <v-card> 
            <v-card-title class="d-flex align-center pe-2">
              <v-icon size="60">mdi-target</v-icon>
              <span class="text-h5">Capaian Kinerja Misi Tahun 2024</span>
            </v-card-title>
            <v-divider></v-divider>
            <v-data-table-server
              density="compact"            
              :headers="headers_misi"
              :items="datatable_misi"
              :loading="datatableLoadingMisi"        
              item-value="RpjmdMisiID"
              @update:options="initializeDataTableMisi"
              items-per-page="-1"
              itemsLength="-1"
              disable-sort
              hide-default-footer
            >
            <template v-slot:item="{ item }">
              <tr>
                <td>{{ item.Kd_RpjmdMisi }}</td>
                <td>{{ item.Nm_RpjmdMisi }}</td>
                <td class="text-center">{{ item.tingkat_kinerja }}</td>
                <!-- <td class="text-center" v-bind:class="getClass(item.predikat_kinerja)">{{ item.predikat_kinerja }}</td> -->
                <!-- <td class="text-center">{{ item.tingkat_anggaran }}</td> -->
                <!-- <td class="text-center" v-bind:class="getClass(item.predikat_anggaran)">{{ item.predikat_anggaran }}</td> -->
              </tr>            
            </template>
            </v-data-table-server>
          </v-card>
        </v-col>
      </v-row>
      <v-row dense>
        <v-col xs="12" sm="12" md="12">
          <v-card>
            <v-card-title class="d-flex align-center pe-2">
              <v-icon size="60">mdi-target</v-icon>
              <span class="text-h5">Capaian Program Dalam Misi Tahun 2024</span>
            </v-card-title>
            <v-divider></v-divider>
            <v-data-table-server
              density="compact"            
              :headers="headers_misi_program"
              :items="datatable_misi_program"
              :loading="datatableLoadingMisiProgram"
              item-value="RpjmdMisiID"
              @update:options="initializeDataTableMisi"
              items-per-page="-1"
              itemsLength="-1"
              disable-sort
              hide-default-footer
            >
            </v-data-table-server>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </v-front-layout>  
</template>
<script>
  import frontLayout from '@/layouts/FrontLayout.vue'
  export default {
    name: 'DashboardFront',
    created() {
      this.initialize()
    },
    data: () => ({
      statistik: {},
      datatableLoadingMisi: false,
      //data table misi
      datatable_misi: [],
      headers_misi: [
        {
          title: 'KODE MISI',
          key: 'Kd_RpjmdMisi',
          align: 'start',
          width: 120,
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'NAMA MISI',
          key: 'Nm_RpjmdMisi',
          align: 'start',
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'CAPAIAN KINERJA (%)',
          key: 'tingkat_kinerja',
          align: 'center',
          headerProps: {
            class: 'font-weight-bold',
          },
        },   
      ],
      //data table misi program
      datatableLoadingMisiProgram: false,
      datatable_misi_program: [],
      headers_misi_program: [
      {
          title: 'KODE MISI',
          key: 'Kd_RpjmdMisi',
          align: 'start',
          width: 120,
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'NAMA MISI',
          key: 'Nm_RpjmdMisi',
          align: 'start',
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'JUMLAH PROGRAM',
          key: 'jumlah_program',
          align: 'center',
          headerProps: {
            class: 'font-weight-bold',
          },
        },
        {
          title: 'CAPAIAN KINERJA (%)',
          key: 'tingkat_kinerja_program',
          align: 'center',
          headerProps: {
            class: 'font-weight-bold',
          },
        },
      ]
    }),
    methods: {
      async initialize() {
        await this.$ajax.post('/rpjmd/dashboard/statistik').then(({ data }) => {
          this.statistik = data.payload
        })
      },
      initializeDataTableMisi() {
        this.datatableLoadingMisi = true
        this.datatableLoadingMisiProgram = true
        this.$ajax.post('/rpjmd/dashboard/misi').then(({ data }) => {
          this.datatable_misi = data.payload
          this.datatable_misi_program = data.payload
          this.datatableLoadingMisi = false
          this.datatableLoadingMisiProgram = false
        })
      }
    },
    components: {
      'v-front-layout': frontLayout,
    }
  }
</script>