<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\DMaster\SubOrganisasiModel;
use App\Models\DMaster\SIPDModel;

class DataMentahMurniController extends Controller 
{
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
      'OrgID' => 'required|exists:tmOrg,OrgID',
    ]);

    $SOrgID = null;
    $unitkerja = null;
    if($request->filled('SOrgID'))
    {
      $this->validate($request, [         
        'SOrgID' => 'required|exists:tmSOrg,SOrgID',
      ]);

      $SOrgID = $request->input('SOrgID');
      $unitkerja = SubOrganisasiModel::find($SOrgID);
    }    
    $tahun = $request->input('tahun');
    $OrgID = $request->input('OrgID');
    $organisasi = OrganisasiModel::find($OrgID);
   
    $data = \DB::table('sipd')
      ->select(\DB::raw('                            
        DISTINCT(kd_keg_gabung) AS kode_kegiatan,                            
        `Kd_Urusan1` AS `Kd_Urusan`,
        `Nm_Urusan`,
        `Kd_Bidang`,
        `Nm_Bidang_Urusan` AS `Nm_Bidang`,
        nm_kegiatan AS `KgtNm`,
        kd_prog_gabungan AS kode_program,                                                        
        nm_program AS `PrgNm`,
        0 AS `PaguDana1`,
        \'BELUM DICOPY\' AS status,
        `TA`
      '))
      ->where('OrgID', $OrgID)
      ->where('TA', $tahun)
      ->where('EntryLevel', 1);
      
      if(!is_null($SOrgID))
      {
        $data = $data->where('SOrgID', $SOrgID);
      }

      $data = $data->orderBy('kode_program', 'ASC')
      ->orderBy('kode_kegiatan', 'ASC')
      ->get();        
    
    $data->transform(function($item, $key) use ($organisasi, $unitkerja) 
    {
      $rka = \DB::table('trRKA')
      ->where('OrgID', $organisasi->OrgID);

      if(!is_null($unitkerja))
      {
        $rka = $rka->where('SOrgID', $unitkerja->SOrgID);
      }
      
      $rka = $rka->where('TA', $organisasi->TA)
      ->where('EntryLvl', 1)
      ->where('kode_kegiatan', $item->kode_kegiatan)
      ->get();

      $item->Kd_Urusan = $item->Kd_Urusan;
      $item->Kd_Bidang = $item->Kd_Urusan.'.'.$item->Kd_Bidang;

      if (isset($rka[0]))
      {
        $item->status='SUDAH DICOPY';
      }
      $item->PaguDana1=\DB::table('sipd')
        ->where('EntryLevel', 1)
        ->where('TA', $organisasi->TA)
        ->where('kd_keg_gabung', $item->kode_kegiatan)
        ->where('OrgID', $organisasi->OrgID)
        ->sum('PaguUraian1');
              
      return $item;
    });
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'organisasi' => $organisasi,
      'unitkerja' => $unitkerja,
      'rka' => $data,
      'message' => 'Fetch data rka perubahan berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);              
  }   

  public function copyrka(Request $request)
  {
    $this->validate($request, [             
      'OrgID' => 'required|exists:tmOrg,OrgID',            
      'kode_kegiatan' => 'required',            
    ]);
    
    $OrgID = $request->input('OrgID');
    $opd = OrganisasiModel::find($OrgID);
    $kode_kegiatan = $request->input('kode_kegiatan');
    
    $user_id = $this->getUserid();

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
        '.$opd->TA.' AS `TA`,
        false AS `Locked`,
        NOW() AS created_at,
        NOW() AS updated_at
      FROM sipd WHERE kd_keg_gabung=\''.$kode_kegiatan.'\' AND 
              `OrgID`=\''.$OrgID.'\' AND
              `TA`='.$opd->TA.' AND 
              `EntryLevel`=1
    ) AS temp
    ';
    \DB::statement($str_insert); 
    return Response()->json([
      'status' => 1,
      'pid' => 'store',                                
      'message' => 'Salin kegiatan dari data mentar ke RKA Murni berhasil'
    ], 200);    
  }

  public function updaterekening(Request $request)
  {
    $this->hasPermissionTo('RENJA-RKA-MURNI_BROWSE');
    
    $this->validate($request, [            
      'tahun' => 'required',
    ]);

    $tahun = $request->input('tahun');
    $show_log = $request->has('log');

    // 1. Ambil semua sipd untuk tahun ini (satu query)
    $data = \DB::table('sipd')
      ->select('SIPDID', 'kd_rek1', 'kd_rek2', 'kd_rek3', 'kd_rek4', 'kd_rek5', 'kd_rek6')
      ->where('TA', $tahun)
      ->get();

    if ($data->isEmpty()) {
      return response()->json([
        'status' => 1,
        'pid' => 'update',
        'message' => 'Tidak ada data sipd untuk tahun ' . $tahun,
      ], 200);
    }

    // 2. Kumpulkan kode rekening unik (biasanya jauh lebih sedikit dari total baris)
    $kodeRekeningUnik = $data->map(function ($item) {
      return implode('.', [$item->kd_rek1, $item->kd_rek2, $item->kd_rek3, $item->kd_rek4, $item->kd_rek5, $item->kd_rek6]);
    })->unique()->values()->all();

    // 3. Ambil semua v_rekening yang dibutuhkan dalam satu query
    $vRekeningRows = \DB::table('v_rekening')
      ->select('kode_rek_6', 'Nm_Akun', 'KlpNm', 'JnsNm', 'ObyNm', 'RObyNm', 'SubRObyNm')
      ->where('TA', $tahun)
      ->whereIn('kode_rek_6', $kodeRekeningUnik)
      ->get();

    $vRekeningMap = $vRekeningRows->keyBy('kode_rek_6');

    // 4. Siapkan daftar update (hanya yang ketemu di v_rekening)
    $updates = [];
    foreach ($data as $item) {
      $kode = implode('.', [$item->kd_rek1, $item->kd_rek2, $item->kd_rek3, $item->kd_rek4, $item->kd_rek5, $item->kd_rek6]);
      $v_rek = $vRekeningMap->get($kode);
      if (is_null($v_rek)) {
        if ($show_log) {
          echo $item->SIPDID . " - Rekening tidak ditemukan<br><br>";
        }
        continue;
      }
      $updates[] = [
        'SIPDID' => $item->SIPDID,
        'nm_rek1' => $v_rek->Nm_Akun,
        'nm_rek2' => $v_rek->KlpNm,
        'nm_rek3' => $v_rek->JnsNm,
        'nm_rek4' => $v_rek->ObyNm,
        'nm_rek5' => $v_rek->RObyNm,
        'nm_rek6' => $v_rek->SubRObyNm,
      ];
      if ($show_log) {
        echo $item->SIPDID . " - " . $v_rek->Nm_Akun . " - " . $v_rek->KlpNm . " - " . $v_rek->JnsNm . " - " . $v_rek->ObyNm . " - " . $v_rek->RObyNm . " - " . $v_rek->SubRObyNm . "<br><br>";
      }
    }

    // 5. Update sipd per batch (500 baris per query) pakai CASE WHEN
    $chunkSize = 500;
    foreach (array_chunk($updates, $chunkSize) as $chunk) {
      $sipdIds = array_column($chunk, 'SIPDID');
      $caseRek1 = $caseRek2 = $caseRek3 = $caseRek4 = $caseRek5 = $caseRek6 = [];
      $bindings = [];
      foreach ($chunk as $u) {
        $caseRek1[] = 'WHEN ? THEN ?';
        $bindings[] = $u['SIPDID'];
        $bindings[] = $u['nm_rek1'];
      }
      foreach ($chunk as $u) {
        $caseRek2[] = 'WHEN ? THEN ?';
        $bindings[] = $u['SIPDID'];
        $bindings[] = $u['nm_rek2'];
      }
      foreach ($chunk as $u) {
        $caseRek3[] = 'WHEN ? THEN ?';
        $bindings[] = $u['SIPDID'];
        $bindings[] = $u['nm_rek3'];
      }
      foreach ($chunk as $u) {
        $caseRek4[] = 'WHEN ? THEN ?';
        $bindings[] = $u['SIPDID'];
        $bindings[] = $u['nm_rek4'];
      }
      foreach ($chunk as $u) {
        $caseRek5[] = 'WHEN ? THEN ?';
        $bindings[] = $u['SIPDID'];
        $bindings[] = $u['nm_rek5'];
      }
      foreach ($chunk as $u) {
        $caseRek6[] = 'WHEN ? THEN ?';
        $bindings[] = $u['SIPDID'];
        $bindings[] = $u['nm_rek6'];
      }
      $placeholders = implode(',', array_fill(0, count($sipdIds), '?'));
      $bindings = array_merge($bindings, $sipdIds);
      $sql = "UPDATE sipd SET "
        . "nm_rek1 = CASE SIPDID " . implode(' ', $caseRek1) . " END, "
        . "nm_rek2 = CASE SIPDID " . implode(' ', $caseRek2) . " END, "
        . "nm_rek3 = CASE SIPDID " . implode(' ', $caseRek3) . " END, "
        . "nm_rek4 = CASE SIPDID " . implode(' ', $caseRek4) . " END, "
        . "nm_rek5 = CASE SIPDID " . implode(' ', $caseRek5) . " END, "
        . "nm_rek6 = CASE SIPDID " . implode(' ', $caseRek6) . " END "
        . "WHERE SIPDID IN (" . $placeholders . ")";
      \DB::update($sql, $bindings);
    }

    return response()->json([
      'status' => 1,
      'pid' => 'update',
      'message' => 'Update rekening berhasil',
    ], 200);
  }
}