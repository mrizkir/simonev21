<?php

namespace App\Http\Controllers\Statistik;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\HelperKegiatan;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Statistik2Model;
use App\Helpers\Helper;

class EvaluasiPerubahanRealisasiTWController extends Controller { 
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
        PaguDana2,
        TargetFisik2,
        RealisasiFisik2,
        TargetKeuangan2,
        RealisasiKeuangan2,
        PersenRealisasiKeuangan2
      '))
      ->where('OrgID', $v->OrgID)
      ->where('TA', $tahun)
      ->where('Bulan', $bulan)
      ->where('EntryLvl', 2)
      ->first();

      if (!is_null($data_opd))
      {
        $pagu_dana = $data_opd->PaguDana2;
        $target_fisik = $data_opd->TargetFisik2;
        $realisasi_fisik = $data_opd->RealisasiFisik2;
        $target_keuangan = $data_opd->TargetKeuangan2;
        $realisasi_keuangan = $data_opd->RealisasiKeuangan2;
        $persen_realisasi_keuangan = $data_opd->PersenRealisasiKeuangan2;
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
}
