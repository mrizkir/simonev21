<?php

namespace App\Http\Controllers\RKPD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;

class RKPDMurniController extends Controller 
{
  public function index(Request $request)
  {
    $this->hasPermissionTo('RKPD-GROUP');

    $this->validate($request, [            
			'tahun'=>'required',
			'no_bulan'=>'required'            
		]);

    $tahun=$request->input('ta');
		$no_bulan=$request->input('no_bulan');

    $kodefikasiurusan=KodefikasiUrusanModel::select(\DB::raw('
        `UrsID`,                                        
        `Kd_Urusan`,
        `Nm_Urusan`        
    '))
    ->orderBy('Kd_Urusan','ASC')                                    
    ->where('TA',$ta)
    ->get();

    $data = [];
    
    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata', 
      'rkpd'=>$data,
      'message'=>'Fetch data rkpd murni berhasil diperoleh'
    ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);      
  }
}