<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\SubOrganisasiModel;
use App\Models\Renja\RKAModel;

use Ramsey\Uuid\Uuid;

class RealisasiIndikatorSubKegiatanPerubahanController extends Controller
{
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('RENJA-FORM-B-PERUBAHAN_BROWSE');

    $this->validate($request, [
      'tahun' => 'required',
      'no_bulan' => 'required',
      'SOrgID' => 'required|exists:tmSOrg,SOrgID',
    ]);
    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    $SOrgID = $request->input('SOrgID');

    $unitkerja = SubOrganisasiModel::find($SOrgID);

    $totalPaguUnit = (float)\DB::table('trRKA')
    ->where('SOrgID', $unitkerja->SOrgID)
    ->where('TA', $tahun)
    ->where('EntryLvl', 2)
    ->sum('PaguDana2');

    $data = [];
    $no = 1;

    // Get all sub kegiatan for this unit kerja
    $daftar_sub_kegiatan = \DB::table('trRKA')
      ->select(\DB::raw('
        `RKAID`,
        `kode_sub_kegiatan`,
        `Nm_Sub_Kegiatan`,
        `PaguDana2`,
        `keluaran2`,
        `tk_keluaran2`,
        `tk_capaian2`,
        `RealisasiKinerja`
      '))
      ->where('SOrgID', $unitkerja->SOrgID)
      ->where('TA', $tahun)
      ->where('EntryLvl', 2)
      ->orderByRaw('kode_urusan="X" DESC')
      ->orderBy('kode_bidang', 'ASC')
      ->orderBy('kode_program', 'ASC')
      ->orderBy('kode_kegiatan', 'ASC')
      ->orderBy('kode_sub_kegiatan', 'ASC')
      ->get();

    foreach ($daftar_sub_kegiatan as $data_sub_kegiatan)
    {
      $RKAID = $data_sub_kegiatan->RKAID;

      // Get components (uraian) for this sub kegiatan
      $daftar_komponen = \DB::table('trRKARinc')
        ->select(\DB::raw('
          `RKARincID`,
          `NamaUraian2`,
          `PaguUraian2`
        '))
        ->where('RKAID', $RKAID)
        ->orderBy('kode_uraian2', 'ASC')
        ->get();

      // Get realisasi for the sub kegiatan
      $data_realisasi = \DB::table('trRKARealisasiRinc')
        ->select(\DB::raw('COALESCE(SUM(realisasi2),0) AS realisasi2'))
        ->where('RKAID', $RKAID)
        ->where('bulan1', '<=', $no_bulan)
        ->first();

      $realisasi_dana = $data_realisasi->realisasi2 ?? 0;      
      $realisasi_pagu = $realisasi_dana;
      $rasio_realisasi = Helper::formatPersen($realisasi_dana, $data_sub_kegiatan->PaguDana2);

      $tk_keluaran2 = '-';
      if (!empty($data_sub_kegiatan->keluaran2)) {
        $numberString = filter_var($data_sub_kegiatan->tk_keluaran2, FILTER_SANITIZE_NUMBER_INT);
        if (!empty($numberString)) {
          $tk_keluaran2 = (int)$numberString;
        }
      }

      // Extract number from RealisasiKinerja (e.g., "7 dokumen" -> 7, "dokumen 7" -> 7)
      $realisasi = '-';
      if (!empty($data_sub_kegiatan->RealisasiKinerja)) {
        $numberString = filter_var($data_sub_kegiatan->RealisasiKinerja, FILTER_SANITIZE_NUMBER_INT);
        if (!empty($numberString)) {
          $realisasi = (int)$numberString;
        }
      }
      
      // Calculate pencapaian: ($realisasi / $tk_keluaran2) * 100
      $pencapaian = 0;
      if (is_numeric($realisasi) && is_numeric($tk_keluaran2) && $tk_keluaran2 != 0) {
        $pencapaian = Helper::formatPersen($realisasi, $tk_keluaran2);
      }
      
      $data[] = [
        'no' => $no++,
        'nama_sub_kegiatan' => $data_sub_kegiatan->Nm_Sub_Kegiatan,
        'indikator_kegiatan' => $data_sub_kegiatan->keluaran2 ?? '-',
        'komponen' => $tk_keluaran2,
        'realisasi' => $realisasi,
        'pencapaian' => $pencapaian,
        'pagu' => $data_sub_kegiatan->PaguDana2,
        'realisasi_pagu' => $realisasi_pagu,
        'rasio_realisasi' => $rasio_realisasi,
      ];      
    }

    // Calculate totals
    $total_pagu = array_sum(array_column($data, 'pagu'));
    $total_realisasi_pagu = array_sum(array_column($data, 'realisasi_pagu'));
    $total_rasio_realisasi = $total_pagu > 0 ? Helper::formatPersen($total_realisasi_pagu, $total_pagu) : 0;

    $total_data = [
      'total_pagu' => $total_pagu,
      'total_realisasi_pagu' => $total_realisasi_pagu,
      'total_rasio_realisasi' => $total_rasio_realisasi,
    ];

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'unitkerja' => $unitkerja,
      'data' => $data,
      'total_data' => $total_data,
      'message' => 'Fetch data realisasi indikator sub kegiatan perubahan berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }

  public function printtoexcel(Request $request)
  {
    $this->hasPermissionTo('RENJA-FORM-B-PERUBAHAN_BROWSE');

    $this->validate($request, [
      'tahun' => 'required',
      'no_bulan' => 'required',
      'SOrgID' => 'required|exists:tmSOrg,SOrgID',
    ]);
    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    $SOrgID = $request->input('SOrgID');

    $unitkerja = SubOrganisasiModel::find($SOrgID);
    if (\DB::table('trRKA')->where('SOrgID', $unitkerja->SOrgID)->where('EntryLvl', 2)->where('TA', $tahun)->count() > 0)
    {
      $data_report = [
        'kode_sub_organisasi' => $unitkerja->kode_sub_organisasi,
        'SOrgID' => $unitkerja->SOrgID,
        'Nm_Sub_Organisasi' => $unitkerja->Nm_Sub_Organisasi,
        'tahun' => $tahun,
        'no_bulan' => $no_bulan,
        'nama_pengguna_anggaran' => $unitkerja->NamaKepalaUnitKerja,
        'nip_pengguna_anggaran' => $unitkerja->NIPKepalaUnitKerja
      ];
      $report = new \App\Models\Renja\RealisasiIndikatorSubKegiatanPerubahanModel($data_report);
      $generate_date = date('Y-m-d_H_m_s');
      return $report->download("realisasi_indikator_sub_kegiatan_perubahan_$generate_date.xlsx");
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

