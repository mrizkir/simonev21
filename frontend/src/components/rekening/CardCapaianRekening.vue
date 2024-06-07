<template>
  <v-data-table
    :headers="headers"
    :items="datatable"
    item-key="rekening_id"
    dense
    :single-expand="true"
    class="elevation-1"
    :loading="datatableLoading"
    loading-text="Loading... Please wait"
    :disable-pagination="true"
    :hide-default-footer="true"
    style="font-size: 11px"
  >
    <template v-slot:top>
      <v-toolbar flat color="white">
        <v-toolbar-title>{{ mode.toUpperCase() }} TW {{ tw }}</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              v-bind="attrs"
              v-on="on"
              color="primary"
              icon
              outlined
              small
              class="ma-2"
              @click.stop="loadstatistik7"
              :disabled="btnLoading"
            >
              <v-icon>mdi-database-refresh</v-icon>
            </v-btn>
          </template>
          <span>TAMBAH RKA</span>
        </v-tooltip>
      </v-toolbar>
    </template>
  </v-data-table>
</template>
<script>
  export default {
    name: "CardCapaianRekening",
    props: {
      tw: {
        type: Number,
        required: true,
      },
      tahun_anggaran: {
        type: Number,
        required: true,
      },
      mode: {
        type: String,
        required: true,
      },
    },
    mounted() {
      this.initialized();
    },
    data() {
      var header = [];
      switch(this.tw) {
        case 1:
          header = [            
            {
              text: "REKENING",
              value: "nama_rekening",  
              sortable: false,
            },
            {
              text: "JAN",
              value: "data_1",
              sortable: false,
            },
            {
              text: "FEB",
              value: "data_2",
              sortable: false,
            },
            {
              text: "MAR",
              value: "data_3",
              sortable: false,
            },
          ];
        break;
        case 2:
          header = [            
            {
              text: "REKENING",
              value: "nama_rekening",  
              sortable: false,
            },
            {
              text: "APR",
              value: "data_4",
              sortable: false,
            },
            {
              text: "MEI",
              value: "data_5",
              sortable: false,
            },
            {
              text: "JUN",
              value: "data_6",
              sortable: false,
            },
          ];
        break;
        case 3:
          header = [            
            {
              text: "REKENING",
              value: "nama_rekening",  
              sortable: false,
            },
            {
              text: "JUL",
              value: "data_7",
              sortable: false,
            },
            {
              text: "AGUS",
              value: "data_8",
              sortable: false,
            },
            {
              text: "SEPT",
              value: "data_9",
              sortable: false,
            },
          ];
        break;
        case 4:
          header = [            
            {
              text: "REKENING",
              value: "nama_rekening",  
              sortable: false,
            },
            {
              text: "OKT",
              value: "data_10",
              sortable: false,
            },
            {
              text: "NOV",
              value: "data_11",
              sortable: false,
            },
            {
              text: "DES",
              value: "data_12",
              sortable: false,
            },
          ];
        break;
      }
      return {
        btnLoading: false,  
        //data table
        datatableLoading: false,
        expanded: [],
        datatable: [],
        headers: header,
      };
    },
    methods: {
      async initialized() {
        this.datatableLoading = true;
        await this.$ajax
          .post(
            "/renjamurni/statistik/capaianrek",
            {
              ta: this.tahun_anggaran,
              tw: this.tw,
              mode: this.mode,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            if(data.status == 1) {
              let data_rekening = data.result;
              data_rekening.forEach(element => {
                
                var data_;
                switch(this.tw) {
                  case 1:
                    data_ = {
                      rekening_id: element.rekening_id,
                      nama_rekening: element.nama_rekening,
                      data_1: "target: " + element.target[1] + ", realisasi: " + element.realisasi[1],
                      data_2: "target: " + element.target[2] + ", realisasi: " + element.realisasi[2],
                      data_3: "target: " + element.target[3] + ", realisasi: " + element.realisasi[3],
                    };
                  break;
                  case 2:
                    data_ = {
                      rekening_id: element.rekening_id,
                      nama_rekening: element.nama_rekening,
                      data_4: "target: " + element.target[4] + ", realisasi: " + element.realisasi[4],
                      data_5: "target: " + element.target[5] + ", realisasi: " + element.realisasi[5],
                      data_6: "target: " + element.target[6] + ", realisasi: " + element.realisasi[6],
                    };
                  break;
                  case 3:
                    data_ = {
                      rekening_id: element.rekening_id,
                      nama_rekening: element.nama_rekening,
                      data_7: "target: " + element.target[7] + ", realisasi: " + element.realisasi[7],
                      data_8: "target: " + element.target[8] + ", realisasi: " + element.realisasi[8],
                      data_9: "target: " + element.target[9] + ", realisasi: " + element.realisasi[9],
                    };
                  break;
                  case 4:
                    data_ = {
                      rekening_id: element.rekening_id,
                      nama_rekening: element.nama_rekening,
                      data_10: "target: " + element.target[10] + ", realisasi: " + element.realisasi[10],
                      data_11: "target: " + element.target[11] + ", realisasi: " + element.realisasi[11],
                      data_12: "target: " + element.target[12] + ", realisasi: " + element.realisasi[12],
                    };
                  break;
                }
                this.datatable.push(data_);
              });
            }
            this.datatableLoading = false;
          });
      },
      async loadstatistik7() {
        this.datatable = [];
        this.btnLoading = true;
        await this.$ajax
          .post(
            "/renjamurni/statistik/reloadcapaianrek",
            {
              ta: this.tahun_anggaran,
              tw: this.tw,
              mode: this.mode,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            if(data.status == 1) {
              let data_rekening = data.result;
              data_rekening.forEach(element => {
                var data_;
                switch(this.tw) {
                  case 1:
                    data_ = {
                      rekening_id: element.rekening_id,
                      nama_rekening: element.nama_rekening,
                      data_1: "target: " + element.target[1] + ", realisasi: " + element.realisasi[1],
                      data_2: "target: " + element.target[2] + ", realisasi: " + element.realisasi[2],
                      data_3: "target: " + element.target[3] + ", realisasi: " + element.realisasi[3],
                    };
                  break;
                  case 2:
                    data_ = {
                      rekening_id: element.rekening_id,
                      nama_rekening: element.nama_rekening,
                      data_4: "target: " + element.target[4] + ", realisasi: " + element.realisasi[4],
                      data_5: "target: " + element.target[5] + ", realisasi: " + element.realisasi[5],
                      data_6: "target: " + element.target[6] + ", realisasi: " + element.realisasi[6],
                    };
                  break;
                  case 3:
                    data_ = {
                      rekening_id: element.rekening_id,
                      nama_rekening: element.nama_rekening,
                      data_7: "target: " + element.target[7] + ", realisasi: " + element.realisasi[7],
                      data_8: "target: " + element.target[8] + ", realisasi: " + element.realisasi[8],
                      data_9: "target: " + element.target[9] + ", realisasi: " + element.realisasi[9],
                    };
                  break;
                  case 4:
                    data_ = {
                      rekening_id: element.rekening_id,
                      nama_rekening: element.nama_rekening,
                      data_10: "target: " + element.target[10] + ", realisasi: " + element.realisasi[10],
                      data_11: "target: " + element.target[11] + ", realisasi: " + element.realisasi[11],
                      data_12: "target: " + element.target[12] + ", realisasi: " + element.realisasi[12],
                    };
                  break;
                }
                this.datatable.push(data_);
              });
            }
            this.btnLoading = false;
          });
      },
    },
  };
</script>
