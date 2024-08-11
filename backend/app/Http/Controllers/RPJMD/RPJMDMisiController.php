<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\RPJMD\RPJMDVisiModel;
use App\Models\RPJMD\RPJMDMisiModel;

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
    $this->hasPermissionTo('RPJMD-MISI_BROWSE');
    
    $this->validate($request, [      
      'PeriodeRPJMDID'=>'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $totalRecords = RPJMDMisiModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdVisiID');
    
    $data = RPJMDMisiModel::select(\DB::raw('*'))
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
      $data = $data->where('Nm_RpjmdMisi', 'LIKE', "%$search%")
      ->orWhere('Kd_RpjmdMisi', $search);
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $data->get(),
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data Misi berhasil diperoleh'
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
    $this->hasPermissionTo('RPJMD-MISI_STORE');

    $this->validate($request, [      
      'RpjmdVisiID'=>'required|exists:tmRPJMDVisi,RpjmdVisiID',      
      'Kd_RpjmdMisi'=>'required',      
      'Nm_RpjmdMisi'=>'required',      
    ]);         

    $visi = RPJMDVisiModel::find($request->input('RpjmdVisiID'));

    $misi = RPJMDMisiModel::create([
      'RpjmdMisiID'=> Uuid::uuid4()->toString(),
      'PeriodeRPJMDID' => $visi->PeriodeRPJMDID,
      'RpjmdVisiID' => $request->input('RpjmdVisiID'),      
      'Kd_RpjmdMisi' => $request->input('Kd_RpjmdMisi'),
      'Nm_RpjmdMisi' => $request->input('Nm_RpjmdMisi'),      
    ]);        
    
    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $misi,                                    
      'message' => 'Data misi berhasil disimpan.'
    ], 200); 	
  }
  public function show(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-MISI_SHOW');

    $misi = RPJMDMisiModel::find($id);

    if(is_null($misi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Misi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $payload = $misi;
      $payload->visi;

      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',
        'payload' => $misi,
        'message' => 'Data Misi berhasil diperoleh.'
      ], 200); 
    }
  }
  public function tujuan(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-MISI_SHOW');

    $misi = RPJMDMisiModel::find($id);

    if(is_null($misi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Misi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $data = $misi->tujuan();

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
    $this->hasPermissionTo('RPJMD-MISI_UPDATE');

    $misi = RPJMDMisiModel::find($id);

    if(is_null($misi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Misi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {

      $misi->Kd_RpjmdMisi = $request->input('Kd_RpjmdMisi');
      $misi->Nm_RpjmdMisi = $request->input('Nm_RpjmdMisi');
      $misi->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $misi,                                    
        'message' => 'Data misi berhasil disimpan.'
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
    $this->hasPermissionTo('RPJMD-MISI_DESTROY');

    $misi = RPJMDMisiModel::find($id);

    if(is_null($misi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Misi dengan dengan ($id) gagal diperoleh"]
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
      $misi->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data RPJMD Misi dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}