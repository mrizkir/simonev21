<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDVisiModel;
use App\Models\RPJMD\RPJMDPeriodeModel;

use Ramsey\Uuid\Uuid;

class RPJMDVisiController extends Controller 
{
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('RPJMD-VISI_BROWSE');
    
    $totalRecords = RPJMDVisiModel::count('RpjmdVisiID');
    
    $data = RPJMDVisiModel::select(\DB::raw('*'));
    
    if($request->filled('offset'))
    {
      $this->validate($request, [              
        'offset'=>'required|numeric',      
      ]);

      $offset = $request->input('offset');
      $data = $data->offset($offset);
    }

    if($request->filled('limit'))
    {
      $this->validate($request, [              
        'limit'=>'required|numeric|gt:0',   
      ]);

      $limit = $request->input('limit');
      $data = $data->limit($limit);
    }

    if($request->filled('sortBy'))
    {
      $sortBy = $request->input('sortBy');
      if(is_array($sortBy))
      {
        foreach ($sortBy as $item)
        {
          $data = $data->orderBy($item['key'], $item['order']);
        }
      }
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $data->get(),
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data Visi berhasil diperoleh'
    ], 200);  

  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->hasPermissionTo('RPJMD-VISI_STORE');

    $this->validate($request, [      
      'PeriodeRPJMDID'=>'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',
      'Nm_RpjmdVisi'=>'required',      
    ]);

    $periode = RPJMDPeriodeModel::find($request->input('PeriodeRPJMDID'));

    $rpjmdvisi = RPJMDVisiModel::create([
      'RpjmdVisiID'=> Uuid::uuid4()->toString(),
      'PeriodeRPJMDID' => $request->input('PeriodeRPJMDID'),
      'Nm_RpjmdVisi' => $request->input('Nm_RpjmdVisi'),
      'TA_AWAL' => $periode->TA_AWAL,
      'TA_AKHIR' => $periode->TA_AKHIR,
    ]);        
    
    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $rpjmdvisi,                                    
      'message' => 'Data visi berhasil disimpan.'
    ], 200); 	
  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-VISI_UPDATE');

    $visi = RPJMDVisiModel::find($id);

    if(is_null($visi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Visi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $this->validate($request, [      
        'PeriodeRPJMDID'=>'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',
        'Nm_RpjmdVisi'=>'required',      
      ]);
  
      $periode = RPJMDPeriodeModel::find($request->input('PeriodeRPJMDID'));
  
      $visi->Nm_RpjmdVisi = $request->input('Nm_RpjmdVisi');      
      $visi->TA_Awal = $periode->TA_Awal;
      $visi->TA_Akhir = $periode->TA_Akhir;
      $visi->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $visi,                                    
        'message' => 'Data visi berhasil disimpan.'
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
    $this->hasPermissionTo('RPJMD-VISI_DESTROY');

    $visi = RPJMDVisiModel::find($id);

    if(is_null($visi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Visi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $visi->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data RPJMD Visi dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}