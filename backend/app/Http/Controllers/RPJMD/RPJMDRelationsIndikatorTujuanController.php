<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDPeriodeModel;
use App\Models\RPJMD\RPJMDRelasiIndikatorModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class RPJMDRelationsIndikatorTujuanController extends Controller
{
  /**
   * mendapatkan daftar seluruh indikator
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_BROWSE');
  }
  public function store(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_STORE');

    $indikatortujuan = RPJMDRelasiIndikatorModel::create([
      'RpjmdRelasiIndikatorID' => Uuid::uuid4()->toString(),
      'IndikatorKinerjaID' => $request->input('IndikatorKinerjaID'),
      'RpjmdCascadingID' => $request->input('RpjmdCascadingID'),
      'PeriodeRPJMDID' => $request->input('RpjmdCascadingID'),      
      'data_1' => $request->input('data_1'),    
      'data_2' => $request->input('data_2'),    
      'data_3' => $request->input('data_3'),    
      'data_4' => $request->input('data_4'),    
      'data_5' => $request->input('data_5'),    
      'data_6' => $request->input('data_6'),    
      'data_7' => $request->input('data_7'),    
      'data_8' => 0,    
      'data_9' => 0,    
      'data_10' => 0,
    ]);

    return Response()->json([
      'status'=>1,
      'pid'=>'store',
      'payload'=>$indikatortujuan,                                    
      'message'=>'Data Indikator Tujuan RPJMD berhasil disimpan.'
    ], 200);
  }
}