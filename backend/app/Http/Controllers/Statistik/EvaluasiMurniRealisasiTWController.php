<?php

namespace App\Http\Controllers\Statistik;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\HelperKegiatan;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Statistik2Model;
use App\Helpers\Helper;

class EvaluasiMurniRealisasiTWController extends Controller 
{ 
  public function front(Request $request)
	{
    $rule = [            
			'tahun' => 'required|digits:4|integer|min:2020|max:'. (date('Y')),
			'tw_realisasi' => 'required|in:1,2,3,4',
		];
        
    $this->validate($request, $rule);
    
		$tahun = $request->input('tahun');
		$tw_realisasi = $request->input('tw_realisasi');
    
    switch ($tw_realisasi)
    {
      case 1:
        $bulan = 3;
      break;
      case 2:
        $bulan = 6;
      break;
      case 3:
        $bulan = 9;
      break;
      case 4:
        $bulan = 12;
      break;
    }
    $evaluasi_realisasi = [];

    $daftar_opd = OrganisasiModel::select(\DB::raw('
      `OrgID`,
      kode_organisasi,
      `Nm_Organisasi`
    '))
    ->where('TA', $tahun)
    ->orderBy('kode_organisasi', 'ASC')
    ->get();

    $index = 0;
    $TotalPaguDana = 0;
    $TotalTargetFisik = 0;
    $TotalRealisasiFisik = 0;
    $TotalTargetKeuangan = 0;
    $TotalRealisasiKeuangan = 0;
    $TotalPersenRealisasiKeuangan = 0;
    
    foreach ($daftar_opd as $v) {
      $pagu_dana = 0;
      $target_fisik = 0;
      $realisasi_fisik = 0;
      $target_keuangan = 0;
      $realisasi_keuangan = 0;
      $persen_realisasi_keuangan = 0;

      $data_opd = Statistik2Model::select(\DB::raw('
        PaguDana1,
        TargetFisik1,
        RealisasiFisik1,
        TargetKeuangan1,
        RealisasiKeuangan1,
        PersenRealisasiKeuangan1
      '))
      ->where('OrgID', $v->OrgID)
      ->where('TA', $tahun)
      ->where('Bulan', $bulan)
      ->where('EntryLvl', 1)
      ->first();

      if (!is_null($data_opd))
      {
        $pagu_dana = $data_opd->PaguDana1;
        $target_fisik = $data_opd->TargetFisik1;
        $realisasi_fisik = $data_opd->RealisasiFisik1;
        $target_keuangan = $data_opd->TargetKeuangan1;
        $realisasi_keuangan = $data_opd->RealisasiKeuangan1;
        $persen_realisasi_keuangan = $data_opd->PersenRealisasiKeuangan1;
      }
      $index = $index + 1;
      $evaluasi_realisasi[] = [
        'index' => $index,
        'kode_organisasi' => $v->kode_organisasi,
        'Nm_Organisasi' => $v->Nm_Organisasi,
        'pagu_dana' => $pagu_dana,
        'target_fisik' => $target_fisik,
        'realisasi_fisik' => $realisasi_fisik,        
        'target_keuangan' => $target_keuangan,
        'realisasi_keuangan' => $realisasi_keuangan,
        'persen_keuangan' => $persen_realisasi_keuangan,        
      ];
      $TotalPaguDana += $pagu_dana;
      $TotalTargetFisik += $target_fisik;
      $TotalRealisasiFisik += $realisasi_fisik;
      $TotalTargetKeuangan += $target_keuangan;
      $TotalRealisasiKeuangan += $realisasi_keuangan;
      $TotalPersenRealisasiKeuangan += $persen_realisasi_keuangan;      
    }
    
    $evaluasi_total = [      
      'total_pagu_dana' => $TotalPaguDana,
      'total_target_fisik'=>Helper::formatPecahan($TotalTargetFisik, $index),
      'total_realisasi_fisik'=>Helper::formatPecahan($TotalRealisasiFisik,$index),
      'total_target_keuangan' => $TotalTargetKeuangan,
      'total_realisasi_keuangan' => $TotalRealisasiKeuangan,
      'total_persen_keuangan'=>Helper::formatPecahan($TotalPersenRealisasiKeuangan, $index),      
    ];

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'evaluasi_realisasi' => $evaluasi_realisasi,
      'laporan_total' => $evaluasi_total,
      'message' => 'Fetch data untuk laporan realisasi berhasil diperoleh'
    ], 200);
  }

  public function printtoexcel(Request $request)
  {
    $rule = [            
      'tahun' => 'required|digits:4|integer|min:2020|max:'. (date('Y')),
      'tw_realisasi' => 'required|in:1,2,3,4',
    ];
        
    $this->validate($request, $rule);
    
    $tahun = $request->input('tahun');
    $tw_realisasi = $request->input('tw_realisasi');
    
    // Check if there's any data
    if (\DB::table('statistik2')->where('TA', $tahun)->where('EntryLvl', 1)->count() > 0)
    {
      $data_report = [
        'tahun' => $tahun,
        'tw_realisasi' => $tw_realisasi,
      ];
      $report = new \App\Models\Statistik\EvaluasiMurniRealisasiTWModel($data_report);
      $generate_date = date('Y-m-d_H_i_s');
      return $report->download("evaluasi_realisasi_tw_$generate_date.xlsx");
    }
    else
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',                                                                            
        'message' => ['Print excel gagal dilakukan karena tidak ada data statistik pada tahun ini']
      ], 422); 
    }
  }
}
