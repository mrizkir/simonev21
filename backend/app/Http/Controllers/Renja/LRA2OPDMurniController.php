<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\OrganisasiModel;
use App\Models\ReportModel;

class LRA2OPDMurniController extends Controller
{
  /**
   * Preview kegiatan dan rincian belanja per OPD. Nomor kontrak tidak wajib.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('RENJA-LRA2-MURNI_BROWSE');

    $this->validate($request, [
      'tahun' => 'required|numeric',
      'no_bulan' => 'required',
      'OrgID' => 'required|exists:tmOrg,OrgID',
    ]);

    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    $OrgID = $request->input('OrgID');
    $opd = OrganisasiModel::find($OrgID);
    $data = (new ReportModel([]))->getDataKontrakOPD($OrgID, $tahun, $no_bulan, 1);

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'opd' => $opd,
      'rka' => $data['rows'],
      'total_data' => $data['total_data'],
      'message' => 'Fetch data LRA OPD 2 murni berhasil diperoleh'
    ], 200);
  }

  public function printtoexcel(Request $request)
  {
    $this->hasPermissionTo('RENJA-LRA2-MURNI_BROWSE');

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
      $report = new \App\Models\Renja\FormLRA2OPDMurniModel($data_report);
      $generate_date = date('Y-m-d_H_m_s');
      return $report->download("lra_opd_2_murni_$generate_date.xlsx");
    }

    return Response()->json([
      'status' => 0,
      'pid' => 'fetchdata',
      'message' => ['Print excel gagal dilakukan karena belum ada kegiatan pada OPD ini']
    ], 422);
  }
}
