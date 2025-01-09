<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\SubOrganisasiModel;
use App\Models\DMaster\SIPDModel;
use App\Models\DMaster\KodefikasiSubKegiatanModel;
use App\Models\Renja\RKAModel;
use App\Models\Renja\RKARincianModel;
use App\Models\Renja\RKARencanaTargetModel;
use App\Models\Renja\RKARealisasiModel;

use Ramsey\Uuid\Uuid;
use Illuminate\Validation\Rule;

class TargetKinerjaMurniController extends Controller 
{
  protected $total_pagu = 0;
  protected $total_target_keuangan = 0;
  protected $total_target_fisik = 0;
  protected $total_sub_kegiatan = 0;

  private function recalculate($RKAID)
  {
    $paguuraian = \DB::table('trRKARinc')                            
    ->where('RKAID', $RKAID)
    ->sum('PaguUraian1');

    $jumlah_uraian = \DB::table('trRKARinc')                            
              ->where('RKAID', $RKAID)
              ->count('RKARincID');

    $data_realisasi = \DB::table('trRKARealisasiRinc')
              ->select(\DB::raw('
                COALESCE(SUM(realisasi1),0) AS jumlah_realisasi,
                COALESCE(SUM(fisik1),0) AS jumlah_fisik
              '))
              ->where('RKAID', $RKAID)
              ->get();
    
    $rka = RKAModel::find($RKAID);
    $rka->PaguDana1 = $paguuraian;
    $rka->RealisasiKeuangan1 = $data_realisasi[0]->jumlah_realisasi;
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
              ->leftJoin('tmSumberDana', 'tmSumberDana.SumberDanaID', 'trRKA.SumberDanaID')
              ->where('trRKA.EntryLvl', 1)
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
      'totalanggarankas' => 0,
      'totalrealisasi' => 0,
      'totaltargetfisik' => 0,
      'totalfisik' => 0,
      'sisa_anggaran' => 0,
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
              ->where('RKARincID', $RKARincID)
              ->orderBy('bulan1', 'ASC')
              ->get();

      $daftar_realisasi = [];
      $totalanggarankas=0;
      $totalrealisasi=0;
      $totaltargetfisik = 0;
      $totalfisik = 0;

      foreach ($r as $item)
      {
        $sum_realisasi = \DB::table('trRKARealisasiRinc')
                ->where('RKARincID', $RKARincID)
                ->where('bulan1', '<=', $item->bulan1)
                ->sum('realisasi1');

        $sisa_anggaran = $datauraian->PaguUraian1-$sum_realisasi;            
        $daftar_realisasi[]=[
          'RKARealisasiRincID' => $item->RKARealisasiRincID,
          'bulan1' => $item->bulan1,
          'NamaBulan'=>Helper::getNamaBulan($item->bulan1),
          'target1' => $item->target1,
          'realisasi1' => $item->realisasi1,
          'target_fisik1' => $item->target_fisik1,
          'fisik1' => $item->fisik1,
          'sisa_anggaran' => $sisa_anggaran,
          'Descr' => $item->Descr,
          'TA' => $item->TA,
          'created_at' => $item->created_at,
          'updated_at' => $item->updated_at,
        ];
        
        $totalanggarankas+= $item->target1;
        $totalrealisasi+= $item->realisasi1;
        $totaltargetfisik+= $item->target_fisik1;
        $totalfisik+= $item->fisik1;
      }
      
      $data['datarealisasi'] = $daftar_realisasi;
      $data['totalanggarankas'] = $totalanggarankas;
      $data['totalrealisasi'] = $totalrealisasi;
      $data['totaltargetfisik']=round($totaltargetfisik,2);
      $data['totalfisik']=round($totalfisik,2);
      $data['sisa_anggaran'] = $datauraian->PaguUraian1-$totalrealisasi;
      
    }        
    return $data;
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
      'tahun' => 'required',            
      'SOrgID' => 'required|exists:tmSOrg,SOrgID',            
    ]);
    $tahun = $request->input('tahun');
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
              0 AS `TargetKeuangan1`,                            
              0 AS `TargetFisik1`,
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
            ->orderBy('kode_bidang', 'ASC')
            ->orderBy('kode_program', 'ASC')
            ->orderBy('kode_kegiatan', 'ASC')
            ->orderBy('kode_sub_kegiatan', 'ASC')
            ->get();        
    
    $data->transform(function ($item, $key) {
      $item->TargetKeuangan1 = \DB::table('trRKATargetRinc')
        ->where('RKAID', $item->RKAID)				
        ->sum('target1');
      $this->total_target_keuangan += $item->TargetKeuangan1;

      $jumlah_uraian = \DB::table('trRKARinc')
        ->where('RKAID', $item->RKAID)				
        ->count();

      $total_fisik = \DB::table('trRKATargetRinc')
        ->where('RKAID', $item->RKAID)				
        ->sum('fisik1');

      $item->TargetFisik1 = Helper::formatPecahan($total_fisik,$jumlah_uraian);
      $this->total_target_fisik += $item->TargetFisik1;
      $this->total_sub_kegiatan += 1;
      $this->total_pagu += $item->PaguDana1;
      return $item;
    });		

    return Response()->json([
                'status' => 1,
                'pid' => 'fetchdata',
                'unitkerja' => $unitkerja,
                'total_pagu' => $this->total_pagu,
                'total_target_keuangan' => $this->total_target_keuangan,
                'total_target_fisik'=>Helper::formatPecahan($this->total_target_fisik,$this->total_sub_kegiatan),
                'rka' => $data,
                'message' => 'Fetch data rka murni berhasil diperoleh'
              ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  /**
   * digunakan untuk mendapatkan daftar 
   */
  public function targetkinerjauraian(Request $request, $id)
  {
    $data = \DB::table('trRKATargetRinc')
      ->select(\DB::raw('
        `RKATargetRincID`,
        bulan1,
        target1,
        fisik1,
        `Locked`,
        created_at,
        updated_at
      '))
      ->where('RKARincID', $id)
      ->orderBy('bulan1', 'asc')
      ->get();

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',			
      'targetkinerja' => $data,
      'message' => 'Fetch data rencana target berhasil diperoleh'
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
      'RKARincID' => 'required|exists:trRKARinc,RKARincID',            
      'bulan_fisik.*' => 'required',
    ]);

    
    $bulan_fisik= $request->input('bulan_fisik');      
    $data = [];
    $now = \Carbon\Carbon::now('Asia/Jakarta')->toDateTimeString();
    for ($i=0;$i < 12; $i+=1)
    {
      $data[]=[
        'RKATargetRincID'=>Uuid::uuid4()->toString(),
        'RKAID' => $request->input('RKAID'),
        'RKARincID' => $request->input('RKARincID'),
        'bulan1' => $i+1,
        'bulan2' => $i+1,
        'target1' => 0,
        'target2' => 0,
        'fisik1' => $bulan_fisik[$i],
        'fisik2' => 0,
        'EntryLvl' => 1,
        'Descr' => $request->input('Descr'),
        'TA' => $request->input('tahun'),
        'created_at' => $now,
        'updated_at' => $now,
      ];
    }
    RKARencanaTargetModel::insert($data);

    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'message' => 'Rencana target fisik uraian berhasil disimpan.'
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
      'RKATargetRincID' => 'required|exists:trRKATargetRinc,RKATargetRincID',            
      'fisik1' => 'required|numeric|between:0,100.00',
    ]);
    
    $target_kinerja = RKARencanaTargetModel::find($request->input('RKATargetRincID'));
    $uraian = $target_kinerja->uraian;

    $jumlah_target = RKARencanaTargetModel::where('RKARincID', $target_kinerja->RKARincID)->sum('fisik1') - $target_kinerja->fisik1;
    $jumlah_target = $jumlah_target + $request->input('fisik1');
    if ($jumlah_target > 100)
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',				
        'message'=>"Rencana target fisik uraian gagal diubah karena jumlah fisik ($jumlah_target) melampaui 100."
      ], 422)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    }		

    $target_kinerja->fisik1 = $request->input('fisik1');
    $target_kinerja->save();

    return Response()->json([
                'status' => 1,
                'pid' => 'update',
                'target_kinerja' => $target_kinerja,
                'message' => 'Rencana target fisik uraian berhasil diubah.'
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
      'RKARincID' => 'required|exists:trRKARinc,RKARincID',            
      'bulan_fisik.*' => 'required',
    ]);

    $bulan_anggaran= $request->input('bulan_anggaran');      
    $data = [];
    $now = \Carbon\Carbon::now('Asia/Jakarta')->toDateTimeString();
    for ($i=0;$i < 12; $i+=1)
    {
      $data[]=[
        'RKATargetRincID'=>Uuid::uuid4()->toString(),
        'RKAID' => $request->input('RKAID'),
        'RKARincID' => $request->input('RKARincID'),
        'bulan1' => $i+1,
        'bulan2' => $i+1,
        'fisik1' => 0,
        'fisik2' => 0,
        'target1' => $bulan_anggaran[$i],
        'target2' => 0,
        'EntryLvl' => 1,
        'Descr' => $request->input('Descr'),
        'TA' => $request->input('tahun'),
        'created_at' => $now,
        'updated_at' => $now,
      ];
    }
    RKARencanaTargetModel::insert($data);

    return Response()->json([
                'status' => 1,
                'pid' => 'store',
                'message' => 'Rencana target anggaran kas uraian berhasil disimpan.'
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
      'RKATargetRincID' => 'required|exists:trRKATargetRinc,RKATargetRincID',            
      'target1' => 'required|numeric',
    ]);
    $RKATargetRincID = $request->input('RKATargetRincID');
    $target1 = $request->input('target1');
    $target_kinerja = RKARencanaTargetModel::find($RKATargetRincID);
    $uraian = $target_kinerja->uraian;

    $jumlah_target = RKARencanaTargetModel::where('RKARincID', $target_kinerja->RKARincID)->sum('target1') - $target_kinerja->target1;
    $jumlah_target = $jumlah_target + $target1;
    if ($jumlah_target > $uraian->PaguUraian1)
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',				
        'message'=>"Rencana target anggaran kas uraian gagal diubah karena jumlah anggaran kas ($jumlah_target) melampaui Pagu Uraian ({$uraian->PaguUraian1})."
      ], 422)->setEncodingOptions(JSON_NUMERIC_CHECK); 
    }
    
    \DB::statement("UPDATE `trRKATargetRinc` SET `target1`='$target1' WHERE `RKATargetRincID`='$RKATargetRincID'");				

    return Response()->json([
                'status' => 1,
                'pid' => 'update',
                'target_kinerja' => $target_kinerja,
                'message' => 'Rencana target anggaran kas uraian berhasil diubah.'
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
    $target_kinerja = [];
    return Response()->json([
      'status' => 1,
      'pid' => 'update',
      'target_kinerja' => $target_kinerja,
      'message' => 'Rencana target anggaran kas uraian berhasil diubah.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK); 
  }	
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  { 
    $this->hasPermissionTo('RENJA-RKA-MURNI_DESTROY');

    $target_kinerja = RKARencanaTargetModel::find($id);

    if (is_null($target_kinerja))
    {
      return Response()->json([
                              'status' => 0,
                              'pid' => 'destroy',                
                              'message'=>["Target Kinerja ($id) gagal dihapus"]
                          ], 422); 
    }
    else
    {	
      $target_kinerja->delete();
      return Response()->json([
                              'status' => 1,
                              'pid' => 'destroy',                
                              'message'=>"Target Kinerja dengan ID ($id) berhasil dihapus"
                          ], 200);
    }		
        
  }
}