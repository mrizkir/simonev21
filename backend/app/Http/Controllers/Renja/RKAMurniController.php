<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;
use Exception;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Helpers\HelperKegiatan;
use App\Models\DMaster\SubOrganisasiModel;
use App\Models\DMaster\SIPDModel;
use App\Models\DMaster\KodefikasiSubKegiatanModel;
use App\Models\Renja\RKAModel;
use App\Models\Renja\RKARincianModel;
use App\Models\Renja\RKARencanaTargetModel;
use App\Models\Renja\RKARealisasiModel;

class RKAMurniController extends Controller 
{
  private function recalculate($RKAID)
  {
    $paguuraian = \DB::table('trRKARinc')                            
      ->where('RKAID',$RKAID)
      ->sum('PaguUraian1');

    $jumlah_uraian = \DB::table('trRKARinc')                            
      ->where('RKAID',$RKAID)
      ->count('RKARincID');

    $data_realisasi = \DB::table('trRKARealisasiRinc')
      ->select(\DB::raw('
        COALESCE(SUM(realisasi1),0) AS jumlah_realisasi,
        COALESCE(SUM(fisik1),0) AS jumlah_fisik
      '))
      ->where('RKAID',$RKAID)
      ->get();
    
    $rka = RKAModel::find($RKAID);
    $rka->PaguDana1 = $paguuraian;
    $rka->RealisasiKeuangan1=$data_realisasi[0]->jumlah_realisasi;
    $rka->RealisasiFisik1=Helper::formatPecahan($data_realisasi[0]->jumlah_fisik,$jumlah_uraian);
    $rka->save();  
  }
  private function getDataRKA ($id)
  {
    $rka = RKAModel::select(\DB::raw('`RKAID`,
      `trRKA`.`kode_urusan`,
      `trRKA`.`Nm_Bidang`,
      `trRKA`.`kode_organisasi`,
      `trRKA`.`Nm_Organisasi`,
      `trRKA`.`kode_sub_organisasi`,
      `trRKA`.`Nm_Sub_Organisasi`,
      `trRKA`.`kode_program`,
      `trRKA`.`Nm_Program`,
      `trRKA`.`kode_kegiatan`,
      `trRKA`.`Nm_Kegiatan`,
      `trRKA`.`kode_sub_kegiatan`,
      `trRKA`.`Nm_Sub_Kegiatan`,
      `trRKA`.`lokasi_kegiatan1`,
      `trRKA`.`SumberDanaID`,
      `tmSumberDana`.`Nm_SumberDana`,
      `trRKA`.`tk_capaian1`,
      `trRKA`.`capaian_program1`,
      `trRKA`.`masukan1`,
      `trRKA`.`tk_keluaran1`,
      `trRKA`.`keluaran1`,
      `trRKA`.`tk_hasil1`,
      `trRKA`.`hasil1`,
      `trRKA`.`ksk1`,
      `trRKA`.`sifat_kegiatan1`,
      `trRKA`.`waktu_pelaksanaan1`,
      `trRKA`.`PaguDana1`,
      `trRKA`.`Descr`,
      `trRKA`.`EntryLvl`,
      `trRKA`.`Locked`,
      `trRKA`.`created_at`,
      `trRKA`.`updated_at`
      '))
    ->leftJoin('tmSumberDana','tmSumberDana.SumberDanaID','trRKA.SumberDanaID')
    ->where('trRKA.EntryLvl',1)
    ->find($id);

    return $rka;
  }

  /**
   * collect data from resources for datauraian view
   *
   * @return resources
   */
  public function populateDataRealisasi ($RKARincID)
  {
    $datauraian = RKARincianModel::find($RKARincID);

    $data=[
      'datarealisasi'=>[],
      'totalanggarankas'=>0,
      'totalrealisasi'=>0,
      'totaltargetfisik'=>0,
      'totalfisik'=>0,
      'sisa_anggaran'=>0,
    ];
    if (!is_null($datauraian))        
    {
      $r = \DB::table('trRKARealisasiRinc')
        ->select(\DB::raw('
          `RKARealisasiRincID`,
          `bulan1`,
          `target1`,
          `realisasi1`,
          target_fisik1,
          fisik1,
          `TA`,
          `Descr`,
          `created_at`,
          `updated_at`
          '))
        ->where('RKARincID',$RKARincID)
        ->orderBy('bulan1','ASC')
        ->get();

      $daftar_realisasi = [];
      $totalanggarankas=0;
      $totalrealisasi=0;
      $totaltargetfisik=0;
      $totalfisik=0;

      foreach ($r as $item)
      {
        $sum_realisasi = \DB::table('trRKARealisasiRinc')
          ->where('RKARincID',$RKARincID)
          ->where('bulan1','<=',$item->bulan1)
          ->sum('realisasi1');

        $sisa_anggaran=$datauraian->PaguUraian1-$sum_realisasi;            
        $daftar_realisasi[]=[
          'RKARealisasiRincID'=>$item->RKARealisasiRincID,
          'bulan1'=>$item->bulan1,
          'NamaBulan'=>Helper::getNamaBulan($item->bulan1),
          'target1'=>$item->target1,
          'realisasi1'=>$item->realisasi1,
          'target_fisik1'=>$item->target_fisik1,
          'fisik1'=>$item->fisik1,
          'sisa_anggaran'=>$sisa_anggaran,
          'Descr'=>$item->Descr,
          'TA'=>$item->TA,
          'created_at'=>$item->created_at,
          'updated_at'=>$item->updated_at,
        ];
        
        $totalanggarankas+=$item->target1;
        $totalrealisasi+=$item->realisasi1;
        $totaltargetfisik+=$item->target_fisik1;
        $totalfisik+=$item->fisik1;
      }
      
      $data['datarealisasi']=$daftar_realisasi;
      $data['totalanggarankas']=$totalanggarankas;
      $data['totalrealisasi']=$totalrealisasi;
      $data['totaltargetfisik']=round($totaltargetfisik,2);
      $data['totalfisik']=round($totalfisik,2);
      $data['sisa_anggaran']=$datauraian->PaguUraian1-$totalrealisasi;			
    }        
    return $data;
  }    
  
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function loaddatakegiatanFirsttime(Request $request)
  { 
    $this->validate($request, [            
      'tahun'=>'required',
      'SOrgID'=>'required|exists:tmSOrg,SOrgID',
    ]); 
    
    $tahun=$request->input('tahun');
    $SOrgID=$request->input('SOrgID');
    $unitkerja = SubOrganisasiModel::find($SOrgID);

    $str_insert = '
    INSERT INTO `trRKA` (
      `RKAID`,
      `OrgID`,
      `SOrgID`,
      kode_urusan,
      kode_bidang,
      kode_organisasi,
      `Nm_Organisasi`,
      kode_sub_organisasi,
      `Nm_Sub_Organisasi`,
      kode_program,
      kode_kegiatan,
      kode_sub_kegiatan,
      `Nm_Urusan`,
      `Nm_Bidang`,
      `Nm_Program`,
      `Nm_Kegiatan`,
      `Nm_Sub_Kegiatan`,
      `PaguDana1`,
      `RealisasiKeuangan1`,
      `RealisasiKeuangan2`,
      `user_id`,
      `EntryLvl`,
      `Descr`,
      `TA`,
      `Locked`,
      created_at,
      updated_at
    )
    SELECT
      uuid() AS `RKAID`,
      `OrgID`,
      `SOrgID`,
      kode_urusan,
      kode_bidang,
      kode_organisasi,
      `Nm_Organisasi`,
      kode_sub_organisasi,
      `Nm_Sub_Organisasi`,
      kode_program,
      kode_kegiatan,
      kode_sub_kegiatan,
      `Nm_Urusan`,
      `Nm_Bidang_Urusan`,
      `Nm_Program`,
      `Nm_Kegiatan`,
      `Nm_Sub_Kegiatan`,
      `PaguDana1`,
      `RealisasiKeuangan1`,
      `RealisasiKeuangan2`,
      `user_id`,
      `EntryLevel`,
      `Descr`,
      `TA`,
      `Locked`,
      created_at,
      updated_at
    FROM
    (
      SELECT 
        DISTINCT(kd_sub_keg_gabung) AS kode_sub_kegiatan,
        `OrgID`,
        `SOrgID`,
        `kd_Urusan1` AS kode_urusan,
        CONCAT(`kd_Urusan1`,\'.\',`kd_Bidang`) AS kode_bidang,
        kode_organisasi,
        `Nm_Organisasi`,
        kode_sub_organisasi,
        `Nm_Sub_Organisasi`,
        kd_prog_gabungan AS kode_program,
        kd_keg_gabung AS kode_kegiatan,		
        `Nm_Urusan`,
        `Nm_Bidang_Urusan`,
        `Nm_Program`,
        `Nm_Kegiatan`,
        `Nm_Sub_Kegiatan`,
        0 AS `PaguDana1`,
        0 AS `RealisasiKeuangan1`,
        0 AS `RealisasiKeuangan2`,
        \''.$this->getUserid().'\' AS `user_id`,
        1 AS `EntryLevel`,
        \'IMPORTED FROM SIPD\' AS `Descr`,
        '.$tahun.' AS `TA`,
        false AS `Locked`,
        NOW() AS created_at,
        NOW() AS updated_at
      FROM sipd WHERE kode_sub_organisasi=\''.$unitkerja->kode_sub_organisasi.'\' AND 
              `TA`='.$tahun.' AND 
              `EntryLevel`=1
    ) AS temp
    ';
    \DB::statement($str_insert); 
    
    $data = RKAModel::where('kode_sub_organisasi',$unitkerja->kode_sub_organisasi)
      ->where('TA',$tahun)
      ->where('EntryLvl', 1)
      ->get();
              
    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'unitkerja'=>$unitkerja,
      'rka'=>$data,
      'message'=>'Fetch data rka murni berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
    
  }
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function loaddatauraianFirsttime(Request $request)
  {   
    $tahun=$request->input('tahun');
    $this->validate($request, [            
      'RKAID'=>'required|exists:trRKA,RKAID',
    ]); 
    
    $RKAID=$request->input('RKAID');
    $rka = RKAModel::find($RKAID);

    $str_insert = '
      INSERT INTO `trRKARinc` (
        `RKARincID`,
        `RKAID`,
        `SIPDID`,
        kode_rek_3,
        kode_uraian1,
        kode_uraian2,
        `NamaUraian1`,
        `NamaUraian2`,
        volume1,
        satuan1,
        volume2,
        satuan2,
        harga_satuan1,
        harga_satuan2,
        `PaguUraian1`,
        `PaguUraian2`,
        SumberDanaID,
        `EntryLvl`,
        `TA`,
        created_at,
        updated_at
      )
      SELECT 
        uuid() AS `RKARincID`,                
        \''.$rka->RKAID.'\' AS `RKAID`,
        A.`SIPDID`,
        CONCAT(kd_rek1,\'.\',kd_rek2,\'.\',kd_rek3) AS kode_rek_3,
        CONCAT(kd_rek1,\'.\',kd_rek2,\'.\',kd_rek3,\'.\',kd_rek4,\'.\',kd_rek5,\'.\',kd_rek6) AS kode_uraian1,
        CONCAT(kd_rek1,\'.\',kd_rek2,\'.\',kd_rek3,\'.\',kd_rek4,\'.\',kd_rek5,\'.\',kd_rek6) AS kode_uraian2,
        nm_rek6 AS `NamaUraian1`,
        nm_rek6 AS `NamaUraian2`,
        1 AS volume1,
        \'Kegiatan\' AS satuan1,
        1 AS volume2,
        \'Kegiatan\' AS satuan2,
        A.`PaguUraian1` AS `harga_satuan1`,
        A.`PaguUraian2` AS `harga_satuan2`,
        A.`PaguUraian1` AS `PaguUraian1`,
        A.`PaguUraian2` AS `PaguUraian2`,
        A.SumberDanaID,
        1 AS `EntryLvl`,
        '.$rka->TA.' AS `TA`,
        NOW() AS created_at,
        NOW() AS updated_at
      FROM sipd A
      LEFT JOIN `trRKARinc` B ON B.`RKARincID`=A.`SIPDID`
        WHERE A.kd_sub_keg_gabung=\''.$rka->kode_sub_kegiatan.'\' AND 
        A.kode_sub_organisasi=\''.$rka->kode_sub_organisasi.'\' AND 
        A.`EntryLevel`=1 AND 
        A.`TA`='.$rka->TA.' AND 
        B.`SIPDID` IS NULL
    ';
    \DB::statement($str_insert); 
    
    $data = RKARincianModel::select(\DB::raw('
      `RKARincID`,
      `SIPDID`,
      kode_uraian1 AS kode_uraian,
      `NamaUraian1` AS nama_uraian,
      CONCAT(volume1,\' \',satuan1) AS volume,
      `volume1`,
      `satuan1`,
      `harga_satuan1`,
      `PaguUraian1`,
      0 AS `realisasi1`,
      0 AS `fisik1`,
      `JenisPelaksanaanID`,
      `TA`,
      created_at,
      updated_at
    '))                                
    ->where('RKAID',$rka->RKAID)
    ->get();
    
    $rka->PaguDana1 = $data->sum('PaguUraian1');
    $rka->PaguDana2 = $data->sum('PaguUraian2');

    $rka->save();

    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'rka'=>$rka,
      'uraian'=>$data,
      'message'=>'Fetch data uraian rka murni berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
    
  }
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {             
    $this->hasPermissionTo('RENJA-RKA-MURNI_BROWSE');
    
    $this->validate($request, [            
      'tahun'=>'required',            
      'bulan'=>'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
      'SOrgID'=>'required|exists:tmSOrg,SOrgID',            
    ]);

    $tahun = $request->input('tahun');
    $bulan = $request->input('bulan');
    $SOrgID = $request->input('SOrgID');
    $unitkerja = SubOrganisasiModel::find($SOrgID);
   
    $data = RKAModel::select(\DB::raw('
      `RKAID`,
      `SumberDanaID`,
      kode_urusan,
      kode_bidang,
      kode_organisasi,
      kode_sub_organisasi,
      kode_program,
      kode_kegiatan,
      kode_sub_kegiatan,
      `Nm_Urusan`,
      `Nm_Bidang`,
      `Nm_Organisasi`,
      `Nm_Sub_Organisasi`,
      `Nm_Program`,
      `Nm_Kegiatan`,
      `Nm_Sub_Kegiatan`,
      keluaran1,
      tk_keluaran1,                            
      hasil1,                            
      tk_hasil1,                            
      capaian_program1,                            
      tk_capaian1,                            
      masukan1,                            
      ksk1,                            
      sifat_kegiatan1,                            
      waktu_pelaksanaan1,                            
      lokasi_kegiatan1,                            
      `PaguDana1`,                            
      `RealisasiKeuangan1`,                            
      `RealisasiFisik1`,   
      0 AS persen_keuangan1,
      nip_pa1,                            
      nip_kpa1,
      nip_ppk1,
      nip_pptk1,
      `Descr`,
      `TA`,
      `Locked`,
      created_at,
      updated_at
    '))
    ->where('SOrgID', $unitkerja->SOrgID)
    ->where('TA', $tahun)
    ->where('EntryLvl', 1)
    ->orderByRaw('kode_urusan="X" DESC')
    ->orderBy('kode_bidang','ASC')
    ->orderBy('kode_program','ASC')
    ->orderBy('kode_kegiatan','ASC')
    ->orderBy('kode_sub_kegiatan','ASC')
    ->get();        
    
    if($this->hasRole(['opd', 'unitkerja']))
    {
      $is_locked = HelperKegiatan::isLocked($unitkerja->OrgID, $bulan, $tahun);

      $data->transform(function ($item, $key) use ($is_locked) {                            
        $item->persen_keuangan1 = Helper::formatPersen($item->RealisasiKeuangan1, $item->PaguDana1);
        $item->Locked = $is_locked;
        return $item;
      });
    }
    else
    {
      $is_locked = 0;
      $data->transform(function ($item,$key) {                            
        $item->persen_keuangan1=Helper::formatPersen($item->RealisasiKeuangan1, $item->PaguDana1);
        return $item;
      });
    }
    $jumlah_sub_kegiatan1 = $data->count();
    $unitkerja->PaguDana1 = $data->sum('PaguDana1');
    $unitkerja->RealisasiKeuangan1=$data->sum('RealisasiKeuangan1');
    $jumlah_realisasi_fisik=$data->sum('RealisasiFisik1');
    $unitkerja->RealisasiFisik1=Helper::formatPecahan($jumlah_realisasi_fisik,$jumlah_sub_kegiatan1);
    $unitkerja->JumlahSubKegiatan1=$jumlah_sub_kegiatan1;
    $unitkerja->save();

    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'unitkerja'=>$unitkerja,
      'rka'=>$data,
      'locked'=>$is_locked == 1,
      'message'=>'Fetch data rka murni berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);              
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function storekegiatan(Request $request)
  {       
    $this->hasPermissionTo('RENJA-RKA-MURNI_STORE');		
    try
    {
      $validator = Validator::make($request->all(), [
        'OrgID'=>'required|exists:tmOrg,OrgID',
        'SOrgID'=>'required|exists:tmSOrg,SOrgID',
        'SubKgtID'=> [
          'required',                
          'exists:tmSubKegiatan,SubKgtID',
        ]
      ]);     

      if ($validator->stopOnFirstFailure()->fails())
      {				
        $errors = $validator->errors();				
        foreach ($errors->all() as $k=>$message) {					
          throw new Exception($message);
        }				
      }

      $SubKgtID = $request->input('SubKgtID');
    
      $organisasi = SubOrganisasiModel::select(\DB::raw('
        tmSOrg.kode_sub_organisasi,
        tmSOrg.Nm_Sub_Organisasi,
        tmOrg.kode_organisasi,
        tmOrg.Nm_Organisasi
      '))
      ->join('tmOrg','tmOrg.OrgID','tmSOrg.OrgID')
      ->where('tmSOrg.SOrgID', $request->input('SOrgID'))                                
      ->first();			
      
      $kodefikasisubkegiatan=KodefikasiSubKegiatanModel::select(\DB::raw("
        tmSubKegiatan.`SubKgtID`,
        tmKegiatan.`KgtID`,
        tmKegiatan.`PrgID`,                                      
        COALESCE(tmUrusan.`Kd_Urusan`,'X') AS kode_urusan,
        `tmUrusan`.`Nm_Urusan`,
        CASE 
          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
          CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`)
          ELSE
          CONCAT('X.','XX.')
        END AS kode_bidang,                                      
        `tmBidangUrusan`.`Nm_Bidang`,
        CASE 
          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
          CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`)
          ELSE
          CONCAT('X.','XX.',tmProgram.`Kd_Program`)
        END AS kode_program,
        CASE 
          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
          CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`)
          ELSE
          CONCAT('X.','XX.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`)
        END AS kode_kegiatan,
        CASE 
          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
          CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`,'.',`tmSubKegiatan`.`Kd_SubKegiatan`)
          ELSE
          CONCAT('X.','XX.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`,'.',`tmSubKegiatan`.`Kd_SubKegiatan`)
        END AS kode_sub_kegiatan,
        COALESCE(tmUrusan.`Nm_Urusan`,'SEMUA URUSAN') AS Nm_Urusan,
        COALESCE(tmBidangUrusan.`Nm_Bidang`,'SEMUA BIDANG URUSAN') AS Nm_Bidang,
        `tmProgram`.`Nm_Program`,
        `tmKegiatan`.`Nm_Kegiatan`,
        `tmSubKegiatan`.`Nm_SubKegiatan`,
        `tmSubKegiatan`.`TA`
      "))
      ->join('tmKegiatan','tmKegiatan.KgtID','tmSubKegiatan.KgtID')
      ->join('tmProgram','tmKegiatan.PrgID','tmProgram.PrgID')
      ->leftJoin('tmUrusanProgram','tmProgram.PrgID','tmUrusanProgram.PrgID')
      ->leftJoin('tmBidangUrusan','tmBidangUrusan.BidangID','tmUrusanProgram.BidangID')
      ->leftJoin('tmUrusan','tmBidangUrusan.UrsID','tmUrusan.UrsID')                                    
      ->where('tmSubKegiatan.SubKgtID', $SubKgtID)
      ->first();			

      $masa_pelaporan = HelperKegiatan::getMasaPelaporan($kodefikasisubkegiatan->TA);

      if ($masa_pelaporan == 'perubahan')
      {
        throw new Exception('Tidak bisa tambah kegiatan karena berada di APBD ' . strtoupper($masa_pelaporan));
      }
      
      $rka = RKAModel::create([
        'RKAID' => Uuid::uuid4()->toString(),
        'OrgID' => $request->input('OrgID'),
        'SOrgID' => $request->input('SOrgID'),
        'PrgID' => $kodefikasisubkegiatan->PrgID,
        'KgtID' => $kodefikasisubkegiatan->KgtID,
        'SubKgtID' => $SubKgtID,            

        'kode_urusan' => $kodefikasisubkegiatan->kode_urusan,
        'kode_bidang' => $kodefikasisubkegiatan->kode_bidang,
        'kode_organisasi' => $organisasi->kode_organisasi,
        'kode_sub_organisasi' => $organisasi->kode_sub_organisasi,
        'kode_program' => $kodefikasisubkegiatan->kode_program,
        'kode_kegiatan' => $kodefikasisubkegiatan->kode_kegiatan,
        'kode_sub_kegiatan' => $kodefikasisubkegiatan->kode_sub_kegiatan,

        'Nm_Urusan' => $kodefikasisubkegiatan->Nm_Urusan,
        'Nm_Bidang' => $kodefikasisubkegiatan->Nm_Bidang,
        'Nm_Organisasi' => $organisasi->Nm_Organisasi,
        'Nm_Sub_Organisasi' => $organisasi->Nm_Sub_Organisasi,

        'Nm_Program' => $kodefikasisubkegiatan->Nm_Program,
        'Nm_Kegiatan' => $kodefikasisubkegiatan->Nm_Kegiatan,
        'Nm_Sub_Kegiatan' => $kodefikasisubkegiatan->Nm_SubKegiatan,

        'user_id' => $this->getUserid(),
        'EntryLvl' => 1,

        'TA'=>$kodefikasisubkegiatan->TA,
      ]);

      return Response()->json([
        'status'=>1,
        'pid'=>'store',
        'rka'=>$rka,                                    
        'message'=>'Data RKA berhasil disimpan.'
      ], 200); 
    }
    catch(Exception $e)
    {
      return Response()->json([
        'status'=>0,
        'pid'=>'store',
        'rka'=>[],                                    
        'message'=>$e->getMessage()
      ], 422); 			
    }
  }               
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function storeuraian(Request $request)
  {       
    $this->hasPermissionTo('RENJA-RKA-MURNI_STORE');
    
    $this->validate($request, [
      'RKAID'=>'required|exists:trRKA,RKAID',
      'SubRObyID'=>'required|exists:tmSubROby,SubRObyID',
      'kode_uraian1'=> 'required',
      'nama_uraian1'=> 'required',
      'volume1'=> 'required|numeric',
      'satuan1'=> 'required',
      'harga_satuan1'=> 'required|numeric',
      'PaguUraian1'=> 'required',            
    ]);     

    $rka = RKAModel::select('TA')
      ->find($request->input('RKAID'));

    $kode_uraian_1 = $request->input('kode_uraian_1');
    $uraian = RKARincianModel::create([
      'RKARincID' => Uuid::uuid4()->toString(),
      'RKAID' => $request->input('RKAID'),
      'kode_rek_3' => substr($kode_uraian_1, 0, 6),
      'kode_uraian1' => $kode_uraian_1,
      'NamaUraian1' => $request->input('nama_uraian1'),
      'volume1' => $request->input('volume1'),
      'satuan1' => $request->input('satuan1'),
      'harga_satuan1' => $request->input('harga_satuan1'),
      'PaguUraian1' => $request->input('PaguUraian1'),
      'Descr' => $request->input('Descr'),
      'EntryLvl' => 1,
      'TA' => $rka->TA,
    ]);

    return Response()->json([
      'status'=>1,
      'pid'=>'store',
      'uraian'=>$uraian,                                    
      'message'=>'Data Uraian RKA berhasil disimpan.'
    ], 200); 
  }               
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function updatekegiatan(Request $request,$id)
  {
    $this->hasPermissionTo('RENJA-RKA-MURNI_UPDATE');

    $kegiatan = RKAModel::find($id);
    
    if (is_null($kegiatan) )
    {
      return Response()->json([
        'status'=>0,
        'pid'=>'fetchdata',                
        'message'=>["Kegiatan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else if ($kegiatan->Locked)
    {
      return Response()->json([
        'status'=>0,
        'pid'=>'fetchdata',                
        'message'=>["Kegiatan dengan dengan ($id) tidak bisa diubah karena sudah dikunci, saat copy data ke Perubahan."]
      ], 422); 
    }
    else
    {
      $this->validate($request, [
        'SumberDanaID'=>'required',                
        'keluaran1'=>'required',                
        'tk_keluaran1'=>'required',                
        'hasil1'=>'required',                
        'tk_hasil1'=>'required',                
        'capaian_program1'=>'required',                
        'tk_capaian1'=>'required',                
        'masukan1'=>'required',                
        'ksk1'=>'required',                
        'sifat_kegiatan1'=>'required',                
        'waktu_pelaksanaan1'=>'required',                
        'lokasi_kegiatan1'=>'required',                                
        'nip_pa1'=>'required',                
        'nip_kpa1'=>'required',                
        'nip_ppk1'=>'required',                
        'nip_pptk1'=>'required', 
      ]);
      
      $kegiatan->SumberDanaID=$request->input('SumberDanaID');                
      $kegiatan->keluaran1=$request->input('keluaran1');                
      $kegiatan->tk_keluaran1=$request->input('tk_keluaran1');                
      $kegiatan->hasil1=$request->input('hasil1');                
      $kegiatan->tk_hasil1=$request->input('tk_hasil1');                
      $kegiatan->capaian_program1=$request->input('capaian_program1');                
      $kegiatan->tk_capaian1=$request->input('tk_capaian1');                
      $kegiatan->masukan1=$request->input('masukan1');                
      $kegiatan->ksk1=$request->input('ksk1');                
      $kegiatan->sifat_kegiatan1=$request->input('sifat_kegiatan1');                
      $kegiatan->waktu_pelaksanaan1=$request->input('waktu_pelaksanaan1');                
      $kegiatan->lokasi_kegiatan1=$request->input('lokasi_kegiatan1');                                
      $kegiatan->nip_pa1=$request->input('nip_pa1');                
      $kegiatan->nip_kpa1=$request->input('nip_kpa1');                
      $kegiatan->nip_ppk1=$request->input('nip_ppk1');                
      $kegiatan->nip_pptk1=$request->input('nip_pptk1'); 
      $kegiatan->Descr=$request->input('Descr'); 
      $kegiatan->save();

      $PaguDana1 = $request->input('PaguDana1');
      \DB::statement("UPDATE `trRKA` SET `PaguDana1`='$PaguDana1' WHERE `RKAID`='$id'");

      return Response()->json([
        'status'=>1,
        'pid'=>'update',
        'message'=>'Update RKA berhasil disimpan.'
      ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    }
  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function resetdatakegiatan(Request $request,$id)
  {
    $this->hasPermissionTo('RENJA-RKA-MURNI_UPDATE');

    $kegiatan = RKAModel::find($id);
    
    if (is_null($kegiatan) )
    {
      return Response()->json([
        'status'=>0,
        'pid'=>'fetchdata',                
        'message'=>["Kegiatan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else if ($kegiatan->Locked)
    {
      return Response()->json([
        'status'=>0,
        'pid'=>'fetchdata',                
        'message'=>["Kegiatan dengan dengan ($id) tidak bisa diubah karena sudah dikunci, saat copy data ke Perubahan."]
      ], 422); 
    }
    else
    {
      $this->recalculate($kegiatan->RKAID);
      return Response()->json([
        'status'=>1,
        'pid'=>'update',
        'message'=>'Update RKA berhasil disimpan.'
      ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    }
  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function updateuraian(Request $request,$id)
  {
    $this->hasPermissionTo('RENJA-RKA-MURNI_UPDATE');

    $rinciankegiatan = RKARincianModel::find($id);
    if (is_null($rinciankegiatan) )
    {
      return Response()->json([
        'status'=>0,
        'pid'=>'fetchdata',                
        'message'=>["Rincian Kegiatan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else if ($rinciankegiatan->Locked)
    {
      return Response()->json([
        'status'=>0,
        'pid'=>'fetchdata',                
        'message'=>["Rincian Kegiatan dengan dengan ($id) tidak bisa diubah karena sudah dikunci, saat copy data ke Perubahan."]
      ], 422); 
    }
    else
    {
      $this->validate($request, [
        'volume1'=>'required',
        'satuan1'=>'required',
        'harga_satuan1'=>'required',
        'PaguUraian1'=>'required',
      ]);
      
      $rinciankegiatan = \DB::transaction(function () use ($request,$rinciankegiatan) {
        $rinciankegiatan->volume1=$request->input('volume1');
        $rinciankegiatan->satuan1=$request->input('satuan1');
        $rinciankegiatan->harga_satuan1=$request->input('harga_satuan1');
        $rinciankegiatan->PaguUraian1=$request->input('PaguUraian1');
        $rinciankegiatan->JenisPelaksanaanID = $request->input('JenisPelaksanaanID');                   
        $rinciankegiatan->save();

        \DB::table('sipd')
          ->where('SIPDID',$rinciankegiatan->SIPDID)
          ->update(['PaguUraian1'=>$request->input('PaguUraian1')]);                

        $paguuraian=RKARincianModel::where('RKAID',$rinciankegiatan->RKAID)                                 
                      ->sum('PaguUraian1');                
        
        \DB::table('trRKA')
          ->where('RKAID', $rinciankegiatan->RKAID)
          ->update([
            'PaguDana1'=>$paguuraian
          ]);                    
      
        return $rinciankegiatan;
      });
      $rka=$this->getDataRKA($rinciankegiatan->RKAID);
      return Response()->json([
        'status'=>1,
        'pid'=>'update',
        'rka'=>$rka,
        'rinciankegiatan'=>$rinciankegiatan,
        'message'=>'Update uraian berhasil disimpan.'
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
  public function updatedetailuraian(Request $request,$id)
  {
    $this->hasPermissionTo('RENJA-RKA-MURNI_UPDATE');

    $rinciankegiatan = RKARincianModel::find($id);
    
    $this->validate($request, [
      'SumberDanaID'=>'required',            
    ]);
    
    $rinciankegiatan->JenisPelaksanaanID= $request->input('JenisPelaksanaanID');
    $rinciankegiatan->SumberDanaID= $request->input('SumberDanaID');
    $rinciankegiatan->JenisPembangunanID= $request->input('JenisPembangunanID');                        
    $rinciankegiatan->idlok= $request->input('idlok');                
    $rinciankegiatan->ket_lok= $request->input('ket_lok');                
    $rinciankegiatan->rw= $request->input('rw');                
    $rinciankegiatan->rt= $request->input('rt');                
    $rinciankegiatan->nama_perusahaan= $request->input('nama_perusahaan');                
    $rinciankegiatan->alamat_perusahaan= $request->input('alamat_perusahaan');                
    $rinciankegiatan->no_telepon= $request->input('no_telepon');                                                                              
    $rinciankegiatan->nama_direktur= $request->input('nama_direktur');                
    $rinciankegiatan->npwp= $request->input('npwp');                
    $rinciankegiatan->no_kontrak= $request->input('no_kontrak');                
    $rinciankegiatan->tgl_kontrak= $request->input('tgl_kontrak');                                        
    $rinciankegiatan->tgl_mulai_pelaksanaan= $request->input('tgl_mulai_pelaksanaan');                
    $rinciankegiatan->tgl_selesai_pelaksanaan= $request->input('tgl_selesai_pelaksanaan');                
    $rinciankegiatan->status_lelang= $request->input('status_lelang');             
    $rinciankegiatan->Descr= $request->input('Descr');      
    $rinciankegiatan->save();

    
    return Response()->json([
                'status'=>1,
                'pid'=>'update',
                'message'=>'Update detail uraian berhasil disimpan.'
              ], 200); 
  }
  /**
   * Show the form for creating a new resource. [menambah realisasi uraian]
   *
   * @return \Illuminate\Http\Response
   */
  public function bulanrealisasi(Request $request, $id)
  { 
    $this->hasPermissionTo('RENJA-RKA-MURNI_BROWSE');

    $bulan=Helper::getNamaBulan();
    $bulan_realisasi=RKARealisasiModel::select('bulan1')
      ->where('RKARincID',$id)
      ->get()
      ->pluck('bulan1','bulan1')
      ->toArray();
      
    $data = [];
    foreach($bulan as $k=>$v)
    {
      if (!array_key_exists($k, $bulan_realisasi))
      {
        $data[$k]=['value'=>$k,'text'=>$v];
      }
    }
    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'bulan'=>$data,
      'message'=>'Fetch data bulan realisasi berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  
  /**
   * Store a newly created resource in storage. [simpan rencana target fisik dan anggaran kas]
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function savetargetfisik(Request $request)
  {            
    $this->hasPermissionTo('RENJA-RKA-MURNI_STORE');

    $this->validate($request, [
      'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
      'bulan_fisik.*'=>'required',
    ]);

    
    $bulan_fisik= $request->input('bulan_fisik');      
    $data = [];
    $now = \Carbon\Carbon::now('Asia/Jakarta')->toDateTimeString();
    for ($i=0;$i < 12; $i+=1)
    {
      $data[]=[
        'RKATargetRincID'=>Uuid::uuid4()->toString(),
        'RKAID'=>$request->input('RKAID'),
        'RKARincID'=>$request->input('RKARincID'),
        'bulan1'=>$i+1,
        'bulan2'=>$i+1,
        'target1'=>0,
        'target2'=>0,
        'fisik1'=>$bulan_fisik[$i],
        'fisik2'=>0,
        'EntryLvl'=>1,
        'Descr'=>$request->input('Descr'),
        'TA'=>$request->input('tahun'),
        'created_at'=>$now,
        'updated_at'=>$now,
      ];
    }
    RKARencanaTargetModel::insert($data);

    return Response()->json([
                  'status'=>1,
                  'pid'=>'store',
                  'message'=>'Rencana target fisik uraian berhasil disimpan.'
                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    
      
  }   
  /**
   * Store a newly created resource in storage. [simpan rencana target fisik dan anggaran kas]
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function updatetargetfisik(Request $request)
  {                
    $this->hasPermissionTo('RENJA-RKA-MURNI_UPDATE');

    $this->validate($request, [
      'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
      'bulan_fisik.*'=>'required',
    ]);

    $bulan_fisik= $request->input('bulan_fisik');      
    $data = [];
    $now = \Carbon\Carbon::now('Asia/Jakarta')->toDateTimeString();
    for ($i=0;$i < 12; $i+=1)
    {
      \DB::table('trRKATargetRinc')
        ->where('RKARincID',$request->input('RKARincID'))
        ->where('bulan1',$i+1)
        ->update(['fisik1'=>$bulan_fisik[$i]]);
    }
    return Response()->json([
                'status'=>1,
                'pid'=>'update',
                'message'=>'Rencana target fisik uraian berhasil diubah.'
              ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    
      
  }  
  /**
   * Store a newly created resource in storage. [simpan rencana target fisik dan anggaran kas]
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function savetargetanggarankas(Request $request)
  {                
    $this->hasPermissionTo('RENJA-RKA-MURNI_STORE');

    $this->validate($request, [
      'RKAID'=>'required|exists:trRKA,RKAID',            
      'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
      'tahun'=>'required',
      'bulan_anggaran'=>'required',
    ]);		
    $RKARincID = $request->input('RKARincID');
    $bulan_anggaran = json_decode($request->input('bulan_anggaran', true));		
    $data = [];
    $now = \Carbon\Carbon::now('Asia/Jakarta')->toDateTimeString();
    foreach($bulan_anggaran as $item)
    {
      $data[]=[
        'RKATargetRincID'=>Uuid::uuid4()->toString(),
        'RKAID'=>$request->input('RKAID'),
        'RKARincID'=>$RKARincID,
        'bulan1'=>$item->no_bulan,
        'bulan2'=>$item->no_bulan,
        'fisik1'=>0,
        'fisik2'=>0,
        'target1'=>$item->target,
        'target2'=>0,
        'EntryLvl'=>1,
        'Descr'=>$request->input('Descr'),
        'TA'=>$request->input('tahun'),
        'created_at'=>$now,
        'updated_at'=>$now,
      ];
    }	
    RKARencanaTargetModel::insert($data);

    return Response()->json([
                'status'=>1,
                'pid'=>'store',
                'message'=>'Rencana target anggaran kas uraian berhasil disimpan.',
                'bulan_anggaran'=>$bulan_anggaran,
              ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    
      
  } 
  /**
   * Store a newly created resource in storage. [simpan rencana target fisik dan anggaran kas]
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function updatetargetanggarankas(Request $request)
  {                
    $this->hasPermissionTo('RENJA-RKA-MURNI_UPDATE');

    $this->validate($request, [
      'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
      'bulan_anggaran'=>'required',
    ]);

    $RKARincID = $request->input('RKARincID');
    $bulan_anggaran = json_decode($request->input('bulan_anggaran', true));		
    foreach($bulan_anggaran as $item)
    {
      \DB::table('trRKATargetRinc')
      ->where('RKARincID', $RKARincID)
      ->where('bulan1', $item->no_bulan)
      ->update([
        'target1'=>$item->target,
      ]);
    }	

    return Response()->json([
      'status'=>1,
      'pid'=>'update',
      'message'=>'Rencana target anggaran kas uraian berhasil diubah.',
      'bulan_anggaran'=>$bulan_anggaran,
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    
      
  }      
  /**
   * Store a newly created resource in storage. [simpan realisasi rincian kegiatan]
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function saverealisasi(Request $request)
  {
    $this->hasPermissionTo('RENJA-RKA-MURNI_STORE');

    $this->validate($request, [
      'RKARincID'=>'required',
      'RKAID'=>'required',
      'bulan1'=>'required',            
      'target1'=>'required',
      'realisasi1'=>'required',
      'target_fisik1'=>'required',
      'fisik1'=>'required', 
      'TA'=>'required|numeric',     
    ]);
    $RKAID=$request->input('RKAID');
    $realisasi = RKARealisasiModel::create([
      'RKARealisasiRincID' => Uuid::uuid4()->toString(),
      'RKAID' => $RKAID,
      'RKARincID' => $request->input('RKARincID'),            
      'bulan1' => $request->input('bulan1'),
      'bulan2' => 0,
      'target1' => $request->input('target1'),            
      'target2' => 0,            
      'realisasi1' => $request->input('realisasi1'),            
      'realisasi2' => 0,            
      'target_fisik1' => $request->input('target_fisik1'),           
      'target_fisik2' => 0,           
      'fisik1' => $request->input('fisik1'),           
      'fisik2' => 0,           
      'EntryLvl' => 1,
      'Descr' => $request->input('Descr'),            
      'TA' => $request->input('TA'),
    ]);      
    
    $this->recalculate($RKAID);

    return Response()->json([
      'status'=>1,
      'pid'=>'store',
      'realisasi'=>$realisasi,                                    
      'message'=>'Data realisasi berhasil disimpan.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    
  }    
  /**
   * Store a newly created resource in storage. [update realisasi rincian kegiatan]
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function updaterealisasi(Request $request, $id)
  {    
    $this->hasPermissionTo('RENJA-RKA-MURNI_UPDATE');
    
    $realisasi = RKARealisasiModel::find($id);
    
    $this->validate($request, [                    
      'target1'=>'required',
      'realisasi1'=>'required',
      'target_fisik1'=>'required',
      'fisik1'=>'required',      
    ]);

    $target1 = $request->input('target1');
    $realisasi1 = $request->input('realisasi1');
    $target_fisik1 = $request->input('target_fisik1');
    $fisik1 = $request->input('fisik1');        
    $Descr = $request->input('Descr');

    \DB::statement("UPDATE trRKARealisasiRinc SET target1='$target1', realisasi1='$realisasi1', target_fisik1='$target_fisik1', fisik1='$fisik1', `Descr`='$Descr' WHERE `RKARealisasiRincID`='$id'");		
    $this->recalculate($realisasi->RKAID);                    

    return Response()->json([
      'status'=>1,
      'pid'=>'update',
      'realisasi'=>$realisasi,                                    
      'message'=>'Data realisasi berhasil diubah.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    
    

  }  
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $this->hasPermissionTo('RENJA-RKA-MURNI_SHOW');

    $rka = $this->getDataRKA($id);

    if (is_null($rka))
    {
      return Response()->json([
        'status'=>0,
        'pid'=>'fetchdata',                
        'message'=>"Fetch data kegiatan murni dengan id ($id) gagal diperoleh"
      ], 422); 
    }
    else
    {
      $data = RKARincianModel::select(\DB::raw('
        `trRKARinc`.`RKARincID`,
        `trRKARinc`.`RKAID`,
        `trRKARinc`.`SIPDID`,
        `trRKARinc`.`JenisPelaksanaanID`,
        `trRKARinc`.`SumberDanaID`,
        `trRKARinc`.`JenisPembangunanID`,
        `trRKARinc`.kode_uraian1 AS kode_uraian,
        `trRKARinc`.`NamaUraian1` AS nama_uraian,
        `trRKARinc`.`volume1`,
        `trRKARinc`.`satuan1`,
        CONCAT(`trRKARinc`.volume1,\' \',`trRKARinc`.satuan1) AS volume,
        `trRKARinc`.`harga_satuan1`,
        `trRKARinc`.`PaguUraian1`,
        0 AS `realisasi1`,
        0 AS `persen_keuangan1`,
        0 AS `fisik1`,                                                                        
        \'\' AS `provinsi_id`,
        \'\' AS `kabupaten_id`,
        \'\' AS `kecamatan_id`,
        \'\' AS `desa_id`,                                    
        `trRKARinc`.`idlok`,
        `trRKARinc`.`ket_lok`,
        `trRKARinc`.`rw`,
        `trRKARinc`.`rt`,
        `trRKARinc`.`nama_perusahaan`,
        `trRKARinc`.`alamat_perusahaan`,
        `trRKARinc`.`no_telepon`,
        `trRKARinc`.`nama_direktur`,
        `trRKARinc`.`npwp`,
        `trRKARinc`.`no_kontrak`,
        `trRKARinc`.`tgl_mulai_pelaksanaan`,
        `trRKARinc`.`tgl_selesai_pelaksanaan`,
        `trRKARinc`.`status_lelang`,
        `trRKARinc`.`Descr`,                                    
        `trRKARinc`.`TA`,
        `trRKARinc`.`Locked`,
        `trRKARinc`.created_at,
        `trRKARinc`.updated_at
      '))                                
      ->where('RKAID',$rka->RKAID)
      ->orderBy('trRKARinc.kode_uraian1', 'ASC')
      ->get();
      
      $data->transform(function ($item,$key) {
        $item->realisasi1=\DB::table('trRKARealisasiRinc')->where('RKARincID',$item->RKARincID)->sum('realisasi1');    
        $item->fisik1=\DB::table('trRKARealisasiRinc')->where('RKARincID',$item->RKARincID)->sum('fisik1');
        $item->persen_keuangan1=Helper::formatPersen($item->realisasi1,$item->PaguUraian1);
        switch($item->ket_lok)
        {
          case 'desa' :
            $lokasi=\App\Models\DMaster\DesaModel::select(\DB::raw('`wilayah_desa`.`id` AS desa_id, `wilayah_kecamatan`.`id` AS kecamatan_id, `wilayah_kabupaten`.`id` AS kabupaten_id, `wilayah_provinsi`.`id` AS provinsi_id'))
                              ->join('wilayah_kecamatan','wilayah_kecamatan.id','wilayah_desa.kecamatan_id')
                              ->join('wilayah_kabupaten','wilayah_kecamatan.kabupaten_id','wilayah_kabupaten.id')
                              ->join('wilayah_provinsi','wilayah_provinsi.id','wilayah_kabupaten.provinsi_id')                                                            
                              ->find($item->idlok);
            
            if (!is_null($lokasi))
            {
              $item->desa_id=$lokasi->desa_id;
              $item->kecamatan_id=$lokasi->kecamatan_id;
              $item->kabupaten_id=$lokasi->kabupaten_id;
              $item->provinsi_id=$lokasi->provinsi_id;                            
            }
          break;
          case 'kecamatan' :
            $lokasi=\App\Models\DMaster\KecamatanModel::select(\DB::raw('`wilayah_kecamatan`.`id` AS kecamatan_id, `wilayah_kabupaten`.`id` AS kabupaten_id, `wilayah_provinsi`.`id` AS provinsi_id'))                                                            
                              ->join('wilayah_kabupaten','wilayah_kecamatan.kabupaten_id','wilayah_kabupaten.id')
                              ->join('wilayah_provinsi','wilayah_provinsi.id','wilayah_kabupaten.provinsi_id')                                                            
                              ->find($item->idlok);

            if (!is_null($lokasi))
            {
              $item->kecamatan_id=$lokasi->kecamatan_id;
              $item->kabupaten_id=$lokasi->kabupaten_id;
              $item->provinsi_id=$lokasi->provinsi_id;
            }
          break;
          case 'kota' :
            $lokasi=\App\Models\DMaster\KabupatenModel::select(\DB::raw('`wilayah_kabupaten`.`id` AS kabupaten_id, `wilayah_provinsi`.`id` AS provinsi_id'))                                                                                                                        
                              ->join('wilayah_provinsi','wilayah_provinsi.id','wilayah_kabupaten.provinsi_id')                                                            
                              ->find($item->idlok);

            if (!is_null($lokasi))
            {
              $item->kabupaten_id=$lokasi->kabupaten_id;
              $item->provinsi_id=$lokasi->provinsi_id;
            }
          break;
          case 'provinsi' :
            $lokasi=\App\Models\DMaster\ProvinsiModel::select(\DB::raw('`wilayah_provinsi`.`id` AS provinsi_id'))                                                                                                                                                                                                                                            
                              ->find($item->idlok);

            if (!is_null($lokasi))
            {
              $item->provinsi_id=$lokasi->provinsi_id;
            }
          break;                
        }
        return $item;
      });
      
      return Response()->json([
        'status'=>1,
        'pid'=>'fetchdata',
        'datakegiatan'=>$rka,
        'uraian'=>$data,
        'message'=>'Fetch data rincian kegiatan berhasil diperoleh'
      ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    }            
  }
  /**
   * Display the specified resource. [rencanatarget]
   *
   * @return \Illuminate\Http\Response
   */
  public function rencanatarget(Request $request)
  {
    $this->hasPermissionTo('RENJA-RKA-MURNI_SHOW');

    $this->validate($request, [            
      'mode'=>'required',            
      'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
    ]);
    $mode = $request->input('mode');
    $RKARincID = $request->input('RKARincID');
    
    $data_uraian = RKARincianModel::select(\DB::raw('
      `SIPDID`,
      kode_uraian1 AS kode_uraian,
      NamaUraian1 AS nama_uraian,
      `PaguUraian1`
    '))
    ->find($RKARincID);
    
    $data_realisasi = \DB::table('trRKARealisasiRinc')
      ->select(\DB::raw('
        COALESCE(SUM(target1),0) AS jumlah_targetanggarankas,
        COALESCE(SUM(realisasi1),0) AS jumlah_realisasi,
        COALESCE(SUM(target_fisik1),0) AS jumlah_targetfisik,
        COALESCE(SUM(fisik1),0) AS jumlah_fisik
      '))
      ->where('RKARincID',$RKARincID)
      ->get();

    $target = ['fisik'=>0,'anggaran'=>0];			
    if ($mode == 'targetfisik')
    {
      $data = \DB::table('trRKATargetRinc')
              ->select(\DB::raw("
              CONCAT('{',
                GROUP_CONCAT(
                  TRIM(
                    LEADING '{' FROM TRIM(
                      TRAILING '}' FROM JSON_OBJECT(CONCAT('fisik_',`trRKATargetRinc`.`bulan1`),`trRKATargetRinc`.`fisik1`)
                    )
                  )
                ),
              '}') AS `fisik1`							
            "))
            ->where('RKARincID',$RKARincID)
            ->get();                    
      $target=isset($data[0]) ? json_decode($data[0]->fisik1, true) : [];
    }
    else if ($mode == 'targetanggarankas')
    {            
      $data = \DB::table('trRKATargetRinc')
            ->select(\DB::raw("						
            CONCAT('{',
              GROUP_CONCAT(
                TRIM(
                  LEADING '{' FROM TRIM(
                    TRAILING '}' FROM JSON_OBJECT(CONCAT('anggaran_',`trRKATargetRinc`.`bulan1`),`trRKATargetRinc`.`target1`)
                  )
                )
              ),
            '}') AS `anggaran1`
          "))
          ->where('RKARincID',$RKARincID)
          ->get();      

      $target=isset($data[0]) ? json_decode($data[0]->anggaran1, true) : [];
    }
    else if ($mode == 'bulan' && $request->has('bulan1'))
    {
      $bulan1 = $request->input('bulan1');
      
      $data = \DB::table('trRKATargetRinc')
        ->select(\DB::raw("
          CONCAT('{',
            GROUP_CONCAT(
              TRIM(
                LEADING '{' FROM TRIM(
                  TRAILING '}' FROM JSON_OBJECT(CONCAT('fisik_',`trRKATargetRinc`.`bulan1`),`trRKATargetRinc`.`fisik1`)
                )
              )
            ),
          '}') AS `fisik1`,
          CONCAT('{',
            GROUP_CONCAT(
              TRIM(
                LEADING '{' FROM TRIM(
                  TRAILING '}' FROM JSON_OBJECT(CONCAT('anggaran_',`trRKATargetRinc`.`bulan1`),`trRKATargetRinc`.`target1`)
                )
              )
            ),
          '}') AS `anggaran1`
        "))
        ->where('RKARincID',$RKARincID)
        ->groupBy('RKARincID')
        ->get();                  		
      
      if (isset($data[0]))
      {
        $fisik1 = json_decode($data[0]->fisik1, true);
        $anggaran1 = json_decode($data[0]->anggaran1, true);                
        $target['fisik'] = is_null($fisik1) ? 0 : $fisik1["fisik_$bulan1"];
        $target['anggaran'] = is_null($anggaran1) ? 0 : $anggaran1["anggaran_$bulan1"];                
      }            
    }
    
    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'mode'=>$mode,
      'datauraian'=>$data_uraian,
      'target'=>$target,
      'datarealisasi'=>$data_realisasi[0],
      'message'=>"Fetch data target $mode berhasil diperoleh"
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  

  }
  /**
   * Display the specified resource. [daftar realisasi]
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function realisasi(Request $request)
  {  
    $this->hasPermissionTo('RENJA-RKA-MURNI_SHOW');

    $this->validate($request, [            
      'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
    ]);
    
    $RKARincID=$request->input('RKARincID');
    $data=$this->populateDataRealisasi($RKARincID); 

    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'realisasi'=>$data['datarealisasi'],
      'totalanggarankas'=>$data['totalanggarankas'],
      'totalrealisasi'=>$data['totalrealisasi'],
      'totaltargetfisik'=>$data['totaltargetfisik'],
      'totalfisik'=>$data['totalfisik'],
      'sisa_anggaran'=>$data['sisa_anggaran'],
      'message'=>"Fetch data realisasi berhasil diperoleh"
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request,$id)
  { 
    $this->hasPermissionTo('RENJA-RKA-MURNI_DESTROY');

    $pid=$request->input('pid');
    switch ($pid)
    {          
      case 'datarka' :
        $rka = RKAModel::find($id);
        $rka->delete();
        $message="data rka murni dengan ID ($id) Berhasil di Hapus";                 
      break;  
      case 'datauraian' :
        $rincian = RKARincianModel::find($id);
        $RKAID=$rincian->RKAID;
        $result=$rincian->delete();
        $message="data uraian kegiatan dengan ID ($id) Berhasil di Hapus";      
        
        $this->recalculate($RKAID);
      break;
      case 'datarealisasi' :
        $realisasi = RKARealisasiModel::find($id);
        $RKAID=$realisasi->RKAID;
        $result=$realisasi->delete();
        $message="data realisasi uraian kegiatan dengan ID ($id) Berhasil di Hapus";      
        
        $this->recalculate($RKAID);
      break;
    }            
    
    return Response()->json([
      'status'=>1,
      'pid'=>'destroy',                
      'message'=>$message
    ], 200);  
        
  }
}