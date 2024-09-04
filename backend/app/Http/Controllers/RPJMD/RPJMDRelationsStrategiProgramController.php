<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDRelasiStrategiProgramModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class RPJMDRelationsStrategiProgramController extends Controller
{
  /**
   * mendapatkan daftar seluruh indikator
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_BROWSE');

    $this->validate($request, [      
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $totalRecords = RPJMDRelasiStrategiProgramModel::join('tmRpjmdStrategi AS b', 'b.RpjmdStrategiID', 'tmRpjmdRelasiStrategiProgram.RpjmdStrategiID')
    ->where('b.PeriodeRPJMDID', $PeriodeRPJMDID)
    ->count('StrategiProgramID');

    $data = RPJMDRelasiStrategiProgramModel::select(\DB::raw("
      tmRpjmdRelasiStrategiProgram.`StrategiProgramID`,
      tmRpjmdRelasiStrategiProgram.`PrgID`,
      e.BidangID,
      f.`Kd_Urusan`,
      e.`Kd_Bidang`,			 
      c.`Kd_Program`,
      CASE 
        WHEN e.`UrsID` IS NOT NULL OR e.`BidangID` IS NOT NULL THEN
          CONCAT(f.`Kd_Urusan`,'.',e.`Kd_Bidang`,'.',c.`Kd_Program`)
        ELSE
          CONCAT('X.','XX.',c.`Kd_Program`)
      END AS kode_program,                                        
      COALESCE(f.`Nm_Urusan`,'SEMUA URUSAN') AS Nm_Urusan,
      COALESCE(e.`Nm_Bidang`,'SEMUA BIDANG URUSAN') AS Nm_Bidang,
      c.`Nm_Program`,
      CASE 
        WHEN e.`UrsID` IS NOT NULL OR e.`BidangID` IS NOT NULL THEN
          CONCAT('[',f.`Kd_Urusan`,'.',e.`Kd_Bidang`,'.',c.`Kd_Program`,'] ',c.Nm_Program)
        ELSE
          CONCAT('[X.','XX.',c.`Kd_Program`,'] ',c.Nm_Program)
      END AS nama_program,
      tmRpjmdRelasiStrategiProgram.Kd_ProgramRPJMD,
      tmRpjmdRelasiStrategiProgram.Nm_ProgramRPJMD,
      c.`Jns`,
      c.`TA`,                                        
      c.`Descr`,
      c.`Locked`,
      tmRpjmdRelasiStrategiProgram.`created_at`,
      tmRpjmdRelasiStrategiProgram.`updated_at`
    "))
    ->join('tmRpjmdStrategi AS b', 'b.RpjmdStrategiID', 'tmRpjmdRelasiStrategiProgram.RpjmdStrategiID')
    ->join('tmProgram AS c', 'c.PrgID', 'tmRpjmdRelasiStrategiProgram.PrgID')
    ->leftJoin('tmUrusanProgram AS d', 'c.PrgID', 'd.PrgID')
    ->leftJoin('tmBidangUrusan AS e', 'e.BidangID' ,'d.BidangID')
    ->leftJoin('tmUrusan AS f','e.UrsID','f.UrsID')
    ->where('b.PeriodeRPJMDID', $PeriodeRPJMDID); 
    
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
      $data = $data->where('Nm_Program', 'LIKE', "%$search%")
      ->orWhere('Kd_Program', $search);
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $data->get(),
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data Program Strategi berhasil diperoleh'
    ], 200);  
  }
  public function store(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_STORE');

    $rules = [      
      'RpjmdStrategiID' => 'required|exists:tmRpjmdStrategi,RpjmdStrategiID',      
      'PrgID' => 'required|exists:tmProgram,PrgID',            
      'Kd_ProgramRPJMD' => 'required',            
      'Nm_ProgramRPJMD' => 'required',            
    ];

    $this->validate($request, $rules);

    $strategiprogram = RPJMDRelasiStrategiProgramModel::create([
      'StrategiProgramID' => Uuid::uuid4()->toString(),
      'PrgID' => $request->input('PrgID'),
      'RpjmdStrategiID' => $request->input('RpjmdStrategiID'),   
      'Kd_ProgramRPJMD' => $request->input('Kd_ProgramRPJMD'),   
      'Nm_ProgramRPJMD' => $request->input('Nm_ProgramRPJMD'),   
    ]);

    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $strategiprogram,                                    
      'message' => 'Data Program Strategi berhasil disimpan.'
    ], 200);
  }
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_UPDATE');

    $strategiprogram = RPJMDRelasiStrategiProgramModel::find($id);

    if(is_null($strategiprogram))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Strategi Program dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $rules = [              
        'PrgID' => 'required|exists:tmProgram,PrgID',            
        'Kd_ProgramRPJMD' => 'required',            
        'Nm_ProgramRPJMD' => 'required',            
      ];

      $this->validate($request, $rules);
      
      $strategiprogram->PrgID = $request->input('PrgID');
      $strategiprogram->Kd_ProgramRPJMD = $request->input('Kd_ProgramRPJMD');
      $strategiprogram->Nm_ProgramRPJMD = $request->input('Nm_ProgramRPJMD');
      $strategiprogram->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $strategiprogram,                                    
        'message' => 'Data indikator sasaran berhasil disimpan.'
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
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_DESTROY');

    $strategiprogram = RPJMDRelasiStrategiProgramModel::find($id);

    if(is_null($strategiprogram))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Strategi Program dengan dengan ($id) gagal diperoleh"]
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
      $strategiprogram->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data Strategi Program dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}