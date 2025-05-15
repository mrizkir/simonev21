<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\RPJMD\RPJMDRelasiStrategiProgramModel;

use Ramsey\Uuid\Uuid;

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
    $ta = $request->input('ta');
    //dapatkan program berdasarkan sasaran
    $data = \DB::table('tmRpjmdRelasiStrategiProgram AS a')
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
      0 AS realisasi_pagu_8
    "))
    ->join('tmRpjmdStrategi AS b', 'a.RpjmdStrategiID', 'b.RpjmdStrategiID')
    ->where('b.RpjmdSasaranID', $RpjmdSasaranID)
    ->get();

    $result = [];
    foreach($data as $k => $v)
    {
      $PrgID = $v->PrgID;

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
        a.created_at,
        a.updated_at
      '))
      ->join('tmRPJMDIndikatorKinerja AS b', 'a.IndikatorKinerjaID', 'b.IndikatorKinerjaID')
      ->where('RpjmdCascadingID', $PrgID)
      ->get();
      
      $indikator_kinerja->transform(function($item, $key) 
      {

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