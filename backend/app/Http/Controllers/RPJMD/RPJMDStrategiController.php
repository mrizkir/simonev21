<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\RPJMD\RPJMDSasaranModel;
use App\Models\RPJMD\RPJMDStrategiModel;

use Ramsey\Uuid\Uuid;

class RPJMDStrategiController extends Controller 
{    
  
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {                
    $this->hasPermissionTo('RPJMD-STRATEGI_BROWSE');
    
    $this->validate($request, [      
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $totalRecords = RPJMDStrategiModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdSasaranID');
    
    $data = RPJMDStrategiModel::select(\DB::raw('
      tmRpjmdStrategi.*,
      CONCAT(d.Kd_RpjmdMisi,".",c.Kd_RpjmdTujuan,".",b.Kd_RpjmdSasaran,".",tmRpjmdStrategi.Kd_RpjmdStrategi) AS kode_strategi,
      0 AS jumlah_program
    '))
    ->join('tmRpjmdSasaran AS b', 'b.RpjmdSasaranID', 'tmRpjmdStrategi.RpjmdSasaranID')
    ->join('tmRpjmdTujuan AS c', 'b.RpjmdTujuanID', 'c.RpjmdTujuanID')
    ->join('tmRpjmdMisi AS d', 'c.RpjmdMisiID', 'd.RpjmdMisiID')    
    ->where('tmRpjmdStrategi.PeriodeRPJMDID', $PeriodeRPJMDID);
    
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

    $daftar_strategi = $data->get()->transform(function($item, $key) {
      $item->jumlah_program = \DB::table('tmRpjmdRelasiStrategiProgram')          
      ->where('RpjmdStrategiID', $item->RpjmdStrategiID)
      ->count('PrgID');

      return $item;
    });

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $daftar_strategi,
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data Strategi berhasil diperoleh'
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
    $this->hasPermissionTo('RPJMD-STRATEGI_STORE');

    $this->validate($request, [      
      'RpjmdSasaranID' => 'required|exists:tmRpjmdSasaran,RpjmdSasaranID',      
      'Kd_RpjmdStrategi' => 'required',      
      'Nm_RpjmdStrategi' => 'required',      
      'Nm_RpjmdArahKebijakan' => 'required',      
    ]);         

    $strategi = RPJMDSasaranModel::find($request->input('RpjmdSasaranID'));

    $strategi = RPJMDStrategiModel::create([
      'RpjmdStrategiID'=> Uuid::uuid4()->toString(),
      'PeriodeRPJMDID' => $strategi->PeriodeRPJMDID,
      'RpjmdSasaranID' => $request->input('RpjmdSasaranID'),      
      'Kd_RpjmdStrategi' => $request->input('Kd_RpjmdStrategi'),
      'Nm_RpjmdStrategi' => $request->input('Nm_RpjmdStrategi'),      
      'Nm_RpjmdArahKebijakan' => $request->input('Nm_RpjmdArahKebijakan'),      
    ]);        
    
    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $strategi,                                    
      'message' => 'Data strategi berhasil disimpan.'
    ], 200); 	
  }
  public function show(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-STRATEGI_SHOW');

    $strategi = RPJMDStrategiModel::select(\DB::raw('
      tmRpjmdStrategi.*,
      b.Nm_RpjmdSasaran
    '))
    ->join('tmRpjmdSasaran AS b', 'b.RpjmdSasaranID', 'tmRpjmdStrategi.RpjmdSasaranID')    
    ->where('tmRpjmdStrategi.RpjmdStrategiID', $id)
    ->first();

    if(is_null($strategi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Strategi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',
        'payload' => $strategi,                                    
        'message' => 'Data Strategi berhasil diperoleh.'
      ], 200); 
    }
  }
  public function program(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-STRATEGI_SHOW');

    $strategi = RPJMDStrategiModel::find($id);

    if(is_null($strategi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Strategi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $data = $strategi->program();

      $totalRecords = $data->count('StrategiProgramID');

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
        'message' => 'Data arah kebijakan berhasil diperoleh.'
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
    $this->hasPermissionTo('RPJMD-STRATEGI_UPDATE');

    $strategi = RPJMDStrategiModel::find($id);

    if(is_null($strategi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Strategi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $this->validate($request, [              
        'Kd_RpjmdStrategi' => 'required',      
        'Nm_RpjmdStrategi' => 'required',      
        'Nm_RpjmdArahKebijakan' => 'required',      
      ]);         

      $strategi->Kd_RpjmdStrategi = $request->input('Kd_RpjmdStrategi');
      $strategi->Nm_RpjmdStrategi = $request->input('Nm_RpjmdStrategi');
      $strategi->Nm_RpjmdArahKebijakan = $request->input('Nm_RpjmdArahKebijakan');
      $strategi->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $strategi,                                    
        'message' => 'Data strategi berhasil disimpan.'
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
    $this->hasPermissionTo('RPJMD-STRATEGI_DESTROY');

    $strategi = RPJMDStrategiModel::find($id);

    if(is_null($strategi))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Strategi dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else if($strategi->program->count('StrategiProgramID') > 0)
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Strategi dengan dengan ($id) gagal dihapus karena masih terhubung ke Program Strategi"]
      ], 422); 
    }
    else
    {
      $strategi->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data RPJMD Strategi dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}