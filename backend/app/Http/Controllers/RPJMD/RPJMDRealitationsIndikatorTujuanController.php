<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDRealisasiIndikatorModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class RPJMDRealitationsIndikatorTujuanController extends Controller
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
      'Operasi' => 'required|in:MAX,MIN,RANGE'
    ]);

    $Operasi = $request->input('Operasi');
    
    $rules = [      
      'RpjmdRelasiIndikatorID' => 'required|exists:tmRpjmdRelasiIndikator,RpjmdRelasiIndikatorID',            
      'RpjmdCascadingID' => 'required|exists:tmRpjmdTujuan,RpjmdTujuanID',            
      'IndikatorKinerjaID' => 'required|exists:tmRPJMDIndikatorKinerja,IndikatorKinerjaID',      
      'data_2' => 'required|numeric',      
      'data_3' => 'required|numeric',      
      'data_4' => 'required|numeric',      
      'data_5' => 'required|numeric',      
      'data_6' => 'required|numeric',      
      'data_7' => 'required|numeric',   
    ];
    $this->validate($request, $rules); 

    $indikatortujuan = RPJMDRealisasiIndikatorModel::create([
      'RpjmdRealisasiIndikatorID' => Uuid::uuid4()->toString(),
      'RpjmdRelasiIndikatorID' => $request->input('RpjmdRelasiIndikatorID'),      
      'RpjmdCascadingID' => $request->input('RpjmdCascadingID'),
      'IndikatorKinerjaID' => $request->input('IndikatorKinerjaID'),
      'PeriodeRPJMDID' => $request->input('PeriodeRPJMDID'),      
      'TipeCascading' => 'tujuan',      
      'data_1' => 0,    
      'data_2' => $request->input('data_2'),    
      'data_3' => $request->input('data_3'),    
      'data_4' => $request->input('data_4'),    
      'data_5' => $request->input('data_5'),    
      'data_6' => $request->input('data_6'),    
      'data_7' => $request->input('data_7'),    
      'data_8' => 0,    
      'data_9' => 0,    
      'data_10' => 0,
      'data_11' => 0,
      'data_12' => 0,
      'data_13' => 0,
      'data_14' => 0,
      'data_15' => 0,    
      'data_16' => 0,
      'data_17' => 0,
      'data_18' => 0,
      'data_19' => 0,
      'data_20' => 0,
    ]);

    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $indikatortujuan,                                    
      'message' => 'Data Realisasi Indikator Program berhasil disimpan.',
    ], 200);
  }   
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_UPDATE');

    $indikatortujuan = RPJMDRealisasiIndikatorModel::find($id);

    if(is_null($indikatortujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Realisasi Indikator Program dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $rules = [        
        'data_2' => 'required|numeric',      
        'data_3' => 'required|numeric',      
        'data_4' => 'required|numeric',      
        'data_5' => 'required|numeric',      
        'data_6' => 'required|numeric',      
        'data_7' => 'required|numeric',      
      ];

     
      $this->validate($request, $rules); 

      $indikatortujuan->data_2 = $request->input('data_2');
      $indikatortujuan->data_3 = $request->input('data_3');
      $indikatortujuan->data_4 = $request->input('data_4');
      $indikatortujuan->data_5 = $request->input('data_5');
      $indikatortujuan->data_6 = $request->input('data_6');
      $indikatortujuan->data_7 = $request->input('data_7');      
      
      $indikatortujuan->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $indikatortujuan,                                    
        'message' => 'Data realisasi indikator tujuan berhasil disimpan.'
      ], 200); 
    }
  }  
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_DESTROY');

    $indikatortujuan = RPJMDRealisasiIndikatorModel::find($id);

    if(is_null($indikatortujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Realisasi Indikator Program dengan dengan ($id) gagal diperoleh"]
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
        'message' => "Data Realisasi Indikator Program dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}