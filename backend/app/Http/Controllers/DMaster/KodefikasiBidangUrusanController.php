<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\KodefikasiBidangUrusanModel;
use App\Models\DMaster\KodefikasiProgramModel;
use App\Models\RPJMD\RPJMDPeriodeModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class KodefikasiBidangUrusanController extends Controller {              
  /**
   * get all kodefikasi urusan
   *
   * @return \Illuminate\Http\Response
   */
  public function index (Request $request)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_BROWSE');
    
    $default_role = $this->getDefaultRole();

    $this->validate($request, [        
      'TA' => 'required',
      'pid' => 'required|in:simonev,evrpjmd',
    ]);    
    
    $ta = $request->input('TA');
    $pid = $request->input('pid');

    $kodefikasibidangurusan = KodefikasiBidangUrusanModel::select(\DB::raw("
      `BidangID`,
      `tmBidangUrusan`.`UrsID`,
      `Kd_Bidang`,
      `Nm_Bidang`,
      CONCAT(`tmUrusan`.`Kd_Urusan`,'.',`tmBidangUrusan`.`Kd_Bidang`) AS `kode_bidang`,
      CONCAT('[',`tmUrusan`.`Kd_Urusan`,'.',`tmBidangUrusan`.`Kd_Bidang`,'] ',`Nm_Bidang`) AS `bidangurusan`,
      `tmBidangUrusan`.`Descr`,
      `tmBidangUrusan`.`TA`,
      `tmBidangUrusan`.`created_at`,
      `tmBidangUrusan`.`updated_at`
    "))
    ->join('tmUrusan', 'tmBidangUrusan.UrsID', 'tmUrusan.UrsID')
    ->orderBy('kode_bidang', 'ASC')                                    
    ->where('tmBidangUrusan.TA', $ta);

    if($pid == 'evrpjmd' && $default_role == 'superadmin')
    {

    }

    $kodefikasibidangurusan = $kodefikasibidangurusan->get();

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'kodefikasibidangurusan' => $kodefikasibidangurusan,
      'message' => 'Fetch data kodefikasi urusan berhasil.'
    ], 200);
  }
  /**
   * get all kodefikasi program
   *
   * @return \Illuminate\Http\Response
   */
  public function program (Request $request, $id)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-PROGRAM_BROWSE');

    $this->validate($request, [        
      'TA' => 'required',            
    ]);  

    if ($id == 'all')
    {
      $ta = $request->input('TA');
      $program = KodefikasiProgramModel::select(\DB::raw('
        `PrgID`,
        CONCAT("[X.XX.",`Kd_Program`,"] ",`Nm_Program`) AS nama_program	
      '))
      ->where('TA', $ta)
      ->where('Jns',0)
      ->orderBy('Kd_Program', 'ASC')                                    
      ->get();
    }
    else
    {
      $program = KodefikasiProgramModel::select(\DB::raw("
        tmProgram.`PrgID`,
        CASE 
          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
          CONCAT('[',tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`,'] ',tmProgram.Nm_Program)
          ELSE
          CONCAT('[X.', 'XX.',tmProgram.`Kd_Program`,'] ',tmProgram.Nm_Program)
        END AS nama_program
      "))
      ->leftJoin('tmUrusanProgram', 'tmProgram.PrgID', 'tmUrusanProgram.PrgID')
      ->leftJoin('tmBidangUrusan', 'tmBidangUrusan.BidangID', 'tmUrusanProgram.BidangID')
      ->leftJoin('tmUrusan', 'tmBidangUrusan.UrsID', 'tmUrusan.UrsID')
      ->orderBy('tmUrusan.Kd_Urusan', 'ASC')                                    
      ->orderBy('tmBidangUrusan.Kd_Bidang', 'ASC')                                    
      ->orderBy('tmProgram.Kd_Program', 'ASC')                                    
      ->where('tmBidangUrusan.BidangID', $id)
      ->get();
    }
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'program' => $program,
      'message' => 'Fetch data kodefikasi program rka berhasil.'
    ], 200);
  }
  /**
   * get all kodefikasi program rpjmd
   *
   * @return \Illuminate\Http\Response
   */
  public function programrpjmd (Request $request, $id)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-PROGRAM_BROWSE');

    $this->validate($request, [        
      'pid' => 'required|in:relasiprogram,realisasiprogram,realisasiprogram2', //realisasiprogram,realisasiprogram2[tanpa program semua urusan]
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',
    ]);  
    
    $periode = RPJMDPeriodeModel::find($request->input('PeriodeRPJMDID'));
    $pid = $request->input('pid');

    $ta = $periode->TA_AWAL;

    if ($id == 'all')
    { 
      $data = KodefikasiProgramModel::select(\DB::raw("
        `PrgID`,
        CONCAT('[X.XX.',`Kd_Program`,'] ',`Nm_Program`) AS nama_program,
        '{}' AS indikator
      "))
      ->where('TA', $ta)
      ->where('Jns', 0);

      $totalRecords = 0;

    }
    else
    {
      $data = KodefikasiProgramModel::from('tmProgram AS a')->select(\DB::raw("
        a.PrgID,
        b.BidangID,
        COALESCE(d.`Kd_Urusan`, 'X') AS `Kd_Urusan`, 
        COALESCE(c.`Kd_Bidang`, 'X') AS `Kd_Bidang`,
        a.`Kd_Program`,
        CASE 
          WHEN c.`UrsID` IS NOT NULL OR c.`BidangID` IS NOT NULL THEN 
            CONCAT(d.`Kd_Urusan`,'.',c.`Kd_Bidang`,'.',a.`Kd_Program`) 
          ELSE 
            CONCAT('X.', 'XX.',a.`Kd_Program`) 
        END AS kode_program, 
        COALESCE(d.`Nm_Urusan`,'SEMUA URUSAN') AS Nm_Urusan, 
        COALESCE(c.`Nm_Bidang`,'SEMUA BIDANG URUSAN') AS Nm_Bidang,
        a.Nm_Program,
        CASE 
          WHEN c.`UrsID` IS NOT NULL OR c.`BidangID` IS NOT NULL THEN 
            CONCAT('[',d.`Kd_Urusan`,'.',c.`Kd_Bidang`,'.',a.`Kd_Program`,'] ',a.Nm_Program) 
          ELSE 
            CONCAT('[X.', 'XX.',a.`Kd_Program`,'] ',a.Nm_Program) 
        END AS nama_program,
        a.`Jns`, 
        a.`TA`, 
        a.`Descr`, 
        a.`Locked`, 
        a.`created_at`, 
        a.`updated_at`, 
        '{}' AS pagu,
        '{}' AS indikator
      "))
      ->leftJoin('tmUrusanProgram AS b', 'a.PrgID', 'b.PrgID')
      ->leftJoin('tmBidangUrusan AS c', 'b.BidangID', 'c.BidangID')
      ->leftJoin('tmUrusan AS d', 'c.UrsID', 'd.UrsID')
      ->whereRaw("(b.BidangID = '$id' OR c.BidangID IS NULL)")
      ->where('a.TA', $ta)
      ->orderBy('d.Kd_Urusan', 'asc')
      ->orderBy('c.Kd_Bidang', 'asc')
      ->orderBy('a.Kd_Program', 'asc');

      $totalRecords = 0;      
    }
    
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

    if($pid == 'realisasiprogram2') //tanpa semua urusan
    {
      $data = $data->whereNotNull('c.UrsID');
    }

    $program = $data
    ->get()
    ->transform(function($item, $key) use ($pid)
    {
      switch($pid)
      {
        case 'relasiprogram':
          $item->pagu = \DB::table('tmRpjmdRelasiIndikator')->select(\DB::raw('
            RpjmdRelasiIndikatorID,
            IndikatorKinerjaID,
            "-" AS Operasi,
            data_1,
            data_2,
            data_3,
            data_4,
            data_5,
            data_6,
            data_7,
            data_8,        
            created_at,
            updated_at
          '))
          ->whereNull('IndikatorKinerjaID')
          ->where('RpjmdCascadingID', $item->PrgID)
          ->get();

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
            a.created_at,
            a.updated_at
          '))
          ->join('tmRPJMDIndikatorKinerja AS b', 'a.IndikatorKinerjaID', 'b.IndikatorKinerjaID')
          ->where('RpjmdCascadingID', $item->PrgID)
          ->get();
        break;
        case 'realisasiprogram':
        case 'realisasiprogram2':
          $item->pagu = \DB::table('tmRpjmdRelasiIndikator AS a')
          ->select(\DB::raw('
            b.RpjmdRealisasiIndikatorID,              
            a.data_1 AS target_1,
            a.data_2 AS target_2,
            a.data_3 AS target_3,
            a.data_4 AS target_4,
            a.data_5 AS target_5,
            a.data_6 AS target_6,
            a.data_7 AS target_7,
            b.data_2 AS realisasi_2,
            b.data_3 AS realisasi_3,
            b.data_4 AS realisasi_4,
            b.data_5 AS realisasi_5,
            b.data_6 AS realisasi_6,
            b.data_7 AS realisasi_7,
            b.created_at,
            b.updated_at
          '))
          ->join('tmRpjmdRealisasiIndikator AS b', 'a.RpjmdRelasiIndikatorID', 'b.RpjmdRelasiIndikatorID')          
          ->whereNull('a.IndikatorKinerjaID')
          ->where('a.RpjmdCascadingID', $item->PrgID)
          ->get();          

          $item->indikator = \DB::table('tmRpjmdRealisasiIndikator AS a')
          ->select(\DB::raw('
            a.RpjmdRealisasiIndikatorID,
            c.Satuan,
            c.Operasi,
            c.NamaIndikator,
            b.data_2 AS target_2,
            b.data_3 AS target_3,
            b.data_4 AS target_4,
            b.data_5 AS target_5,
            b.data_6 AS target_6,
            b.data_7 AS target_7,
            b.data_8 AS target_8,	
            a.data_2 AS realisasi_2,
            a.data_3 AS realisasi_3,
            a.data_4 AS realisasi_4,
            a.data_5 AS realisasi_5,
            a.data_6 AS realisasi_6,
            a.data_7 AS realisasi_7,
            a.created_at,
            a.updated_at
          '))
          ->join('tmRpjmdRelasiIndikator AS b', 'a.RpjmdRelasiIndikatorID', 'b.RpjmdRelasiIndikatorID')
          ->join('tmRPJMDIndikatorKinerja AS c', 'a.IndikatorKinerjaID', 'c.IndikatorKinerjaID')
          ->where('b.RpjmdCascadingID', $item->PrgID)
          ->get();
        break;
      }
      return $item;
    });    
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $program,
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data program rpjmd berhasil.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function salin(Request $request)
  {       
    $this->validate($request, [            
      'tahun_asal' => 'required|numeric',
      'tahun_tujuan' => 'required|numeric|gt:tahun_asal',
    ]);

    $tahun_asal = $request->input('tahun_asal');
    $tahun_tujuan = $request->input('tahun_tujuan');

    \DB::table('tmBidangUrusan')
    ->where('TA', $tahun_tujuan)
    ->whereRaw('BidangID_Src IS NOT NULL')
    ->delete();

    $str_insert = '
      INSERT INTO `tmBidangUrusan` (
        `BidangID`,
        `UrsID`,
        `Kd_Bidang`,
        `Nm_Bidang`,
        `Descr`,
        `TA`,
        `BidangID_Src`,			
        created_at,
        updated_at
      )		
      SELECT
        uuid() AS id,
        t2.UrsID,
        t1.Kd_Bidang,
        t1.Nm_Bidang,
        "DI IMPOR DARI TAHUN '.$tahun_asal.'" AS `Descr`,
        '.$tahun_tujuan.' AS `TA`,
        t1.BidangID AS BidangID_Src,
        NOW() AS created_at,
        NOW() AS updated_at
      FROM tmBidangUrusan t1
      JOIN tmUrusan t2 ON (t1.UrsID=t2.UrsID_Src)
      WHERE t1.TA='.$tahun_asal.'        
    ';    

    \DB::statement($str_insert); 

    return Response()->json([
      'status' => 1,
      'pid' => 'store',            
      'message'=>"Salin bidang urusan dari tahun anggaran $tahun_asal berhasil."
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_STORE');

    $this->validate($request, [
      'Kd_Bidang'=> [
            Rule::unique('tmBidangUrusan')->where(function($query) use ($request) {
              return $query->where('UrsID', $request->input('UrsID'))
                    ->where('TA', $request->input('TA'));
            }),
            'required',
            'regex:/^[0-9]+$/'],
      'Nm_Bidang' => 'required',
      'TA' => 'required'
    ]);     
      
    $ta = $request->input('TA');
    
    $kodefikasibidangurusan = KodefikasiBidangUrusanModel::create([
      'BidangID' => Uuid::uuid4()->toString(),
      'UrsID' => $request->input('UrsID'),            
      'Kd_Bidang' => $request->input('Kd_Bidang'),
      'Nm_Bidang' => strtoupper($request->input('Nm_Bidang')),
      'Descr' => $request->input('Descr'),
      'TA' => $ta,
    ]);

    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'kodefikasibidangurusan' => $kodefikasibidangurusan,                                    
      'message' => 'Data Kodefikasi Bidang Urusan berhasil disimpan.'
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_UPDATE');

    $kodefikasibidangurusan = KodefikasiBidangUrusanModel::find($id);
    
    if (is_null($kodefikasibidangurusan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',                
        'message' => ["Data Kodefikasi Bidang Urusan ($id) gagal diupdate"]
      ], 422); 
    }
    else
    {
      $this->validate($request, [    
        'Kd_Bidang' => [
          Rule::unique('tmBidangUrusan')->where(function($query) use ($request, $kodefikasibidangurusan) {  
            if ($request->input('Kd_Bidang') == $kodefikasibidangurusan->Kd_Bidang) 
            {
              return $query->where('Kd_Bidang', 'ignore')
                    ->where('TA', $kodefikasibidangurusan->TA);
            }                 
            else
            {
              return $query->where('Kd_Bidang', $request->input('Kd_Bidang'))
                  ->where('UrsID', $kodefikasibidangurusan->UrsID)
                  ->where('TA', $kodefikasibidangurusan->TA);
            }                                                                                    
          }),
          'required',
          'regex:/^[0-9]+$/'
        ],
        'Nm_Bidang' => 'required',
      ]);
      
      
      $kodefikasibidangurusan->Kd_Bidang = $request->input('Kd_Bidang');
      $kodefikasibidangurusan->Nm_Bidang = strtoupper($request->input('Nm_Bidang'));
      $kodefikasibidangurusan->Descr = $request->input('Descr');
      $kodefikasibidangurusan->save();

      return Response()->json([
                  'status' => 1,
                  'pid' => 'update',
                  'kodefikasibidangurusan' => $kodefikasibidangurusan,                                    
                  'message' => 'Data Kodefikasi Bidang Urusan '.$kodefikasibidangurusan->Nm_Bidang.' berhasil diubah.'
                ], 200);
    }
    
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $uuid
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {   
    $this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_DESTROY');

    $kodefikasibidangurusan = KodefikasiBidangUrusanModel::find($id);

    if (is_null($kodefikasibidangurusan))
    {
      return Response()->json([
                  'status' => 0,
                  'pid' => 'destroy',                
                  'message' => ["Data Kodefikasi Bidang Urusan ($id) gagal dihapus"]
                ], 422); 
    }
    else
    {
      
      $result = $kodefikasibidangurusan->delete();

      return Response()->json([
                  'status' => 1,
                  'pid' => 'destroy',                
                  'message'=>"Data Kodefikasi Bidang Urusan dengan ID ($id) berhasil dihapus"
                ], 200);
    }
  }
}