<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;

use App\Models\DMaster\OrganisasiModel;
use App\Models\DMaster\SubOrganisasiModel;
use App\Models\DMaster\RekeningJenisModel;
use App\Models\Statistik1Model;
use App\Models\Statistik2Model;
use App\Models\Statistik5Model;

class RenjaMurniController extends Controller 
{
  public function index(Request $request)
  {
    $this->validate($request, [            
      'ta' => 'required',
      'bulan_realisasi' => 'required'            
    ]);
    
    $tahun = $request->input('ta');
    $bulan_realisasi = $request->input('bulan_realisasi');
    
    $statistik1=[
      'PaguDana1' => 0,             
      'JumlahProgram1' => 0,             
      'JumlahKegiatan1' => 0,             
      'JumlahSubKegiatan1' => 0,             
      'RealisasiKeuangan1' => 0,             
      'RealisasiFisik1' => 0, 
    ];
    
    $chart_keuangan=[
      [
        0,0,0,0,0,0,0,0,0,0,0,0
      ],
      [
        0,0,0,0,0,0,0,0,0,0,0,0
      ]
    ];
    $chart_fisik=[
      [
        0,0,0,0,0,0,0,0,0,0,0,0
      ],
      [
        0,0,0,0,0,0,0,0,0,0,0,0
      ]
    ];

    $daftar_opd = [];
    $daftar_sub_opd = [];
    
    $daftar_kelompok_rekening = [
      [
        'id' => '1',
        'kode_rekening' => ['5.1.01'],
        'nama_rekening' => 'Belanja Pegawai',
        'data' => [],
      ],
      [
        'id' => '2',
        'kode_rekening' => ['5.1.02'],
        'nama_rekening' => 'Belanja Barang dan Jasa',
        'data' => [],
      ],
      [
        'id' => '3',
        'kode_rekening' => ['5.2.01', '5.2.02', '5.2.03', '5.2.04', '5.2.05'],
        'nama_rekening' => 'Belanja Modal',
        'data' => [],
      ],
    ];
    
    if ($this->hasRole(['superadmin', 'bapelitbang']))
    {
      $daftar_opd = [];
      $statistik1 = Statistik1Model::select(\DB::raw('
        `PaguDana1`,
        `JumlahProgram1`,
        `JumlahKegiatan1`,
        `JumlahSubKegiatan1`,
        `RealisasiKeuangan1`,
        `RealisasiFisik1`,
        0 AS `PersenRealisasiKeuangan1`
      '))
      ->find($tahun);			
      
      if (is_null($statistik1))
      {
        $statistik1 = [
          'PaguDana1' => 0,             
          'JumlahProgram1' => 0,             
          'JumlahKegiatan1' => 0,             
          'JumlahSubKegiatan1' => 0,             
          'RealisasiKeuangan1' => 0,             
          'RealisasiFisik1' => 0, 
          'PersenRealisasiKeuangan1' => 0, 	
        ];       
      }
      else
      {
        $statistik1->PersenRealisasiKeuangan1=Helper::formatPersen($statistik1->RealisasiKeuangan1,$statistik1->PaguDana1);
        $statistik1=[
          'PaguDana1' => $statistik1->PaguDana1,             
          'JumlahProgram1' => $statistik1->JumlahProgram1,             
          'JumlahKegiatan1' => $statistik1->JumlahKegiatan1,             
          'JumlahSubKegiatan1' => $statistik1->JumlahSubKegiatan1,             
          'RealisasiKeuangan1' => $statistik1->RealisasiKeuangan1,             
          'RealisasiFisik1' => $statistik1->RealisasiFisik1, 
          'PersenRealisasiKeuangan1' => $statistik1->PersenRealisasiKeuangan1, 
        ];       
      }
      $statistik2=Statistik2Model::select(\DB::raw('
        `Bulan`,
        SUM(`PersenTargetKeuangan1`) AS `PersenTargetKeuangan1`,
        SUM(`PersenRealisasiKeuangan1`) AS `PersenRealisasiKeuangan1`,                                                
        SUM(`TargetFisik1`) AS `TargetFisik1`,
        SUM(`RealisasiFisik1`) AS `RealisasiFisik1`                                                
      '))
      ->where('TA', $tahun)                                                                                       
      ->where('EntryLvl', 1)        
      ->groupBy('Bulan')                                
      ->get();

      $jumlah_opd = OrganisasiModel::select('OrgID')
      ->where('TA', $tahun)
      ->count();
                    
      foreach($statistik2 as $v)
      {
        switch($v->Bulan)
        {
          case 1 :
            $chart_keuangan[0][0]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][0]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

            $chart_fisik[0][0]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][0]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 2 :
            $chart_keuangan[0][1]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][1]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

            $chart_fisik[0][1]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][1]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 3 :
            $chart_keuangan[0][2]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][2]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

            $chart_fisik[0][2]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][2]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 4 :
            $chart_keuangan[0][3]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][3]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

            $chart_fisik[0][3]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][3]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 5 :
            $chart_keuangan[0][4]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][4]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

            $chart_fisik[0][4]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][4]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 6 :
            $chart_keuangan[0][5]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][5]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

            $chart_fisik[0][5]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][5]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 7 :
            $chart_keuangan[0][6]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][6]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
            
            $chart_fisik[0][6]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][6]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 8 :
            $chart_keuangan[0][7]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][7]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
            
            $chart_fisik[0][7]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][7]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 9 :
            $chart_keuangan[0][8]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][8]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
            
            $chart_fisik[0][8]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][8]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 10 :
            $chart_keuangan[0][9]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][9]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
            
            $chart_fisik[0][9]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][9]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 11 :
            $chart_keuangan[0][10]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][10]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
            
            $chart_fisik[0][10]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][10]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
          case 12 :
            $chart_keuangan[0][11]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
            $chart_keuangan[1][11]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
            
            $chart_fisik[0][11]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
            $chart_fisik[1][11]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
          break;
        }
      }

      //kelompok rekening
      // $daftar_kelompok_rekening = RekeningJenisModel::select(\DB::raw('
      //   `tmJns`.`JnsID`,                                        
      //   `tmJns`.`KlpID`,                                        
      //   `tmJns`.`Kd_Rek_3`,
      //   `tmJns`.`JnsNm`,    
      //   CONCAT(`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`) AS `kode_jenis`,                                    
      //   CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'] \',`KlpNm`) AS `nama_rek2`,
      //   CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'] \',`JnsNm`) AS `nama_rek3`,
      //   `tmJns`.`Descr`,
      //   `tmJns`.`TA`
      // '))
      // ->join('tmKlp', 'tmJns.KlpID', 'tmKlp.KlpID')
      // ->join('tmAkun', 'tmAkun.AkunID', 'tmKlp.AkunID')
      // ->where('tmKlp.TA', $tahun)
      // ->where("Kd_Rek_1", 5)
      // ->whereIn("Kd_Rek_2", [1, 2])
      // ->whereIn("Kd_Rek_3", ['01', '02', '03', '04'])
      // ->orderBy('Kd_Rek_1', 'ASC')
      // ->orderBy('Kd_Rek_2', 'ASC')
      // ->orderBy('Kd_Rek_3', 'ASC')
      // ->get();
      

    }       
    else if ($this->hasRole('opd'))
    {
      $daftar_opd = $this->getUserOrgID($tahun);
      $jumlah_opd=count($daftar_opd);
      if ($jumlah_opd > 0)
      {
        $statistik1 = OrganisasiModel::where('TA', $tahun)
        ->select(\DB::raw('
          COALESCE(SUM(`PaguDana1`),0) AS `PaguDana1`, 
          COALESCE(SUM(`JumlahProgram1`),0) AS `JumlahProgram1`, 
          COALESCE(SUM(`JumlahKegiatan1`),0) AS `JumlahKegiatan1`,
          COALESCE(SUM(`JumlahSubKegiatan1`),0) AS `JumlahSubKegiatan1`,
          COALESCE(SUM(`RealisasiKeuangan1`),0) AS `RealisasiKeuangan1`,
          COALESCE(SUM(`RealisasiFisik1`),0) AS `RealisasiFisik1`,
          0 AS `PersenRealisasiKeuangan1`
        '))
        ->whereIn('OrgID', $daftar_opd)                                
        ->first();

        $statistik1->PersenRealisasiKeuangan1=Helper::formatPersen($statistik1->RealisasiKeuangan1,$statistik1->PaguDana1);                
        $statistik1=[
          'PaguDana1' => $statistik1->PaguDana1,             
          'JumlahProgram1' => $statistik1->JumlahProgram1,             
          'JumlahKegiatan1' => $statistik1->JumlahKegiatan1,             
          'JumlahSubKegiatan1' => $statistik1->JumlahSubKegiatan1,             
          'RealisasiKeuangan1' => $statistik1->RealisasiKeuangan1,             
          'RealisasiFisik1'=>Helper::formatPecahan($statistik1->RealisasiFisik1,$jumlah_opd), 
          'PersenRealisasiKeuangan1'=>Helper::formatPecahan($statistik1->PersenRealisasiKeuangan1,$jumlah_opd), 
        ];

        $statistik2=Statistik2Model::select(\DB::raw('
          `Bulan`,
          SUM(`PersenTargetKeuangan1`) AS `PersenTargetKeuangan1`,
          SUM(`PersenRealisasiKeuangan1`) AS `PersenRealisasiKeuangan1`,                                                
          SUM(`TargetFisik1`) AS `TargetFisik1`,
          SUM(`RealisasiFisik1`) AS `RealisasiFisik1`                                                
        '))
        ->where('TA', $tahun)
        ->whereIn('OrgID', $daftar_opd)                                             
        ->where('EntryLvl', 1)        
        ->groupBy('Bulan')                                
        ->get();
                      
        
        foreach($statistik2 as $v)
        {
          switch($v->Bulan)
          {
            case 1 :
              $chart_keuangan[0][0]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][0]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

              $chart_fisik[0][0]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][0]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 2 :
              $chart_keuangan[0][1]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][1]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

              $chart_fisik[0][1]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][1]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 3 :
              $chart_keuangan[0][2]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][2]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

              $chart_fisik[0][2]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][2]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 4 :
              $chart_keuangan[0][3]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][3]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

              $chart_fisik[0][3]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][3]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 5 :
              $chart_keuangan[0][4]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][4]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

              $chart_fisik[0][4]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][4]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 6 :
              $chart_keuangan[0][5]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][5]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);

              $chart_fisik[0][5]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][5]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 7 :
              $chart_keuangan[0][6]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][6]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
              
              $chart_fisik[0][6]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][6]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 8 :
              $chart_keuangan[0][7]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][7]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
              
              $chart_fisik[0][7]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][7]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 9 :
              $chart_keuangan[0][8]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][8]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
              
              $chart_fisik[0][8]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][8]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 10 :
              $chart_keuangan[0][9]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][9]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
              
              $chart_fisik[0][9]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][9]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 11 :
              $chart_keuangan[0][10]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][10]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
              
              $chart_fisik[0][10]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][10]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
            case 12 :
              $chart_keuangan[0][11]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_opd);
              $chart_keuangan[1][11]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_opd);
              
              $chart_fisik[0][11]=Helper::formatPecahan($v->TargetFisik1,$jumlah_opd);
              $chart_fisik[1][11]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_opd);
            break;
          }
        }
      }			
    }
    else if ($this->hasRole('unitkerja'))
    {
      $daftar_sub_opd = $this->getUserSOrgID($tahun);			
      $jumlah_sub_opd=count($daftar_sub_opd);
      if ($jumlah_sub_opd > 0)
      {
        $statistik1 = SubOrganisasiModel::where('TA', $tahun)
        ->select(\DB::raw('
          COALESCE(SUM(`PaguDana1`),0) AS `PaguDana1`, 
          COALESCE(SUM(`JumlahProgram1`),0) AS `JumlahProgram1`, 
          COALESCE(SUM(`JumlahKegiatan1`),0) AS `JumlahKegiatan1`,
          COALESCE(SUM(`JumlahSubKegiatan1`),0) AS `JumlahSubKegiatan1`,
          COALESCE(SUM(`RealisasiKeuangan1`),0) AS `RealisasiKeuangan1`,
          COALESCE(SUM(`RealisasiFisik1`),0) AS `RealisasiFisik1`,
          0 AS `PersenRealisasiKeuangan1`
        '))
        ->whereIn('SOrgID', $daftar_sub_opd)                                
        ->first();

        $statistik1->PersenRealisasiKeuangan1=Helper::formatPersen($statistik1->RealisasiKeuangan1, $statistik1->PaguDana1);                
        $statistik1=[
          'PaguDana1' => $statistik1->PaguDana1,             
          'JumlahProgram1' => $statistik1->JumlahProgram1,             
          'JumlahKegiatan1' => $statistik1->JumlahKegiatan1,             
          'JumlahSubKegiatan1' => $statistik1->JumlahSubKegiatan1,             
          'RealisasiKeuangan1' => $statistik1->RealisasiKeuangan1,             
          'RealisasiFisik1'=>Helper::formatPecahan($statistik1->RealisasiFisik1,$jumlah_sub_opd), 
          'PersenRealisasiKeuangan1'=>Helper::formatPecahan($statistik1->PersenRealisasiKeuangan1,$jumlah_sub_opd), 
        ];

        $statistik5=Statistik5Model::select(\DB::raw('
          `Bulan`,
          SUM(`PersenTargetKeuangan1`) AS `PersenTargetKeuangan1`,
          SUM(`PersenRealisasiKeuangan1`) AS `PersenRealisasiKeuangan1`,                                                
          SUM(`TargetFisik1`) AS `TargetFisik1`,
          SUM(`RealisasiFisik1`) AS `RealisasiFisik1`                                                
        '))
        ->where('TA', $tahun)
        ->whereIn('SOrgID', $daftar_sub_opd)                                             
        ->where('EntryLvl', 1)        
        ->groupBy('Bulan')                                
        ->get();
                      
        
        foreach($statistik5 as $v)
        {
          switch($v->Bulan)
          {
            case 1 :
              $chart_keuangan[0][0]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][0]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);

              $chart_fisik[0][0]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][0]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 2 :
              $chart_keuangan[0][1]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][1]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);

              $chart_fisik[0][1]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][1]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 3 :
              $chart_keuangan[0][2]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][2]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);

              $chart_fisik[0][2]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][2]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 4 :
              $chart_keuangan[0][3]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][3]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);

              $chart_fisik[0][3]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][3]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 5 :
              $chart_keuangan[0][4]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][4]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);

              $chart_fisik[0][4]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][4]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 6 :
              $chart_keuangan[0][5]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][5]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);

              $chart_fisik[0][5]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][5]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 7 :
              $chart_keuangan[0][6]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][6]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);
              
              $chart_fisik[0][6]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][6]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 8 :
              $chart_keuangan[0][7]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][7]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);
              
              $chart_fisik[0][7]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][7]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 9 :
              $chart_keuangan[0][8]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][8]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);
              
              $chart_fisik[0][8]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][8]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 10 :
              $chart_keuangan[0][9]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][9]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);
              
              $chart_fisik[0][9]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][9]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 11 :
              $chart_keuangan[0][10]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][10]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);
              
              $chart_fisik[0][10]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][10]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
            case 12 :
              $chart_keuangan[0][11]=Helper::formatPecahan($v->PersenTargetKeuangan1,$jumlah_sub_opd);
              $chart_keuangan[1][11]=Helper::formatPecahan($v->PersenRealisasiKeuangan1,$jumlah_sub_opd);
              
              $chart_fisik[0][11]=Helper::formatPecahan($v->TargetFisik1,$jumlah_sub_opd);
              $chart_fisik[1][11]=Helper::formatPecahan($v->RealisasiFisik1,$jumlah_sub_opd);
            break;
          }
        }
      }			
    }
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'statistik1' => $statistik1,
      'daftar_opd' => $daftar_opd,
      'daftar_sub_opd' => $daftar_sub_opd,
      'daftar_kelompok_rekening' => $daftar_kelompok_rekening,
      'chart_keuangan' => $chart_keuangan,
      'chart_fisik' => $chart_fisik,
      'message' => 'Fetch data ringkasan murni berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  public function reloadstatistik1(Request $request)
  {
    $this->validate($request, [            
      'ta' => 'required',          
    ]);
    
    $tahun = $request->input('ta');        
    $statistik1=[
      'PaguDana1' => 0,             
      'JumlahProgram1' => 0,             
      'JumlahKegiatan1' => 0,             
      'JumlahSubKegiatan1' => 0,             
      'RealisasiKeuangan1' => 0,             
      'RealisasiFisik1' => 0, 
    ];
    if ($this->hasRole(['superadmin', 'bapelitbang']))
    {
      $str_jumlah_pagudana="
        UPDATE 
          statistik1,
          (
            SELECT 
              SUM(`PaguUraian1`) AS PaguDana1 
            FROM
              `trRKARinc` 
            WHERE `TA` = $tahun 
            AND `EntryLvl`=1
          ) AS level1
        SET 
          statistik1.`PaguDana1`=level1.PaguDana1 
        WHERE statistik1.`statistikID` = $tahun
      ";
      \DB::statement($str_jumlah_pagudana); 

      $str_jumlah_program1='
        UPDATE
          statistik1,
          (
            SELECT 
              COUNT(`kode_program`) AS jumlah_program
            FROM 
            (
              SELECT 
                `kode_program` 
              FROM 
                `trRKA` 
              WHERE `TA`='.$tahun.' 
              AND `EntryLvl`=1 
              GROUP BY 
                `kode_program`
            ) AS level1
          ) AS level2
        SET 
          statistik1.`JumlahProgram1`=level2.jumlah_program
        WHERE statistik1.`statistikID`='.$tahun;
  
      \DB::statement($str_jumlah_program1); 
  
      $str_jumlah_kegiatan1='
        UPDATE
          statistik1,
          (
            SELECT 
              COUNT(`kode_kegiatan`) AS jumlah_kegiatan
            FROM 
            (
              SELECT 
                `kode_kegiatan` 
              FROM 
                `trRKA` 
              WHERE `TA`='.$tahun.' 
              AND `EntryLvl`=1 
              GROUP BY 
                `kode_kegiatan`
            ) AS level1
          ) AS level2
        SET 
          statistik1.`JumlahKegiatan1`=level2.jumlah_kegiatan
        WHERE statistik1.`statistikID`='.$tahun;

      \DB::statement($str_jumlah_kegiatan1);  

      $str_jumlah_sub_kegiatan1='
        UPDATE
          statistik1,
          (
            SELECT 
              COUNT(`kode_sub_kegiatan`) AS jumlah_sub_kegiatan
            FROM 
            (
              SELECT 
                `kode_sub_kegiatan` 
              FROM 
                `trRKA` 
              WHERE `TA`='.$tahun.' 
              AND `EntryLvl`=1 
              GROUP BY 
                `kode_sub_kegiatan`
            ) AS level1
          ) AS level2
        SET 
          statistik1.`JumlahSubKegiatan1`=level2.jumlah_sub_kegiatan
        WHERE statistik1.`statistikID`='.$tahun;

      \DB::statement($str_jumlah_sub_kegiatan1);             
      
      $jumlahuraian = \DB::table('trRKARinc')
      ->where('EntryLvl', 1)
      ->where('TA', $tahun)
      ->count();

      $totalfisik=\DB::table('trRKARealisasiRinc')                                   
      ->where('TA', $tahun)                                    
      ->where('EntryLvl', 1)
      ->sum('fisik1');
      
      $persen_realisasi_fisik=Helper::formatPecahan($totalfisik,$jumlahuraian);
      
      $str_jumlah_realisasi1="
        UPDATE 
          statistik1,
          (
            SELECT 
              SUM(`realisasi1`) AS realisasi1 
            FROM
              `trRKARealisasiRinc` 
            WHERE `TA` = $tahun 
            AND `EntryLvl`=1
          ) AS level1
        SET 
          statistik1.`RealisasiKeuangan1`=level1.realisasi1,
          statistik1.`RealisasiFisik1` = $persen_realisasi_fisik
        WHERE statistik1.`statistikID` = $tahun
      ";
      \DB::statement($str_jumlah_realisasi1); 

      $statistik1 = Statistik1Model::select(\DB::raw('
                      `PaguDana1`,
                      `JumlahProgram1`,
                      `JumlahKegiatan1`,
                      `JumlahSubKegiatan1`,
                      `RealisasiKeuangan1`,
                      `RealisasiFisik1`,
                      0 AS `PersenRealisasiKeuangan1`
                    '))
                    ->find($tahun);

      $statistik1->PersenRealisasiKeuangan1=Helper::formatPersen($statistik1->RealisasiKeuangan1,$statistik1->PaguDana1);
      $statistik1=[
              'PaguDana1' => $statistik1->PaguDana1,             
              'JumlahProgram1' => $statistik1->JumlahProgram1,             
              'JumlahKegiatan1' => $statistik1->JumlahKegiatan1,             
              'JumlahSubKegiatan1' => $statistik1->JumlahSubKegiatan1,             
              'RealisasiKeuangan1' => $statistik1->RealisasiKeuangan1,             
              'RealisasiFisik1' => $statistik1->RealisasiFisik1, 
              'PersenRealisasiKeuangan1' => $statistik1->PersenRealisasiKeuangan1, 
            ];
    }
    else if ($this->hasRole('opd'))
    {
      $daftar_opd = $this->getUserOrgID($tahun);
      $jumlah_opd=count($daftar_opd);
      if ($jumlah_opd > 0)
      {
        foreach ($daftar_opd as $OrgID)
        {
          $total_data = $this->generateStatistikOPD($OrgID, $tahun);
          $persen_realisasi_fisik = $total_data['totalPersenRealisasiFisik'];
          $total_realisasi = $total_data['totalRealisasiKeuanganKeseluruhan'];

          $str_jumlah_realisasi1='UPDATE `tmOrg` SET `RealisasiKeuangan1`='.$total_realisasi.',`RealisasiFisik1`='.$persen_realisasi_fisik.' WHERE `tmOrg`.`OrgID`=\''.$OrgID.'\'';

          \DB::statement($str_jumlah_realisasi1); 
        }                                          
        $statistik1 = OrganisasiModel::where('TA', $tahun)
                ->select(\DB::raw('
                  COALESCE(SUM(`PaguDana1`),0) AS `PaguDana1`, 
                  COALESCE(SUM(`JumlahProgram1`),0) AS `JumlahProgram1`, 
                  COALESCE(SUM(`JumlahKegiatan1`),0) AS `JumlahKegiatan1`,
                  COALESCE(SUM(`JumlahSubKegiatan1`),0) AS `JumlahSubKegiatan1`,
                  COALESCE(SUM(`RealisasiKeuangan1`),0) AS `RealisasiKeuangan1`,
                  COALESCE(SUM(`RealisasiFisik1`),0) AS `RealisasiFisik1`,
                  0 AS `PersenRealisasiKeuangan1`
                '))
                ->whereIn('OrgID', $daftar_opd)                                
                ->first();

        $statistik1->PersenRealisasiKeuangan1=Helper::formatPersen($statistik1->RealisasiKeuangan1,$statistik1->PaguDana1);                
        $statistik1=[
              'PaguDana1' => $statistik1->PaguDana1,             
              'JumlahProgram1' => $statistik1->JumlahProgram1,             
              'JumlahKegiatan1' => $statistik1->JumlahKegiatan1,             
              'JumlahSubKegiatan1' => $statistik1->JumlahSubKegiatan1,             
              'RealisasiKeuangan1' => $statistik1->RealisasiKeuangan1,             
              'RealisasiFisik1' => $statistik1->RealisasiFisik1, 
              'PersenRealisasiKeuangan1' => $statistik1->PersenRealisasiKeuangan1, 
            ];
      }                         
    }
    else if ($this->hasRole('unitkerja'))
    {
      $daftar_sub_opd = $this->getUserSOrgID($tahun);
      $jumlah_sub_opd=count($daftar_sub_opd);
      if ($jumlah_sub_opd > 0)
      {
        foreach ($daftar_sub_opd as $SOrgID)
        {
          $total_data = $this->generateStatistikUnitKerja($SOrgID, $tahun);
          $persen_realisasi_fisik = $total_data['totalPersenRealisasiFisik'];
          $total_realisasi = $total_data['totalRealisasiKeuanganKeseluruhan'];

          $str_jumlah_realisasi1='UPDATE `tmSOrg` SET `RealisasiKeuangan1`='.$total_realisasi.',`RealisasiFisik1`='.$persen_realisasi_fisik.' WHERE `tmSOrg`.`SOrgID`=\''.$SOrgID.'\'';

          \DB::statement($str_jumlah_realisasi1); 
        }                                          
        $statistik1 = SubOrganisasiModel::where('TA', $tahun)
          ->select(\DB::raw('
            COALESCE(SUM(`PaguDana1`),0) AS `PaguDana1`, 
            COALESCE(SUM(`JumlahProgram1`),0) AS `JumlahProgram1`, 
            COALESCE(SUM(`JumlahKegiatan1`),0) AS `JumlahKegiatan1`,
            COALESCE(SUM(`JumlahSubKegiatan1`),0) AS `JumlahSubKegiatan1`,
            COALESCE(SUM(`RealisasiKeuangan1`),0) AS `RealisasiKeuangan1`,
            COALESCE(SUM(`RealisasiFisik1`),0) AS `RealisasiFisik1`,
            0 AS `PersenRealisasiKeuangan1`
          '))
          ->whereIn('SOrgID', $daftar_sub_opd)                                
          ->first();

        $statistik1->PersenRealisasiKeuangan1=Helper::formatPersen($statistik1->RealisasiKeuangan1,$statistik1->PaguDana1);                
        $statistik1=[
          'PaguDana1' => $statistik1->PaguDana1,             
          'JumlahProgram1' => $statistik1->JumlahProgram1,             
          'JumlahKegiatan1' => $statistik1->JumlahKegiatan1,             
          'JumlahSubKegiatan1' => $statistik1->JumlahSubKegiatan1,             
          'RealisasiKeuangan1' => $statistik1->RealisasiKeuangan1,             
          'RealisasiFisik1' => $statistik1->RealisasiFisik1, 
          'PersenRealisasiKeuangan1' => $statistik1->PersenRealisasiKeuangan1, 
        ];
      }                         
    }
    return Response()->json([
      'status' => 1,
      'pid' => 'update',                                    
      'statistik1' => $statistik1,                                    
      'message' => 'Data statistik1 berhasil di update'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  public function reloadstatistik2(Request $request)
  {
    $this->validate($request, [            
      'ta' => 'required',          
    ]);
    $tahun = $request->input('ta');  
    if ($this->hasRole(['superadmin', 'bapelitbang']))
    {
      $daftar_opd = OrganisasiModel::select('OrgID')
                    ->where('TA', $tahun)
                    ->get();

      foreach ($daftar_opd as $v)
      {
        $this->generateStatistikOPDBulan($v->OrgID,$tahun);
      }  
    }
    else if($this->hasRole('opd'))
    {
      $daftar_opd = $this->getUserOrgID($tahun);
      $jumlah_opd=count($daftar_opd);
      if ($jumlah_opd > 0)
      {
        foreach ($daftar_opd as $OrgID)
        {
          $this->generateStatistikOPDBulan($OrgID,$tahun);
        }                  
      }
    }
    else if($this->hasRole('unitkerja'))
    {
      $daftar_sub_opd = $this->getUserSOrgID($tahun);
      $jumlah_sub_opd=count($daftar_sub_opd);
      if ($jumlah_sub_opd > 0)
      {
        foreach ($daftar_sub_opd as $SOrgID)
        {
          $this->generateStatistikUnitKerjaBulan($SOrgID, $tahun);
        }                  
      }
    }
  }
  private function generateStatistikOPD ($OrgID,$tahun)
  {
    $str_jumlah_program_kegiatan = "
      UPDATE 
        tmOrg AS dest,
        (
          SELECT 
            COUNT(DISTINCT(kode_program)) AS jumlah_program,
            COUNT(DISTINCT(kode_kegiatan)) AS kode_kegiatan,
            COUNT(DISTINCT(kode_sub_kegiatan)) AS kode_sub_kegiatan
          FROM trRKA 
          WHERE OrgID='$OrgID'
          AND `EntryLvl`=1
        ) AS src
      SET 
        dest.JumlahProgram1 = src.jumlah_program,
        dest.JumlahKegiatan1 = src.kode_kegiatan,
        dest.JumlahSubKegiatan1 = src.kode_sub_kegiatan
      WHERE OrgID='$OrgID'
    ";

    \DB::statement($str_jumlah_program_kegiatan); 
    
    $str_pagu = "
      UPDATE 
        tmOrg AS dest,
        (
          SELECT 
            SUM(PaguUraian1) AS PaguUraian
          FROM trRKARinc A
          JOIN trRKA B ON A.RKAID=B.RKAID
          WHERE OrgID='$OrgID'
          AND A.`EntryLvl`=1
        ) AS src
      SET 
        dest.PaguDana1 = src.PaguUraian                
      WHERE OrgID='$OrgID'
    ";
    \DB::statement($str_pagu); 
    
    $opd = OrganisasiModel::find($OrgID); 
    $total_data=[
      'totalPaguOPD' => 0,
      'totalPersenBobot' => 0,
      'totalPersenTargetFisik' => 0,
      'totalPersenRealisasiFisik' => 0,
      'total_ttb_fisik' => 0,
      'totalTargetKeuanganKeseluruhan' => 0,
      'totalRealisasiKeuanganKeseluruhan' => 0,
      'totalPersenTargetKeuangan' => 0,
      'totalPersenRealisasiKeuangan' => 0,
      'total_ttb_keuangan' => 0,
      'totalSisaAnggaran' => 0,
      'totalPersenSisaAnggaran' => 0,
    ];                    
    if (!is_null($opd))
    {
      $totalPaguOPD = $opd->PaguDana1; 

      $total_kegiatan = 0;
      $total_sub_kegiatan = 0;
      $total_uraian = 0;
      $totalPersenBobot = 0;
      $totalPersenTargetFisik = 0;
      $totalPersenRealisasiFisik = 0;
      $total_ttb_fisik = 0;
      $totalTargetKeuanganKeseluruhan = 0;
      $totalRealisasiKeuanganKeseluruhan = 0;
      $total_ttb_keuangan = 0;
      $totalSisaAnggaran = 0;     
      
      $daftar_sub_kegiatan = \DB::table('trRKA')
                    ->select(\DB::raw('`RKAID`,`PaguDana1`'))                                                                             
                    ->where('OrgID', $opd->OrgID)                                            
                    ->where('TA', $tahun)  
                    ->where('EntryLvl', 1)                                        
                    ->get();

      
      if(isset($daftar_sub_kegiatan[0]))
      {
        foreach ($daftar_sub_kegiatan as $n)
        {
          $RKAID = $n->RKAID;
          $nilai_pagu_proyek = $n->PaguDana1;
          $persen_bobot=Helper::formatPersen($nilai_pagu_proyek,$totalPaguOPD);
          $totalPersenBobot+= $persen_bobot;

          //jumlah baris uraian
          $jumlahuraian = \DB::table('trRKARinc')->where('RKAID', $RKAID)->count();	
          $total_uraian+= $jumlahuraian;

          $data_target=\DB::table('trRKATargetRinc')
                    ->select(\DB::raw('COALESCE(SUM(target1),0) AS totaltarget, COALESCE(SUM(fisik1),0) AS jumlah_fisik'))
                    ->where('RKAID', $RKAID)                                        
                    ->get();

          $data_realisasi=\DB::table('trRKARealisasiRinc')
                  ->select(\DB::raw('COALESCE(SUM(realisasi1),0) AS realisasi1, COALESCE(SUM(fisik1),0) AS fisik1'))
                  ->where('RKAID', $RKAID)                                    
                  ->get();

          //menghitung persen target fisik         
          $target_fisik=Helper::formatPecahan($data_target[0]->jumlah_fisik,$jumlahuraian);                            
          $persen_target_fisik= $target_fisik > 100 ? 100.00 : $target_fisik;
          $totalPersenTargetFisik+= $persen_target_fisik;               

          //menghitung persen realisasi fisik                
          $persen_realisasi_fisik=Helper::formatPecahan($data_realisasi[0]->fisik1,$jumlahuraian);
          $totalPersenRealisasiFisik+= $persen_realisasi_fisik; 
          
          $persen_tertimbang_fisik = 0.00;
          if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
          {
            $persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100, 2);                            
          }							
          $total_ttb_fisik+= $persen_tertimbang_fisik;

          //menghitung total target dan realisasi keuangan 
          $totalTargetKeuangan = $data_target[0]->totaltarget;
          $totalTargetKeuanganKeseluruhan+= $totalTargetKeuangan;
          $persen_target_keuangan=Helper::formatPersen($totalTargetKeuangan,$nilai_pagu_proyek);                            							                                 
        
          $totalRealisasiKeuangan = $data_realisasi[0]->realisasi1;
          $totalRealisasiKeuanganKeseluruhan+= $totalRealisasiKeuangan;
          $persen_realisasi_keuangan=Helper::formatPersen($totalRealisasiKeuangan,$nilai_pagu_proyek);  
          
          $persen_tertimbang_keuangan = 0.00;
          if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
          {
            $persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100, 2);                            
          }	
          $total_ttb_keuangan += $persen_tertimbang_keuangan;

          $sisa_anggaran = $nilai_pagu_proyek-$totalRealisasiKeuangan;
          $totalSisaAnggaran+= $sisa_anggaran; 
          
          $persen_sisa_anggaran = Helper::formatPersen($sisa_anggaran,$nilai_pagu_proyek);

          $total_sub_kegiatan+=1;
        }
      }        
      
      if ($totalPersenBobot > 100) {
        $totalPersenBobot = 100.000;
      }
      $totalPersenTargetFisik = Helper::formatPecahan($totalPersenTargetFisik,$total_sub_kegiatan);        
      $totalPersenRealisasiFisik=Helper::formatPecahan($totalPersenRealisasiFisik,$total_sub_kegiatan); 
      $totalPersenTargetKeuangan=Helper::formatPersen($totalTargetKeuanganKeseluruhan,$totalPaguOPD);                
      $totalPersenRealisasiKeuangan=Helper::formatPersen($totalRealisasiKeuanganKeseluruhan,$totalPaguOPD);
      $totalPersenSisaAnggaran = Helper::formatPersen($totalSisaAnggaran,$totalPaguOPD);
      $totalPersenBobot=round($totalPersenBobot,2);
      $total_ttb_fisik=round($total_ttb_fisik,2);
      $total_ttb_keuangan=round($total_ttb_keuangan,2);
      $total_data=[
        'totalPaguOPD' => $totalPaguOPD,
        'totalPersenBobot' => $totalPersenBobot,
        'totalPersenTargetFisik' => $totalPersenTargetFisik,
        'totalPersenRealisasiFisik' => $totalPersenRealisasiFisik,
        'total_ttb_fisik' => $total_ttb_fisik,
        'totalTargetKeuanganKeseluruhan' => $totalTargetKeuanganKeseluruhan,
        'totalRealisasiKeuanganKeseluruhan' => $totalRealisasiKeuanganKeseluruhan,
        'totalPersenTargetKeuangan' => $totalPersenTargetKeuangan,
        'totalPersenRealisasiKeuangan' => $totalPersenRealisasiKeuangan,
        'total_ttb_keuangan' => $total_ttb_keuangan,
        'totalSisaAnggaran' => $totalSisaAnggaran,
        'totalPersenSisaAnggaran' => $totalPersenSisaAnggaran,
      ];                           
    }
    return $total_data;
  }
  private function generateStatistikOPDBulan ($OrgID,$tahun)
  {      
    $str_jumlah_program_kegiatan = "
      UPDATE 
        tmOrg AS dest,
        (
          SELECT 
            COUNT(DISTINCT(kode_program)) AS jumlah_program,
            COUNT(DISTINCT(kode_kegiatan)) AS kode_kegiatan,
            COUNT(DISTINCT(kode_sub_kegiatan)) AS kode_sub_kegiatan
          FROM trRKA 
          WHERE OrgID='$OrgID'
          AND `EntryLvl`=1
        ) AS src
      SET 
        dest.JumlahProgram1 = src.jumlah_program,
        dest.JumlahKegiatan1 = src.kode_kegiatan,
        dest.JumlahSubKegiatan1 = src.kode_sub_kegiatan
      WHERE OrgID='$OrgID'
    ";

    \DB::statement($str_jumlah_program_kegiatan); 
    
    $str_pagu = "
      UPDATE 
        tmOrg AS dest,
        (
          SELECT 
            SUM(PaguUraian1) AS PaguUraian
          FROM trRKARinc A
          JOIN trRKA B ON A.RKAID=B.RKAID
          WHERE OrgID='$OrgID'
          AND A.`EntryLvl`=1
        ) AS src
      SET 
        dest.PaguDana1 = src.PaguUraian                
      WHERE OrgID='$OrgID'
    ";
    \DB::statement($str_pagu); 
    
    $opd = OrganisasiModel::find($OrgID);  
    if (!is_null($opd))
    {
      $totalPaguOPD = $opd->PaguDana1;        
      
      $total_kegiatan = 0;
      $total_sub_kegiatan = 0;
      $total_uraian = 0;
      $totalPersenBobot = 0;
      $totalPersenTargetFisik = 0;
      $totalPersenRealisasiFisik = 0;
      $total_ttb_fisik = 0;
      $totalTargetKeuanganKeseluruhan = 0;
      $totalRealisasiKeuanganKeseluruhan = 0;
      $total_ttb_keuangan = 0;
      $totalSisaAnggaran = 0; 
    
      for ($i=1;$i<=12;$i++)
      {   
        $total_kegiatan = 0;
        $total_sub_kegiatan = 0;
        $total_uraian = 0;
        $totalPersenBobot = 0;
        $totalPersenTargetFisik = 0;
        $totalPersenRealisasiFisik = 0;
        $total_ttb_fisik = 0;
        $totalTargetKeuanganKeseluruhan = 0;
        $totalRealisasiKeuanganKeseluruhan = 0;
        $total_ttb_keuangan = 0;
        $totalSisaAnggaran = 0;     

        $daftar_sub_kegiatan = \DB::table('trRKA')
          ->select(\DB::raw('`RKAID`,`PaguDana1`'))                                                                             
          ->where('OrgID', $opd->OrgID)                                            
          ->where('TA', $tahun)  
          ->where('EntryLvl', 1)                                        
          ->get();

        if(isset($daftar_sub_kegiatan[0]))
        {
          $total_kegiatan = \DB::table('trRKA')
            ->where('OrgID', $opd->OrgID)                                            
            ->where('TA', $tahun)  
            ->where('EntryLvl', 1) 
            ->distinct('kode_kegiatan')
            ->count('kode_kegiatan');

          foreach ($daftar_sub_kegiatan as $n)
          {
            $RKAID = $n->RKAID;
            $nilai_pagu_proyek = $n->PaguDana1;
            $persen_bobot=Helper::formatPersen($nilai_pagu_proyek,$totalPaguOPD);
            $totalPersenBobot+= $persen_bobot;

            //jumlah baris uraian
            $jumlahuraian = \DB::table('trRKARinc')->where('RKAID', $RKAID)->count();	
            $total_uraian+= $jumlahuraian;

            $data_target=\DB::table('trRKATargetRinc')
                    ->select(\DB::raw('COALESCE(SUM(target1),0) AS totaltarget, COALESCE(SUM(fisik1),0) AS jumlah_fisik'))
                    ->where('RKAID', $RKAID)                   
                    ->where('bulan1', '<=', $i)                     
                    ->get();

            $data_realisasi=\DB::table('trRKARealisasiRinc')
                    ->select(\DB::raw('COALESCE(SUM(realisasi1),0) AS realisasi1, COALESCE(SUM(fisik1),0) AS fisik1'))
                    ->where('RKAID', $RKAID)      
                    ->where('bulan1', '<=', $i)                                   
                    ->get();

            //menghitung persen target fisik         
            $target_fisik=Helper::formatPecahan($data_target[0]->jumlah_fisik,$jumlahuraian);                            
            $persen_target_fisik= $target_fisik > 100 ? 100.00 : $target_fisik;
            $totalPersenTargetFisik+= $persen_target_fisik;               

            //menghitung persen realisasi fisik                
            $persen_realisasi_fisik=Helper::formatPecahan($data_realisasi[0]->fisik1,$jumlahuraian);
            $totalPersenRealisasiFisik+= $persen_realisasi_fisik; 
            
            $persen_tertimbang_fisik = 0.00;
            if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
            {
              $persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100, 2);                            
            }							
            $total_ttb_fisik+= $persen_tertimbang_fisik;

            //menghitung total target dan realisasi keuangan 
            $totalTargetKeuangan = $data_target[0]->totaltarget;
            $totalTargetKeuanganKeseluruhan+= $totalTargetKeuangan;
            $persen_target_keuangan=Helper::formatPersen($totalTargetKeuangan,$nilai_pagu_proyek);                            							                                 
          
            $totalRealisasiKeuangan = $data_realisasi[0]->realisasi1;
            $totalRealisasiKeuanganKeseluruhan+= $totalRealisasiKeuangan;
            $persen_realisasi_keuangan=Helper::formatPersen($totalRealisasiKeuangan,$nilai_pagu_proyek);  
            
            $persen_tertimbang_keuangan = 0.00;
            if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
            {
              $persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100, 2);                            
            }	
            $total_ttb_keuangan += $persen_tertimbang_keuangan;

            $sisa_anggaran = $nilai_pagu_proyek-$totalRealisasiKeuangan;
            $totalSisaAnggaran+= $sisa_anggaran; 
            
            $persen_sisa_anggaran = Helper::formatPersen($sisa_anggaran,$nilai_pagu_proyek);

            $total_sub_kegiatan+=1;
          }
        }
        if ($totalPersenBobot > 100) {
          $totalPersenBobot = 100.000;
        }
        $totalPersenTargetFisik = Helper::formatPecahan($totalPersenTargetFisik,$total_sub_kegiatan);        
        $totalPersenRealisasiFisik=Helper::formatPecahan($totalPersenRealisasiFisik,$total_sub_kegiatan); 
        $totalPersenTargetKeuangan=Helper::formatPersen($totalTargetKeuanganKeseluruhan,$totalPaguOPD);                
        $totalPersenRealisasiKeuangan=Helper::formatPersen($totalRealisasiKeuanganKeseluruhan,$totalPaguOPD);
        $totalPersenSisaAnggaran = Helper::formatPersen($totalSisaAnggaran,$totalPaguOPD);
        $totalPersenBobot=round($totalPersenBobot,2);
        $total_ttb_fisik=round($total_ttb_fisik,2);
        $total_ttb_keuangan=round($total_ttb_keuangan,2);

        $statistik2 = Statistik2Model::where('Bulan', $i)
        ->where('OrgID', $OrgID)
        ->where('TA', $tahun)   
        ->where('EntryLvl', 1)                                                                                   
        ->first();

        if (is_null($statistik2))
        {
          Statistik2Model::create([
            'Statistik2ID' => uniqid ('uid'),
            'OrgID' => $OrgID,
            'kode_organisasi' => $opd->kode_organisasi,
            'OrgNm' => $opd->Nm_Organisasi,   
                                 
            'PaguDana1' => $totalPaguOPD,            
            'PaguDana2' => 0,            
            'PaguDana3' => 0,      

            'JumlahKegiatan1' => $total_kegiatan,
            'JumlahKegiatan2' => 0,
            'JumlahKegiatan3' => 0,

            'JumlahSubKegiatan1' => $total_sub_kegiatan,
            'JumlahSubKegiatan2' => 0,
            'JumlahSubKegiatan3' => 0,

            'JumlahUraian1' => $total_uraian,
            'JumlahUraian2' => 0,
            'JumlahUraian3' => 0,
              
            'TargetFisik1' => $totalPersenTargetFisik,
            'TargetFisik2' => 0,
            'TargetFisik3' => 0,

            'RealisasiFisik1' => $totalPersenRealisasiFisik,
            'RealisasiFisik2' => 0,
            'RealisasiFisik3' => 0,

            'TargetKeuangan1' => $totalTargetKeuanganKeseluruhan,
            'TargetKeuangan2' => 0,
            'TargetKeuangan3' => 0,

            'RealisasiKeuangan1' => $totalRealisasiKeuanganKeseluruhan,
            'RealisasiKeuangan2' => 0,
            'RealisasiKeuangan3' => 0,

            'PersenTargetKeuangan1' => $totalPersenTargetKeuangan,
            'PersenTargetKeuangan2' => 0,
            'PersenTargetKeuangan3' => 0,

            'PersenRealisasiKeuangan1' => $totalPersenRealisasiKeuangan,
            'PersenRealisasiKeuangan2' => 0,
            'PersenRealisasiKeuangan3' => 0,
              
            'SisaPaguDana1' => $totalSisaAnggaran,
            'SisaPaguDana2' => 0,
            'SisaPaguDana3' => 0,

            'PersenSisaPaguDana1' => $totalPersenSisaAnggaran,
            'PersenSisaPaguDana2' => 0,
            'PersenSisaPaguDana3' => 0,

            'Bobot1' => $totalPersenBobot,
            'Bobot2' => 0,
            'Bobot3' => 0,
            
            'Bulan' => $i,
            'TA' => $tahun,
            'EntryLvl' => 1,
          ]);
        }
        else
        {
        
          $statistik2->PaguDana1 = $totalPaguOPD;            
          $statistik2->JumlahKegiatan1 = $total_kegiatan;
          $statistik2->JumlahSubKegiatan1 = $total_sub_kegiatan;
          $statistik2->JumlahUraian1 = $total_uraian;              
          $statistik2->TargetFisik1 = $totalPersenTargetFisik;
          $statistik2->RealisasiFisik1 = $totalPersenRealisasiFisik;
          $statistik2->TargetKeuangan1 = $totalTargetKeuanganKeseluruhan;
          $statistik2->RealisasiKeuangan1 = $totalRealisasiKeuanganKeseluruhan;
          $statistik2->PersenTargetKeuangan1 = $totalPersenTargetKeuangan;
          $statistik2->PersenRealisasiKeuangan1 = $totalPersenRealisasiKeuangan;
          $statistik2->SisaPaguDana1 = $totalSisaAnggaran;
          $statistik2->PersenSisaPaguDana1 = $totalPersenSisaAnggaran;
          $statistik2->Bobot2 = $totalPersenBobot;
          $statistik2->save();				
        }
      }
    }
  }
  private function generateStatistikUnitKerja ($SOrgID, $tahun)
  {
    $str_jumlah_program_kegiatan = "
      UPDATE 
        tmSOrg AS dest,
        (
          SELECT 
            COUNT(DISTINCT(kode_program)) AS jumlah_program,
            COUNT(DISTINCT(kode_kegiatan)) AS kode_kegiatan,
            COUNT(DISTINCT(kode_sub_kegiatan)) AS kode_sub_kegiatan
          FROM trRKA 
          WHERE SOrgID='$SOrgID'
          AND `EntryLvl`=1
        ) AS src
      SET 
        dest.JumlahProgram1 = src.jumlah_program,
        dest.JumlahKegiatan1 = src.kode_kegiatan,
        dest.JumlahSubKegiatan1 = src.kode_sub_kegiatan
      WHERE SOrgID='$SOrgID'
    ";

    \DB::statement($str_jumlah_program_kegiatan); 
    
    $str_pagu = "
      UPDATE 
        tmSOrg AS dest,
        (
          SELECT 
            SUM(PaguUraian1) AS PaguUraian
          FROM trRKARinc A
          JOIN trRKA B ON A.RKAID=B.RKAID
          WHERE SOrgID='$SOrgID'
          AND A.`EntryLvl`=1
        ) AS src
      SET 
        dest.PaguDana1 = src.PaguUraian                
      WHERE SOrgID='$SOrgID'
    ";
    \DB::statement($str_pagu); 
    
    $unitkerja = SubOrganisasiModel::find($SOrgID); 
    $total_data=[
      'totalPaguOPD' => 0,
      'totalPersenBobot' => 0,
      'totalPersenTargetFisik' => 0,
      'totalPersenRealisasiFisik' => 0,
      'total_ttb_fisik' => 0,
      'totalTargetKeuanganKeseluruhan' => 0,
      'totalRealisasiKeuanganKeseluruhan' => 0,
      'totalPersenTargetKeuangan' => 0,
      'totalPersenRealisasiKeuangan' => 0,
      'total_ttb_keuangan' => 0,
      'totalSisaAnggaran' => 0,
      'totalPersenSisaAnggaran' => 0,
    ];                    
    if (!is_null($unitkerja))
    {
      $totalPaguOPD = $unitkerja->PaguDana1; 

      $total_kegiatan = 0;
      $total_sub_kegiatan = 0;
      $total_uraian = 0;
      $totalPersenBobot = 0;
      $totalPersenTargetFisik = 0;
      $totalPersenRealisasiFisik = 0;
      $total_ttb_fisik = 0;
      $totalTargetKeuanganKeseluruhan = 0;
      $totalRealisasiKeuanganKeseluruhan = 0;
      $total_ttb_keuangan = 0;
      $totalSisaAnggaran = 0;     
      
      $daftar_sub_kegiatan = \DB::table('trRKA')
        ->select(\DB::raw('`RKAID`,`PaguDana1`'))                                                                             
        ->where('SOrgID', $unitkerja->SOrgID)                                            
        ->where('TA', $tahun)  
        ->where('EntryLvl', 1)                                        
        ->get();

      
      if(isset($daftar_sub_kegiatan[0]))
      {
        foreach ($daftar_sub_kegiatan as $n)
        {
          $RKAID = $n->RKAID;
          $nilai_pagu_proyek = $n->PaguDana1;
          $persen_bobot=Helper::formatPersen($nilai_pagu_proyek,$totalPaguOPD);
          $totalPersenBobot+= $persen_bobot;

          //jumlah baris uraian
          $jumlahuraian = \DB::table('trRKARinc')->where('RKAID', $RKAID)->count();	
          $total_uraian+= $jumlahuraian;

          $data_target=\DB::table('trRKATargetRinc')
            ->select(\DB::raw('COALESCE(SUM(target1),0) AS totaltarget, COALESCE(SUM(fisik1),0) AS jumlah_fisik'))
            ->where('RKAID', $RKAID)                                        
            ->get();

          $data_realisasi=\DB::table('trRKARealisasiRinc')
          ->select(\DB::raw('COALESCE(SUM(realisasi1),0) AS realisasi1, COALESCE(SUM(fisik1),0) AS fisik1'))
          ->where('RKAID', $RKAID)                                    
          ->get();

          //menghitung persen target fisik         
          $target_fisik=Helper::formatPecahan($data_target[0]->jumlah_fisik,$jumlahuraian);                            
          $persen_target_fisik= $target_fisik > 100 ? 100.00 : $target_fisik;
          $totalPersenTargetFisik+= $persen_target_fisik;               

          //menghitung persen realisasi fisik                
          $persen_realisasi_fisik=Helper::formatPecahan($data_realisasi[0]->fisik1,$jumlahuraian);
          $totalPersenRealisasiFisik+= $persen_realisasi_fisik; 
          
          $persen_tertimbang_fisik = 0.00;
          if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
          {
            $persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100, 2);                            
          }							
          $total_ttb_fisik+= $persen_tertimbang_fisik;

          //menghitung total target dan realisasi keuangan 
          $totalTargetKeuangan = $data_target[0]->totaltarget;
          $totalTargetKeuanganKeseluruhan+= $totalTargetKeuangan;
          $persen_target_keuangan=Helper::formatPersen($totalTargetKeuangan,$nilai_pagu_proyek);                            							                                 
        
          $totalRealisasiKeuangan = $data_realisasi[0]->realisasi1;
          $totalRealisasiKeuanganKeseluruhan+= $totalRealisasiKeuangan;
          $persen_realisasi_keuangan=Helper::formatPersen($totalRealisasiKeuangan,$nilai_pagu_proyek);  
          
          $persen_tertimbang_keuangan = 0.00;
          if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
          {
            $persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100, 2);                            
          }	
          $total_ttb_keuangan += $persen_tertimbang_keuangan;

          $sisa_anggaran = $nilai_pagu_proyek-$totalRealisasiKeuangan;
          $totalSisaAnggaran+= $sisa_anggaran; 
          
          $persen_sisa_anggaran = Helper::formatPersen($sisa_anggaran,$nilai_pagu_proyek);

          $total_sub_kegiatan+=1;
        }
      }        
      
      if ($totalPersenBobot > 100) {
        $totalPersenBobot = 100.000;
      }
      $totalPersenTargetFisik = Helper::formatPecahan($totalPersenTargetFisik,$total_sub_kegiatan);        
      $totalPersenRealisasiFisik=Helper::formatPecahan($totalPersenRealisasiFisik,$total_sub_kegiatan); 
      $totalPersenTargetKeuangan=Helper::formatPersen($totalTargetKeuanganKeseluruhan,$totalPaguOPD);                
      $totalPersenRealisasiKeuangan=Helper::formatPersen($totalRealisasiKeuanganKeseluruhan,$totalPaguOPD);
      $totalPersenSisaAnggaran = Helper::formatPersen($totalSisaAnggaran,$totalPaguOPD);
      $totalPersenBobot=round($totalPersenBobot,2);
      $total_ttb_fisik=round($total_ttb_fisik,2);
      $total_ttb_keuangan=round($total_ttb_keuangan,2);
      $total_data=[
        'totalPaguOPD' => $totalPaguOPD,
        'totalPersenBobot' => $totalPersenBobot,
        'totalPersenTargetFisik' => $totalPersenTargetFisik,
        'totalPersenRealisasiFisik' => $totalPersenRealisasiFisik,
        'total_ttb_fisik' => $total_ttb_fisik,
        'totalTargetKeuanganKeseluruhan' => $totalTargetKeuanganKeseluruhan,
        'totalRealisasiKeuanganKeseluruhan' => $totalRealisasiKeuanganKeseluruhan,
        'totalPersenTargetKeuangan' => $totalPersenTargetKeuangan,
        'totalPersenRealisasiKeuangan' => $totalPersenRealisasiKeuangan,
        'total_ttb_keuangan' => $total_ttb_keuangan,
        'totalSisaAnggaran' => $totalSisaAnggaran,
        'totalPersenSisaAnggaran' => $totalPersenSisaAnggaran,
      ];                           
    }
    return $total_data;
  }
  private function generateStatistikUnitKerjaBulan ($SOrgID, $tahun)
  {      
    $str_jumlah_program_kegiatan = "
      UPDATE 
        tmSOrg AS dest,
        (
          SELECT 
            COUNT(DISTINCT(kode_program)) AS jumlah_program,
            COUNT(DISTINCT(kode_kegiatan)) AS kode_kegiatan,
            COUNT(DISTINCT(kode_sub_kegiatan)) AS kode_sub_kegiatan
          FROM trRKA 
          WHERE SOrgID='$SOrgID'
          AND `EntryLvl`=1
        ) AS src
      SET 
        dest.JumlahProgram1 = src.jumlah_program,
        dest.JumlahKegiatan1 = src.kode_kegiatan,
        dest.JumlahSubKegiatan1 = src.kode_sub_kegiatan
      WHERE SOrgID='$SOrgID'
    ";

    \DB::statement($str_jumlah_program_kegiatan); 
    
    $str_pagu = "
      UPDATE 
        tmSOrg AS dest,
        (
          SELECT 
            SUM(PaguUraian1) AS PaguUraian
          FROM trRKARinc A
          JOIN trRKA B ON A.RKAID=B.RKAID
          WHERE SOrgID='$SOrgID'
          AND A.`EntryLvl`=1
        ) AS src
      SET 
        dest.PaguDana1 = src.PaguUraian                
      WHERE SOrgID='$SOrgID'
    ";
    \DB::statement($str_pagu); 
    
    $unitkerja = SubOrganisasiModel::find($SOrgID);  
    if (!is_null($unitkerja))
    {
      $totalPaguOPD = $unitkerja->PaguDana1;        
      
      $total_kegiatan = 0;
      $total_sub_kegiatan = 0;
      $total_uraian = 0;
      $totalPersenBobot = 0;
      $totalPersenTargetFisik = 0;
      $totalPersenRealisasiFisik = 0;
      $total_ttb_fisik = 0;
      $totalTargetKeuanganKeseluruhan = 0;
      $totalRealisasiKeuanganKeseluruhan = 0;
      $total_ttb_keuangan = 0;
      $totalSisaAnggaran = 0; 
    
      for ($i=1;$i<=12;$i++)
      {   
        $total_kegiatan = 0;
        $total_sub_kegiatan = 0;
        $total_uraian = 0;
        $totalPersenBobot = 0;
        $totalPersenTargetFisik = 0;
        $totalPersenRealisasiFisik = 0;
        $total_ttb_fisik = 0;
        $totalTargetKeuanganKeseluruhan = 0;
        $totalRealisasiKeuanganKeseluruhan = 0;
        $total_ttb_keuangan = 0;
        $totalSisaAnggaran = 0;     

        $daftar_sub_kegiatan = \DB::table('trRKA')
          ->select(\DB::raw('`RKAID`,`PaguDana1`'))                                                                             
          ->where('SOrgID', $unitkerja->SOrgID)                                            
          ->where('TA', $tahun)  
          ->where('EntryLvl', 1)                                        
          ->get();

        if(isset($daftar_sub_kegiatan[0]))
        {
          $total_kegiatan = \DB::table('trRKA')
            ->where('SOrgID', $unitkerja->SOrgID)                                            
            ->where('TA', $tahun)  
            ->where('EntryLvl', 1) 
            ->distinct('kode_kegiatan')
            ->count('kode_kegiatan');

          foreach ($daftar_sub_kegiatan as $n)
          {
            $RKAID = $n->RKAID;
            $nilai_pagu_proyek = $n->PaguDana1;
            $persen_bobot=Helper::formatPersen($nilai_pagu_proyek,$totalPaguOPD);
            $totalPersenBobot+= $persen_bobot;

            //jumlah baris uraian
            $jumlahuraian = \DB::table('trRKARinc')->where('RKAID', $RKAID)->count();	
            $total_uraian+= $jumlahuraian;

            $data_target=\DB::table('trRKATargetRinc')
              ->select(\DB::raw('COALESCE(SUM(target1),0) AS totaltarget, COALESCE(SUM(fisik1),0) AS jumlah_fisik'))
              ->where('RKAID', $RKAID)                   
              ->where('bulan1', '<=', $i)                     
              ->get();

            $data_realisasi=\DB::table('trRKARealisasiRinc')
              ->select(\DB::raw('COALESCE(SUM(realisasi1),0) AS realisasi1, COALESCE(SUM(fisik1),0) AS fisik1'))
              ->where('RKAID', $RKAID)      
              ->where('bulan1', '<=', $i)                                   
              ->get();

            //menghitung persen target fisik         
            $target_fisik=Helper::formatPecahan($data_target[0]->jumlah_fisik,$jumlahuraian);                            
            $persen_target_fisik= $target_fisik > 100 ? 100.00 : $target_fisik;
            $totalPersenTargetFisik+= $persen_target_fisik;               

            //menghitung persen realisasi fisik                
            $persen_realisasi_fisik=Helper::formatPecahan($data_realisasi[0]->fisik1,$jumlahuraian);
            $totalPersenRealisasiFisik+= $persen_realisasi_fisik; 
            
            $persen_tertimbang_fisik = 0.00;
            if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
            {
              $persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100, 2);                            
            }							
            $total_ttb_fisik+= $persen_tertimbang_fisik;

            //menghitung total target dan realisasi keuangan 
            $totalTargetKeuangan = $data_target[0]->totaltarget;
            $totalTargetKeuanganKeseluruhan+= $totalTargetKeuangan;
            $persen_target_keuangan=Helper::formatPersen($totalTargetKeuangan,$nilai_pagu_proyek);                            							                                 
          
            $totalRealisasiKeuangan = $data_realisasi[0]->realisasi1;
            $totalRealisasiKeuanganKeseluruhan+= $totalRealisasiKeuangan;
            $persen_realisasi_keuangan=Helper::formatPersen($totalRealisasiKeuangan,$nilai_pagu_proyek);  
            
            $persen_tertimbang_keuangan = 0.00;
            if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
            {
              $persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100, 2);                            
            }	
            $total_ttb_keuangan += $persen_tertimbang_keuangan;

            $sisa_anggaran = $nilai_pagu_proyek-$totalRealisasiKeuangan;
            $totalSisaAnggaran+= $sisa_anggaran; 
            
            $persen_sisa_anggaran = Helper::formatPersen($sisa_anggaran,$nilai_pagu_proyek);

            $total_sub_kegiatan+=1;
          }
        }
        if ($totalPersenBobot > 100) {
          $totalPersenBobot = 100.000;
        }
        $totalPersenTargetFisik = Helper::formatPecahan($totalPersenTargetFisik,$total_sub_kegiatan);        
        $totalPersenRealisasiFisik=Helper::formatPecahan($totalPersenRealisasiFisik,$total_sub_kegiatan); 
        $totalPersenTargetKeuangan=Helper::formatPersen($totalTargetKeuanganKeseluruhan,$totalPaguOPD);                
        $totalPersenRealisasiKeuangan=Helper::formatPersen($totalRealisasiKeuanganKeseluruhan,$totalPaguOPD);
        $totalPersenSisaAnggaran = Helper::formatPersen($totalSisaAnggaran,$totalPaguOPD);
        $totalPersenBobot=round($totalPersenBobot,2);
        $total_ttb_fisik=round($total_ttb_fisik,2);
        $total_ttb_keuangan=round($total_ttb_keuangan,2);

        $statistik5 = Statistik5Model::where('Bulan', $i)
        ->where('SOrgID', $SOrgID)
        ->where('TA', $tahun)   
        ->where('EntryLvl', 1)                                                                                   
        ->first();

        if (is_null($statistik5))
        {
          Statistik5Model::create([
            'Statistik5ID' => uniqid ('uid'),
            'SOrgID' => $SOrgID,
            'kode_sub_organisasi' => $unitkerja->kode_sub_organisasi,
            'SOrgNm' => $unitkerja->Nm_Sub_Organisasi,   
                                 
            'PaguDana1' => $totalPaguOPD,            
            'PaguDana2' => 0,            
            'PaguDana3' => 0,      

            'JumlahKegiatan1' => $total_kegiatan,
            'JumlahKegiatan2' => 0,
            'JumlahKegiatan3' => 0,

            'JumlahSubKegiatan1' => $total_sub_kegiatan,
            'JumlahSubKegiatan2' => 0,
            'JumlahSubKegiatan3' => 0,

            'JumlahUraian1' => $total_uraian,
            'JumlahUraian2' => 0,
            'JumlahUraian3' => 0,
              
            'TargetFisik1' => $totalPersenTargetFisik,
            'TargetFisik2' => 0,
            'TargetFisik3' => 0,

            'RealisasiFisik1' => $totalPersenRealisasiFisik,
            'RealisasiFisik2' => 0,
            'RealisasiFisik3' => 0,

            'TargetKeuangan1' => $totalTargetKeuanganKeseluruhan,
            'TargetKeuangan2' => 0,
            'TargetKeuangan3' => 0,

            'RealisasiKeuangan1' => $totalRealisasiKeuanganKeseluruhan,
            'RealisasiKeuangan2' => 0,
            'RealisasiKeuangan3' => 0,

            'PersenTargetKeuangan1' => $totalPersenTargetKeuangan,
            'PersenTargetKeuangan2' => 0,
            'PersenTargetKeuangan3' => 0,

            'PersenRealisasiKeuangan1' => $totalPersenRealisasiKeuangan,
            'PersenRealisasiKeuangan2' => 0,
            'PersenRealisasiKeuangan3' => 0,
              
            'SisaPaguDana1' => $totalSisaAnggaran,
            'SisaPaguDana2' => 0,
            'SisaPaguDana3' => 0,

            'PersenSisaPaguDana1' => $totalPersenSisaAnggaran,
            'PersenSisaPaguDana2' => 0,
            'PersenSisaPaguDana3' => 0,

            'Bobot1' => $totalPersenBobot,
            'Bobot2' => 0,
            'Bobot3' => 0,
            
            'Bulan' => $i,
            'TA' => $tahun,
            'EntryLvl' => 1,
          ]);
        }
        else
        {				
          $statistik5->PaguDana1 = $totalPaguOPD;            
          $statistik5->JumlahKegiatan1 = $total_kegiatan;
          $statistik5->JumlahSubKegiatan1 = $total_sub_kegiatan;
          $statistik5->JumlahUraian1 = $total_uraian;              
          $statistik5->TargetFisik1 = $totalPersenTargetFisik;
          $statistik5->RealisasiFisik1 = $totalPersenRealisasiFisik;
          $statistik5->TargetKeuangan1 = $totalTargetKeuanganKeseluruhan;
          $statistik5->RealisasiKeuangan1 = $totalRealisasiKeuanganKeseluruhan;
          $statistik5->PersenTargetKeuangan1 = $totalPersenTargetKeuangan;
          $statistik5->PersenRealisasiKeuangan1 = $totalPersenRealisasiKeuangan;
          $statistik5->SisaPaguDana1 = $totalSisaAnggaran;
          $statistik5->PersenSisaPaguDana1 = $totalPersenSisaAnggaran;
          $statistik5->Bobot2 = $totalPersenBobot;
          $statistik5->save();				
        }
      }
    }
  }
}