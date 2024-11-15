<template>
  <v-main-layout :token="token">
    <v-row class="mb-4" no-gutters>
      <v-col cols="12">          
        <v-card>
          <v-card-title class="d-flex align-center pe-2">
            EVALUASI CAPAIAN KINERJA MISI
          </v-card-title>
          <v-divider></v-divider>
          <v-data-table-server
            density="compact"            
            :headers="headers"
            :items="datatable"
            :loading="datatableLoading"        
            item-value="RpjmdMisiID"
            @update:options="initialize"
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
              <td class="text-center" v-bind:class="getClass(item.predikat_kinerja)">{{ item.predikat_kinerja }}</td>
              <td class="text-center">{{ item.tingkat_anggaran }}</td>
              <td class="text-center" v-bind:class="getClass(item.predikat_anggaran)">{{ item.predikat_anggaran }}</td>
            </tr>            
          </template>
          </v-data-table-server>
        </v-card>
      </v-col>
    </v-row>
  </v-main-layout>  
</template>
<script>
  import { usesUserStore } from '@/stores/UsersStore'
  import mainLayout from '@/layouts/MainLayout.vue'  
  export default {
    name: 'DashboardAdmin',
    created() {
      this.userStore = usesUserStore()
      this.token = this.$route.params.token
      this.initialize()
    },
    data: () => ({
      datatableLoading: false,
      //data table
      datatable: [],
      headers: [        
        {
          title: 'KODE MISI',
          key: 'Kd_RpjmdMisi',
          align: 'start',
          width: 70,
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
          title: 'TINGKAT CAPAIAN KINERJA(%)',
          key: 'tingkat_kinerja',
          align: 'center',
          headerProps: {
            class: 'font-weight-bold',
          },
        },      
        {
          title: 'PREDIKAT KINERJA',
          key: 'predikat_kinerja',
          align: 'center',
          headerProps: {
            class: 'font-weight-bold',
          },
        },      
        {
          title: 'TINGKAT CAPAIAN ANGGARAN(%)',
          key: 'tingkat_kinerja',
          align: 'center',
          headerProps: {
            class: 'font-weight-bold',
          },
        },      
        {
          title: 'PREDIKAT ANGGARAN',
          key: 'predikat_kinerja',
          align: 'center',
          headerProps: {
            class: 'font-weight-bold',
          },
        },      
      ],
      //pinia
      userStore: null,
      token: null,
    }),
    methods: {
      async initialize() {
        var request_param = {
          PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,          
        }
        await this.$ajax
        .post('/rpjmd/dashboard/misi', 
          request_param,
          {
            headers: {
              Authorization: this.userStore.Token,
            },
          }
        )
        .then(({ data }) => {
          let payload = data.payload
          this.datatable = payload          
          this.datatableLoading = false    
        })
      },
      getClass(predikat) {
        if(predikat == 'SANGAT RENDAH') {
          return 'sangat-rendah'
        } else if(predikat == 'RENDAH') {
          return 'rendah'
        } else if(predikat == 'SEDANG') {
          return 'sedang'
        } else if(predikat == 'TINGGI') {
          return 'tinggi'
        } else if(predikat == 'SANGAT TINGGI') {
          return 'sangat-tinggi'
        }
      },
    },
    components: {
      'v-main-layout': mainLayout,
    }
  }
</script>