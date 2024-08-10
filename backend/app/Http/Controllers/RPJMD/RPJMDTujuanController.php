<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\RPJMD\RPJMDMisiModel;
use App\Models\RPJMD\RPJMDTujuanModel;

use Ramsey\Uuid\Uuid;

class RPJMDMisiController extends Controller 
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
    
    $totalRecords = RPJMDTujuanModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdMisiID');
    
    $data = RPJMDTujuanModel::select(\DB::raw('*'))
    ->where('PeriodeRPJMDID', $PeriodeRPJMDID);
    
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
    $this->hasPermissionTo('RPJMD-TUJUAN_STORE');

    $this->validate($request, [      
      'RpjmdMisiID'=>'required|exists:tmRPJMDVisi,RpjmdMisiID',      
      'Kd_RpjmdTujuan'=>'required',      
      'Nm_RpjmdTujuan'=>'required',      
    ]);         

    $misi = RPJMDMisiModel::find($request->input('RpjmdMisiID'));

    $tujuan = RPJMDTujuanModel::create([
      'RpjmdMisiID'=> Uuid::uuid4()->toString(),
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
        'message' => ["RPJMD Misi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',
        'payload' => $tujuan,                                    
        'message' => 'Data Misi berhasil diperoleh.'
      ], 200); 
    }
  }
  public function tujuan(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-TUJUAN_SHOW');

    $tujuan = RPJMDTujuanModel::find($id);

    if(is_null($tujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Misi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $data = $tujuan->tujuan();

      $totalRecords = $data->count('RpjmdTujuanID');

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
        'message' => 'Data tujuan berhasil diperoleh.'
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
        'message' => ["RPJMD Misi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {

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
        'message' => ["RPJMD Misi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    // else if($misi->tujuan->count('RpjmdMisiID') > 0)
    // {
    //   return Response()->json([
    //     'status' => 0,
    //     'pid' => 'fetchdata',
    //     'message' => ["RPJMD Misi dengan dengan ($id) gagal dihapus karena masih terhubung ke Misi"]
    //   ], 422); 
    // }
    else
    {
      $tujuan->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data RPJMD Misi dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}