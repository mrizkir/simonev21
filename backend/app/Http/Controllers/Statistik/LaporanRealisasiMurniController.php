<?php

namespace App\Http\Controllers\Statistik;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Statistik2Model;

class LaporanRealisasiMurniController extends Controller { 
  public function front(Request $request)
	{
		$this->validate($request, [            
			'tahun'=>'required',                     
		]);

		$tahun=$request->input('tahun');

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
      $target_fisik = 0;
      $realisasi_fisik = 0;
      $target_keuangan = 0;
      $realisasi_keuangan = 0;

      $laporan_realisasi[] = [
        'index'=>$index,
        'kode_organisasi'=>$v->kode_organisasi,
        'Nm_Organisasi'=>$v->Nm_Organisasi,
        'target_fisik'=>$target_fisik,
        'realisasi_fisik'=>$realisasi_fisik,
        'target_keuangan'=>$target_keuangan,
        'realisasi_keuangan'=>$realisasi_keuangan,
        'indikator_kinerja'=>'red',
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
