<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//models
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Renja\FormLRAMurniOPDModel;

use Ramsey\Uuid\Uuid;

class LRAOPDMurniController extends Controller 
{
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {             
    $this->hasPermissionTo('RENJA-FORM-B-MURNI_BROWSE');

    $this->validate($request, [            
      'tahun' => 'required|numeric',
      'no_bulan' => 'required',   
      'OrgID' => 'required|exists:tmOrg,OrgID',
    ]);

    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    $OrgID = $request->input('OrgID');
    
    $opd = OrganisasiModel::find($OrgID);

    $form_lra_opd = new FormLRAMurniOPDModel([], false);
    $data_lra = $form_lra_opd->getDataLRA($OrgID, $no_bulan, $tahun, 1);
    $tangkat_lra = $form_lra_opd->getRekeningProyek();    
    $data = [];

    if(isset($tangkat_lra[1]))
    {
      $tingkat_1 = $tangkat_lra[1];
      $tingkat_2 = $tangkat_lra[2];
      $tingkat_3 = $tangkat_lra[3];
      $tingkat_4 = $tangkat_lra[4];
      $tingkat_5 = $tangkat_lra[5];
      $tingkat_6 = $tangkat_lra[6];

      foreach($tingkat_1 as $k1 => $v1)
      {
        $data[] = [
          'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
          'tingkat' => 1,
          'kode' => $k1,
          'nama_uraian' => $v1,
          'pagu_uraian' => 0,
          'realisasi' => 0,
          'persen_realisasi' => 0,
        ];
        foreach($tingkat_2 as $k2 => $v2)
        {
          $rek1_level2 = substr($k2, 0, 1);          
          if($rek1_level2 == $k1)
          {
            $data[] = [
              'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
              'tingkat' => 2,
              'kode' => $k2,
              'nama_uraian' => $v2,
              'pagu_uraian' => 0,
              'realisasi' => 0,
              'persen_realisasi' => 0,
            ];            
            foreach($tingkat_3 as $k3 => $v3)
            {
              $rek2_level3 = substr($k3, 0, 3);              
              if($rek2_level3 == $k2)
              {
                $data[] = [
                  'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
                  'tingkat' => 3,
                  'kode' => $k3,
                  'nama_uraian' => $v3,
                  'pagu_uraian' => 0,
                  'realisasi' => 0,
                  'persen_realisasi' => 0,
                ];
                foreach($tingkat_4 as $k4 => $v4)
                {
                  $rek3_level4 = substr($k4, 0, 6);
                  if($rek3_level4 == $k3)
                  {
                    $data[] = [
                      'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
                      'tingkat' => 4,
                      'kode' => $k4,
                      'nama_uraian' => $v4,
                      'pagu_uraian' => 0,
                      'realisasi' => 0,
                      'persen_realisasi' => 0,
                    ];
                    foreach($tingkat_5 as $k5 => $v5)
                    {
                      $rek4_level5 = substr($k5, 0, 9);
                      if($rek4_level5 == $k4)
                      {
                        $data[] = [
                          'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
                          'tingkat' => 5,
                          'kode' => $k5,
                          'nama_uraian' => $v5,
                          'pagu_uraian' => 0,
                          'realisasi' => 0,
                          'persen_realisasi' => 0,
                        ];
                        foreach($tingkat_6 as $k6 => $v6)
                        {
                          $rek5_level6 = substr($k6, 0, 12);
                          if($rek5_level6 == $k5)
                          {
                            $data[] = [
                              'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
                              'tingkat' => 6,
                              'kode' => $k6,
                              'nama_uraian' => $v6,                              
                              'pagu_uraian' => $data_lra[$k6]['pagu_uraian'],    
                              'realisasi' => 0,
                              'persen_realisasi' => 0,                          
                            ];
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
      // foreach($tingkat_1 as $k1 => $v1)
      // {
      //   foreach($tingkat_6 as $k6 => $v6)
      //   {
      //     $rek1 = substr($k6, 0, 1);
      //     if($rek1 == $k1)
      //     {
      //       //tingkat i
      //       $data[] = [
      //         'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
      //         'tingkat' => 1,
      //         'kode' => $k1,
      //         'nama_uraian' => $v1,              
      //       ];
      //       //tingkat ii
      //       foreach($tingkat_2 as $k2 => $v2)
      //       {
      //         $rek2_tampil = [];
      //         foreach ($tingkat_5 as $k5_level2 => $v5_level2) {
      //           $rek2 = substr($k5_level2, 0, 3);
      //           if ($rek2 == $k2) {
      //             if (!array_key_exists($k2, $rek2_tampil)) {
      //               $rek2_tampil[$rek2] = $v2;
      //             }
      //           }
      //         }
      //         foreach ($rek2_tampil as $a=>$b)
      //         {
      //           $totalPaguDana_Rek2 = \App\Models\Renja\FormLRAMurniOPDModel::calculateEachLevelLRA($data_lra, $a, 'Kd_Rek_2');
      //           $no_= explode ('.', $a);
      //           $data[] = [
      //             'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
      //             'tingkat' => 2,
      //             'kode' => $no_[0] . '.' . $no_[1],
      //             'nama_uraian' => $b,
      //           ];
      //         //   //tingkat iii
      //         //   foreach ($tingkat_3 as $k3 => $v3)
      //         //   {
      //         //     $rek3 = substr($k3, 0, 3);
      //         //     if ($a == $rek3)
      //         //     {
      //         //       $totalPaguDana_Rek3 = \App\Models\Renja\FormLRAMurniOPDModel::calculateEachLevelLRA($data_lra, $k3, 'Kd_Rek_3');
      //         //       $no_ = explode(".",$k3);
      //         //       $data[] = [
      //         //         'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
      //         //         'tingkat' => 3,
      //         //         'kode' => $no_[0] . '.' . $no_[1] . '.' . $no_[2],
      //         //         'nama_uraian' => $v3,
      //         //       ];
      //         //       //tingkat iv
      //         //       foreach ($tingkat_4 as $k4 => $v4)
      //         //       {
      //         //         if (preg_match("/^$k3/", $k4))
      //         //         {
      //         //           $totalPaguDana_Rek4 = \App\Models\Renja\FormLRAMurniOPDModel::calculateEachLevelLRA($data_lra, $k4, 'Kd_Rek_4');
      //         //           $no_ = explode (".", $k4);
      //         //           $data[] = [
      //         //             'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
      //         //             'tingkat' => 4,
      //         //             'kode' => $no_[0] . '.' . $no_[1] . '.' . $no_[2] . '.' . $no_[3],
      //         //             'nama_uraian' => $v4,
      //         //           ];
      //         //           //tingkat v
      //         //           foreach ($tingkat_5 as $k5 => $v5)
      //         //           {
      //         //             if (preg_match("/^$k4/", $k5))
      //         //             {
      //         //               $totalPaguDana_Rek5 = \App\Models\Renja\FormLRAMurniOPDModel::calculateEachLevelLRA($data_lra, $k5, 'Kd_Rek_5');
      //         //               $no_=explode (".",$k5);
      //         //               $data[] = [
      //         //                 'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
      //         //                 'tingkat' => 5,
      //         //                 'kode' => $no_[0] . '.' . $no_[1] . '.' . $no_[2] . '.' . $no_[3] . '.' . $no_[4],
      //         //                 'nama_uraian' => $v5,
      //         //               ];
      //         //               //tingkat vi
      //         //               foreach ($tingkat_6 as $k6 => $v6)
      //         //               {
      //         //                 if (preg_match("/^$k5/", $k6))
      //         //                 {
      //         //                   $totalPaguDana_Rek6 = \App\Models\Renja\FormLRAMurniOPDModel::calculateEachLevelLRA($data_lra, $k6, 'Kd_Rek_6');                                
      //         //                   $no_ = explode(".", $k6);                                
      //         //                   $data[] = [
      //         //                     'FormLRAMurniDetailID' => Uuid::uuid4()->toString(),
      //         //                     'tingkat' => 6,
      //         //                     'kode' => $no_[0] . '.' . $no_[1] . '.' . $no_[2] . '.' . $no_[3] . '.' . $no_[4] . '.' . $no_[5],
      //         //                     'nama_uraian' => $v6,
      //         //                   ];
      //         //                 }
      //         //               }
      //         //             }
      //         //           }
      //         //         }
      //         //       }
      //         //     }
      //         //   }
      //         }
      //       }
      //     }
      //   }
      // }
    }
    else
    {
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'opd' => $opd,
      'lra' => $data,
      'message' => 'Fetch data lra opd murni berhasil diperoleh'
    ], 200);    
  }  
  public function printtoexcel (Request $request)
  {
    $this->hasPermissionTo('RENJA-FORM-B-MURNI_BROWSE');

    $this->validate($request, [            
      'tahun' => 'required',         
      'no_bulan' => 'required',   
      'OrgID' => 'required|exists:tmOrg,OrgID',            
    ]);
    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    $OrgID = $request->input('OrgID');
    
    $opd = OrganisasiModel::find($OrgID);
    if (\DB::table('trRKA')->where('OrgID', $opd->OrgID)->where('EntryLvl', 1)->where('TA', $tahun)->count() > 0)
    {
      $data_report = [
        'OrgID' => $opd->OrgID,
        'kode_organisasi' => $opd->kode_organisasi,
        'Nm_Organisasi' => $opd->Nm_Organisasi,
        'tahun' => $tahun,
        'no_bulan' => $no_bulan,
        'nama_pengguna_anggaran' => $opd->NamaKepalaOPD,
        'nip_pengguna_anggaran' => $opd->NIPKepalaOPD
      ];
      $report = new \App\Models\Renja\FormBOPDMurniModel($data_report);
      $generate_date = date('Y-m-d_H_m_s');
      return $report->download("form_b_$generate_date.xlsx");
    }
    else
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',                                                                            
        'message' => ['Print excel gagal dilakukan karena tidak ada belum ada Uraian pada kegiatan ini']
      ], 422); 
    }
  }

}