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
      'RpjmdSasaranID' => 'required'
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    $RpjmdSasaranID = $request->input('RpjmdSasaranID');    

    //dapatkan program berdasarkan sasaran
    $data = \DB::table('tmRpjmdRelasiStrategiProgram AS a')
    ->select(\DB::raw("
      a.PrgID,
      a.Nm_ProgramRPJMD,
      '' AS indikator_kinerja
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
        data_1,
        data_2,
        data_3,
        data_4,
        data_5,
        data_6,
        data_7,
        data_8,        
        a.created_at,
        a.updated_at
      '))
      ->join('tmRPJMDIndikatorKinerja AS b', 'a.IndikatorKinerjaID', 'b.IndikatorKinerjaID')
      ->where('RpjmdCascadingID', $PrgID)
      ->get();

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