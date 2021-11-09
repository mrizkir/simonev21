<?php

namespace App\Http\Controllers\Statistik;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\HelperKegiatan;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Statistik2Model;

class LaporanRealisasiMurniController extends Controller { 
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
      `Nm_Organisasi`
    '))
    ->where('TA', $tahun)
    ->orderBy('kode_organisasi', 'ASC')
    ->get();

    $index = 1;
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
      
      $warna_keuangan = HelperKegiatan::getKodeWarna(0, $persen_realisasi_keuangan);

      $laporan_realisasi[] = [
        'index'=>$index,
        'kode_organisasi'=>$v->kode_organisasi,
        'Nm_Organisasi'=>$v->Nm_Organisasi,
        'pagu_dana'=>$pagu_dana,
        'target_fisik'=>$target_fisik,
        'realisasi_fisik'=>$realisasi_fisik,        
        'target_keuangan'=>$target_keuangan,
        'realisasi_keuangan'=>$realisasi_keuangan,
        'warna_keuangan'=>$warna_keuangan,
        'indikator_kinerja'=>$warna_keuangan,        
      ];
      $index = $index + 1;
    }    
    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'laporan_realisasi'=>$laporan_realisasi,
      'message'=>'Fetch data untuk laporan realisasi berhasil diperoleh'
    ],200);
  }
}
