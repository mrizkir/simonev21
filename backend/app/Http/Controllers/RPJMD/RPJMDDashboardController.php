<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Helpers\HelperRPJMD;

use App\Models\RPJMD\RPJMDMisiModel;
use App\Models\RPJMD\RPJMDTujuanModel;
use App\Models\RPJMD\RPJMDSasaranModel;
use App\Models\RPJMD\RPJMDRelasiIndikatorModel;
use App\Models\RPJMD\RPJMDRelasiArahKebijakanProgramModel;
use App\Models\RPJMD\RPJMDIndikatorKinerjaModel;

class RPJMDDashboardController extends Controller
{
  public function index(Request $request)
  {
    // $this->validate($request, [      
    //   'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    // ]);

    // $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    // $totalRecords = RPJMDMisiModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdVisiID');
    $totalRecords = RPJMDMisiModel::count('RpjmdVisiID');
    
    $data = RPJMDMisiModel::select(\DB::raw('
      RpjmdMisiID,
      Kd_RpjmdMisi,
      Nm_RpjmdMisi,
      0 AS tingkat_kinerja_total,
      0 AS tingkat_kinerja,
      "SANGAT RENDAH" AS predikat_kinerja,
      "" AS warna_kinerja,
      0 AS tingkat_anggaran,
      "SANGAT RENDAH" AS predikat_anggaran,
      "" AS warna_anggaran,
      0 AS jumlah_program,
      0 AS tingkat_kinerja_program
    '))
    // ->where('PeriodeRPJMDID', $PeriodeRPJMDID)    
    ->orderBy('Kd_RpjmdMisi', 'asc')
    ->get()
    // ->transform(function($item, $key) use ($PeriodeRPJMDID) {
    ->transform(function($item, $key) {
      //realisasi tahun 2024 => data_3
      //target tahun 2024 => data_5
      $daftar_tujuan = \DB::table('tmRpjmdRealisasiIndikator AS a')
      ->select(\DB::raw('
        COALESCE(a.data_3, 0) AS realisasi_3,
        COALESCE(c.data_5, 0) AS target_5
      '))
      ->join('tmRpjmdTujuan AS b', 'a.RpjmdCascadingID', 'b.RpjmdTujuanID')
      ->join('tmRpjmdRelasiIndikator AS c', 'b.RpjmdTujuanID', 'c.RpjmdCascadingID')
      // ->where('a.PeriodeRPJMDID', $PeriodeRPJMDID)      
      ->where('b.RpjmdMisiID', $item->RpjmdMisiID)
      ->where('a.TipeCascading', 'tujuan')
      ->whereNotNull('a.IndikatorKinerjaID')      
      ->get();
      
      $jumlah_tujuan = $daftar_tujuan->count();

      $tingkat_kinerja_total = 0;
      foreach($daftar_tujuan as $tujuan)
      {
        $realisasi_3 = $tujuan->realisasi_3;
        $target_5 = $tujuan->target_5;

        $capaian_kinerja = Helper::formatPersen($realisasi_3, $target_5);
        $tingkat_kinerja_total += $capaian_kinerja;
      }
      $item->tingkat_kinerja_total = $tingkat_kinerja_total;

      $tingkat_kinerja = Helper::formatPecahan($tingkat_kinerja_total, $jumlah_tujuan);
      
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
  public function statistik(Request $request)
  {
    $statistik = [
      'jumlah_tujuan' => RPJMDTujuanModel::count('RpjmdTujuanID'),
      'jumlah_indikator_tujuan' => RPJMDRelasiIndikatorModel::where('TipeCascading', 'tujuan')->count('RpjmdRelasiIndikatorID'),
      'jumlah_sasaran' => RPJMDSasaranModel::count('RpjmdSasaranID'),
      'jumlah_indikator_sasaran' => RPJMDRelasiIndikatorModel::where('TipeCascading', 'sasaran')->count('RpjmdRelasiIndikatorID'),
      'jumlah_program' => RPJMDRelasiArahKebijakanProgramModel::count('ArahKebijakanProgramID'),
      'jumlah_indikator_program' => RPJMDRelasiIndikatorModel::where('TipeCascading', 'program')->count('RpjmdRelasiIndikatorID'),
      'jumlah_iku' => RPJMDIndikatorKinerjaModel::where('is_iku', 1)->count('IndikatorKinerjaID'),
      'jumlah_ikk' => RPJMDIndikatorKinerjaModel::where('is_ikk', 1)->count('IndikatorKinerjaID'),
    ];

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => $statistik,
      'message' => 'Fetch data statistik rpjmd berhasil diperoleh'
    ], 200);
  }
}