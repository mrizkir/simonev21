<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDArahKebijakanModel;
use App\Models\RPJMD\RPJMDRelasiArahKebijakanProgramModel;
use App\Models\RPJMD\RPJMDReportArahKebijakanModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class RPJMDRelationsArahKebijakanProgramController extends Controller
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
    
    $totalRecords = RPJMDRelasiArahKebijakanProgramModel::join('tmRpjmdArahKebijakan AS b', 'b.RpjmdArahKebijakanID', 'tmRpjmdRelasiArahKebijakanProgram.RpjmdArahKebijakanID')
    ->where('b.PeriodeRPJMDID', $PeriodeRPJMDID)
    ->count('ArahKebijakanProgramID');

    $data = RPJMDRelasiArahKebijakanProgramModel::from('tmRpjmdRelasiArahKebijakanProgram AS a')->select(\DB::raw("
      a.`ArahKebijakanProgramID`,
      a.`PrgID`,
      f.BidangID,
      g.`Kd_Urusan`,
      f.`Kd_Bidang`,			 
      d.`Kd_Program`,
      CASE 
        WHEN f.`UrsID` IS NOT NULL OR f.`BidangID` IS NOT NULL THEN
          CONCAT(g.`Kd_Urusan`,'.',f.`Kd_Bidang`,'.',d.`Kd_Program`)
        ELSE
          CONCAT('X.', 'XX.',d.`Kd_Program`)
      END AS kode_program,                                        
      COALESCE(g.`Nm_Urusan`,'SEMUA URUSAN') AS Nm_Urusan,
      COALESCE(f.`Nm_Bidang`,'SEMUA BIDANG URUSAN') AS Nm_Bidang,
      d.`Nm_Program`,
      CASE 
        WHEN f.`UrsID` IS NOT NULL OR f.`BidangID` IS NOT NULL THEN
          CONCAT('[',g.`Kd_Urusan`,'.',f.`Kd_Bidang`,'.',d.`Kd_Program`,'] ',d.Nm_Program)
        ELSE
          CONCAT('[X.', 'XX.',d.`Kd_Program`,'] ',d.Nm_Program)
      END AS nama_program,
      a.Kd_ProgramRPJMD,
      a.Nm_ProgramRPJMD,
      b.Nm_RpjmdArahKebijakan,
      c.Nm_RpjmdStrategi,
      c1.Nm_RpjmdSasaran,
      d.`Jns`,
      d.`TA`,                                        
      d.`Descr`,
      d.`Locked`,
      a.`created_at`,
      a.`updated_at`
    "))
    ->join('tmRpjmdArahKebijakan AS b', 'b.RpjmdArahKebijakanID', 'a.RpjmdArahKebijakanID')
    ->join('tmRpjmdStrategi AS c', 'c.RpjmdStrategiID', 'b.RpjmdStrategiID')
    ->join('tmRpjmdSasaran AS c1', 'c.RpjmdSasaranID', 'c1.RpjmdSasaranID')
    ->join('tmProgram AS d', 'd.PrgID', 'a.PrgID')
    ->leftJoin('tmUrusanProgram AS e', 'e.PrgID', 'd.PrgID')
    ->leftJoin('tmBidangUrusan AS f', 'f.BidangID' ,'e.BidangID')
    ->leftJoin('tmUrusan AS g', 'g.UrsID', 'f.UrsID')
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
      'message' => 'Fetch data Program Arah Kebijakan berhasil diperoleh'
    ], 200);  
  }
  public function store(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_STORE');

    $rules = [      
      'RpjmdArahKebijakanID' => 'required|exists:tmRpjmdArahKebijakan,RpjmdArahKebijakanID',      
      'PrgID' => 'required|exists:tmProgram,PrgID',            
      'Kd_ProgramRPJMD' => 'required',            
      'Nm_ProgramRPJMD' => 'required',            
    ];

    $this->validate($request, $rules);
    
    $arah_kebijakan = RPJMDArahKebijakanModel::find($request->input('RpjmdArahKebijakanID'));

    $arahkebijakanprogram = RPJMDRelasiArahKebijakanProgramModel::create([
      'ArahKebijakanProgramID' => Uuid::uuid4()->toString(),
      'PeriodeRPJMDID' => $arah_kebijakan->PeriodeRPJMDID,
      'PrgID' => $request->input('PrgID'),
      'RpjmdArahKebijakanID' => $request->input('RpjmdArahKebijakanID'),   
      'Kd_ProgramRPJMD' => $request->input('Kd_ProgramRPJMD'),   
      'Nm_ProgramRPJMD' => $request->input('Nm_ProgramRPJMD'),   
    ]);

    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $arahkebijakanprogram,                                    
      'message' => 'Data Program Arah Kebijakan berhasil disimpan.'
    ], 200);
  }
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_UPDATE');

    $arahkebijakanprogram = RPJMDRelasiArahKebijakanProgramModel::find($id);

    if(is_null($arahkebijakanprogram))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Arah Kebijakan Program dengan dengan ($id) gagal diperoleh"]
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
      
      $arahkebijakanprogram->PrgID = $request->input('PrgID');
      $arahkebijakanprogram->Kd_ProgramRPJMD = $request->input('Kd_ProgramRPJMD');
      $arahkebijakanprogram->Nm_ProgramRPJMD = $request->input('Nm_ProgramRPJMD');
      $arahkebijakanprogram->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $arahkebijakanprogram,                                    
        'message' => 'Data Arah Kebijakan Program berhasil disimpan.'
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
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_DESTROY');

    $arahkebijakanprogram = RPJMDRelasiArahKebijakanProgramModel::find($id);

    if(is_null($arahkebijakanprogram))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Arah Kebijakan Program dengan dengan ($id) gagal diperoleh"]
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
      $arahkebijakanprogram->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data Arah Kebijakan Program dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
  public function printcascading(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-PROGRAM_BROWSE');

    $this->validate($request, [      
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    
    $data_report = [
      'PeriodeRPJMDID' => $PeriodeRPJMDID,
    ];

    $report = new RPJMDReportArahKebijakanModel($data_report);
    $report->printCascadingProgram();
    $generate_date = date('Y-m-d_H_m_s');
    return $report->download("arah_kebijakan_cascading_$generate_date.xlsx");
  } 
}