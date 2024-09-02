<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDRelasiStrategiProgramModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class RPJMDRelationsStrategiProgramController extends Controller
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

    $rules = [      
      'RpjmdStrategiID' => 'required|exists:tmRpjmdStrategi,RpjmdStrategiID',      
      'PrgID' => 'required|exists:tmProgram,PrgID',            
    ];

    $this->validate($request, $rules);

    $strategiprogram = RPJMDRelasiStrategiProgramModel::create([
      'StrategiProgramID' => Uuid::uuid4()->toString(),
      'PrgID' => $request->input('PrgID'),
      'RpjmdStrategiID' => $request->input('RpjmdStrategiID'),   
    ]);

    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload'=>$strategiprogram,                                    
      'message' => 'Data Program Strategi berhasil disimpan.'
    ], 200);
  }
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_UPDATE');

    $strategiprogram = RPJMDRelasiStrategiProgramModel::find($id);

    if(is_null($strategiprogram))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Strategi Program dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      
      $strategiprogram->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $strategiprogram,                                    
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
  public function destroy(Request $request,$id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_DESTROY');

    $strategiprogram = RPJMDRelasiStrategiProgramModel::find($id);

    if(is_null($strategiprogram))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Strategi Program dengan dengan ($id) gagal diperoleh"]
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
      $strategiprogram->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data Strategi Program dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}