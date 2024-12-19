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
      *
    "))
    ->join('tmRpjmdStrategi AS b', 'a.RpjmdStrategiID', 'b.RpjmdStrategiID')
    ->where('b.RpjmdSasaranID', $RpjmdSasaranID);
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $data->get(),        
      ],
      'message' => 'Fetch data Formulir E,78 berhasil diperoleh'
    ], 200);
  }
  
}