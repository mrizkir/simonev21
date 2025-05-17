<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Ramsey\Uuid\Uuid;

use App\Models\RPJMD\RPJMDPeriodeModel;
use App\Models\RPJMD\RPJMDRelasiStrategiProgramModel;

//helper
use App\Helpers\Helper;

class RPJMDReportFormulirE78Controller extends Controller 
{
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->validate($request, [      
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
      'RpjmdSasaranID' => 'required',
      'ta' => 'required'
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    $RpjmdSasaranID = $request->input('RpjmdSasaranID');    
    $ta_selected = $request->input('ta');

    $periode = RPJMDPeriodeModel::find($PeriodeRPJMDID);
    $ta_awal = $periode->TA_AWAL;
    $ta_akhir = $periode->TA_AKHIR;

    $tahun_ke = ($ta_selected - $ta_awal) + 1;

    //dapatkan program berdasarkan sasaran
    //tingkat_capaian_target_pagu itu mengikuti tahun_ke yang diambil dari request
    $data = \DB::table('tmRpjmdRelasiArahKebijakanProgram AS a')
    ->select(\DB::raw("
      a.PrgID,
      a.Nm_ProgramRPJMD,
      '' AS indikator_kinerja,
      0 AS target_pagu_1,
      0 AS target_pagu_2, 
      0 AS target_pagu_3,
      0 AS target_pagu_4,
      0 AS target_pagu_5,
      0 AS target_pagu_6,
      0 AS target_pagu_7,
      0 AS target_pagu_8,
      0 AS realisasi_pagu_1,
      0 AS realisasi_pagu_2,
      0 AS realisasi_pagu_3,
      0 AS realisasi_pagu_4,
      0 AS realisasi_pagu_5,
      0 AS realisasi_pagu_6,
      0 AS realisasi_pagu_7,
      0 AS realisasi_pagu_8,
      0 AS tingkat_capaian_target_pagu
    "))
    ->join('tmRpjmdArahKebijakan AS b', 'b.RpjmdArahKebijakanID', 'a.RpjmdArahKebijakanID')
    ->join('tmRpjmdStrategi AS c', 'c.RpjmdStrategiID', 'b.RpjmdStrategiID')
    ->where('c.RpjmdSasaranID', $RpjmdSasaranID)
    ->get();

    $result = [];
    foreach($data as $k => $v)
    {
      $PrgID = $v->PrgID;
      //catatan tingkat_capaian_target_fisik itu mengikuti tahun_ke yang diambil dari request
      $indikator_pagu = \DB::table('tmRpjmdRelasiIndikator AS a')
      ->select(\DB::raw('
        b.RpjmdRealisasiIndikatorID,              
        a.data_1 AS target_1,
        a.data_2 AS target_2,
        a.data_3 AS target_3,
        a.data_4 AS target_4,
        a.data_5 AS target_5,
        a.data_6 AS target_6,
        a.data_7 AS target_7,
        b.data_2 AS realisasi_2,
        b.data_3 AS realisasi_3,
        b.data_4 AS realisasi_4,
        b.data_5 AS realisasi_5,
        b.data_6 AS realisasi_6,
        b.data_7 AS realisasi_7,
        0 AS tingkat_capaian_target_fisik,
        b.created_at,
        b.updated_at
      '))
      ->join('tmRpjmdRealisasiIndikator AS b', 'a.RpjmdRelasiIndikatorID', 'b.RpjmdRelasiIndikatorID')          
      ->whereNull('a.IndikatorKinerjaID')
      ->orderBy('a.updated_at', 'desc')
      ->where('a.RpjmdCascadingID', $PrgID)
      ->first();  

      if(!is_null($indikator_pagu))
      {
        $v->target_pagu_1 = $indikator_pagu->target_1;
        $v->target_pagu_2 = $indikator_pagu->target_2;
        $v->target_pagu_3 = $indikator_pagu->target_3;
        $v->target_pagu_4 = $indikator_pagu->target_4;
        $v->target_pagu_5 = $indikator_pagu->target_5;
        $v->target_pagu_6 = $indikator_pagu->target_6;
        $v->target_pagu_7 = $indikator_pagu->target_7;        
        $v->realisasi_pagu_2 = $indikator_pagu->realisasi_2;
        $v->realisasi_pagu_3 = $indikator_pagu->realisasi_3;
        $v->realisasi_pagu_4 = $indikator_pagu->realisasi_4;
        $v->realisasi_pagu_5 = $indikator_pagu->realisasi_5;
        $v->realisasi_pagu_6 = $indikator_pagu->realisasi_6;
        $v->realisasi_pagu_7 = $indikator_pagu->realisasi_7;
        
        switch($tahun_ke)
        {
          case 2:
            $v->tingkat_capaian_target_pagu = Helper::formatPersen($v->realisasi_pagu_2, $v->target_pagu_2);
          break;
          case 3:
            $v->tingkat_capaian_target_pagu = Helper::formatPersen($v->realisasi_pagu_3, $v->target_pagu_3);
          break;
          case 4:
            $v->tingkat_capaian_target_pagu = Helper::formatPersen($v->realisasi_pagu_4, $v->target_pagu_4);
          break;
          case 5:
            $v->tingkat_capaian_target_pagu = Helper::formatPersen($v->realisasi_pagu_5, $v->target_pagu_5);
          break;
          case 6:
            $v->tingkat_capaian_target_pagu = Helper::formatPersen($v->realisasi_pagu_6, $v->target_pagu_6);
          break;
          case 7:
            $v->tingkat_capaian_target_pagu = Helper::formatPersen($v->realisasi_pagu_7, $v->target_pagu_7);
          break;
        }        
      }
      $indikator_kinerja = \DB::table('tmRpjmdRelasiIndikator AS a')->select(\DB::raw('
        a.RpjmdRelasiIndikatorID,
        b.IndikatorKinerjaID,
        b.NamaIndikator,
        b.Satuan,
        b.Operasi,
        data_1 AS target_fisik_1,
        data_2 AS target_fisik_2,
        data_3 AS target_fisik_3,
        data_4 AS target_fisik_4,
        data_5 AS target_fisik_5,
        data_6 AS target_fisik_6,
        data_7 AS target_fisik_7,
        data_8 AS target_fisik_8,           
        0 AS realisasi_fisik_1,
        0 AS realisasi_fisik_2,
        0 AS realisasi_fisik_3,
        0 AS realisasi_fisik_4,
        0 AS realisasi_fisik_5,
        0 AS realisasi_fisik_6,
        0 AS realisasi_fisik_7,
        0 AS realisasi_fisik_8,
        0 AS tingkat_capaian_target_fisik,
        a.created_at,
        a.updated_at
      '))
      ->join('tmRPJMDIndikatorKinerja AS b', 'a.IndikatorKinerjaID', 'b.IndikatorKinerjaID')
      ->where('RpjmdCascadingID', $PrgID)
      ->get();
      
      $indikator_kinerja->transform(function($item, $key) 
      {
        $realisasi_fisik = \DB::table('tmRpjmdRealisasiIndikator AS a')
        ->select(\DB::raw('
          a.data_1 AS realisasi_fisik_1,
          a.data_2 AS realisasi_fisik_2,
          a.data_3 AS realisasi_fisik_3,
          a.data_4 AS realisasi_fisik_4,
          a.data_5 AS realisasi_fisik_5,
          a.data_6 AS realisasi_fisik_6,
          a.data_7 AS realisasi_fisik_7,
          a.data_8 AS realisasi_fisik_8
        '))
        ->where('RpjmdRelasiIndikatorID', $item->RpjmdRelasiIndikatorID)
        ->where('IndikatorKinerjaID', $item->IndikatorKinerjaID)
        ->first();

        if(!is_null($realisasi_fisik))
        {
          $item->realisasi_fisik_1 = $realisasi_fisik->realisasi_fisik_1;
          $item->realisasi_fisik_2 = $realisasi_fisik->realisasi_fisik_2;
          $item->realisasi_fisik_3 = $realisasi_fisik->realisasi_fisik_3;
          $item->realisasi_fisik_4 = $realisasi_fisik->realisasi_fisik_4;
          $item->realisasi_fisik_5 = $realisasi_fisik->realisasi_fisik_5;
          $item->realisasi_fisik_6 = $realisasi_fisik->realisasi_fisik_6;
          $item->realisasi_fisik_7 = $realisasi_fisik->realisasi_fisik_7;
        }
        
        return $item;
      });

      $v->indikator_kinerja = $indikator_kinerja;

      $result[$k] = $v;
    }
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $result,        
      ],
      'message' => 'Fetch data Formulir E,78 berhasil diperoleh'
    ], 200);
  }
  
}