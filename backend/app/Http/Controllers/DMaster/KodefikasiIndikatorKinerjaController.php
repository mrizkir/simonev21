<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use App\Models\DMaster\IndikatorKinerjaModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class KodefikasiIndikatorKinerjaController extends Controller
{    
  /**
   * mendapatkan daftar seluruh ASN
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-INDIKATOR-KINERJA_BROWSE');
    
    $totalRecords = IndikatorKinerjaModel::count('IndikatorKinerjaID');
    
    $data = IndikatorKinerjaModel::select(\DB::raw('*'));
    
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

    if($request->filled('search'))
    {
      $search = $request->input('search');
      $data = $data->where('NamaIndikator', 'LIKE', "%$search%");
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $data->get(),
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data indikator kinerja berhasil diperoleh'
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-INDIKATOR-KINERJA_STORE');

    $this->validate($request, [      
      'NamaIndikator'=>'required',
      'is_iku'=>'required|in:0,1',
      'is_ikk'=>'required|in:0,1',
    ]);
        
    $indikator = IndikatorKinerjaModel::create ([
      'IndikatorKinerjaID'=> Uuid::uuid4()->toString(),
      'NamaIndikator' => $request->input('NamaIndikator'),
      'is_iku' => $request->input('is_iku'),
      'is_ikk' => $request->input('is_ikk'),
    ]);  
    
    return Response()->json([
      'status'=>1,
      'pid'=>'store',
      'payload'=>$indikator,                                    
      'message'=>'Data Indikator Kinerja berhasil disimpan.'
    ], 200); 		
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {        
    $this->hasPermissionTo('DMASTER-KODEFIKASI-INDIKATOR-KINERJA_DESTROY');

    $indikator = IndikatorKinerjaModel::find($id);

    if(is_null($indikator))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Indikator Kinerja dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
    
      $this->validate($request, [      
        'NamaIndikator'=>'required',
        'is_iku'=>'required|in:0,1',
        'is_ikk'=>'required|in:0,1',
      ]);
      
      $indikator->NamaIndikator = $request->input('NamaIndikator');
      $indikator->is_iku = $request->input('is_iku');
      $indikator->is_ikk = $request->input('is_ikk');
      $indikator->save();

      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => [
          'data' => $indikator,                                    
        ],
        'message' => 'Data Indikator Kinerja berhasil disimpan.'
      ], 200);
    }    
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $uuid
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request,$id)
  {   
    $this->hasPermissionTo('DMASTER-KODEFIKASI-INDIKATOR-KINERJA_DESTROY');

    $indikator = IndikatorKinerjaModel::find($id);

    if(is_null($indikator))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Indikator Kinerja dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $indikator->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data Indikator Kinerja dengan ID ($id) berhasil dihapus"
      ], 200);
    }
  }
}
