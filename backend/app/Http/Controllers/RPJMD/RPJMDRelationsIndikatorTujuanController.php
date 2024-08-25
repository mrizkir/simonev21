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

    $this->validate($request, [      
      'IndikatorKinerjaID'=>'required|exists:tmRPJMDIndikatorKinerja,IndikatorKinerjaID',      
      'RpjmdCascadingID'=>'required|exists:tmRpjmdTujuan,RpjmdTujuanID',      
      'data_1'=>'required|numeric',      
      'data_2'=>'required|numeric',      
      'data_3'=>'required|numeric',      
      'data_4'=>'required|numeric',      
      'data_5'=>'required|numeric',      
      'data_6'=>'required|numeric',      
      'data_7'=>'required|numeric',      
      'data_8'=>'required|numeric',
    ]);         

    $indikatortujuan = RPJMDRelasiIndikatorModel::create([
      'RpjmdRelasiIndikatorID' => Uuid::uuid4()->toString(),
      'IndikatorKinerjaID' => $request->input('IndikatorKinerjaID'),
      'RpjmdCascadingID' => $request->input('RpjmdCascadingID'),
      'PeriodeRPJMDID' => $request->input('RpjmdCascadingID'),      
      'TipeCascading' => 'tujuan',      
      'data_1' => $request->input('data_1'),    
      'data_2' => $request->input('data_2'),    
      'data_3' => $request->input('data_3'),    
      'data_4' => $request->input('data_4'),    
      'data_5' => $request->input('data_5'),    
      'data_6' => $request->input('data_6'),    
      'data_7' => $request->input('data_7'),    
      'data_8' => $request->input('data_8'),    
      'data_9' => 0,    
      'data_10' => 0,
    ]);

    return Response()->json([
      'status'=>1,
      'pid'=>'store',
      'payload'=>$indikatortujuan,                                    
      'message'=>'Data Indikator Tujuan berhasil disimpan.'
    ], 200);
  }
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_UPDATE');

    $indikatortujuan = RPJMDRelasiIndikatorModel::find($id);

    if(is_null($indikatortujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Indikator Tujuan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $this->validate($request, [      
        'IndikatorKinerjaID'=>'required|exists:tmRPJMDIndikatorKinerja,IndikatorKinerjaID',              
        'data_1'=>'required|numeric',      
        'data_2'=>'required|numeric',      
        'data_3'=>'required|numeric',      
        'data_4'=>'required|numeric',      
        'data_5'=>'required|numeric',      
        'data_6'=>'required|numeric',      
        'data_7'=>'required|numeric',      
        'data_8'=>'required|numeric',
      ]);         

      $indikatortujuan->IndikatorKinerjaID = $request->input('IndikatorKinerjaID');
      $indikatortujuan->data_1 = $request->input('data_1');
      $indikatortujuan->data_2 = $request->input('data_2');
      $indikatortujuan->data_3 = $request->input('data_3');
      $indikatortujuan->data_4 = $request->input('data_4');
      $indikatortujuan->data_5 = $request->input('data_5');
      $indikatortujuan->data_6 = $request->input('data_6');
      $indikatortujuan->data_7 = $request->input('data_7');
      $indikatortujuan->data_8 = $request->input('data_8');
      $indikatortujuan->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $indikatortujuan,                                    
        'message' => 'Data indikator tujuan berhasil disimpan.'
      ], 200); 
    }
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request,$id)
  {
    $this->hasPermissionTo('RPJMD-MISI_DESTROY');

    $indikatortujuan = RPJMDRelasiIndikatorModel::find($id);

    if(is_null($indikatortujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Indikator Tujuan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    // else if($visi->misi->count('RpjmdMisiID') > 0)
    // {
    //   return Response()->json([
    //     'status' => 0,
    //     'pid' => 'fetchdata',
    //     'message' => ["RPJMD Misi dengan dengan ($id) gagal dihapus karena masih terhubung ke Misi"]
    //   ], 422); 
    // }
    else
    {
      $indikatortujuan->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data Indikator Tujuan dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}