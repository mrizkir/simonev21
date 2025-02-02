<?php

namespace App\Http\Controllers\RPJMD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\RPJMD\RPJMDStrategiModel;
use App\Models\RPJMD\RPJMDArahKebijakanModel;
use App\Models\RPJMD\RPJMDReportArahKebijakanModel;

use Ramsey\Uuid\Uuid;

class RPJMDArahKebijakanController extends Controller 
{    
  
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {                
    $this->hasPermissionTo('RPJMD-ARAH-KEBIJAKAN_BROWSE');
    
    $this->validate($request, [      
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $totalRecords = RPJMDArahKebijakanModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdArahKebijakanID');
    
    $data = RPJMDArahKebijakanModel::select(\DB::raw('
      tmRpjmdArahKebijakan.*,
      CONCAT(e.Kd_RpjmdMisi,".",d.Kd_RpjmdTujuan,".",c.Kd_RpjmdSasaran,".",b.Kd_RpjmdStrategi,".",tmRpjmdArahKebijakan.Kd_RpjmdArahKebijakan) AS kode_arah_kebijakan,
      0 AS jumlah_program
    '))
    ->join('tmRpjmdStrategi AS b', 'b.RpjmdStrategiID', 'tmRpjmdArahKebijakan.RpjmdStrategiID')
    ->join('tmRpjmdSasaran AS c', 'c.RpjmdSasaranID', 'b.RpjmdSasaranID')
    ->join('tmRpjmdTujuan AS d', 'd.RpjmdTujuanID', 'c.RpjmdTujuanID')
    ->join('tmRpjmdMisi AS e', 'e.RpjmdMisiID', 'd.RpjmdMisiID')    
    ->where('tmRpjmdArahKebijakan.PeriodeRPJMDID', $PeriodeRPJMDID);
    
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
      $data = $data->where('Nm_RpjmdArahKebijakan', 'LIKE', "%$search%")
      ->orWhere('Kd_RpjmdArahKebijakan', $search);
    }

    $daftar_arah_kebijakan = $data->get()->transform(function($item, $key) {
      $item->jumlah_program = \DB::table('tmRpjmdRelasiArahKebijakanProgram')          
      ->where('RpjmdArahKebijakanID', $item->RpjmdArahKebijakanID)
      ->count('PrgID');

      return $item;
    });

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $daftar_arah_kebijakan,
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data Arah Kebijakan berhasil diperoleh'
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
    $this->hasPermissionTo('RPJMD-ARAH-KEBIJAKAN_STORE');

    $this->validate($request, [      
      'RpjmdStrategiID' => 'required|exists:tmRpjmdStrategi,RpjmdStrategiID',      
      'Kd_RpjmdArahKebijakan' => 'required',      
      'Nm_RpjmdArahKebijakan' => 'required',                 
    ]);         

    $strategi = RPJMDStrategiModel::find($request->input('RpjmdStrategiID'));

    $arah_kebijakan = RPJMDArahKebijakanModel::create([
      'RpjmdArahKebijakanID'=> Uuid::uuid4()->toString(),
      'PeriodeRPJMDID' => $strategi->PeriodeRPJMDID,
      'RpjmdStrategiID' => $request->input('RpjmdStrategiID'),      
      'Kd_RpjmdArahKebijakan' => $request->input('Kd_RpjmdArahKebijakan'),
      'Nm_RpjmdArahKebijakan' => $request->input('Nm_RpjmdArahKebijakan')     
    ]);        
    
    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $arah_kebijakan,                                    
      'message' => 'Data arah kebijakan berhasil disimpan.'
    ], 200); 	
  }
  public function show(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-ARAH-KEBIJAKAN_SHOW');

    $arah_kebijakan = RPJMDArahKebijakanModel::select(\DB::raw('
      tmRpjmdArahKebijakan.*,
      b.Nm_RpjmdStrategi
    '))
    ->join('tmRpjmdStrategi AS b', 'b.RpjmdStrategiID', 'b.RpjmdStrategiID')
    ->where('RpjmdArahKebijakanID', $id)
    ->first();

    if(is_null($arah_kebijakan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Arah Kebijakan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $payload = $arah_kebijakan;
      $payload->sasaran;
      
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',
        'payload' => $arah_kebijakan,                                    
        'message' => 'Data Arah Kebijakan berhasil diperoleh.'
      ], 200); 
    }
  }

  public function program(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-ARAH-KEBIJAKAN_SHOW');

    $arah_kebijakan = RPJMDArahKebijakanModel::find($id);

    if(is_null($arah_kebijakan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Arah Kebijakan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $data = $arah_kebijakan->program();

      $totalRecords = $data->count('ArahKebijakanProgramID');

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
        $data = $data->where('Nm_RpjmdArahKebijakan', 'LIKE', "%$search%")
        ->orWhere('Kd_RpjmdArahKebijakan', $search);
      }

      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',
        'payload' => [
          'data' => $data->get(),
          'totalRecords' => $totalRecords,
        ],
        'message' => 'Data program arah kebijakan berhasil diperoleh.'
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
    $this->hasPermissionTo('RPJMD-ARAH-KEBIJAKAN_UPDATE');

    $arah_kebijakan = RPJMDArahKebijakanModel::find($id);

    if(is_null($arah_kebijakan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Arah Kebijakan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $this->validate($request, [              
        'Kd_RpjmdArahKebijakan' => 'required',      
        'Nm_RpjmdArahKebijakan' => 'required',       
      ]);         

      $arah_kebijakan->Kd_RpjmdArahKebijakan = $request->input('Kd_RpjmdArahKebijakan');
      $arah_kebijakan->Nm_RpjmdArahKebijakan = $request->input('Nm_RpjmdArahKebijakan');
      $arah_kebijakan->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $arah_kebijakan,                                    
        'message' => 'Data arah kebijakan berhasil disimpan.'
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
    $this->hasPermissionTo('RPJMD-ARAH-KEBIJAKAN_DESTROY');

    $arah_kebijakan = RPJMDArahKebijakanModel::find($id);

    if(is_null($arah_kebijakan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Arah Kebijakan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else if($arah_kebijakan->program->count('ArahKebijakanProgramID') > 0)
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["RPJMD Arah Kebijakan dengan dengan ($id) gagal dihapus karena masih terhubung ke Program Arah Kebijakan"]
      ], 422); 
    }
    else
    {
      $arah_kebijakan->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data RPJMD Arah Kebijakan dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
  public function printcascading(Request $request)
  {
    $this->hasPermissionTo('RPJMD-ARAH-KEBIJAKAN_BROWSE');
    
    $this->validate($request, [      
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $data_report = [
      'PeriodeRPJMDID' => $PeriodeRPJMDID,
    ];

    $report = new RPJMDReportArahKebijakanModel($data_report);
    $report->printCascading();
    $generate_date = date('Y-m-d_H_m_s');
    return $report->download("arah_kebijakan_cascading_$generate_date.xlsx");
  }
}