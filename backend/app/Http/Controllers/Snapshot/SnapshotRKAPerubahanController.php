<?php

namespace App\Http\Controllers\Snapshot;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Helpers\HelperKegiatan;
use App\Models\DMaster\SubOrganisasiModel;
use App\Models\Snapshot\SnapshotRKAModel;
use App\Models\Snapshot\SnapshotRKARincianModel;
use App\Models\Snapshot\SnapshotRKARencanaTargetModel;
use App\Models\Snapshot\SnapshotRKARealisasiModel;

class SnapshotRKAPerubahanController extends Controller 
{
  private function getDataRKA ($id)
  {
    $rka = SnapshotRKAModel::select(\DB::raw('`RKAID`,
      `trSnapshotRKA`.`kode_urusan`,
      `trSnapshotRKA`.`Nm_Bidang`,
      `trSnapshotRKA`.`kode_organisasi`,
      `trSnapshotRKA`.`Nm_Organisasi`,
      `trSnapshotRKA`.`kode_sub_organisasi`,
      `trSnapshotRKA`.`Nm_Sub_Organisasi`,
      `trSnapshotRKA`.`kode_program`,
      `trSnapshotRKA`.`Nm_Program`,
      `trSnapshotRKA`.`kode_kegiatan`,
      `trSnapshotRKA`.`Nm_Kegiatan`,
      `trSnapshotRKA`.`kode_sub_kegiatan`,
      `trSnapshotRKA`.`Nm_Sub_Kegiatan`,
      `trSnapshotRKA`.`lokasi_kegiatan2`,
      `trSnapshotRKA`.`SumberDanaID`,
      `tmSumberDana`.`Nm_SumberDana`,
      `trSnapshotRKA`.`tk_capaian2`,
      `trSnapshotRKA`.`capaian_program2`,
      `trSnapshotRKA`.`masukan2`,
      `trSnapshotRKA`.`tk_keluaran2`,
      `trSnapshotRKA`.`keluaran2`,
      `trSnapshotRKA`.`tk_hasil2`,
      `trSnapshotRKA`.`hasil2`,
      `trSnapshotRKA`.`ksk2`,
      `trSnapshotRKA`.`sifat_kegiatan2`,
      `trSnapshotRKA`.`waktu_pelaksanaan2`,
      `trSnapshotRKA`.`PaguDana2`,
      `trSnapshotRKA`.`Descr`,
      `trSnapshotRKA`.`EntryLvl`,
      `trSnapshotRKA`.`Locked`,
      `trSnapshotRKA`.`TABULAN`,
      `trSnapshotRKA`.`created_at`,
      `trSnapshotRKA`.`updated_at`
      '))
    ->leftJoin('tmSumberDana','tmSumberDana.SumberDanaID','trSnapshotRKA.SumberDanaID')
    ->where('trSnapshotRKA.EntryLvl',2)
    ->find($id);

    return $rka;
  }
  public function loaddatakegiatanFirsttime(Request $request)
  { 
    $this->validate($request, [            
      'tahun' => 'required',
      'bulan' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
      'SOrgID' => 'required|exists:tmSOrg,SOrgID',
    ]); 
    
    $SOrgID = $request->input('SOrgID');
    $unitkerja = SubOrganisasiModel::find($SOrgID);

    $tahun = $request->input('tahun');
    $bulan = $request->input('bulan');

    \DB::table('trSnapshotRKA')
    ->where('TABULAN', $tahun.$bulan)
    ->where('SOrgID', $SOrgID)
    ->where('EntryLvl', 2)
    ->where('TA', $tahun)
    ->delete();

    $str_insert = '
    INSERT INTO `trSnapshotRKA` (
      `SnapshotID`,
      `RKAID`, 
      `OrgID`, 
      `SOrgID`, 
      `PrgID`, 
      `KgtID`, 
      `SubKgtID`, 
      `SumberDanaID`, 

      `kode_urusan`, 
      `kode_bidang`, 
      `kode_organisasi`, 
      `kode_sub_organisasi`, 
      `kode_program`, 
      `kode_kegiatan`, 
      `kode_sub_kegiatan`,
      
      `Nm_Urusan`,         
      `Nm_Bidang`,         
      `Nm_Organisasi`,         
      `Nm_Sub_Organisasi`, 

      `Nm_Program`,         
      `Nm_Kegiatan`,         
      `Nm_Sub_Kegiatan`, 

      `keluaran1`,         
      `keluaran2`,         
      `tk_keluaran1`,         
      `tk_keluaran2`,         
      `hasil1`,         
      `hasil2`,         
      `tk_hasil1`,         
      `tk_hasil2`,         
      `capaian_program1`,  
      `capaian_program2`,  
      `tk_capaian1`,                
      `tk_capaian2`,                
      `masukan1`,         
      `masukan2`,         
      `ksk1`,         
      `ksk2`,         
      `sifat_kegiatan1`,   
      `sifat_kegiatan2`,   
      `waktu_pelaksanaan1`,         
      `waktu_pelaksanaan2`,         
      `lokasi_kegiatan1`,      
      `lokasi_kegiatan2`,      
      `PaguDana1`, 
      `PaguDana2`,        
      `RealisasiKeuangan1`, 
      `RealisasiKeuangan2`,        
      `RealisasiFisik1`,        
      `RealisasiFisik2`,        
      `nip_pa1`, 
      `nip_pa2`, 
      `nip_kpa1`, 
      `nip_kpa2`, 
      `nip_ppk1`, 
      `nip_ppk2`, 
      `nip_pptk1`, 
      `nip_pptk2`, 
      `user_id`, 
      `EntryLvl`, 
      `Descr`, 
      `TA`, 
      `TABULAN`, 
      `Locked`,        
      `RKAID_Src`,
      `created_at`,
      `updated_at`
    )
    SELECT
      UUID(),
      `RKAID`, 
      `OrgID`, 
      `SOrgID`, 
      `PrgID`, 
      `KgtID`, 
      `SubKgtID`, 
      `SumberDanaID`, 

      `kode_urusan`, 
      `kode_bidang`, 
      `kode_organisasi`, 
      `kode_sub_organisasi`, 
      `kode_program`, 
      `kode_kegiatan`, 
      `kode_sub_kegiatan`,
      
      `Nm_Urusan`,         
      `Nm_Bidang`,         
      `Nm_Organisasi`,         
      `Nm_Sub_Organisasi`, 

      `Nm_Program`,         
      `Nm_Kegiatan`,         
      `Nm_Sub_Kegiatan`, 

      `keluaran1`,         
      `keluaran2`,         
      `tk_keluaran1`,         
      `tk_keluaran2`,         
      `hasil1`,         
      `hasil2`,         
      `tk_hasil1`,         
      `tk_hasil2`,         
      `capaian_program1`,  
      `capaian_program2`,  
      `tk_capaian1`,                
      `tk_capaian2`,                
      `masukan1`,         
      `masukan2`,         
      `ksk1`,         
      `ksk2`,         
      `sifat_kegiatan1`,   
      `sifat_kegiatan2`,   
      `waktu_pelaksanaan1`,         
      `waktu_pelaksanaan2`,         
      `lokasi_kegiatan1`,      
      `lokasi_kegiatan2`,      
      `PaguDana1`, 
      `PaguDana2`,        
      `RealisasiKeuangan1`, 
      `RealisasiKeuangan2`,        
      `RealisasiFisik1`,        
      `RealisasiFisik2`,        
      `nip_pa1`, 
      `nip_pa2`, 
      `nip_kpa1`, 
      `nip_kpa2`, 
      `nip_ppk1`, 
      `nip_ppk2`, 
      `nip_pptk1`, 
      `nip_pptk2`, 
      `user_id`, 
      `EntryLvl`, 
      `Descr`, 
      `TA`, 
      '.$tahun.$bulan.',
      `Locked`,        
      `RKAID_Src`,
      NOW(),
      NOW()
    FROM
      trRKA
    WHERE
      SOrgID="'.$SOrgID.'"
      AND EntryLvl=2
      AND TA='.$tahun;
    \DB::statement($str_insert);
    
    \DB::table('trSnapshotRKARinc AS A')
    ->join('trRKA AS B', 'A.RKAID', 'B.RKAID')
    ->where('TABULAN', $tahun.$bulan)
    ->where('B.SOrgID', $SOrgID)
    ->where('A.EntryLvl', 2)
    ->where('A.TA', $tahun)
    ->delete();

    //copy rincian
    $str_insert = '
    INSERT INTO `trSnapshotRKARinc` (
      `SnapshotID`,
      `RKARincID`,
      `RKAID`,
      `SIPDID`,
      `JenisPelaksanaanID`,
      `SumberDanaID`,
      `JenisPembangunanID`,            
      `kode_uraian1`,            
      `kode_uraian2`,            
      `NamaUraian1`,            
      `NamaUraian2`,            
      `volume1`,
      `volume2`,
      `satuan1`,            
      `satuan2`,            
      `harga_satuan1`,
      `harga_satuan2`,
      `PaguUraian1`,
      `PaguUraian2`,
      `idlok`,
      `ket_lok`,
      `rw`,
      `rt`,
      `nama_perusahaan`,
      `alamat_perusahaan`,
      `no_telepon`,
      `nama_direktur`,
      `npwp`,
      `no_kontrak`,
      `tgl_kontrak`,
      `tgl_mulai_pelaksanaan`,
      `tgl_selesai_pelaksanaan`,
      `status_lelang`,
      `EntryLvl`,
      `Descr`,            
      `TA`,
      `TABULAN`,
      `Locked`,
      `RKARincID_Src`,                    
      `created_at`,
      `updated_at`
    )
    SELECT
      UUID(),
      A.`RKARincID`,
      A.`RKAID`,
      A.`SIPDID`,
      A.`JenisPelaksanaanID`,
      A.`SumberDanaID`,
      A.`JenisPembangunanID`,            
      A.`kode_uraian1`,            
      A.`kode_uraian2`,            
      A.`NamaUraian1`,            
      A.`NamaUraian2`,            
      A.`volume1`,
      A.`volume2`,
      A.`satuan1`,            
      A.`satuan2`,            
      A.`harga_satuan1`,
      A.`harga_satuan2`,
      A.`PaguUraian1`,
      A.`PaguUraian2`,
      A.`idlok`,
      A.`ket_lok`,
      A.`rw`,
      A.`rt`,
      A.`nama_perusahaan`,
      A.`alamat_perusahaan`,
      A.`no_telepon`,
      A.`nama_direktur`,
      A.`npwp`,
      A.`no_kontrak`,
      A.`tgl_kontrak`,
      A.`tgl_mulai_pelaksanaan`,
      A.`tgl_selesai_pelaksanaan`,
      A.`status_lelang`,
      A.`EntryLvl`,
      A.`Descr`,            
      A.`TA`,
      '.$tahun.$bulan.',
      A.`Locked`,
      A.`RKARincID_Src`,                          
      NOW(),
      NOW()
    FROM trRKARinc AS A 
    JOIN trRKA AS B ON A.RKAID=B.RKAID 
    WHERE
      B.SOrgID="'.$SOrgID.'"
      AND A.EntryLvl=2
      AND A.TA='.$tahun;
    \DB::statement($str_insert);
    
    \DB::table('trSnapshotRKATargetRinc AS A')
    ->join('trRKA AS B', 'A.RKAID', 'B.RKAID')
    ->where('TABULAN', $tahun.$bulan)
    ->where('B.SOrgID', $SOrgID)
    ->where('A.EntryLvl', 2)
    ->where('A.TA', $tahun)
    ->delete();

    //copy target    
    $str_insert = '
    INSERT INTO `trSnapshotRKATargetRinc` (
      `SnapshotID`,
      `RKATargetRincID`, 
      `RKAID`, 
      `RKARincID`, 
      `bulan1`, 
      `bulan2`, 
      `target1`,         
      `target2`,                
      `fisik1`,         
      `fisik2`,    
      `EntryLvl`, 
      `Descr`, 
      `TA`, 
      `TABULAN`, 
      `Locked`,
      `RKATargetRincID_Src`,
      `created_at`,
      `updated_at`
    )
    SELECT
      UUID(),
      A.`RKATargetRincID`, 
      A.`RKAID`, 
      A.`RKARincID`, 
      A.`bulan1`, 
      A.`bulan2`, 
      A.`target1`,         
      A.`target2`,                
      A.`fisik1`,         
      A.`fisik2`,    
      A.`EntryLvl`, 
      A.`Descr`, 
      A.`TA`, 
      '.$tahun.$bulan.',
      A.`Locked`,
      A.`RKATargetRincID_Src`,
      NOW(),
      NOW()
    FROM trRKATargetRinc AS A     
    JOIN trRKA AS B ON A.RKAID=B.RKAID 
    WHERE
      B.SOrgID="'.$SOrgID.'"
      AND A.EntryLvl=2
      AND A.TA='.$tahun;
    \DB::statement($str_insert);
    
    \DB::table('trSnapshotRKARealisasiRinc AS A')
    ->join('trRKA AS B', 'A.RKAID', 'B.RKAID')
    ->where('TABULAN', $tahun.$bulan)
    ->where('B.SOrgID', $SOrgID)
    ->where('A.EntryLvl', 2)
    ->where('A.TA', $tahun)
    ->delete();

    //copy realisasi    
    $str_insert = '
    INSERT INTO `trSnapshotRKARealisasiRinc` (
      `SnapshotID`,
      `RKARealisasiRincID`, 
      `RKAID`, 
      `RKARincID`, 
      `bulan1`, 
      `bulan2`, 
      `target1`, 
      `target2`,         
      `realisasi1`,  
      `realisasi2`,         
      `target_fisik1`,         
      `target_fisik2`,         
      `fisik1`,         
      `fisik2`,         
      `EntryLvl`,         
      `Descr`,         
      `TA`,         
      `TABULAN`,         
      `Locked`,  
      `RKARealisasiRincID_Src`,   
      `created_at`,
      `updated_at`
    )
    SELECT
      UUID(),
      A.`RKARealisasiRincID`, 
      A.`RKAID`, 
      A.`RKARincID`, 
      A.`bulan1`, 
      A.`bulan2`, 
      A.`target1`, 
      A.`target2`,         
      A.`realisasi1`,  
      A.`realisasi2`,         
      A.`target_fisik1`,         
      A.`target_fisik2`,         
      A.`fisik1`,         
      A.`fisik2`,         
      A.`EntryLvl`,         
      A.`Descr`,         
      A.`TA`,         
      '.$tahun.$bulan.',
      A.`Locked`,  
      A.`RKARealisasiRincID_Src`,                                   
      NOW(),
      NOW()
    FROM trRKARealisasiRinc AS A     
    JOIN trRKA AS B ON A.RKAID=B.RKAID 
    WHERE
      B.SOrgID="'.$SOrgID.'"
      AND A.EntryLvl=2
      AND A.TA='.$tahun;
    \DB::statement($str_insert);    

    $data = SnapshotRKAModel::where('SOrgID', $SOrgID)
      ->where('TA', $tahun)
      ->where('TABULAN', $tahun.$bulan)
      ->where('EntryLvl', 2)
      ->get();
              
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'unitkerja' => $unitkerja,
      'rka' => $data,
      'message' => 'Fetch data rka perubahan berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
    
  }
  public function populateDataRealisasi ($RKARincID)
  {
    $datauraian = SnapshotRKARincianModel::find($RKARincID);

    $data=[
      'datarealisasi'=>[],
      'totalanggarankas' => 0,
      'totalrealisasi' => 0,
      'totaltargetfisik' => 0,
      'totalfisik' => 0,
      'sisa_anggaran' => 0,
    ];
    if (!is_null($datauraian))        
    {
      $r = \DB::table('trSnapshotRKARealisasiRinc')
        ->select(\DB::raw('
          `RKARealisasiRincID`,
          `bulan2`,
          `target2`,
          `realisasi2`,
          target_fisik2,
          fisik2,
          `TA`,
          `TABULAN`,
          `Descr`,
          `created_at`,
          `updated_at`
          '))
        ->where('RKARincID', $RKARincID)
        ->where('TABULAN', $datauraian->TABULAN)
        ->orderBy('bulan2', 'ASC')
        ->get();

      $daftar_realisasi = [];
      $totalanggarankas=0;
      $totalrealisasi=0;
      $totaltargetfisik = 0;
      $totalfisik = 0;

      foreach ($r as $item)
      {
        $sum_realisasi = \DB::table('trSnapshotRKARealisasiRinc')
          ->where('RKARincID', $RKARincID)
          ->where('bulan2','<=', $item->bulan2)
          ->where('TABULAN', $item->TABULAN)
          ->sum('realisasi2');

        $sisa_anggaran = $datauraian->PaguUraian2-$sum_realisasi;            
        $daftar_realisasi[]=[
          'RKARealisasiRincID' => $item->RKARealisasiRincID,
          'bulan2' => $item->bulan2,
          'NamaBulan'=>Helper::getNamaBulan($item->bulan2),
          'target2' => $item->target2,
          'realisasi2' => $item->realisasi2,
          'target_fisik2' => $item->target_fisik2,
          'fisik2' => $item->fisik2,
          'sisa_anggaran' => $sisa_anggaran,
          'Descr' => $item->Descr,
          'TA' => $item->TA,
          'created_at' => $item->created_at,
          'updated_at' => $item->updated_at,
        ];
        
        $totalanggarankas+= $item->target2;
        $totalrealisasi+= $item->realisasi2;
        $totaltargetfisik+= $item->target_fisik2;
        $totalfisik+= $item->fisik2;
      }
      
      $data['datarealisasi'] = $daftar_realisasi;
      $data['totalanggarankas'] = $totalanggarankas;
      $data['totalrealisasi'] = $totalrealisasi;
      $data['totaltargetfisik']=round($totaltargetfisik,2);
      $data['totalfisik']=round($totalfisik,2);
      $data['sisa_anggaran'] = $datauraian->PaguUraian2-$totalrealisasi;			
    }        
    return $data;
  } 
	public function index(Request $request)
  {
    $this->hasPermissionTo('RENJA-SNAPSHOT-RKA-PERUBAHAN_BROWSE');

    $this->validate($request, [            
      'tahun' => 'required',            
      'bulan' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
      'SOrgID' => 'required|exists:tmSOrg,SOrgID',            
    ]);

    $tahun = $request->input('tahun');
    $bulan = $request->input('bulan');
    $SOrgID = $request->input('SOrgID');
    $unitkerja = SubOrganisasiModel::find($SOrgID);

    $data = SnapshotRKAModel::select(\DB::raw('
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
      keluaran2,
      tk_keluaran2,                            
      hasil2,                            
      tk_hasil2,                            
      capaian_program2,                            
      tk_capaian2,                            
      masukan2,                            
      ksk2,                            
      sifat_kegiatan2,                            
      waktu_pelaksanaan2,                            
      lokasi_kegiatan2,                            
      `PaguDana2`,                            
      `RealisasiKeuangan2`,                            
      `RealisasiFisik2`,   
      0 AS persen_keuangan2,
      nip_pa2,                            
      nip_kpa2,
      nip_ppk2,
      nip_pptk2,
      `Descr`,
      `TA`,
      `Locked`,
      created_at,
      updated_at
    '))
    ->where('SOrgID', $unitkerja->SOrgID)
    ->where('TA', $tahun)
    ->where('TABULAN', $tahun.$bulan)
    ->where('EntryLvl', 2)
    ->orderByRaw('kode_urusan="X" DESC')
    ->orderBy('kode_bidang', 'ASC')
    ->orderBy('kode_program', 'ASC')
    ->orderBy('kode_kegiatan', 'ASC')
    ->orderBy('kode_sub_kegiatan', 'ASC')
    ->get();   

    $is_locked = 0;
    $data->transform(function ($item, $key) {                            
      $item->persen_keuangan2=Helper::formatPersen($item->RealisasiKeuangan2, $item->PaguDana2);
      return $item;
    });
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'unitkerja' => $unitkerja,
      'rka' => $data,
      'locked' => $is_locked == 1,
      'message' => 'Fetch data rka perubahan berhasil diperoleh'
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
    $this->hasPermissionTo('RENJA-SNAPSHOT-RKA-PERUBAHAN_SHOW');

    $rka = $this->getDataRKA($id);

    if (is_null($rka))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',                
        'message'=>"Fetch data kegiatan perubahan dengan id ($id) gagal diperoleh"
      ], 422); 
    }
    else
    {
      $data = SnapshotRKARincianModel::select(\DB::raw('
        `trSnapshotRKARinc`.`RKARincID`,
        `trSnapshotRKARinc`.`RKAID`,
        `trSnapshotRKARinc`.`SIPDID`,
        `trSnapshotRKARinc`.`JenisPelaksanaanID`,
        `trSnapshotRKARinc`.`SumberDanaID`,
        `trSnapshotRKARinc`.`JenisPembangunanID`,
        `trSnapshotRKARinc`.kode_uraian2 AS kode_uraian,
        `trSnapshotRKARinc`.`NamaUraian2` AS nama_uraian,
        `trSnapshotRKARinc`.`volume2`,
        `trSnapshotRKARinc`.`satuan2`,
        CONCAT(`trSnapshotRKARinc`.volume2,\' \',`trSnapshotRKARinc`.satuan2) AS volume,
        `trSnapshotRKARinc`.`harga_satuan2`,
        `trSnapshotRKARinc`.`PaguUraian2`,
        0 AS `realisasi2`,
        0 AS `persen_keuangan2`,
        0 AS `fisik2`,                                                                        
        \'\' AS `provinsi_id`,
        \'\' AS `kabupaten_id`,
        \'\' AS `kecamatan_id`,
        \'\' AS `desa_id`,                                    
        `trSnapshotRKARinc`.`idlok`,
        `trSnapshotRKARinc`.`ket_lok`,
        `trSnapshotRKARinc`.`rw`,
        `trSnapshotRKARinc`.`rt`,
        `trSnapshotRKARinc`.`nama_perusahaan`,
        `trSnapshotRKARinc`.`alamat_perusahaan`,
        `trSnapshotRKARinc`.`no_telepon`,
        `trSnapshotRKARinc`.`nama_direktur`,
        `trSnapshotRKARinc`.`npwp`,
        `trSnapshotRKARinc`.`no_kontrak`,
        `trSnapshotRKARinc`.`tgl_mulai_pelaksanaan`,
        `trSnapshotRKARinc`.`tgl_selesai_pelaksanaan`,
        `trSnapshotRKARinc`.`status_lelang`,
        `trSnapshotRKARinc`.`Descr`,                                    
        `trSnapshotRKARinc`.`TA`,
        `trSnapshotRKARinc`.`TABULAN`,
        `trSnapshotRKARinc`.`Locked`,
        `trSnapshotRKARinc`.created_at,
        `trSnapshotRKARinc`.updated_at
      '))                                
      ->where('RKAID', $rka->RKAID)
      ->where('TABULAN', $rka->TABULAN)
      ->orderBy('trSnapshotRKARinc.kode_uraian2', 'ASC')
      ->get();
      
      $data->transform(function ($item, $key) {
        $item->realisasi2=\DB::table('trSnapshotRKARealisasiRinc')
        ->where('RKARincID', $item->RKARincID)
        ->where('TABULAN', $item->TABULAN)
        ->sum('realisasi2');    
        $item->fisik2=\DB::table('trSnapshotRKARealisasiRinc')
        ->where('RKARincID', $item->RKARincID)
        ->where('TABULAN', $item->TABULAN)
        ->sum('fisik2');
        $item->persen_keuangan2=Helper::formatPersen($item->realisasi2,$item->PaguUraian2);
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
              $item->desa_id = $lokasi->desa_id;
              $item->kecamatan_id = $lokasi->kecamatan_id;
              $item->kabupaten_id = $lokasi->kabupaten_id;
              $item->provinsi_id = $lokasi->provinsi_id;                            
            }
          break;
          case 'kecamatan' :
            $lokasi=\App\Models\DMaster\KecamatanModel::select(\DB::raw('`wilayah_kecamatan`.`id` AS kecamatan_id, `wilayah_kabupaten`.`id` AS kabupaten_id, `wilayah_provinsi`.`id` AS provinsi_id'))                                                            
                              ->join('wilayah_kabupaten','wilayah_kecamatan.kabupaten_id','wilayah_kabupaten.id')
                              ->join('wilayah_provinsi','wilayah_provinsi.id','wilayah_kabupaten.provinsi_id')                                                            
                              ->find($item->idlok);

            if (!is_null($lokasi))
            {
              $item->kecamatan_id = $lokasi->kecamatan_id;
              $item->kabupaten_id = $lokasi->kabupaten_id;
              $item->provinsi_id = $lokasi->provinsi_id;
            }
          break;
          case 'kota' :
            $lokasi=\App\Models\DMaster\KabupatenModel::select(\DB::raw('`wilayah_kabupaten`.`id` AS kabupaten_id, `wilayah_provinsi`.`id` AS provinsi_id'))                                                                                                                        
                              ->join('wilayah_provinsi','wilayah_provinsi.id','wilayah_kabupaten.provinsi_id')                                                            
                              ->find($item->idlok);

            if (!is_null($lokasi))
            {
              $item->kabupaten_id = $lokasi->kabupaten_id;
              $item->provinsi_id = $lokasi->provinsi_id;
            }
          break;
          case 'provinsi' :
            $lokasi=\App\Models\DMaster\ProvinsiModel::select(\DB::raw('`wilayah_provinsi`.`id` AS provinsi_id'))                                                                                                                                                                                                                                            
                              ->find($item->idlok);

            if (!is_null($lokasi))
            {
              $item->provinsi_id = $lokasi->provinsi_id;
            }
          break;                
        }
        return $item;
      });
      
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',
        'datakegiatan' => $rka,
        'uraian' => $data,
        'message' => 'Fetch data rincian kegiatan berhasil diperoleh'
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
    $this->hasPermissionTo('RENJA-SNAPSHOT-RKA-PERUBAHAN_SHOW');

    $this->validate($request, [            
      'mode' => 'required',            
      'RKARincID' => 'required|exists:trRKARinc,RKARincID',            
    ]);
    $mode = $request->input('mode');
    $RKARincID = $request->input('RKARincID');
    
    $data_uraian = SnapshotRKARincianModel::select(\DB::raw('
      `SIPDID`,
      kode_uraian2 AS kode_uraian,
      NamaUraian2 AS nama_uraian,
      `PaguUraian2`
    '))
    ->find($RKARincID);
    
    $data_realisasi = \DB::table('trSnapshotRKARealisasiRinc')
      ->select(\DB::raw('
        COALESCE(SUM(target2),0) AS jumlah_targetanggarankas,
        COALESCE(SUM(realisasi2),0) AS jumlah_realisasi,
        COALESCE(SUM(target_fisik2),0) AS jumlah_targetfisik,
        COALESCE(SUM(fisik2),0) AS jumlah_fisik
      '))
      ->where('RKARincID', $RKARincID)
      ->where('TABULAN', $data_uraian->TABULAN)
      ->get();

    $target = ['fisik' => 0,'anggaran' => 0];			
    if ($mode == 'targetfisik')
    {
      $data = \DB::table('trSnapshotRKATargetRinc')
        ->select(\DB::raw("
        CONCAT('{',
          GROUP_CONCAT(
            TRIM(
              LEADING '{' FROM TRIM(
                TRAILING '}' FROM JSON_OBJECT(CONCAT('fisik_',`trSnapshotRKATargetRinc`.`bulan2`),`trSnapshotRKATargetRinc`.`fisik2`)
              )
            )
          ),
        '}') AS `fisik2`							
      "))
      ->where('RKARincID', $RKARincID)
      ->where('TABULAN', $data_uraian->TABULAN)
      ->get();                    
      $target=isset($data[0]) ? json_decode($data[0]->fisik2, true) : [];
    }
    else if ($mode == 'targetanggarankas')
    {            
      $data = \DB::table('trSnapshotRKATargetRinc')
        ->select(\DB::raw("						
        CONCAT('{',
          GROUP_CONCAT(
            TRIM(
              LEADING '{' FROM TRIM(
                TRAILING '}' FROM JSON_OBJECT(CONCAT('anggaran_',`trSnapshotRKATargetRinc`.`bulan2`),`trSnapshotRKATargetRinc`.`target2`)
              )
            )
          ),
        '}') AS `anggaran2`
      "))
      ->where('RKARincID', $RKARincID)
      ->where('TABULAN', $data_uraian->TABULAN)
      ->get();      

      $target=isset($data[0]) ? json_decode($data[0]->anggaran2, true) : [];
    }
    else if ($mode == 'bulan' && $request->has('bulan2'))
    {
      $bulan2 = $request->input('bulan2');
      
      $data = \DB::table('trSnapshotRKATargetRinc')
        ->select(\DB::raw("
          CONCAT('{',
            GROUP_CONCAT(
              TRIM(
                LEADING '{' FROM TRIM(
                  TRAILING '}' FROM JSON_OBJECT(CONCAT('fisik_',`trSnapshotRKATargetRinc`.`bulan2`),`trSnapshotRKATargetRinc`.`fisik2`)
                )
              )
            ),
          '}') AS `fisik2`,
          CONCAT('{',
            GROUP_CONCAT(
              TRIM(
                LEADING '{' FROM TRIM(
                  TRAILING '}' FROM JSON_OBJECT(CONCAT('anggaran_',`trSnapshotRKATargetRinc`.`bulan2`),`trSnapshotRKATargetRinc`.`target2`)
                )
              )
            ),
          '}') AS `anggaran2`
        "))
        ->where('RKARincID', $RKARincID)
        ->where('TABULAN', $data_uraian->TABULAN)
        ->groupBy('RKARincID')
        ->get();                  		
      
      if (isset($data[0]))
      {
        $fisik2 = json_decode($data[0]->fisik2, true);
        $anggaran2 = json_decode($data[0]->anggaran2, true);                
        $target['fisik'] = is_null($fisik2) ? 0 : $fisik2["fisik_$bulan2"];
        $target['anggaran'] = is_null($anggaran2) ? 0 : $anggaran2["anggaran_$bulan2"];                
      }            
    }
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'mode' => $mode,
      'datauraian' => $data_uraian,
      'target' => $target,
      'datarealisasi' => $data_realisasi[0],
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
    $this->hasPermissionTo('RENJA-SNAPSHOT-RKA-PERUBAHAN_SHOW');

    $this->validate($request, [            
      'RKARincID' => 'required|exists:trSnapshotRKARinc,RKARincID',            
    ]);
    
    $RKARincID = $request->input('RKARincID');
    $data = $this->populateDataRealisasi($RKARincID); 

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'realisasi' => $data['datarealisasi'],
      'totalanggarankas' => $data['totalanggarankas'],
      'totalrealisasi' => $data['totalrealisasi'],
      'totaltargetfisik' => $data['totaltargetfisik'],
      'totalfisik' => $data['totalfisik'],
      'sisa_anggaran' => $data['sisa_anggaran'],
      'message'=>"Fetch data realisasi berhasil diperoleh"
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
  }
  public function destroy(Request $request, $id)
  {
    $this->hasPermissionTo('RENJA-SNAPSHOT-RKA-PERUBAHAN_BROWSE');

    $this->validate($request, [            
			'pid' => 'required|in:all',
			'SOrgID' => 'required|exists:tmSOrg,SOrgID',
		]); 

    $SOrgID = $request->input('SOrgID');
    $pid = $request->input('pid');

		switch ($pid)
		{ 
      case 'all':
        \DB::table('trSnapshotRKARealisasiRinc AS A')
        ->join('trSnapshotRKA AS B', 'B.RKAID', 'A.RKAID')
        ->where('B.TABULAN', $id)
        ->where('B.SOrgID', $SOrgID)
        ->delete();

        \DB::table('trSnapshotRKATargetRinc AS A')
        ->join('trSnapshotRKA AS B', 'B.RKAID', 'A.RKAID')
        ->where('B.TABULAN', $id)
        ->where('B.SOrgID', $SOrgID)
        ->delete();

        \DB::table('trSnapshotRKARinc AS A')
        ->join('trSnapshotRKA AS B', 'B.RKAID', 'A.RKAID')
        ->where('B.TABULAN', $id)
        ->where('B.SOrgID', $SOrgID)
        ->delete();

        $str = "DELETE FROM trSnapshotRKA WHERE TABULAN='$id' AND SOrgID='$SOrgID'";
        \DB::statement($str);        
        
        $message = 'Hapus snapshot berhasil';
      break;
    }
        
    return Response()->json([
      'status' => 1,
      'pid' => 'destroy',                
      'message' => $message,
    ], 200);
  }
}