<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDPeriodeModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class RPJMDPeriodeController extends Controller
{    
  /**
   * mendapatkan daftar seluruh ASN
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $totalRecords = RPJMDPeriodeModel::count('PeriodeRPJMDID');
    
    $data = RPJMDPeriodeModel::select(\DB::raw('*'));
    
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
      'message' => 'Fetch data periode rpjmd berhasil diperoleh'
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
    

    $periode = RPJMDPeriodeModel::create ([
      'PeriodeRPJMDID'=> Uuid::uuid4()->toString(),
      'NamaPeriode' => $request->input('NamaPeriode'),
      'TA_AWAL' => $request->input('TA_AWAL'),
      'TA_AKHIR' => $request->input('TA_AKHIR'),
    ]);  
    
    return Response()->json([
      'status'=>1,
      'pid'=>'store',
      'payload'=>$periode,                                    
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

    $periode = RPJMDPeriodeModel::find($id);

    if(is_null($periode))
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
      
      $periode->NamaPeriode = $request->input('NamaPeriode');
      $periode->TA_AWAL = $request->input('TA_AWAL');
      $periode->TA_AKHIR = $request->input('TA_AKHIR');
      $periode->save();

      \DB::table('tmRPJMDIndikatorKinerja')
      ->where('PeriodeRPJMDID', $periode->PeriodeRPJMDID)
      ->update([
        'TA_AWAL' => $periode->TA_AWAL,
        'TA_AKHIR' => $periode->TA_AKHIR,
      ]);

      \DB::table('tmRPJMDVisi')
      ->where('PeriodeRPJMDID', $periode->PeriodeRPJMDID)
      ->update([
        'TA_AWAL' => $periode->TA_AWAL,
        'TA_AKHIR' => $periode->TA_AKHIR,
      ]);

      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => [
          'data' => $periode,                                    
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

    $periode = RPJMDPeriodeModel::find($id);

    if(is_null($periode))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Periode RPJMD dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else if($periode->indikatorprogram->count('PeriodeRPJMDID') > 0)
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Periode RPJMD tidak bisa dihapus karena memiliki record di indikator program"]
      ], 422); 
    }
    else
    {
      $periode->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data Periode RPJMD dengan ID ($id) berhasil dihapus"
      ], 200);
    }
  }
}
