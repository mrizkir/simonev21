<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Helpers\HelperRPJMD;

use App\Models\RPJMD\RPJMDMisiModel;

class RPJMDDashboardMisiController extends Controller
{
  public function index(Request $request)
  {
    $this->validate($request, [      
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $totalRecords = RPJMDMisiModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdVisiID');
    
    $data = RPJMDMisiModel::select(\DB::raw('
      RpjmdMisiID,
      Kd_RpjmdMisi,
      Nm_RpjmdMisi,
      0 AS tingkat_kinerja,
      "SANGAT RENDAH" AS predikat_kinerja,
      "" AS warna_kinerja,
      0 AS tingkat_anggaran,
      "SANGAT RENDAH" AS predikat_anggaran,
      "" AS warna_anggaran
    '))
    ->where('PeriodeRPJMDID', $PeriodeRPJMDID)    
    ->orderBy('Kd_RpjmdMisi', 'asc')
    ->get()
    ->transform(function($item, $key) use($PeriodeRPJMDID) {
      $daftar_tingkat_kinerja = \DB::table('tmRpjmdRealisasiIndikator AS a')
      ->select(\DB::raw('
        COALESCE(ROUND((SUM(a.data_2 + a.data_3 + a.data_4 + a.data_5 + a.data_6 + a.data_7) / 6), 2), 0) AS total
      '))
      ->join('tmRpjmdTujuan AS b', 'a.RpjmdCascadingID', 'b.RpjmdTujuanID')
      ->where('a.PeriodeRPJMDID', $PeriodeRPJMDID)
      ->where('b.RpjmdMisiID', $item->RpjmdMisiID)
      ->where('TipeCascading', 'tujuan')
      ->whereNotNull('IndikatorKinerjaID')      
      ->first();
      
      $tingkat_kinerja = 0;
      if(!is_null($daftar_tingkat_kinerja))
      {
        $tingkat_kinerja = $daftar_tingkat_kinerja->total;
      }
      $item->tingkat_kinerja = $tingkat_kinerja;
      $item->predikat_kinerja = HelperRPJMD::getKriteriaPenilaianRealisasi($tingkat_kinerja);
      
      $tingkat_anggaran = 0;
      $item->tingkat_anggaran = $tingkat_anggaran;
      $item->predikat_anggaran = HelperRPJMD::getKriteriaPenilaianRealisasi($tingkat_anggaran);

      $item->warna_kinerja = HelperRPJMD::getWarnaPenilaianRealisasi($tingkat_kinerja);
      $item->warna_anggaran = HelperRPJMD::getWarnaPenilaianRealisasi($tingkat_anggaran);
      return $item;
    });

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => $data,
      'message' => 'Fetch data dashboard rpjmd berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
}