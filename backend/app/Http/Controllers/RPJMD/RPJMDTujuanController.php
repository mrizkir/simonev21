<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\RPJMD\RPJMDMisiModel;
use App\Models\RPJMD\RPJMDTujuanModel;
use App\Models\RPJMD\RPJMDRelasiIndikatorModel;

use Ramsey\Uuid\Uuid;

class RPJMDTujuanController extends Controller 
{    
  
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {                
    $this->hasPermissionTo('RPJMD-TUJUAN_BROWSE');
    
    $this->validate($request, [      
      'PeriodeRPJMDID'=>'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $totalRecords = RPJMDTujuanModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdTujuanID');
    
    $data = RPJMDTujuanModel::select(\DB::raw('
      tmRpjmdTujuan.*,
      CONCAT(b.Kd_RpjmdMisi,".",tmRpjmdTujuan.Kd_RpjmdTujuan) AS kode_tujuan
    '))
    ->join('tmRpjmdMisi AS b', 'b.RpjmdMisiID', 'tmRpjmdTujuan.RpjmdMisiID')
    ->where('tmRpjmdTujuan.PeriodeRPJMDID', $PeriodeRPJMDID);
    
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
      $data = $data->where('Nm_RpjmdTujuan', 'LIKE', "%$search%")
      ->orWhere('Kd_RpjmdTujuan', $search);
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $data->get(),
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data Tujuan berhasil diperoleh'
    ], 200);  
  }    
  public function indikatortujuan(Request $request)
  {                
    $this->hasPermissionTo('RPJMD-TUJUAN_BROWSE');
    
    $this->validate($request, [      
      'PeriodeRPJMDID'=>'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $totalRecords = RPJMDTujuanModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdMisiID');
    
    $data = RPJMDTujuanModel::select(\DB::raw('
      tmRpjmdTujuan.*,
      CONCAT(b.Kd_RpjmdMisi,".",tmRpjmdTujuan.Kd_RpjmdTujuan) AS kode_tujuan,
      "{}" AS indikator
    '))
    ->join('tmRpjmdMisi AS b', 'b.RpjmdMisiID', 'tmRpjmdTujuan.RpjmdMisiID')
    ->where('tmRpjmdTujuan.PeriodeRPJMDID', $PeriodeRPJMDID);
    
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

    $indikatorkinerja = $data
    ->orderBy('kode_tujuan', 'asc')
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
      ->where('RpjmdCascadingID', $item->RpjmdTujuanID)
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
      'message' => 'Fetch data Indikator Tujuan berhasil diperoleh'
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
    $this->hasPermissionTo('RPJMD-TUJUAN_STORE');

    $this->validate($request, [      
      'RpjmdMisiID'=>'required|exists:tmRpjmdMisi,RpjmdMisiID',      
      'Kd_RpjmdTujuan'=>'required',      
      'Nm_RpjmdTujuan'=>'required',      
    ]);         

    $misi = RPJMDMisiModel::find($request->input('RpjmdMisiID'));

    $tujuan = RPJMDTujuanModel::create([
      'RpjmdTujuanID'=> Uuid::uuid4()->toString(),
      'PeriodeRPJMDID' => $misi->PeriodeRPJMDID,
      'RpjmdMisiID' => $request->input('RpjmdMisiID'),      
      'Kd_RpjmdTujuan' => $request->input('Kd_RpjmdTujuan'),
      'Nm_RpjmdTujuan' => $request->input('Nm_RpjmdTujuan'),      
    ]);        
    
    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $tujuan,                                    
      'message' => 'Data tujuan berhasil disimpan.'
    ], 200); 	
  }
  public function show(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-TUJUAN_SHOW');

    $tujuan = RPJMDTujuanModel::find($id);

    if(is_null($tujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Tujuan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $payload = $tujuan;
      $payload->misi;

      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',
        'payload' => $payload,                                    
        'message' => 'Data Tujuan berhasil diperoleh.'
      ], 200); 
    }
  }
  public function sasaran(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-TUJUAN_SHOW');

    $tujuan = RPJMDTujuanModel::find($id);

    if(is_null($tujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Tujuan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $data = $tujuan->sasaran();

      $totalRecords = $data->count('b.RpjmdTujuanID');

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
        $data = $data->where('Nm_RpjmdTujuan', 'LIKE', "%$search%")
        ->orWhere('Kd_RpjmdTujuan', $search);
      }

      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',
        'payload' => [
          'data' => $data->get(),
          'totalRecords' => $totalRecords,
        ],
        'message' => 'Data sasaran berhasil diperoleh.'
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
    $this->hasPermissionTo('RPJMD-TUJUAN_UPDATE');

    $tujuan = RPJMDTujuanModel::find($id);

    if(is_null($tujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Tujuan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {

      $this->validate($request, [              
        'Kd_RpjmdTujuan'=>'required',      
        'Nm_RpjmdTujuan'=>'required',      
      ]);         

      $tujuan->Kd_RpjmdTujuan = $request->input('Kd_RpjmdTujuan');
      $tujuan->Nm_RpjmdTujuan = $request->input('Nm_RpjmdTujuan');
      $tujuan->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $tujuan,                                    
        'message' => 'Data tujuan berhasil disimpan.'
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
    $this->hasPermissionTo('RPJMD-TUJUAN_DESTROY');

    $tujuan = RPJMDTujuanModel::find($id);

    if(is_null($tujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Tujuan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else if($tujuan->sasaran->count('RpjmdSasaranID') > 0)
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Tujuan dengan dengan ($id) gagal dihapus karena masih terhubung ke Sasaran"]
      ], 422); 
    }
    else
    {
      $tujuan->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data RPJMD Tujuan dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}