<?php

namespace App\Http\Controllers\Snapshot;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

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

    $str_insert = '
    INSERT INTO `trSnapshotRKA` (
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
      `TABULAN`,
      `Locked`,
      created_at,
      updated_at
    )
    SELECT
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
      `'.$tahun.$bulan.'`,
      `Locked`,
      created_at,
      updated_at
    FROM
      trRKA
    WHERE
      EntryLvl=1
      AND TA='.$tahun;
    \DB::statement($str_insert); 
    
    $data = SnapshotRKAModel::where('kode_sub_organisasi',$unitkerja->kode_sub_organisasi)
      ->where('TA',$tahun)
      ->where('TABULAN',$tahun)
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
}