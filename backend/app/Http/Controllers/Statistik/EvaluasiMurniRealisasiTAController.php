<?php

namespace App\Http\Controllers\Statistik;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\HelperKegiatan;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Statistik2Model;
use App\Helpers\Helper;

class EvaluasiMurniRealisasiTAController extends Controller { 
  public function front(Request $request)
	{
		$this->validate($request, [            
			'tahun'=>'required|numeric',
			'bulan'=>'required|numeric',
		]);

		$tahun=$request->input('tahun');
		$bulan=$request->input('bulan');

    $laporan_realisasi = [];

    $daftar_opd = OrganisasiModel::select(\DB::raw('
      `OrgID`,
      kode_organisasi,
      `Nm_Organisasi`,
      `Alias_Organisasi`
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
      $persen_target_keuangan = 0;
      $realisasi_keuangan = 0;
      $persen_realisasi_keuangan = 0;

      $data_opd = Statistik2Model::select(\DB::raw('
        PaguDana1,
        TargetFisik1,
        RealisasiFisik1,
        TargetKeuangan1,
        PersenTargetKeuangan1,
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
        $persen_target_keuangan = $data_opd->PersenTargetKeuangan1;
        $realisasi_keuangan = $data_opd->RealisasiKeuangan1;
        $persen_realisasi_keuangan = $data_opd->PersenRealisasiKeuangan1;
      }
      $index = $index + 1;
      $laporan_realisasi[] = [
        'index'=>$index,
        'kode_organisasi'=>$v->kode_organisasi,
        'Nm_Organisasi'=>$v->Nm_Organisasi,
        'Alias_Organisasi'=>$v->Alias_Organisasi,
        'pagu_dana'=>$pagu_dana,
        'pagu_dana_formatted'=>Helper::formatUang($pagu_dana),
        'target_fisik'=>$target_fisik,
        'realisasi_fisik'=>$realisasi_fisik,        
        'target_keuangan'=>$target_keuangan,
        'target_keuangan_formated'=>Helper::formatUang($target_keuangan),
        'persen_target_keuangan'=>$persen_target_keuangan,
        'realisasi_keuangan'=>$realisasi_keuangan,
        'realisasi_keuangan_formatted'=>Helper::formatUang($realisasi_keuangan),
        'persen_keuangan'=>$persen_realisasi_keuangan,        
      ];
      $TotalPaguDana += $pagu_dana;
      $TotalTargetFisik += $target_fisik;
      $TotalRealisasiFisik += $realisasi_fisik;
      $TotalTargetKeuangan += $target_keuangan;
      $TotalRealisasiKeuangan += $realisasi_keuangan;
      $TotalPersenRealisasiKeuangan += $persen_realisasi_keuangan;      
    }
    
    $laporan_total = [      
      'total_pagu_dana'=>$TotalPaguDana,
      'total_pagu_dana_formatted'=>Helper::formatUang($TotalPaguDana),
      'total_target_fisik'=>Helper::formatPecahan($TotalTargetFisik, $index),
      'total_realisasi_fisik'=>Helper::formatPecahan($TotalRealisasiFisik,$index),
      'total_target_keuangan'=>$TotalTargetKeuangan,
      'total_target_keuangan_formatted'=>Helper::formatUang($TotalTargetKeuangan),
      'total_realisasi_keuangan'=>$TotalRealisasiKeuangan,      
      'total_realisasi_keuangan_formatted'=>Helper::formatUang($TotalRealisasiKeuangan),      
      'total_persen_keuangan'=>Helper::formatPersen($TotalRealisasiKeuangan,$TotalPaguDana),
    ];

    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'laporan_realisasi'=>$laporan_realisasi,
      'laporan_total'=>$laporan_total,
      'message'=>'Fetch data untuk laporan realisasi berhasil diperoleh'
    ], 200);
  }
}
