<template>
  <v-main-layout :token="userStore.AccessToken" :temporaryleftsidebar="true">
    <v-page-header>
      <template v-slot:icon>
        mdi-ruler-square-compass
      </template>
      <template v-slot:name>
        INDIKATOR TUJUAN
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
          Halaman ini digunakan untuk mengelola relasi indikator tujuan RPJMD.
        </v-alert>
      </template>      
    </v-page-header>
    <v-container fluid>
      <v-data-table-server
        density="compact"
        v-model:items-per-page="itemsPerPage"    
        :headers="fetchHeader"
        :items="datatable"
        :items-length="totalRecords"
        :loading="datatableLoading"
        :search="searchTrigger"
        item-value="RpjmdTujuanID"
        @update:options="initialize"
        items-per-page-text="Jumlah record per halaman"
        disable-sort
      >
        <template v-slot:loading>
          <v-skeleton-loader :type="'table-row@' + itemsPerPage"></v-skeleton-loader>
        </template>
        <template v-slot:top>
          <v-toolbar flat>
            <v-dialog
              v-model="dialogfrm"
              max-width="600px"
              persistent
            >
              <v-form ref="frmdata" v-model="form_valid">
                <v-card>
                  <v-card-title>
                    <v-icon icon="mdi-pencil"></v-icon> &nbsp;
                    <span class="headline">{{ formTitle }}</span>
                  </v-card-title>
                  <v-card-text>
                    <v-row tag="dl" class="text-body-2" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        ID TUJUAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ datatujuan.RpjmdTujuanID }}
                      </v-col>
                    </v-row>
                    <v-row tag="dl" class="text-body-2" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        KODE TUJUAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ datatujuan.kode_tujuan }}
                      </v-col>
                    </v-row>                    
                    <v-row tag="dl" class="text-body-2 mb-3" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        NAMA TUJUAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ datatujuan.Nm_RpjmdTujuan }}
                      </v-col>
                    </v-row>
                    <v-row class="mb-3" no-gutters>
                      <v-col cols="auto" md="12" lg="12">
                        <v-alert title="Perhatian" type="info">
                          Nilai N.A (Not Available) diganti dengan -99999.
                        </v-alert>
                      </v-col>
                    </v-row>
                    <hr class="mb-3">
                    <v-autocomplete
                      v-model="formdata_IndikatorKinerja"  
                      label="INDIKATOR"
                      density="compact"
                      variant="outlined"
                      prepend-inner-icon="mdi-graph"
                      hint="Pilih indikator tujuan"
                      :items="daftarindikator"
                      item-value="IndikatorKinerjaID"
                      item-title="NamaIndikator"
                      @update:modelValue="indikatorselected"
                      :disabled="editedIndex > -1"
                      clearable
                      return-object
                    />
                    <v-row tag="dl" class="text-body-2 mb-3" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        SATUAN
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ formdata_Satuan }}
                      </v-col>
                    </v-row>
                    <v-row tag="dl" class="text-body-2 mb-3" no-gutters>
                      <v-col cols="auto" md="3" lg="3" tag="dt" class="font-weight-bold">
                        OPERASI
                      </v-col>
                      <v-col cols="auto" md="9" lg="9" tag="dt">
                        {{ formdata_Operasi }}
                      </v-col>
                    </v-row>
                    <hr class="mb-3">
                    <v-row no-gutters>
                      <v-col cols="auto" md="9" lg="9">
                        <v-number-input
                          v-model="formdata_data_1"
                          :precision="2"
                          density="compact"
                          :label="'KONDISI AWAL ' + labeltahun[0]"
                          variant="outlined"
                          prepend-inner-icon="mdi-graph"
                          :disabled="disabledtarget || data_1_na"
                          :rules="rule_kondisi_awal"
                          class="mr-4"
                        />                    
                      </v-col>
                      <v-col cols="auto" md="3" lg="3" class="text-center">
                        <v-switch
                          v-model="data_1_na"
                          color="primary"
                          label="N.A"
                          hide-details
                          :disabled="disabledtarget"
                        />
                      </v-col>                      
                    </v-row>
                    <template v-if="formdata_Operasi == 'RANGE'">
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_2"  
                            :precision="2"
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_3"  
                            :precision="2"
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>    
                      </v-row>
                      <hr class="mb-3">
                      <p class="mb-3">Target Tahun {{ labeltahun[2] }}:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_4"  
                            :precision="2"
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_5"  
                            :precision="2"
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>                      
                      </v-row>
                      <p class="mb-3">Target Tahun {{ labeltahun[3] }}:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_6"  
                            :precision="2"
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_7"  
                            :precision="2"
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>
                      </v-row>
                      <p class="mb-3">Target Tahun {{ labeltahun[4] }}:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_8"  
                            :precision="2"
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_9"  
                            :precision="2"
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>
                      </v-row>
                      <p class="mb-3">Target Tahun {{ labeltahun[5] }}:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_10"  
                            :precision="2"
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_11"  
                            :precision="2"
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>
                      </v-row>
                      <p class="mb-3">Target Tahun {{ labeltahun[6] }}:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_12"  
                            :precision="2"
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_13"  
                            :precision="2"
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>
                      </v-row>
                      <p class="mb-3">Akhir RPJMD:</p>
                      <v-row no-gutters>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_14"  
                            :precision="2"
                            density="compact"
                            label="AWAL RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :rules="rule_range_awal"
                          />    
                        </v-col>
                        <v-col cols="auto" md="6" lg="6">
                          <v-number-input
                            v-model="formdata_data_15"  
                            :precision="2"
                            density="compact"
                            label="AKHIR RANGE"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            class="mr-1"
                            :disabled="disabledtarget"
                            :rules="rule_range_akhir"
                          />
                        </v-col>
                      </v-row>
                    </template>
                    <template v-else>
                      <v-row no-gutters>                      
                        <v-col cols="auto" md="9" lg="9">
                          <v-number-input
                            v-model="formdata_data_2"  
                            :precision="2"
                            density="compact"
                            :label="'KONDISI AWAL ' + labeltahun[1]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget || data_2_na"
                            :rules="rule_kondisi_awal"
                            class="mr-4"
                          />
                        </v-col>
                        <v-col cols="auto" md="3" lg="3" class="text-center">
                          <v-switch
                            v-model="data_2_na"
                            color="primary"
                            label="N.A"
                            hide-details
                            :disabled="disabledtarget"
                          />
                        </v-col>
                      </v-row>
                      <v-row no-gutters>                      
                        <v-col cols="auto" md="9" lg="9">
                          <v-number-input
                            v-model="formdata_data_3"  
                            :precision="2"
                            density="compact"
                            :label="'TARGET TAHUN ' + labeltahun[2]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget || data_3_na"
                            :rules="rule_target"
                            class="mr-4"
                          />    
                        </v-col>
                        <v-col cols="auto" md="3" lg="3" class="text-center">
                          <v-switch
                            v-model="data_3_na"
                            color="primary"
                            label="N.A"
                            hide-details
                            :disabled="disabledtarget"
                          />
                        </v-col>
                      </v-row>
                      <v-row no-gutters>
                        <v-col cols="auto" md="9" lg="9">
                          <v-number-input
                            v-model="formdata_data_4"  
                            :precision="2"
                            density="compact"
                            :label="'TARGET TAHUN ' + labeltahun[3]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget || data_4_na"
                            :rules="rule_target"
                            class="mr-4"
                          />
                        </v-col> 
                        <v-col cols="auto" md="3" lg="3" class="text-center">
                          <v-switch
                            v-model="data_4_na"
                            color="primary"
                            label="N.A"
                            hide-details
                            :disabled="disabledtarget"
                          />
                        </v-col>
                      </v-row>
                      <v-row no-gutters>
                        <v-col cols="auto" md="9" lg="9">
                          <v-number-input
                            v-model="formdata_data_5"  
                            :precision="2"
                            density="compact"
                            :label="'TARGET TAHUN ' + labeltahun[4]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget || data_5_na"
                            :rules="rule_target"
                            class="mr-4"
                          />
                        </v-col>
                        <v-col cols="auto" md="3" lg="3" class="text-center">
                          <v-switch
                            v-model="data_5_na"
                            color="primary"
                            label="N.A"
                            hide-details
                            :disabled="disabledtarget"
                          />
                        </v-col>
                      </v-row>
                      <v-row no-gutters>
                        <v-col cols="auto" md="9" lg="9">                      
                          <v-number-input
                            v-model="formdata_data_6"  
                            :precision="2"
                            density="compact"
                            :label="'TARGET TAHUN ' + labeltahun[5]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget || data_6_na"
                            :rules="rule_target"
                            class="mr-4"
                          />              
                        </v-col>
                        <v-col cols="auto" md="3" lg="3" class="text-center">
                          <v-switch
                            v-model="data_6_na"
                            color="primary"
                            label="N.A"
                            hide-details
                            :disabled="disabledtarget"
                          />
                        </v-col>
                      </v-row>
                      <v-row no-gutters>
                        <v-col cols="auto" md="9" lg="9">                       
                          <v-number-input
                            v-model="formdata_data_7"  
                            :precision="2"
                            density="compact"
                            :label="'TARGET TAHUN ' + labeltahun[6]"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget || data_7_na"
                            :rules="rule_target"
                            class="mr-4"
                          />     
                        </v-col>
                        <v-col cols="auto" md="3" lg="3" class="text-center">
                          <v-switch
                            v-model="data_7_na"
                            color="primary"
                            label="N.A"
                            hide-details
                            :disabled="disabledtarget"
                          />
                        </v-col>
                      </v-row>
                      <v-row no-gutters>
                        <v-col cols="auto" md="9" lg="9">                      
                          <v-number-input
                            v-model="formdata_data_8"  
                            :precision="2"
                            density="compact"
                            label="AKHIR RPJMD"
                            variant="outlined"
                            prepend-inner-icon="mdi-graph"
                            :disabled="disabledtarget || data_8_na"
                            :rules="rule_target"
                            class="mr-4"
                          />
                        </v-col>
                        <v-col cols="auto" md="3" lg="3" class="text-center">
                          <v-switch
                            v-model="data_8_na"
                            color="primary"
                            label="N.A"
                            hide-details
                            :disabled="disabledtarget"
                          />
                        </v-col>
                      </v-row>     
                    </template>
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                      color="orange-darken-1"
                      @click.stop="closedialogfrm"
                      prepend-icon="mdi-close"
                      rounded="sm"
                    >
                      TUTUP
                    </v-btn>
                    <v-btn
                      color="primary"
                      @click.stop="save"
                      rounded="sm"
                      prepend-icon="mdi-content-save"
                      :disabled="!form_valid || btnLoading"
                    >
                      SIMPAN
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-form>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item="{ index, item }">
          <tr class="bg-grey-lighten-5">
            <td>{{ (indexOffset + index) + 1 }}</td>
            <td colspan="10">[{{ item.kode_tujuan }}] {{ item.Nm_RpjmdTujuan }}</td>
            <td>
              <v-btn
                class="mr-2"
                v-tooltip:bottom="'Tambah Indikator'"                
                color="primary"
                variant="outlined"
                prepend-icon="mdi-plus"
                density="compact"
                @click.stop="addItem(item)"                
              >
                Tambah
              </v-btn>
            </td>
          </tr>
          <template v-if="item.indikator.length > 0">            
            <tr v-for="(indikator, i) in item.indikator" :key="indikator.RpjmdRelasiIndikatorID" class="bg-green-lighten-5">
              <td>
                <v-icon icon="mdi-arrow-right" />
              </td>
              <td>{{ indikator.NamaIndikator }}</td>
              <td>{{ indikator.Satuan }}</td>
              <td>{{ indikator.data_1 == '-99999' ? 'n.a' :  indikator.data_1 }}</td>
              <template v-if="indikator.Operasi == 'RANGE'">
                <td>
                  {{ indikator.data_2 == '-99999' ? 'n.a' :  indikator.data_2 }} s.d 
                  {{ indikator.data_3 == '-99999' ? 'n.a' :  indikator.data_3 }}
                </td>                
                <td>
                  {{ indikator.data_4 == '-99999' ? 'n.a' :  indikator.data_4 }} s.d 
                  {{ indikator.data_5 == '-99999' ? 'n.a' :  indikator.data_5 }}
                </td>                
                <td>
                  {{ indikator.data_6 == '-99999' ? 'n.a' :  indikator.data_6 }} s.d 
                  {{ indikator.data_7 == '-99999' ? 'n.a' :  indikator.data_7 }}
                </td>                
                <td>
                  {{ indikator.data_8 == '-99999' ? 'n.a' :  indikator.data_8 }} s.d 
                  {{ indikator.data_9 == '-99999' ? 'n.a' :  indikator.data_9 }}
                </td>                
                <td>
                  {{ indikator.data_10 == '-99999' ? 'n.a' :  indikator.data_10 }} s.d 
                  {{ indikator.data_11 == '-99999' ? 'n.a' :  indikator.data_11 }}
                </td>                
                <td>
                  {{ indikator.data_12 == '-99999' ? 'n.a' :  indikator.data_12 }} s.d 
                  {{ indikator.data_13 == '-99999' ? 'n.a' :  indikator.data_13 }}
                </td>                
                <td>
                  {{ indikator.data_14 == '-99999' ? 'n.a' :  indikator.data_14 }} s.d 
                  {{ indikator.data_15 == '-99999' ? 'n.a' :  indikator.data_15 }}
                </td>                                
              </template>
              <template v-else>
                <td>{{ indikator.data_2 == '-99999' ? 'n.a' :  indikator.data_2 }}</td>
                <td>{{ indikator.data_3 == '-99999' ? 'n.a' :  indikator.data_3 }}</td>
                <td>{{ indikator.data_4 == '-99999' ? 'n.a' :  indikator.data_4 }}</td>
                <td>{{ indikator.data_5 == '-99999' ? 'n.a' :  indikator.data_5 }}</td>
                <td>{{ indikator.data_6 == '-99999' ? 'n.a' :  indikator.data_6 }}</td>
                <td>{{ indikator.data_7 == '-99999' ? 'n.a' :  indikator.data_7 }}</td>
                <td>{{ indikator.data_8 == '-99999' ? 'n.a' :  indikator.data_8 }}</td>
              </template>
              <td>
                <v-icon
                  class="mr-2"
                  v-tooltip:bottom="'Ubah Indikator'"
                  @click.stop="editItem(item, indikator)"
                  size="small"
                  color="primary"
                >
                  mdi-pencil
                </v-icon>
                <v-icon
                  v-tooltip:bottom="'Hapus Indikator'"
                  @click.stop="deleteItem(indikator)"
                  size="small"
                  color="error"
                >
                  mdi-delete
                </v-icon>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr class="bg-green-lighten-5">
              <td colspan="12" class="text-center">Belum ada indikator. Silahkan tambah</td>
            </tr>
          </template>
        </template>
      </v-data-table-server>
    </v-container>
  </v-main-layout>  
</template>
<script>
  import mainLayout from '@/layouts/MainLayout.vue'
  import pageHeader from '@/layouts/PageHeader.vue'  
  import { usesUserStore } from '@/stores/UsersStore'
  export default {
    name: 'RelationTujuanIndikator',
    created() {
      this.userStore = usesUserStore()
      this.breadcrumbs = [
        {
          title: 'HOME',
          href: '/admin/' + this.userStore.AccessToken,
        },
        {
          title: 'RELASI',
          disabled: false,
          href: '#',
        },
        {
          title: 'INDIKATOR - TUJUAN',
          disabled: true,
          href: '#',
        },
      ]
    },
    data: () => ({
      btnLoading: false,
      datatableLoading: false,
      //data table
      datatable: [],
      itemsPerPage: 10,
      totalRecords: 0,
      indexOffset: 0,
      search: '',
      //dialog
      datatujuan: {},
      dialogfrm: false,
      //form data      
      form_valid: true,      
      daftarindikator: [],
      disabledtarget: true,
      data_1_na: null,
      data_2_na: null,
      data_3_na: null,
      data_4_na: null,
      data_5_na: null,
      data_6_na: null,
      data_7_na: null,
      data_8_na: null,
      formdata_RpjmdRelasiIndikatorID: null,
      formdata_IndikatorKinerja: null,
      formdata_Satuan: '-',
      formdata_Operasi: '-',
      formdata_data_1: null,
      formdata_data_2: null,
      formdata_data_3: null,
      formdata_data_4: null,
      formdata_data_5: null,
      formdata_data_6: null,
      formdata_data_7: null,
      formdata_data_8: null,
      formdata_data_9: null,
      formdata_data_10: null,
      formdata_data_11: null,
      formdata_data_12: null,
      formdata_data_13: null,
      formdata_data_14: null,
      formdata_data_15: null,
      formdata_old_data_1: null,
      formdata_old_data_2: null,
      formdata_old_data_3: null,
      formdata_old_data_4: null,
      formdata_old_data_5: null,
      formdata_old_data_6: null,
      formdata_old_data_7: null,
      formdata_old_data_8: null,
      formdata_old_data_9: null,
      formdata_old_data_10: null,
      formdata_old_data_11: null,
      formdata_old_data_12: null,
      formdata_old_data_13: null,
      formdata_old_data_14: null,
      formdata_old_data_15: null,
      labeltahun: [],
      editedIndex: -1,
      //form rules range
      rule_kondisi_awal: [
        value => value === 0 || value !== null || 'Mohon untuk diisi nilai kondisi awal !!!',
      ],
      rule_range_awal: [
        value => value === 0 || value !== null || 'Mohon untuk diisi nilai awal target !!!',
      ],
      rule_range_akhir: [
        value => value === 0 || value !== null || 'Mohon untuk diisi nilai akhir target !!!',
      ],
      rule_target: [
        value => value === 0 || value !== null || 'Mohon untuk diisi nilai target !!!',
      ],
      //pinia
      userStore: null,
    }),
    methods: {
      async initialize({ page, itemsPerPage }) {
        this.datatableLoading = true
        
        var request_param = {
          PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
        }

        if(itemsPerPage > 0) {
          const offset = (page - 1) * itemsPerPage
          this.indexOffset = offset

          request_param.offset = offset
          request_param.limit = itemsPerPage
        }
        
        await this.$ajax
          .post('/rpjmd/tujuan/indikatortujuan', 
            request_param,
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            let payload = data.payload
            this.datatable = payload.data
            this.totalRecords = payload.totalRecords
            this.datatableLoading = false    
          })
      },
      async setLabelTahun() {
        let periode = this.userStore.PeriodeRPJMD;
        let TA_AWAL = periode.TA_AWAL
        let TA_AKHIR = periode.TA_AKHIR

        this.labeltahun.push(TA_AWAL - 1)
        this.labeltahun.push(TA_AWAL)
        
        for(var tahun = parseInt(TA_AWAL) + 1; tahun <= TA_AKHIR; tahun++) {
          this.labeltahun.push(tahun);   
        }
      },
      async addItem(item) {
        this.btnLoading = true
        this.dialogfrm = true        
        this.datatujuan = item

        this.setLabelTahun()
        
        await this.$ajax
          .post('/rpjmd/indikatorkinerja/tujuan', 
            {
              PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
              Listed: 1,
            },
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            let payload = data.payload
            this.daftarindikator = payload
            this.btnLoading = false
          })
      },
      async editItem(datatujuan, item) {
        this.editedIndex = this.datatable.indexOf(datatujuan)
        this.setLabelTahun()
        this.datatujuan = datatujuan

        this.btnLoading = true

        this.formdata_RpjmdRelasiIndikatorID = item.RpjmdRelasiIndikatorID
        
        await this.$ajax
          .post('/rpjmd/indikatorkinerja/tujuan', 
            {
              PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
              Listed: 0,
            },
            {
              headers: {
                Authorization: this.userStore.Token,
              },
            }
          )
          .then(({ data }) => {
            let payload = data.payload
            this.daftarindikator = payload

            this.formdata_IndikatorKinerja = {
              IndikatorKinerjaID: item.IndikatorKinerjaID,
              NamaIndikator: item.NamaIndikator,
              Satuan: item.Satuan,
              Operasi: item.Operasi,
            }
            this.formdata_Satuan = item.Satuan
            this.formdata_Operasi = item.Operasi

            this.formdata_data_1 = parseFloat(item.data_1)
            this.formdata_data_2 = parseFloat(item.data_2)
            this.formdata_data_3 = parseFloat(item.data_3)
            this.formdata_data_4 = parseFloat(item.data_4)
            this.formdata_data_5 = parseFloat(item.data_5)
            this.formdata_data_6 = parseFloat(item.data_6)
            this.formdata_data_7 = parseFloat(item.data_7)
            this.formdata_data_8 = parseFloat(item.data_8)
            this.formdata_data_9 = parseFloat(item.data_9)
            this.formdata_data_10 = parseFloat(item.data_10)
            this.formdata_data_11 = parseFloat(item.data_11)
            this.formdata_data_12 = parseFloat(item.data_12)
            this.formdata_data_13 = parseFloat(item.data_13)
            this.formdata_data_14 = parseFloat(item.data_14)
            this.formdata_data_15 = parseFloat(item.data_15)

            this.formdata_old_data_1 = this.formdata_data_1
            this.formdata_old_data_2 = this.formdata_data_2
            this.formdata_old_data_3 = this.formdata_data_3
            this.formdata_old_data_4 = this.formdata_data_4
            this.formdata_old_data_5 = this.formdata_data_5
            this.formdata_old_data_6 = this.formdata_data_6
            this.formdata_old_data_7 = this.formdata_data_7
            this.formdata_old_data_8 = this.formdata_data_8
            this.formdata_old_data_9 = this.formdata_data_9
            this.formdata_old_data_10 = this.formdata_data_10
            this.formdata_old_data_11 = this.formdata_data_11
            this.formdata_old_data_12 = this.formdata_data_12
            this.formdata_old_data_13 = this.formdata_data_13
            this.formdata_old_data_14 = this.formdata_data_14
            this.formdata_old_data_15 = this.formdata_data_15
            
            this.data_1_na = this.formdata_data_1 == '-99999'
            this.data_2_na = this.formdata_data_2 == '-99999'
            this.data_3_na = this.formdata_data_3 == '-99999'
            this.data_4_na = this.formdata_data_4 == '-99999'
            this.data_5_na = this.formdata_data_5 == '-99999'
            this.data_6_na = this.formdata_data_6 == '-99999'
            this.data_7_na = this.formdata_data_7 == '-99999'
            this.data_8_na = this.formdata_data_8 == '-99999'

            this.dialogfrm = true
            this.disabledtarget = false
            this.btnLoading = false            
          })
      },
      indikatorselected() {
        if(this.formdata_IndikatorKinerja == null || typeof this.formdata_IndikatorKinerja === 'undefined') {
          this.formdata_Satuan = '-'
          this.formdata_Operasi = '-'
          this.disabledtarget = true
        } else {
          this.formdata_Satuan = this.formdata_IndikatorKinerja.Satuan
          this.formdata_Operasi = this.formdata_IndikatorKinerja.Operasi
          this.disabledtarget = false
        }
      },
      async save() {
        const { valid } = await this.$refs.frmdata.validate()

        if(valid) {
          this.btnLoading = true
          
          if (this.editedIndex > -1) {
            this.$ajax
              .post(
                '/rpjmd/relations/indikatortujuan/' + this.formdata_RpjmdRelasiIndikatorID,
                {
                  _method: 'PUT',
                  Operasi: this.formdata_Operasi,
                  data_1: this.data_1_na ? -99999 : this.formdata_data_1,
                  data_2: this.data_2_na ? -99999 : this.formdata_data_2,
                  data_3: this.data_3_na ? -99999 : this.formdata_data_3,
                  data_4: this.data_4_na ? -99999 : this.formdata_data_4,
                  data_5: this.data_5_na ? -99999 : this.formdata_data_5,
                  data_6: this.data_6_na ? -99999 : this.formdata_data_6,
                  data_7: this.data_7_na ? -99999 : this.formdata_data_7,
                  data_8: this.data_8_na ? -99999 : this.formdata_data_8,
                  data_9: this.data_9_na ? -99999 : this.formdata_data_9,
                  data_10: this.data_10_na ? -99999 : this.formdata_data_10,
                  data_11: this.data_11_na ? -99999 : this.formdata_data_11,
                  data_12: this.data_12_na ? -99999 : this.formdata_data_12,
                  data_13: this.data_13_na ? -99999 : this.formdata_data_13,
                  data_14: this.data_14_na ? -99999 : this.formdata_data_14,
                  data_15: this.data_15_na ? -99999 : this.formdata_data_15,
                },
                {
                  headers: {
                    Authorization: this.userStore.Token,
                  },
                }
              )
              .then(() => {
                this.$router.go()
              })
              .catch(() => {
                this.btnLoading = false
              })
          } else {    
            this.$ajax
              .post(
                '/rpjmd/relations/indikatortujuan/store',
                {
                  IndikatorKinerjaID: this.formdata_IndikatorKinerja.IndikatorKinerjaID,    
                  RpjmdCascadingID: this.datatujuan.RpjmdTujuanID,
                  PeriodeRPJMDID: this.userStore.PeriodeRPJMD.PeriodeRPJMDID,
                  Operasi: this.formdata_Operasi,
                  data_1: this.data_1_na ? -99999 : this.formdata_data_1,
                  data_2: this.data_2_na ? -99999 : this.formdata_data_2,
                  data_3: this.data_3_na ? -99999 : this.formdata_data_3,
                  data_4: this.data_4_na ? -99999 : this.formdata_data_4,
                  data_5: this.data_5_na ? -99999 : this.formdata_data_5,
                  data_6: this.data_6_na ? -99999 : this.formdata_data_6,
                  data_7: this.data_7_na ? -99999 : this.formdata_data_7,
                  data_8: this.data_8_na ? -99999 : this.formdata_data_8,
                  data_9: this.data_9_na ? -99999 : this.formdata_data_9,
                  data_10: this.data_10_na ? -99999 : this.formdata_data_10,
                  data_11: this.data_11_na ? -99999 : this.formdata_data_11,
                  data_12: this.data_12_na ? -99999 : this.formdata_data_12,
                  data_13: this.data_13_na ? -99999 : this.formdata_data_13,
                  data_14: this.data_14_na ? -99999 : this.formdata_data_14,
                  data_15: this.data_15_na ? -99999 : this.formdata_data_15,
                },
                {
                  headers: {
                    Authorization: this.userStore.Token,
                  },
                }
              )
              .then(() => {
                this.$router.go()
              })
              .catch(() => {
                this.btnLoading = false
              })
          }
        }
      },  
      deleteItem(item) {
        this.$root.$confirm
          .open(
            'Delete',
            'Apakah Anda ingin menghapus data dengan ID ' + item.RpjmdRelasiIndikatorID + ' ?',
            {
              color: 'red',
              width: '400px',
            }
          )
          .then(confirm => {
            if (confirm) {
              this.btnLoading = true
              this.$ajax
                .post(
                  '/rpjmd/relations/indikatortujuan/' + item.RpjmdRelasiIndikatorID,
                  {
                    _method: 'DELETE',
                  },
                  {
                    headers: {
                      Authorization: this.userStore.Token,
                    },
                  }
                )
                .then(() => {
                  this.$router.go()
                })
                .catch(() => {
                  this.btnLoading = false
                })
            }
          })
      },
      closedialogfrm() {
        this.btnLoading = false
        setTimeout(() => {          
          this.editedIndex = -1        
          this.$refs.frmdata.reset()
          this.dialogfrm = false
        }, 300)
      },
    },
    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'TAMBAH INDIKATOR TUJUAN' : 'UBAH INDIKATOR TUJUAN'
      },
      searchTrigger () {
        if (this.search.length >= 3) {
          return this.search
        }
      },
      fetchHeader() {
        let periode = this.userStore.PeriodeRPJMD;
        let TA_AWAL = periode.TA_AWAL
        let TA_AKHIR = periode.TA_AKHIR

        var children_kondisi_awal = [
          {
            title: TA_AWAL - 1,
            value: 'data_1',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: TA_AWAL,
            value: 'data_2',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
        ]
        var children_target_tahun = []
        var i = 3        
        for(var tahun = parseInt(TA_AWAL) + 1; tahun <= TA_AKHIR; tahun++) {
          children_target_tahun.push({
            title: tahun,
            value: 'data_' + i,
            headerProps: {
              class: 'font-weight-bold',
            },
          });   
        }
        
        var headers = [
          {
            title: 'NO',
            align: 'start',
            sortable: false,
            key: 'no',
            width: 50,
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'NAMA TUJUAN / INDIKATOR',
            key: 'Nm_RpjmdTujuan',
            align: 'start',
            headerProps: {
              class: 'font-weight-bold',
            },
          },    
          {
            title: 'SATUAN',
            key: 'Nm_RpjmdTujuan',
            align: 'start',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'KONDISI AWAL',
            align: 'center',
            children: children_kondisi_awal,
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'TARGET TAHUN',
            align: 'center',
            children: children_target_tahun,
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: 'AKHIR RPJMD',
            key: 'Nm_RpjmdTujuan',
            align: 'center',
            headerProps: {
              class: 'font-weight-bold',
            },
          },
          {
            title: "AKSI",
            key: "actions",
            sortable: false,
            width: 110,
            headerProps: {
              class: 'font-weight-bold',
            },
          },
        ]
        return headers
      },
    },
    watch: {
      data_1_na(val, oldVal) {   
        if(oldVal == null) {
          return
        }

        if(val) {
          this.formdata_data_1 = -99999
        } else if (this.editedIndex > -1) {
          this.formdata_data_1 = this.formdata_old_data_1
        } else {
          this.formdata_data_1 = null
        }
      },
      data_2_na(val, oldVal) {
        if(oldVal == null) {
          return
        }

        if(val) {
          this.formdata_data_2 = -99999
        } else if (this.editedIndex > -1) {
          this.formdata_data_2 = this.formdata_old_data_2
        } else {
          this.formdata_data_2 = null
        }
      },
      data_3_na(val, oldVal) {
        if(oldVal == null) {
          return
        }

        if(val) {
          this.formdata_data_3 = -99999
        } else if (this.editedIndex > -1) {
          this.formdata_data_3 = this.formdata_old_data_3
        } else {
          this.formdata_data_3 = null
        }
      },
      data_4_na(val, oldVal) {  
        if(oldVal == null) {
          return
        }

        if(val) {
          this.formdata_data_4 = -99999
        } else if (this.editedIndex > -1) {
          this.formdata_data_4 = this.formdata_old_data_4
        } else {
          this.formdata_data_4 = null
        }
      },
      data_5_na(val, oldVal) { 
        if(oldVal == null) {
          return
        }

        if(val) {
          this.formdata_data_5 = -99999
        } else if (this.editedIndex > -1) {
          this.formdata_data_5 = this.formdata_old_data_5
        } else {
          this.formdata_data_5 = null
        }
      },
      data_6_na(val, oldVal) {        
        if(val) {
          this.formdata_data_6 = -99999
        } else if (this.editedIndex > -1) {
          this.formdata_data_6 = this.formdata_old_data_6
        } else {
          this.formdata_data_6 = null
        }        
      },
      data_7_na(val, oldVal) {
        if(oldVal == null) {
          return
        }
        if(val) {
          this.formdata_data_7 = -99999
        } else if (this.editedIndex > -1) {
          this.formdata_data_7 = this.formdata_old_data_7
        } else {
          this.formdata_data_7 = null
        }        
      },
      data_8_na(val, oldVal) {
        if(oldVal == null) {
          return
        }        
        if(val) {
          this.formdata_data_8 = -99999
        } else if (this.editedIndex > -1) {
          this.formdata_data_8 = this.formdata_old_data_8
        } else {
          this.formdata_data_8 = null
        }
      },
    },
    components: {
      'v-main-layout': mainLayout,
      'v-page-header': pageHeader,
    },
  }
</script>