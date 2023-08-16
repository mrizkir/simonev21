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

class SnapshotRKAMurniController extends Controller 
{
  public function loaddatakegiatanFirsttime(Request $request)
  { 
    $this->validate($request, [            
      'tahun'=>'required',
      'bulan'=>'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
      'SOrgID'=>'required|exists:tmSOrg,SOrgID',
    ]); 
    
    $SOrgID=$request->input('SOrgID');
    $unitkerja = SubOrganisasiModel::find($SOrgID);

    $tahun = $request->input('tahun');
    $bulan = $request->input('bulan');

    \DB::beginTransaction();
    $str_insert = '
    INSERT INTO `trSnapshotRKA` (
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
      kode_sub_organisasi="'.$unitkerja->kode_sub_organisasi.'"
      AND EntryLvl=1
      AND TA='.$tahun;
    \DB::statement($str_insert);
    
    //copy rincian
    $str_insert = '
    INSERT INTO `trSnapshotRKARinc` (
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
    JOIN trRKA AS B ON A.RKAID=A.RKAID 
    WHERE
      B.kode_sub_organisasi="'.$unitkerja->kode_sub_organisasi.'"
      AND A.EntryLvl=1
      AND A.TA='.$tahun;
    \DB::statement($str_insert);
    
    //copy target    
    $str_insert = '
    INSERT INTO `trSnapshotRKATargetRinc` (
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
    JOIN trRKA AS B ON A.RKAID=A.RKAID 
    WHERE
      B.kode_sub_organisasi="'.$unitkerja->kode_sub_organisasi.'"
      AND A.EntryLvl=1
      AND A.TA='.$tahun;
    \DB::statement($str_insert);

    //copy realisasi    
    $str_insert = '
    INSERT INTO `trSnapshotRKARealisasiRinc` (
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
    JOIN trRKA AS B ON A.RKAID=A.RKAID 
    WHERE
      B.kode_sub_organisasi="'.$unitkerja->kode_sub_organisasi.'"
      AND A.EntryLvl=1
      AND A.TA='.$tahun;
    \DB::statement($str_insert);

    \DB::commit();

    $data = SnapshotRKAModel::where('kode_sub_organisasi', $unitkerja->kode_sub_organisasi)
      ->where('TA',$tahun)
      ->where('TABULAN', $tahun.$bulan)
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
	public function index(Request $request)
  {
    $this->hasPermissionTo('RENJA-SNAPSHOT-RKA-MURNI_BROWSE');

    $this->validate($request, [            
      'tahun'=>'required',            
      'bulan'=>'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
      'SOrgID'=>'required|exists:tmSOrg,SOrgID',            
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
    ->where('TABULAN', $tahun.$bulan)
    ->where('EntryLvl', 1)
    ->orderByRaw('kode_urusan="X" DESC')
    ->orderBy('kode_bidang','ASC')
    ->orderBy('kode_program','ASC')
    ->orderBy('kode_kegiatan','ASC')
    ->orderBy('kode_sub_kegiatan','ASC')
    ->get();   

    $is_locked = 0;
    $data->transform(function ($item,$key) {                            
      $item->persen_keuangan1=Helper::formatPersen($item->RealisasiKeuangan1, $item->PaguDana1);
      return $item;
    });
    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'unitkerja'=>$unitkerja,
      'rka'=>$data,
      'locked'=>$is_locked == 1,
      'message'=>'Fetch data rka murni berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  public function destroy(Request $request, $id)
  {
    $this->hasPermissionTo('RENJA-SNAPSHOT-RKA-MURNI_BROWSE');

    $this->validate($request, [            
			'pid'=>'required|in:all',
			'SOrgID'=>'required|exists:tmSOrg,SOrgID',
		]); 

    $SOrgID=$request->input('SOrgID');
    $pid=$request->input('pid');

    \DB::beginTransaction();
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

        \DB::table('trSnapshotRKA')
        ->where('TABULAN', $id)
        ->where('SOrgID', $SOrgID)
        ->delete();       
        
        $message = 'Hapus snapshot berhasil';
      break;
    }
    \DB::commit();
    
    return Response()->json([
      'status'=>1,
      'pid'=>'destroy',                
      'message'=>$message,
    ], 200);
  }
}