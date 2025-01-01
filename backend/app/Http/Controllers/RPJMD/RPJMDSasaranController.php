<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\RPJMD\RPJMDTujuanModel;
use App\Models\RPJMD\RPJMDSasaranModel;

use Ramsey\Uuid\Uuid;

class RPJMDSasaranController extends Controller 
{
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {                
    $this->hasPermissionTo('RPJMD-SASARAN_BROWSE');
    
    $this->validate($request, [      
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $totalRecords = RPJMDSasaranModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdSasaranID');
    
    $data = RPJMDSasaranModel::select(\DB::raw('
      tmRpjmdSasaran.*,
      CONCAT(c.Kd_RpjmdMisi,".",b.Kd_RpjmdTujuan,".",tmRpjmdSasaran.Kd_RpjmdSasaran) AS kode_sasaran
    '))
    ->join('tmRpjmdTujuan AS b', 'b.RpjmdTujuanID', 'tmRpjmdSasaran.RpjmdTujuanID')
    ->join('tmRpjmdMisi AS c', 'c.RpjmdMisiID', 'b.RpjmdMisiID')    
    ->where('tmRpjmdSasaran.PeriodeRPJMDID', $PeriodeRPJMDID);
    
    if($request->filled('offset'))
    {
      $this->validate($request, [              
        'offset' => 'required|numeric',      
      ]);

      $offset = $request->input('offset');
      $data = $data->offset($offset);
    }

    if($request->filled('limit'))
    {
      $this->validate($request, [              
        'limit' => 'required|numeric|gt:0',   
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
      $data = $data->where('Nm_RpjmdSasaran', 'LIKE', "%$search%")
      ->orWhere('Kd_RpjmdSasaran', $search)
      ->orWhere('RpjmdSasaranID', $search);
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $data->get(),
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data Sasaran berhasil diperoleh'
    ], 200);  
  }
  public function indikator(Request $request, $id)  
  {
    $daftar_indikator = \DB::table('tmRpjmdRelasiIndikator AS a')->select(\DB::raw('
      a.RpjmdRelasiIndikatorID,
      b.IndikatorKinerjaID,
      b.NamaIndikator,
      b.Satuan,
      b.Operasi,
      data_1,
      data_2,
      data_3,
      data_4,
      data_5,
      data_6,
      data_7,
      data_8,
      data_9,
      data_10,
      data_11,
      data_12,
      data_13,
      data_14,
      data_15,
      data_16,
      a.created_at,
      a.updated_at
    '))
    ->join('tmRPJMDIndikatorKinerja AS b', 'a.IndikatorKinerjaID', 'b.IndikatorKinerjaID')
    ->where('RpjmdCascadingID', $id)
    ->get();

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => $daftar_indikator,
      'message' => 'Fetch data Indikator Sasaran berhasil diperoleh'
    ], 200); 
  }
  public function indikatorsasaran(Request $request)
  {                
    $this->hasPermissionTo('RPJMD-SASARAN_BROWSE');
    
    $this->validate($request, [      
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $totalRecords = RPJMDSasaranModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdSasaranID');
    
    $data = RPJMDSasaranModel::select(\DB::raw('
      tmRpjmdSasaran.*,
      CONCAT(c.Kd_RpjmdMisi,".",b.Kd_RpjmdTujuan,".",tmRpjmdSasaran.Kd_RpjmdSasaran) AS kode_sasaran,
      "{}" AS indikator
    '))
    ->join('tmRpjmdTujuan AS b', 'b.RpjmdTujuanID', 'tmRpjmdSasaran.RpjmdTujuanID')
    ->join('tmRpjmdMisi AS c', 'c.RpjmdMisiID', 'b.RpjmdMisiID')
    ->where('tmRpjmdSasaran.PeriodeRPJMDID', $PeriodeRPJMDID);
    
    if($request->filled('offset'))
    {
      $this->validate($request, [              
        'offset' => 'required|numeric',      
      ]);

      $offset = $request->input('offset');
      $data = $data->offset($offset);
    }

    if($request->filled('limit'))
    {
      $this->validate($request, [              
        'limit' => 'required|numeric|gt:0',   
      ]);

      $limit = $request->input('limit');
      $data = $data->limit($limit);
    }

    $indikatorkinerja = $data
    ->orderBy('kode_sasaran', 'asc')
    ->get()
    ->transform(function($item, $key) 
    {
      $item->indikator = \DB::table('tmRpjmdRelasiIndikator AS a')->select(\DB::raw('
        a.RpjmdRelasiIndikatorID,
        b.IndikatorKinerjaID,
        b.NamaIndikator,
        b.Satuan,
        b.Operasi,
        data_1,
        data_2,
        data_3,
        data_4,
        data_5,
        data_6,
        data_7,
        data_8,
        data_9,
        data_10,
        data_11,
        data_12,
        data_13,
        data_14,
        data_15,
        data_16,
        a.created_at,
        a.updated_at
      '))
      ->join('tmRPJMDIndikatorKinerja AS b', 'a.IndikatorKinerjaID', 'b.IndikatorKinerjaID')
      ->where('RpjmdCascadingID', $item->RpjmdSasaranID)
      ->get();

      return $item;
    });

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $indikatorkinerja,
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data Indikator Sasaran berhasil diperoleh'
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
    $this->hasPermissionTo('RPJMD-SASARAN_STORE');

    $this->validate($request, [      
      'RpjmdTujuanID' => 'required|exists:tmRpjmdTujuan,RpjmdTujuanID',      
      'Kd_RpjmdSasaran' => 'required',      
      'Nm_RpjmdSasaran' => 'required',      
    ]);         

    $sasaran = RPJMDTujuanModel::find($request->input('RpjmdTujuanID'));

    $sasaran = RPJMDSasaranModel::create([
      'RpjmdSasaranID'=> Uuid::uuid4()->toString(),
      'PeriodeRPJMDID' => $sasaran->PeriodeRPJMDID,
      'RpjmdTujuanID' => $request->input('RpjmdTujuanID'),      
      'Kd_RpjmdSasaran' => $request->input('Kd_RpjmdSasaran'),
      'Nm_RpjmdSasaran' => $request->input('Nm_RpjmdSasaran'),      
    ]);        
    
    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $sasaran,                                    
      'message' => 'Data sasaran berhasil disimpan.'
    ], 200); 	
  }
  public function show(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-SASARAN_SHOW');

    $sasaran = RPJMDSasaranModel::find($id);

    if(is_null($sasaran))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Sasaran dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $payload = $sasaran;
      $payload->tujuan;

      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',
        'payload' => $sasaran,                                    
        'message' => 'Data Sasaran berhasil diperoleh.'
      ], 200); 
    }
  }
  public function strategi(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-SASARAN_SHOW');

    $sasaran = RPJMDSasaranModel::find($id);

    if(is_null($sasaran))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Sasaran dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $data = $sasaran->strategi();

      $totalRecords = $data->count('RpjmdStrategiID');

      if($request->filled('offset'))
      {
        $this->validate($request, [              
          'offset' => 'required|numeric',      
        ]);

        $offset = $request->input('offset');
        $data = $data->offset($offset);
      }

      if($request->filled('limit'))
      {
        $this->validate($request, [              
          'limit' => 'required|numeric|gt:0',   
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
        $data = $data->where('Nm_RpjmdStrategi', 'LIKE', "%$search%")
        ->orWhere('Kd_RpjmdStrategi', $search);
      }

      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',
        'payload' => [
          'data' => $data->get(),
          'totalRecords' => $totalRecords,
        ],
        'message' => 'Data strategi berhasil diperoleh.'
      ], 200); 
    }
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
    $this->hasPermissionTo('RPJMD-SASARAN_UPDATE');

    $sasaran = RPJMDSasaranModel::find($id);

    if(is_null($sasaran))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Sasaran dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {

      $this->validate($request, [              
        'Kd_RpjmdSasaran' => 'required',      
        'Nm_RpjmdSasaran' => 'required',      
      ]);   

      $sasaran->Kd_RpjmdSasaran = $request->input('Kd_RpjmdSasaran');
      $sasaran->Nm_RpjmdSasaran = $request->input('Nm_RpjmdSasaran');
      $sasaran->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $sasaran,                                    
        'message' => 'Data sasaran berhasil disimpan.'
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
    $this->hasPermissionTo('RPJMD-SASARAN_DESTROY');

    $sasaran = RPJMDSasaranModel::find($id);

    if(is_null($sasaran))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Sasaran dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    // else if($sasaran->arahkebijakan->count('RpjmdArahKebijakanID') > 0)
    // {
    //   return Response()->json([
    //     'status' => 0,
    //     'pid' => 'fetchdata',
    //     'message' => ["RPJMD Sasaran dengan dengan ($id) gagal dihapus karena masih terhubung ke Arah Kebijakan"]
    //   ], 422); 
    // }
    else
    {
      $sasaran->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data RPJMD Sasaran dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}