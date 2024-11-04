<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDRelasiIndikatorModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class RPJMDRelationsIndikatorSasaranController extends Controller
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
      'IndikatorKinerjaID' => 'required|exists:tmRPJMDIndikatorKinerja,IndikatorKinerjaID',      
      'RpjmdCascadingID' => 'required|exists:tmRpjmdSasaran,RpjmdSasaranID',      
      'data_1' => 'required|numeric',      
      'data_2' => 'required|numeric',      
      'data_3' => 'required|numeric',      
      'data_4' => 'required|numeric',      
      'data_5' => 'required|numeric',      
      'data_6' => 'required|numeric',      
      'data_7' => 'required|numeric',      
      'data_8' => 'required|numeric',
    ];

    if($Operasi == 'RANGE')
    {
      $rules['data_9'] = 'required|numeric';
      $rules['data_10'] = 'required|numeric';
      $rules['data_11'] = 'required|numeric';
      $rules['data_12'] = 'required|numeric';
      $rules['data_13'] = 'required|numeric';
      $rules['data_14'] = 'required|numeric';
      $rules['data_15'] = 'required|numeric';

      $this->validate($request, $rules); 

      $indikatorsasaran = RPJMDRelasiIndikatorModel::create([
        'RpjmdRelasiIndikatorID' => Uuid::uuid4()->toString(),
        'IndikatorKinerjaID' => $request->input('IndikatorKinerjaID'),
        'RpjmdCascadingID' => $request->input('RpjmdCascadingID'),
        'PeriodeRPJMDID' => $request->input('PeriodeRPJMDID'),      
        'TipeCascading' => 'sasaran',      
        'data_1' => $request->input('data_1'),    
        'data_2' => $request->input('data_2'),    
        'data_3' => $request->input('data_3'),    
        'data_4' => $request->input('data_4'),    
        'data_5' => $request->input('data_5'),    
        'data_6' => $request->input('data_6'),    
        'data_7' => $request->input('data_7'),    
        'data_8' => $request->input('data_8'),    
        'data_9' => $request->input('data_9'),    
        'data_10' => $request->input('data_10'),    
        'data_11' => $request->input('data_11'),    
        'data_12' => $request->input('data_12'),    
        'data_13' => $request->input('data_13'),    
        'data_14' => $request->input('data_14'),            
        'data_15' => $request->input('data_15'),            
        'data_16' => 0,                            
        'data_17' => 0,
        'data_18' => 0,
        'data_19' => 0,
        'data_20' => 0,
      ]);
    }
    else
    {
      $this->validate($request, $rules); 

      $indikatorsasaran = RPJMDRelasiIndikatorModel::create([
        'RpjmdRelasiIndikatorID' => Uuid::uuid4()->toString(),
        'IndikatorKinerjaID' => $request->input('IndikatorKinerjaID'),
        'RpjmdCascadingID' => $request->input('RpjmdCascadingID'),
        'PeriodeRPJMDID' => $request->input('PeriodeRPJMDID'),      
        'TipeCascading' => 'sasaran',      
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
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $indikatorsasaran,                                    
      'message' => 'Data Indikator Sasaran berhasil disimpan.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-SASARAN_UPDATE');

    $indikatorsasaran = RPJMDRelasiIndikatorModel::find($id);

    if(is_null($indikatorsasaran))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Indikator Sasaran dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $this->validate($request, [
        'Operasi' => 'required|in:MAX,MIN,RANGE'
      ]);
  
      $Operasi = $request->input('Operasi');
      
      
      $rules = [
        'data_1' => 'required|numeric',      
        'data_2' => 'required|numeric',      
        'data_3' => 'required|numeric',      
        'data_4' => 'required|numeric',      
        'data_5' => 'required|numeric',      
        'data_6' => 'required|numeric',      
        'data_7' => 'required|numeric',      
        'data_8' => 'required|numeric',
      ];

      if($Operasi == 'RANGE')
      {
        $rules['data_9'] = 'required|numeric';
        $rules['data_10'] = 'required|numeric';
        $rules['data_11'] = 'required|numeric';
        $rules['data_12'] = 'required|numeric';
        $rules['data_13'] = 'required|numeric';
        $rules['data_14'] = 'required|numeric';
        $rules['data_15'] = 'required|numeric';

        $this->validate($request, $rules); 

        $indikatorsasaran->data_1 = $request->input('data_1');
        $indikatorsasaran->data_2 = $request->input('data_2');
        $indikatorsasaran->data_3 = $request->input('data_3');
        $indikatorsasaran->data_4 = $request->input('data_4');
        $indikatorsasaran->data_5 = $request->input('data_5');
        $indikatorsasaran->data_6 = $request->input('data_6');
        $indikatorsasaran->data_7 = $request->input('data_7');
        $indikatorsasaran->data_8 = $request->input('data_8');
        $indikatorsasaran->data_9 = $request->input('data_9');
        $indikatorsasaran->data_10 = $request->input('data_10');
        $indikatorsasaran->data_11 = $request->input('data_11');
        $indikatorsasaran->data_12 = $request->input('data_12');
        $indikatorsasaran->data_13 = $request->input('data_13');
        $indikatorsasaran->data_14 = $request->input('data_14');
        $indikatorsasaran->data_15 = $request->input('data_15');
      }
      else
      {
        $this->validate($request, $rules); 

        $indikatorsasaran->data_1 = $request->input('data_1');
        $indikatorsasaran->data_2 = $request->input('data_2');
        $indikatorsasaran->data_3 = $request->input('data_3');
        $indikatorsasaran->data_4 = $request->input('data_4');
        $indikatorsasaran->data_5 = $request->input('data_5');
        $indikatorsasaran->data_6 = $request->input('data_6');
        $indikatorsasaran->data_7 = $request->input('data_7');
        $indikatorsasaran->data_8 = $request->input('data_8');
      }
      $indikatorsasaran->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $indikatorsasaran,                                    
        'message' => 'Data indikator sasaran berhasil disimpan.'
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

    $indikatorsasaran = RPJMDRelasiIndikatorModel::find($id);

    if(is_null($indikatorsasaran))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Indikator Sasaran dengan dengan ($id) gagal diperoleh"]
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
        'message' => "Data Indikator Sasaran dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}