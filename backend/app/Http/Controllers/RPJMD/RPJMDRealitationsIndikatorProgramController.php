<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDRealisasiIndikatorModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class RPJMDRealitationsIndikatorProgramController extends Controller
{
  /**
   * mendapatkan daftar seluruh indikator
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_BROWSE');
  }
  public function store(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_STORE');

    $this->validate($request, [
      'Operasi' => 'required|in:MAX,MIN,RANGE'
    ]);

    $Operasi = $request->input('Operasi');
    
    $rules = [      
      'RpjmdRelasiIndikatorID' => 'required|exists:tmRpjmdRelasiIndikator,RpjmdRelasiIndikatorID',            
      'RpjmdCascadingID' => 'required|exists:tmProgram,PrgID',            
      'IndikatorKinerjaID' => 'required|exists:tmRPJMDIndikatorKinerja,IndikatorKinerjaID',      
      'data_2' => 'required|numeric',      
      'data_3' => 'required|numeric',      
      'data_4' => 'required|numeric',      
      'data_5' => 'required|numeric',      
      'data_6' => 'required|numeric',      
      'data_7' => 'required|numeric',   
    ];
    $this->validate($request, $rules); 

    $indikatorprogram = RPJMDRealisasiIndikatorModel::create([
      'RpjmdRealisasiIndikatorID' => Uuid::uuid4()->toString(),
      'RpjmdRelasiIndikatorID' => $request->input('RpjmdRelasiIndikatorID'),      
      'RpjmdCascadingID' => $request->input('RpjmdCascadingID'),
      'IndikatorKinerjaID' => $request->input('IndikatorKinerjaID'),
      'PeriodeRPJMDID' => $request->input('PeriodeRPJMDID'),      
      'TipeCascading' => 'program',      
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
      'payload' => $indikatorprogram,                                    
      'message' => 'Data Realisasi Indikator Program berhasil disimpan.',
    ], 200);
  }
  public function storepagu(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_STORE');
    
    $rules = [
      'RpjmdCascadingID' => 'required|exists:tmProgram,PrgID',      
      'data_1' => 'required|numeric',      
      'data_2' => 'required|numeric',      
      'data_3' => 'required|numeric',      
      'data_4' => 'required|numeric',      
      'data_5' => 'required|numeric',      
      'data_6' => 'required|numeric',      
      'data_7' => 'required|numeric',            
    ];

    $this->validate($request, $rules); 

    $paguprogram = RPJMDRealisasiIndikatorModel::create([
      'RpjmdRelasiIndikatorID' => Uuid::uuid4()->toString(),
      'IndikatorKinerjaID' => null,
      'RpjmdCascadingID' => $request->input('RpjmdCascadingID'),
      'PeriodeRPJMDID' => $request->input('PeriodeRPJMDID'),      
      'TipeCascading' => 'program',      
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
      'payload' => $paguprogram,                                    
      'message' => 'Data Pagu Program berhasil disimpan.'
    ], 200);
  }
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_UPDATE');

    $indikatorprogram = RPJMDRealisasiIndikatorModel::find($id);

    if(is_null($indikatorprogram))
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

      $indikatorprogram->data_2 = $request->input('data_2');
      $indikatorprogram->data_3 = $request->input('data_3');
      $indikatorprogram->data_4 = $request->input('data_4');
      $indikatorprogram->data_5 = $request->input('data_5');
      $indikatorprogram->data_6 = $request->input('data_6');
      $indikatorprogram->data_7 = $request->input('data_7');      
      
      $indikatorprogram->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $indikatorprogram,                                    
        'message' => 'Data realisasi indikator program berhasil disimpan.'
      ], 200); 
    }
  }
  public function updatepagu(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_UPDATE');

    $indikatorprogram = RPJMDRealisasiIndikatorModel::find($id);

    if(is_null($indikatorprogram))
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

      $indikatorprogram->data_2 = $request->input('data_2');
      $indikatorprogram->data_3 = $request->input('data_3');
      $indikatorprogram->data_4 = $request->input('data_4');
      $indikatorprogram->data_5 = $request->input('data_5');
      $indikatorprogram->data_6 = $request->input('data_6');
      $indikatorprogram->data_7 = $request->input('data_7');
    
      $indikatorprogram->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $indikatorprogram,                                    
        'message' => 'Data realisasi indikator program berhasil disimpan.'
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
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_DESTROY');

    $indikatorprogram = RPJMDRealisasiIndikatorModel::find($id);

    if(is_null($indikatorprogram))
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
      $indikatorprogram->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data Realisasi Indikator Program dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}