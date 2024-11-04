<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\OrganisasiModel;
use App\Models\System\LockedOPDModel;
use App\Models\Statistik1Model;
use App\Helpers\Helper;

use Ramsey\Uuid\Uuid;

class OrganisasiController extends Controller {     
  
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {                
    $this->hasPermissionTo('DMASTER-OPD_BROWSE');

    $this->validate($request, [            
      'tahun' => 'required',            
    ]);
    
    $tahun = $request->input('tahun');

    if ($this->hasRole(['superadmin', 'bapelitbang']))
    {
      $data = OrganisasiModel::where('TA', $tahun)
        ->orderBy('kode_organisasi','ASC')
        ->get();            
    }       
    else if ($this->hasRole(['opd', 'unitkerja']))
    {
      $daftar_opd = $this->getUserOrgID($tahun);      
      $data = OrganisasiModel::where('TA', $tahun)
        ->whereIn('OrgID', $daftar_opd)
        ->orderBy('kode_organisasi','ASC')
        ->get();
    }
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'opd' => $data,
      'jumlah_apbd' => $data->sum('PaguDana1'),
      'jumlah_apbdp' => $data->sum('PaguDana2'),
      'message' => 'Fetch data opd berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);    
    
  }
  /**
   * load data opd 
   *
   * @return \Illuminate\Http\Response
   */
  public function loadpaguapbdp(Request $request)
  {        
    $this->hasPermissionTo('DMASTER-OPD_STORE');
    
    $this->validate($request, [            
      'tahun' => 'required',            
    ]); 
    $tahun = $request->input('tahun');
    
    $str_statistik_opd1 = "UPDATE 
      `tmOrg`, 	
      (SELECT 
        kode_organisasi,
        SUM(`PaguUraian2`) AS `PaguUraian2` 
      FROM sipd 
      WHERE `TA` = $tahun 
      AND `EntryLevel`=2 
      GROUP BY kode_organisasi
    ) AS level1
    SET 
      `tmOrg`.`PaguDana2`=level1.`PaguUraian2` 
    WHERE 
      level1.kode_organisasi=`tmOrg`.kode_organisasi AND 
      `tmOrg`.`TA` = $tahun    
    ";   
    
    \DB::statement($str_statistik_opd1); 

    $str_update_jumlah_program = "UPDATE `tmOrg`, ( 
      SELECT kode_organisasi, COUNT(kd_prog_gabungan) jumlah_program FROM (
        SELECT * FROM 
          (SELECT kode_organisasi, kd_prog_gabungan FROM sipd WHERE `TA` = $tahun AND `EntryLevel`=2 GROUP BY `kd_prog_gabungan`,kode_organisasi ORDER BY kd_prog_gabungan ASC) AS level1
      ) AS level2 GROUP BY kode_organisasi ORDER BY kode_organisasi
    ) AS level3    
    SET `JumlahProgram2`=level3.jumlah_program 
    WHERE level3.kode_organisasi=`tmOrg`.kode_organisasi
    AND `tmOrg`.`TA` = $tahun";

    \DB::statement($str_update_jumlah_program);     
    
    $str_update_jumlah_kegiatan = "
      UPDATE `tmOrg`, (SELECT kode_organisasi, COUNT(kd_keg_gabung) AS jumlah_kegiatan FROM 
        (
          SELECT 
            DISTINCT(kd_keg_gabung),				
            `kode_organisasi`				
          FROM sipd WHERE `TA` = $tahun AND `EntryLevel`=2
          ORDER BY kd_keg_gabung ASC
        ) AS level1 GROUP BY kd_keg_gabung
      ) AS level2
      SET `JumlahKegiatan2`=level2.jumlah_kegiatan
      WHERE level2.kode_organisasi=`tmOrg`.kode_organisasi
    AND `tmOrg`.`TA` = $tahun";   
    
    \DB::statement($str_update_jumlah_kegiatan);  

    $data = OrganisasiModel::where('TA', $tahun)->get();

    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'opd' => $data,
      'jumlah_apbd' => $data->sum('PaguDana1'),
      'jumlah_apbdp' => $data->sum('PaguDana2'),
      'message' => 'Fetch data opd berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
  }    
  /**
   * STORE the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->hasPermissionTo('DMASTER-OPD_STORE');

    $this->validate($request, [
      'BidangID_1' => 'required|exists:tmBidangUrusan,BidangID',
      'kode_bidang_1' => 'required',
      'Nm_Bidang_1' => 'required',            

      'kode_organisasi' => 'required',
      'Kd_Organisasi' => 'required',
      'Nm_Organisasi' => 'required',
      'Alias_Organisasi' => 'required',
      'Alamat' => 'required|min:5',

      'NamaKepalaSKPD' => 'required|min:5',
      'NIPKepalaSKPD' => 'required|min:5',

      'TA' => 'required|numeric',
    ]);
    
    $BidangID_2 = null;
    $kode_bidang_2 = null;
    $Nm_Bidang_2 = null;

    $BidangID_3 = null;
    $kode_bidang_3 = null;
    $Nm_Bidang_3 = null;

    if ($request->filled('BidangID_2')) {
      $BidangID_2 = $request->input('BidangID_2');
      $kode_bidang_2 = $request->input('kode_bidang_2');
      $Nm_Bidang_2 = $request->input('Nm_Bidang_2');
    }

    if ($request->filled('BidangID_3')) {
      $BidangID_3 = $request->input('BidangID_3');
      $kode_bidang_3 = $request->input('kode_bidang_3');
      $Nm_Bidang_3 = $request->input('Nm_Bidang_3');
    }
    $organisasi = OrganisasiModel::create([
      'OrgID' => Uuid::uuid4()->toString(), 
    
      'BidangID_1' => $request->input('BidangID_1'),         
      'kode_bidang_1' => $request->input('kode_bidang_1'),
      'Nm_Bidang_1' => $request->input('Nm_Bidang_1'),
      
      'BidangID_2' => $BidangID_2,         
      'kode_bidang_2' => $kode_bidang_2,         
      'Nm_Bidang_2' => $Nm_Bidang_2,         

      'BidangID_3' => $BidangID_3,         
      'kode_bidang_3' => $kode_bidang_3,         
      'Nm_Bidang_3' => $Nm_Bidang_3,         

      'kode_organisasi' => $request->input('kode_organisasi'), 
      'Kd_Organisasi' => $request->input('Kd_Organisasi'), 
      'Nm_Organisasi' => $request->input('Nm_Organisasi'), 
      'Alias_Organisasi' => $request->input('Alias_Organisasi'),                
      'Alamat' => $request->input('Alamat'), 
      'NamaKepalaSKPD' => $request->input('NamaKepalaSKPD'), 
      'NIPKepalaSKPD' => $request->input('NIPKepalaSKPD'), 

      'TA' => $request->input('TA'), 
    ]);        
    
    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'opd' => $organisasi,                                    
      'message' => 'Data organisasi '.$organisasi->OrgNm.' berhasil disimpan.'
    ], 200); 
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

    \DB::beginTransaction();

		\DB::table('tmOrg')
		->where('TA', $tahun_tujuan)
		->whereRaw('OrgID_Src IS NOT NULL')
		->delete();

		$str_insert = '
			INSERT INTO `tmOrg` (
				`OrgID`, 
        
        `BidangID_1`,         
        `kode_bidang_1`,         
        `Nm_Bidang_1`,         
        
        `BidangID_2`,         
        `kode_bidang_2`,         
        `Nm_Bidang_2`,         

        `BidangID_3`,         
        `kode_bidang_3`,         
        `Nm_Bidang_3`,         

        `kode_organisasi`, 
        `Kd_Organisasi`, 
        `Nm_Organisasi`, 
        `Alias_Organisasi`,                
        `Alamat`, 
        `NamaKepalaSKPD`, 
        `NIPKepalaSKPD`, 
        `PaguDana1`,
        `PaguDana2`,
        `JumlahProgram1`,
        `JumlahProgram2`,        
        `JumlahKegiatan1`,
        `JumlahKegiatan2`,        
        `JumlahSubKegiatan1`,
        `JumlahSubKegiatan2`,
        `RealisasiKeuangan1`,            
        `RealisasiKeuangan2`,        
        `RealisasiFisik1`,        
        `RealisasiFisik2`,        
        `Descr`, 
        `TA`,
        `OrgID_Src`,
        created_at,
        updated_at
			)		
			SELECT
				uuid() AS id,
				
        t2.`BidangID` AS `BidangID_1`,         
        CONCAT(t3.Kd_Urusan,"-",t2.Kd_Bidang) AS `kode_bidang_1`,         
        t2.`Nm_Bidang` AS `Nm_Bidang_1`,         
        
        t1.`BidangID_2`,         
        t1.`kode_bidang_2`,         
        t1.`Nm_Bidang_2`,         

        t1.`BidangID_3`,         
        t1.`kode_bidang_3`,         
        t1.`Nm_Bidang_3`,         

        t1.`kode_organisasi`, 
        t1.`Kd_Organisasi`, 
        t1.`Nm_Organisasi`, 
        t1.`Alias_Organisasi`,                
        t1.`Alamat`, 
        t1.`NamaKepalaSKPD`, 
        t1.`NIPKepalaSKPD`, 
        0 AS `PaguDana1`,
        0 AS `PaguDana2`,
        0 AS `JumlahProgram1`,
        0 AS `JumlahProgram2`,        
        0 AS `JumlahKegiatan1`,
        0 AS `JumlahKegiatan2`,        
        0 AS `JumlahSubKegiatan1`,
        0 AS `JumlahSubKegiatan2`,
        0 AS `RealisasiKeuangan1`,            
        0 AS `RealisasiKeuangan2`,        
        0 AS `RealisasiFisik1`,        
        0 AS `RealisasiFisik2`,        

				"DI IMPOR DARI TAHUN '.$tahun_asal.'" AS `Descr`,
				'.$tahun_tujuan.' AS `TA`,
				t1.OrgID AS OrgID_Src,
				NOW() AS created_at,
				NOW() AS updated_at
			FROM tmOrg t1
			JOIN tmBidangUrusan t2 ON (t1.BidangID_1=t2.BidangID_Src)
      JOIN tmUrusan t3 ON (t3.UrsID=t2.UrsID)
			WHERE t1.`TA`='.$tahun_asal.'      
		';    
    
		\DB::statement($str_insert); 
    
    $daftar_opd = \DB::table('tmOrg')
    ->select(\DB::raw('
      `OrgID`, 
      `BidangID_1`,         
      `BidangID_2`,         
      `BidangID_3`
    '))
    ->where('TA', $tahun_tujuan)
    ->get();
    
    foreach($daftar_opd as $item)
    {
      if (!is_null($item->BidangID_2) )
      {
        $bidang2 = \DB::table('tmBidangUrusan AS t1')
        ->select(\DB::raw('
          t1.`BidangID`,         
          CONCAT(t2.Kd_Urusan,"-",t1.Kd_Bidang) AS `kode_bidang_2`,         
          t1.`Nm_Bidang`
        '))
        ->join('tmUrusan AS t2','t2.UrsID', 't1.UrsID')
        ->where('t1.BidangID_Src', $item->BidangID_2)
        ->first();
        
        if (!is_null($bidang2))
        {
          \DB::table('tmOrg')
            ->where('OrgID', $item->OrgID)
            ->update([
              'BidangID_2' => $bidang2->BidangID,
              'kode_bidang_2' => $bidang2->kode_bidang_2,
              'Nm_Bidang_2' => $bidang2->Nm_Bidang,
            ]); 
        }      
      }

      if (!is_null($item->BidangID_3))
      {
        $bidang3 = \DB::table('tmBidangUrusan AS t1')
        ->select(\DB::raw('
          t1.`BidangID`,         
          CONCAT(t2.Kd_Urusan,"-",t1.Kd_Bidang) AS `kode_bidang_3`,         
          t1.`Nm_Bidang`
        '))
        ->join('tmUrusan AS t2','t2.UrsID', 't1.UrsID')
        ->where('t1.BidangID_Src', $item->BidangID_3)
        ->first();

        if (!is_null($bidang3))
        {
          \DB::table('tmOrg')
            ->where('OrgID', $item->OrgID)
            ->update([
              'BidangID_3' => $bidang3->BidangID,
              'kode_bidang_3' => $bidang3->kode_bidang_3,
              'Nm_Bidang_3' => $bidang3->Nm_Bidang,
            ]); 
        }
      }
    }
    \DB::commit();

		return Response()->json([
			'status' => 1,
			'pid' => 'store',            
			'message'=>"Salin OPD dari tahun anggaran $tahun_asal berhasil."
		], 200);
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
    $this->hasPermissionTo('DMASTER-OPD_UPDATE');

    $this->validate($request, [            
      'BidangID_1' => 'required|exists:tmBidangUrusan,BidangID',
      'kode_bidang_1' => 'required',
      'Nm_Bidang_1' => 'required',            

      'kode_organisasi' => 'required',
      'Kd_Organisasi' => 'required',
      'Nm_Organisasi' => 'required',
      'Alias_Organisasi' => 'required',
      'Alamat' => 'required|min:5',

      'NamaKepalaSKPD' => 'required|min:5',
      'NIPKepalaSKPD' => 'required|min:5',
    ]);

    $organisasi = OrganisasiModel::find($id);
    
    $organisasi->BidangID_1 = $request->input('BidangID_1');
    $organisasi->kode_bidang_1 = $request->input('kode_bidang_1');
    $organisasi->Nm_Bidang_1 = $request->input('Nm_Bidang_1');
    
    $BidangID_2 = null;
    $kode_bidang_2 = null;
    $Nm_Bidang_2 = null;

    $BidangID_3 = null;
    $kode_bidang_3 = null;
    $Nm_Bidang_3 = null;

    if ($request->filled('BidangID_2')) {
      $BidangID_2 = $request->input('BidangID_2');
      $kode_bidang_2 = $request->input('kode_bidang_2');
      $Nm_Bidang_2 = $request->input('Nm_Bidang_2');
    }

    if ($request->filled('BidangID_3')) {
      $BidangID_3 = $request->input('BidangID_3');
      $kode_bidang_3 = $request->input('kode_bidang_3');
      $Nm_Bidang_3 = $request->input('Nm_Bidang_3');
    }

    $organisasi->BidangID_2 = $BidangID_2;
    $organisasi->kode_bidang_2 = $kode_bidang_2;
    $organisasi->Nm_Bidang_2 = $Nm_Bidang_2;
    
    $organisasi->BidangID_3 = $BidangID_3;
    $organisasi->kode_bidang_3 = $kode_bidang_3;
    $organisasi->Nm_Bidang_3 = $Nm_Bidang_3;
    
    $organisasi->kode_organisasi = $request->input('kode_organisasi');
    $organisasi->Kd_Organisasi = $request->input('Kd_Organisasi');
    $organisasi->Nm_Organisasi = $request->input('Nm_Organisasi');
    $organisasi->Alias_Organisasi = $request->input('Alias_Organisasi');
    $organisasi->Alamat = $request->input('Alamat');
    $organisasi->NamaKepalaSKPD = $request->input('NamaKepalaSKPD');
    $organisasi->NIPKepalaSKPD = $request->input('NIPKepalaSKPD');
    
    $organisasi->Descr = $request->input('Descr');
    $organisasi->save();

    $sql = "UPDATE tmSOrg SET kode_sub_organisasi=CONCAT('{$organisasi->kode_organisasi}', '.', Kd_Sub_Organisasi) WHERE OrgID='$id'";
    \DB::statement($sql);

    return Response()->json([
      'status' => 1,
      'pid' => 'update',
      'opd' => $organisasi,                                    
      'message' => 'Data organisasi '.$organisasi->Nm_Organisasi.' berhasil diubah.'
    ], 200); 

  }
  /**
   * digunakan untuk mengunci
   */
  public function lock(Request $request, $id)
  {
    $this->hasPermissionTo('SYSTEM-SETTING-LOCK-OPD_UPDATE');

    $organisasi = OrganisasiModel::find($id);

    if (is_null($organisasi))
    {
      return Response()->json([
        'status'=>0,
        'pid' => 'destroy',                
        'message'=>["Data OPD ($id) gagal dihapus"]
      ], 422); 
    }
    else
    {
      $this->validate($request, [            
        'tahun' => 'required',
        'bulan' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
        'status' => 'required|in:0,1',
        'masapelaporan' => 'required|in:murni,perubahan'
      ]);

      $tahun = $request->input('tahun');
      $bulan = $request->input('bulan');
      $status = $request->input('status');
      $masapelaporan = $request->input('masapelaporan');
      
      \DB::table('lockedopd')
        ->where('OrgID', $organisasi->OrgID)
        ->where('TA', $tahun)
        ->where('Bulan', $bulan)
        ->delete();

        LockedOPDModel::create([
        'lockedid' => Uuid::uuid4()->toString(), 
        'OrgID' => $organisasi->OrgID,
        'TA' => $tahun,
        'Bulan' => $bulan,
        'Locked' => $status,
        'masa' => $masapelaporan,
      ]);

      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'opd' => $organisasi,                                    
        'message' => 'Status input untuk OPD '.$organisasi->Nm_Organisasi.' berhasil diubah.'
      ], 200);       
    }
  }
  /**
   * digunakan untuk memperoleh daftar opd yang telah di lock
   */
  public function lockedall(Request $request)
  {
    $this->validate($request, [            
      'tahun' => 'required',
      'bulan' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
    ]);
    
    $tahun = $request->input('tahun');
    $bulan = $request->input('bulan');
    $masa = $request->input('masapelaporan');

    $subquery = \DB::table('lockedopd')
			->select(\DB::raw('`OrgID`, Locked'))
			->where('TA', $tahun)
			->where('Bulan', $bulan)
      ->where('masa', $masa);

    if ($this->hasRole(['superadmin', 'bapelitbang']))
    {
      $data = \DB::table('tmOrg AS A')
      ->select(\DB::raw('
        A.OrgID, 
      
        A.BidangID_1,         
        A.kode_bidang_1,         
        A.Nm_Bidang_1,         
        
        A.BidangID_2,         
        A.kode_bidang_2,         
        A.Nm_Bidang_2,         
    
        A.BidangID_3,         
        A.kode_bidang_3,         
        A.Nm_Bidang_3,         
    
        A.kode_organisasi, 
        A.Kd_Organisasi, 
        A.Nm_Organisasi, 
        A.Alias_Organisasi,                
        A.Alamat, 
        A.NamaKepalaSKPD, 
        A.NIPKepalaSKPD, 
        A.PaguDana1,
        A.PaguDana2,
        A.JumlahProgram1,
        A.JumlahProgram2,        
        A.JumlahKegiatan1,
        A.JumlahKegiatan2,        
        A.JumlahSubKegiatan1,
        A.JumlahSubKegiatan2,
        A.RealisasiKeuangan1,            
        A.RealisasiKeuangan2,        
        A.RealisasiFisik1,        
        A.RealisasiFisik2,        
        A.Descr, 
        A.TA,
        B.Locked,
        A.OrgID_Src
      '))
      ->leftJoinSub($subquery, 'B', function($join) {
				$join->on('A.OrgID', '=', 'B.OrgID');				
			})
      ->where('A.TA', $tahun)
      ->orderBy('A.kode_organisasi', 'ASC')
      ->get();      
    }       
    else if ($this->hasRole(['opd', 'unitkerja']))
    {
      $daftar_opd = $this->getUserOrgID($tahun);

      $data = \DB::table('tmOrg AS A')
      ->select(\DB::raw('
        A.OrgID, 
      
        A.BidangID_1,         
        A.kode_bidang_1,         
        A.Nm_Bidang_1,         
        
        A.BidangID_2,         
        A.kode_bidang_2,         
        A.Nm_Bidang_2,         
    
        A.BidangID_3,         
        A.kode_bidang_3,         
        A.Nm_Bidang_3,         
    
        A.kode_organisasi, 
        A.Kd_Organisasi, 
        A.Nm_Organisasi, 
        A.Alias_Organisasi,                
        A.Alamat, 
        A.NamaKepalaSKPD, 
        A.NIPKepalaSKPD, 
        A.PaguDana1,
        A.PaguDana2,
        A.JumlahProgram1,
        A.JumlahProgram2,        
        A.JumlahKegiatan1,
        A.JumlahKegiatan2,        
        A.JumlahSubKegiatan1,
        A.JumlahSubKegiatan2,
        A.RealisasiKeuangan1,            
        A.RealisasiKeuangan2,        
        A.RealisasiFisik1,        
        A.RealisasiFisik2,        
        A.Descr, 
        A.TA,
        B.Locked,
        A.OrgID_Src
      '))
      ->join('lockedopd AS B', 'A.OrgID', 'B.OrgID')
      ->where('A.TA', $tahun)      
      ->whereIn('OrgID', $daftar_opd)
      ->leftJoinSub($subquery, 'B', function($join) {
				$join->on('A.OrgID', '=', 'B.OrgID');				
			})
      ->orderBy('A.kode_organisasi', 'ASC')
      ->get();            
    }
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'opd' => $data,
      'jumlah_apbd' => $data->sum('PaguDana1'),
      'jumlah_apbdp' => $data->sum('PaguDana2'),
      'message' => 'Fetch data opd berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);   
  }
  public function lockall(Request $request)
  {
    $this->hasPermissionTo('SYSTEM-SETTING-LOCK-OPD_UPDATE');

    $this->validate($request, [            
      'tahun' => 'required',
      'bulan' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
      'status' => 'required|in:0,1',
    ]);

    $tahun = $request->input('tahun');
    $bulan = $request->input('bulan');
    $status = $request->input('status');
    
    $daftar_opd = OrganisasiModel::where('TA', $tahun)
      ->select(\DB::raw('
        OrgID
      '))
      ->orderBy('kode_organisasi','ASC')
      ->get();   

    foreach($daftar_opd as $opd)
    {
      \DB::table('lockedopd')
        ->where('OrgID', $opd->OrgID)
        ->where('TA', $tahun)
        ->where('Bulan', $bulan)
        ->delete();

      LockedOPDModel::create([
        'lockedid' => Uuid::uuid4()->toString(), 
        'OrgID' => $opd->OrgID,
        'TA' => $tahun,
        'Bulan' => $bulan,
        'Locked' => $status,
      ]);   
    }
    return Response()->json([
      'status' => 1,
      'pid' => 'update',                                   
      'message' => 'Status input seluruh OPD berhasil diubah.'
    ], 200);  
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $uuid
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {   
    $this->hasPermissionTo('DMASTER-OPD_DESTROY');

    $organisasi = OrganisasiModel::find($id);

    if (is_null($organisasi))
    {
      return Response()->json([
        'status'=>0,
        'pid' => 'destroy',                
        'message'=>["Data OPD ($id) gagal dihapus"]
      ], 422); 
    }
    else
    {
      
      $result = $organisasi->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message'=>"Data OPD dengan ID ($id) berhasil dihapus"
      ], 200);
    }
  }
  /**
   * digunakan untuk mendapat unit kerja berdasarkan OrgID
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function opdunitkerja ($id)
  {
    $organisasi = OrganisasiModel::find($id);
    if ($this->hasRole('unitkerja'))
    {
      $unitkerja = \DB::table('usersunitkerja')
        ->select(\DB::raw('tmSOrg.*'))
        ->join('tmSOrg','tmSOrg.SOrgID','usersunitkerja.SOrgID')
        ->where('usersunitkerja.user_id', $this->getUserid())
        ->where('tmSOrg.TA', $organisasi->TA)
        ->get();
    }
    else
    {            
      $unitkerja = $organisasi->unitkerja()->orderBy('kode_sub_organisasi','ASC')->get();        
    }
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'organisasi' => $organisasi,
      'unitkerja' => $unitkerja,                                    
      'message' => 'Data unit kerja berdasarkan id '.$organisasi->OrgNm.' berhasil diperoleh.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
  }
  /**
   * digunakan untuk mendapat pejabat  berdasarkan OrgID
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function pejabatopd ($id)
  {
    $pejabat = \DB::table('tmASN')
      ->select(\DB::raw('`trRiwayatJabatanASN`.`ASNID`,`tmASN`.`Nm_ASN`,`Jenis_Jabatan`'))
      ->join('trRiwayatJabatanASN','trRiwayatJabatanASN.ASNID','tmASN.ASNID')
      ->where('trRiwayatJabatanASN.OrgID', $id)
      ->get();

    $pa=[];
    $kpa=[];
    $ppk=[];
    $pptk=[];
    foreach ($pejabat as $item)
    {
      switch ($item->Jenis_Jabatan)
      {
        case 'pa' :
          $pa[]=[
            'text' => $item->Nm_ASN,
            'value' => $item->ASNID
          ];
        break;                    
        case 'kpa' :
          $kpa[]=[
            'text' => $item->Nm_ASN,
            'value' => $item->ASNID
          ];;
        break;                    
        case 'ppk' :
          $ppk[]=[
            'text' => $item->Nm_ASN,
            'value' => $item->ASNID
          ];
        break;                    
        case 'pptk' :
          $pptk[]=[
            'text' => $item->Nm_ASN,
            'value' => $item->ASNID
          ];
        break;                   
      }
    }        
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',                                    
      'pejabat'=>[
        'pa' => $pa,
        'kpa' => $kpa,
        'ppk' => $ppk,
        'pptk' => $pptk,
      ],                                    
      'message' => 'Data unit kerja berdasarkan id '.$id.' berhasil diperoleh.'
    ], 200); 
  }
}