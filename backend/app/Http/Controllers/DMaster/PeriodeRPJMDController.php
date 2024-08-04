<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use App\Models\DMaster\PeriodeRPJMDModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class PeriodeRPJMDController extends Controller
{    
  /**
   * mendapatkan daftar seluruh ASN
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $totalRecords = PeriodeRPJMDModel::count('PeriodeRPJMDID');
    
    $data = PeriodeRPJMDModel::select(\DB::raw('*'));
    
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
      $data = $data->where('NamaPeriode', 'LIKE', "%$search%");
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
    $this->hasPermissionTo('DMASTER-TA_STORE');

    $this->validate($request, [      
      'NamaPeriode'=>'required',      
      'TA_AWAL'=>'required|digits:4|integer|min:2015',
      'TA_AKHIR'=>'required|digits:4|integer|gt:TA_AWAL',
    ]);
    

    $indikator = PeriodeRPJMDModel::create ([
      'PeriodeRPJMDID'=> Uuid::uuid4()->toString(),
      'NamaPeriode' => $request->input('NamaPeriode'),
      'TA_AWAL' => $request->input('TA_AWAL'),
      'TA_AKHIR' => $request->input('TA_AKHIR'),
    ]);  
    
    return Response()->json([
      'status'=>1,
      'pid'=>'store',
      'payload'=>$indikator,                                    
      'message'=>'Data Periode RPJMD berhasil disimpan.'
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
    $this->hasPermissionTo('DMASTER-TA_DESTROY');

    $indikator = PeriodeRPJMDModel::find($id);

    if(is_null($indikator))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Periode RPJMD dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
    
      $this->validate($request, [      
        'NamaPeriode'=>'required',
        'TA_AWAL'=>'required|digits:4|integer|min:2015',
        'TA_AKHIR'=>'required|digits:4|integer|gt:TA_AWAL',
      ]);
      
      $indikator->NamaPeriode = $request->input('NamaPeriode');
      $indikator->TA_AWAL = $request->input('TA_AWAL');
      $indikator->TA_AKHIR = $request->input('TA_AKHIR');
      $indikator->save();

      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => [
          'data' => $indikator,                                    
        ],
        'message' => 'Data Periode RPJMD berhasil disimpan.'
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
    $this->hasPermissionTo('DMASTER-TA_DESTROY');

    $indikator = PeriodeRPJMDModel::find($id);

    if(is_null($indikator))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Periode RPJMD dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $indikator->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data Periode RPJMD dengan ID ($id) berhasil dihapus"
      ], 200);
    }
  }
}
