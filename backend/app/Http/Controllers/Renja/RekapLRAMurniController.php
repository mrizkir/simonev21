<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//models
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Renja\FormLRAMurniOPDModel;

use Ramsey\Uuid\Uuid;

class RekapLRAMurniController extends Controller 
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
    ]);

    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    
    // Get all OPDs
    $daftar_opd = OrganisasiModel::select(\DB::raw('
        OrgID,
        kode_organisasi,
        Nm_Organisasi,        
        0 AS belanja_pegawai,
        0 AS rp_belanja_pegawai,
        0 AS belanja_barang_jasa,
        0 AS rp_belanja_barang_jasa,
        0 AS belanja_modal,
        0 AS rp_belanja_modal
      '))
      ->where('TA', $tahun)      
      ->orderBy('kode_organisasi', 'ASC')
      ->get()
      ->transform(function($item, $key) use ($tahun, $no_bulan)
      {
        $item->belanja_pegawai = \DB::table('trRKARealisasiRinc AS a')
        ->select(\DB::raw('
          a.realisasi1
        '))
        ->join('trRKARinc AS b', 'a.RKARincID', '=', 'b.RKARincID')
        ->join('trRKA AS c', 'b.RKAID', '=', 'c.RKAID')
        ->where('b.kode_rek_3', '5.1.01')
        ->where('c.OrgID', $item->OrgID)
        ->where('c.TA', $tahun)
        ->where('c.EntryLvl', 1)
        ->where('c.TA', $tahun)
        ->where('a.bulan1', "<=", $no_bulan)
        ->sum('a.realisasi1');

        $item->rp_belanja_pegawai = Helper::formatUang($item->belanja_pegawai);
        
        $item->belanja_barang_jasa = \DB::table('trRKARealisasiRinc AS a')
        ->select(\DB::raw('
          a.realisasi1
        '))
        ->join('trRKARinc AS b', 'a.RKARincID', '=', 'b.RKARincID')
        ->join('trRKA AS c', 'b.RKAID', '=', 'c.RKAID')
        ->where('b.kode_rek_3', '5.1.02')
        ->where('c.OrgID', $item->OrgID)
        ->where('c.TA', $tahun)
        ->where('c.EntryLvl', 1)
        ->where('c.TA', $tahun)
        ->where('a.bulan1', "<=", $no_bulan)
        ->sum('a.realisasi1');

        $item->rp_belanja_barang_jasa = Helper::formatUang($item->belanja_barang_jasa);

        $item->belanja_modal = \DB::table('trRKARealisasiRinc AS a')
        ->select(\DB::raw('
          a.realisasi1
        '))
        ->join('trRKARinc AS b', 'a.RKARincID', '=', 'b.RKARincID')
        ->join('trRKA AS c', 'b.RKAID', '=', 'c.RKAID')
        ->whereIn('b.kode_rek_3', ['5.2.01', '5.2.02', '5.2.03', '5.2.04', '5.2.05'])
        ->where('c.OrgID', $item->OrgID)
        ->where('c.TA', $tahun)
        ->where('c.EntryLvl', 1)
        ->where('c.TA', $tahun)
        ->where('a.bulan1', "<=", $no_bulan)
        ->sum('a.realisasi1');

        $item->rp_belanja_modal = Helper::formatUang($item->belanja_modal);

        return $item;
      });
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'rekap_lra' => $daftar_opd,   
      'total_belanja_pegawai' => $daftar_opd->sum('belanja_pegawai'),
      'total_belanja_barang_jasa' => $daftar_opd->sum('belanja_barang_jasa'),
      'total_belanja_modal' => $daftar_opd->sum('belanja_modal'),
      'message' => 'Fetch data rekap lra murni berhasil diperoleh'
    ], 200);    
  }  

  public function printtoexcel (Request $request)
  {
    $this->hasPermissionTo('RENJA-FORM-B-MURNI_BROWSE');

    $this->validate($request, [            
      'tahun' => 'required',         
      'no_bulan' => 'required',   
    ]);
    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    
    // Check if there's any RKA data
    if (\DB::table('trRKA')->where('EntryLvl', 1)->where('TA', $tahun)->count() > 0)
    {
      $data_report = [
        'tahun' => $tahun,
        'no_bulan' => $no_bulan,
      ];
      $report = new \App\Models\Renja\FormRekapLRAMurniModel($data_report);
      $generate_date = date('Y-m-d_H_m_s');
      return $report->download("rekap_lra_murni_$generate_date.xlsx");
    }
    else
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',                                                                            
        'message' => ['Print excel gagal dilakukan karena tidak ada data RKA pada tahun ini']
      ], 422); 
    }
  }
}
