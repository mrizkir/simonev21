<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDRealisasiIndikatorModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class RPJMDRealitationsIndikatorSasaranController extends Controller
{
  /**
   * mendapatkan daftar seluruh indikator
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-SASARAN_BROWSE');
  }
  public function store(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-SASARAN_STORE');

    $this->validate($request, [
      'Operasi' => 'required|in:MAX,MIN,RANGE'
    ]);

    $Operasi = $request->input('Operasi');
    
    $rules = [      
      'RpjmdRelasiIndikatorID' => 'required|exists:tmRpjmdRelasiIndikator,RpjmdRelasiIndikatorID',            
      'RpjmdCascadingID' => 'required|exists:tmRpjmdSasaran,RpjmdSasaranID',            
      'IndikatorKinerjaID' => 'required|exists:tmRPJMDIndikatorKinerja,IndikatorKinerjaID',      
      'data_2' => 'required|numeric',      
      'data_3' => 'required|numeric',      
      'data_4' => 'required|numeric',      
      'data_5' => 'required|numeric',      
      'data_6' => 'required|numeric',      
      'data_7' => 'required|numeric',   
    ];
    $this->validate($request, $rules); 

    $indikatorsasaran = RPJMDRealisasiIndikatorModel::create([
      'RpjmdRealisasiIndikatorID' => Uuid::uuid4()->toString(),
      'RpjmdRelasiIndikatorID' => $request->input('RpjmdRelasiIndikatorID'),      
      'RpjmdCascadingID' => $request->input('RpjmdCascadingID'),
      'IndikatorKinerjaID' => $request->input('IndikatorKinerjaID'),
      'PeriodeRPJMDID' => $request->input('PeriodeRPJMDID'),      
      'TipeCascading' => 'sasaran',      
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
      'payload' => $indikatorsasaran,                                    
      'message' => 'Data Realisasi Indikator Sasaran berhasil disimpan.',
    ], 200);
  }   
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-SASARAN_UPDATE');

    $indikatorsasaran = RPJMDRealisasiIndikatorModel::find($id);

    if(is_null($indikatorsasaran))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Realisasi Indikator Sasaran dengan dengan ($id) gagal diperoleh"]
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

      $indikatorsasaran->data_2 = $request->input('data_2');
      $indikatorsasaran->data_3 = $request->input('data_3');
      $indikatorsasaran->data_4 = $request->input('data_4');
      $indikatorsasaran->data_5 = $request->input('data_5');
      $indikatorsasaran->data_6 = $request->input('data_6');
      $indikatorsasaran->data_7 = $request->input('data_7');      
      
      $indikatorsasaran->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $indikatorsasaran,                                    
        'message' => 'Data realisasi indikator sasaran berhasil disimpan.'
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
    $this->hasPermissionTo('RPJMD-INDIKASI-SASARAN_DESTROY');

    $indikatorsasaran = RPJMDRealisasiIndikatorModel::find($id);

    if(is_null($indikatorsasaran))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Realisasi Indikator Sasaran dengan dengan ($id) gagal diperoleh"]
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
      $indikatorsasaran->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data Realisasi Indikator Sasaran dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}