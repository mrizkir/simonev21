<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Belanja\RKAModel;
use App\Models\Statistik2Model;

use Ramsey\Uuid\Uuid;

class FormBOPDPerubahanController extends Controller 
{
   /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {             
    $this->hasPermissionTo('RENJA-FORM-B-PERUBAHAN_BROWSE');

    $this->validate($request, [            
      'tahun' => 'required|numeric',
      'no_bulan' => 'required',   
      'OrgID' => 'required|exists:tmOrg,OrgID',            
    ]);
    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    $OrgID = $request->input('OrgID');
    
    $opd = OrganisasiModel::find($OrgID);
    
    $totalPaguOPD = (float)\DB::table('trRKA')
      ->where('OrgID', $opd->OrgID)                                            
      ->where('TA', $tahun)  
      ->where('EntryLvl', 2)
      ->sum('PaguDana2');        
    
    $total_program = 0;
    $total_kegiatan = 0;
    $total_sub_kegiatan = 0;
    $total_uraian = 0;
    $totalPersenBobot = 0;
    $totalPersenTargetFisik = 0;
    $totalPersenRealisasiFisik = 0;
    $total_ttb_fisik = 0;
    $totalTargetKeuanganKeseluruhan = 0;
    $totalRealisasiKeuanganKeseluruhan = 0;
    $total_ttb_keuangan = 0;

    $daftar_program=\DB::table('trRKA')
      ->select(\DB::raw('DISTINCT(kode_program), `Nm_Program`'))
      ->where('OrgID', $opd->OrgID)							
      ->where('EntryLvl', 2)
      ->orderByRaw('kode_urusan="X" DESC')
      ->orderBy('kode_bidang', 'ASC')
      ->orderBy('kode_program', 'ASC')
      ->orderBy('kode_kegiatan', 'ASC')
      ->orderBy('kode_sub_kegiatan', 'ASC')                            
      ->get();
    
    $data=[];
    $row = 0;
    foreach ($daftar_program as $data_program)
    {
      $total_program += 1;
      $kode_program = $data_program->kode_program;
      $daftar_kegiatan=\DB::table('trRKA')
        ->select(\DB::raw('DISTINCT(kode_kegiatan), `Nm_Kegiatan`'))
        ->where('kode_program', $kode_program)
        ->where('OrgID', $opd->OrgID)
        ->where('EntryLvl', 2)
        ->orderBy('kode_kegiatan', 'ASC')
        ->orderBy('kode_sub_kegiatan', 'ASC')                            
        ->get();

      if(isset($daftar_kegiatan[0]))
      {
        $jumlah_uraian_program = 0;

        $pagu_dana_program = 0; 
        $target_fisik_program = 0; 
        $realisasi_fisik_program = 0; 
        $ttb_fisik_program = 0; 
        $target_keuangan_program = 0;
        $realisasi_keuangan_program = 0;

        $data[$row]=[
          'FormBPerubahanID'=>Uuid::uuid4()->toString(),          
          'RKAID' => null,      
          'kode' => $kode_program,					
          'nama_uraian'=>ucwords(strtolower($data_program->Nm_Program)),
          'pagu_dana2' => 0,       
          'bobot2' => 0,
          'fisik_target2' => 0,
          'fisik_realisasi2' => 0,
          'fisik_ttb2' => 0,
          'keuangan_target2' => 0,
          'keuangan_target_persen_2' => 0,
          'keuangan_realisasi2' => 0,
          'keuangan_realisasi_persen_2' => 0,
          'keuangan_ttb2' => 0,
          'lokasi' => 0,
          'sisa_anggaran' => 0,
          'sisa_anggaran_persen' => 0,               
          'isprogram' => true,
          'iskegiatan' => false,
          'issubkegiatan' => false,
        ];   
        $program_last_row = $row;
        $row += 1;
        foreach ($daftar_kegiatan as $data_kegiatan)
        {
          $total_kegiatan += 1;
          $kode_kegiatan = $data_kegiatan->kode_kegiatan;

          $daftar_sub_kegiatan = \DB::table('trRKA')
            ->select(\DB::raw('`RKAID`,`kode_sub_kegiatan`,`Nm_Sub_Kegiatan`,`PaguDana2`,`lokasi_kegiatan2`'))
            ->where('kode_kegiatan', $kode_kegiatan)                                   
            ->where('OrgID', $opd->OrgID)                                   
            ->where('TA', $tahun)  
            ->where('EntryLvl', 2)                                    
            ->orderBy('kode_sub_kegiatan', 'ASC')
            ->get();

          if(isset($daftar_sub_kegiatan[0]))
          {
            $pagu_dana_kegiatan = (float)\DB::table('trRKA')
              ->where('OrgID', $opd->OrgID)                                   
              ->where('kode_kegiatan', $kode_kegiatan) 
              ->where('EntryLvl', 2)
              ->sum('PaguDana2'); 

            $data[$row]=[
              'FormBPerubahanID'=>Uuid::uuid4()->toString(),
              'RKAID' => null,
              'kode' => $kode_kegiatan,							
              'nama_uraian'=>ucwords(strtolower($data_kegiatan->Nm_Kegiatan)),
              'pagu_dana2' => $pagu_dana_kegiatan,
              'bobot2' => 0,
              'fisik_target2' => 0,
              'fisik_realisasi2' => 0,
              'fisik_ttb2' => 0,
              'keuangan_target2' => 0,
              'keuangan_target_persen_2' => 0,
              'keuangan_realisasi2' => 0,
              'keuangan_realisasi_persen_2' => 0,
              'keuangan_ttb2' => 0,
              'lokasi' => 0,
              'sisa_anggaran' => 0,
              'sisa_anggaran_persen' => 0,
              'isprogram' => false,
              'iskegiatan' => true,
              'issubkegiatan' => false,
            ];
            $jumlah_uraian_kegiatan = 0;

            $pagu_dana_kegiatan = 0; 
            $target_fisik_kegiatan = 0; 
            $realisasi_fisik_kegiatan = 0; 
            $ttb_fisik_kegiatan = 0; 
            $target_keuangan_kegiatan = 0;
            $realisasi_keuangan_kegiatan = 0;
            
            $kegiatan_last_row = $row;
            $row += 1;

            foreach ($daftar_sub_kegiatan as $data_sub_kegiatan) 
            {
              $pagu_dana_program += $data_sub_kegiatan->PaguDana2;
              $pagu_dana_kegiatan += $data_sub_kegiatan->PaguDana2;

              $RKAID = $data_sub_kegiatan->RKAID;
              $kode_sub_kegiatan = $data_sub_kegiatan->kode_sub_kegiatan;

              $persen_bobot=Helper::formatPersen($data_sub_kegiatan->PaguDana2,$totalPaguOPD);
              $totalPersenBobot+= $persen_bobot;

              //jumlah baris uraian
              $jumlahuraian = \DB::table('trRKARinc')->where('RKAID', $RKAID)->count();	
              $jumlah_uraian_program += $jumlahuraian;
              $jumlah_uraian_kegiatan += $jumlahuraian;

              $data_target=\DB::table('trRKATargetRinc')
                ->select(\DB::raw('COALESCE(SUM(target2),0) AS totaltarget, COALESCE(SUM(fisik2),0) AS jumlah_fisik'))
                ->where('RKAID', $RKAID)
                ->where('bulan2', '<=', $no_bulan)
                ->get();

              $data_realisasi=\DB::table('trRKARealisasiRinc')
                ->select(\DB::raw('COALESCE(SUM(realisasi2),0) AS realisasi2, COALESCE(SUM(fisik2),0) AS fisik2'))
                ->where('RKAID', $RKAID)
                ->where('bulan2', '<=', $no_bulan)
                ->get();

              //menghitung persen target fisik    
              $target_fisik_program += $data_target[0]->jumlah_fisik;
              $target_fisik_kegiatan += $data_target[0]->jumlah_fisik;
              $target_fisik=Helper::formatPecahan($data_target[0]->jumlah_fisik,$jumlahuraian);                            
              $persen_target_fisik= $target_fisik > 100 ? 100.00 : $target_fisik;
              $totalPersenTargetFisik+= $persen_target_fisik;

              //menghitung persen realisasi fisik                
              $realisasi_fisik_program += $data_realisasi[0]->fisik2;
              $realisasi_fisik_kegiatan += $data_realisasi[0]->fisik2;
              $persen_realisasi_fisik=Helper::formatPecahan($data_realisasi[0]->fisik2,$jumlahuraian);
              $totalPersenRealisasiFisik+= $persen_realisasi_fisik; 

              $persen_tertimbang_fisik = 0.00;
              if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
              {
                $persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100, 2);                            
              }							
              $total_ttb_fisik+= $persen_tertimbang_fisik;

              //menghitung total target dan realisasi keuangan 
              $totalTargetKeuangan = $data_target[0]->totaltarget;
              $target_keuangan_program += $totalTargetKeuangan;
              $target_keuangan_kegiatan += $totalTargetKeuangan;
              $totalTargetKeuanganKeseluruhan+= $totalTargetKeuangan;
              $persen_target_keuangan=Helper::formatPersen($totalTargetKeuangan,$data_sub_kegiatan->PaguDana2);                            							                                 
            
              $totalRealisasiKeuangan = $data_realisasi[0]->realisasi2;
              $realisasi_keuangan_program += $totalRealisasiKeuangan;
              $realisasi_keuangan_kegiatan += $totalRealisasiKeuangan;
              $totalRealisasiKeuanganKeseluruhan+= $totalRealisasiKeuangan;
              $persen_realisasi_keuangan=Helper::formatPersen($totalRealisasiKeuangan,$data_sub_kegiatan->PaguDana2);  
              
              $persen_tertimbang_keuangan = 0.00;
              if ($persen_realisasi_keuangan > 0 && $persen_bobot > 0)
              {
                $persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100, 2);                            
              }	
              $total_ttb_keuangan += $persen_tertimbang_keuangan;

              $sisa_anggaran = $data_sub_kegiatan->PaguDana2-$totalRealisasiKeuangan;							
              
              $persen_sisa_anggaran = Helper::formatPersen($sisa_anggaran,$data_sub_kegiatan->PaguDana2);                            

              $data[$row]=[
                'FormBPerubahanID'=>Uuid::uuid4()->toString(),
                'RKAID' => $RKAID,
                'kode' => $kode_sub_kegiatan,
                'nama_uraian'=>ucwords(strtolower($data_sub_kegiatan->Nm_Sub_Kegiatan)),
                'pagu_dana2' => $data_sub_kegiatan->PaguDana2,
                'bobot2' => $persen_bobot,
                'fisik_target2' => $persen_target_fisik,
                'fisik_realisasi2' => $persen_realisasi_fisik,
                'fisik_ttb2' => $persen_tertimbang_fisik,
                'keuangan_target2' => $totalTargetKeuangan,
                'keuangan_target_persen_2' => $persen_target_keuangan,
                'keuangan_realisasi2' => $totalRealisasiKeuangan,
                'keuangan_realisasi_persen_2' => $persen_realisasi_keuangan,
                'keuangan_ttb2' => $persen_tertimbang_keuangan,
                'lokasi' => $data_sub_kegiatan->lokasi_kegiatan2,
                'sisa_anggaran' => $sisa_anggaran,
                'sisa_anggaran_persen' => $persen_sisa_anggaran,
                'isprogram' => false,
                'iskegiatan' => false,
                'issubkegiatan' => true,
              ];
              $total_sub_kegiatan += 1;
              $row += 1;
            }
            $persen_bobot=Helper::formatPersen($pagu_dana_kegiatan,$totalPaguOPD);
            $target_fisik=Helper::formatPecahan($target_fisik_kegiatan,$jumlah_uraian_kegiatan);                            
            $persen_target_fisik= $target_fisik > 100 ? 100.00 : $target_fisik;
            
            $persen_realisasi_fisik=Helper::formatPecahan($realisasi_fisik_kegiatan,$jumlah_uraian_kegiatan);
            $persen_tertimbang_fisik = 0.00;
            if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
            {
              $persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100, 2);                            
            }

            $persen_target_keuangan=Helper::formatPersen($target_keuangan_kegiatan,$pagu_dana_kegiatan);
            $persen_realisasi_keuangan=Helper::formatPersen($realisasi_keuangan_kegiatan,$pagu_dana_kegiatan);  
            $persen_tertimbang_keuangan = 0.00;
            if ($persen_realisasi_keuangan > 0 && $persen_bobot > 0)
            {
              $persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100, 2);                            
            }	

            $sisa_anggaran = $pagu_dana_kegiatan - $realisasi_keuangan_kegiatan;
            $persen_sisa_anggaran = Helper::formatPersen($sisa_anggaran,$pagu_dana_kegiatan);
            
            $data[$kegiatan_last_row]=[
              'FormBPerubahanID'=>Uuid::uuid4()->toString(),
              'RKAID' => null,
              'kode' => $kode_kegiatan,							
              'nama_uraian'=>ucwords(strtolower($data_kegiatan->Nm_Kegiatan)),
              'pagu_dana2' => $pagu_dana_kegiatan,
              'bobot2' => $persen_bobot,
              'fisik_target2' => $persen_target_fisik,
              'fisik_realisasi2' => $persen_realisasi_fisik,
              'fisik_ttb2' => $persen_tertimbang_fisik,
              'keuangan_target2' => $target_keuangan_kegiatan,
              'keuangan_target_persen_2' => $persen_target_keuangan,
              'keuangan_realisasi2' => $realisasi_keuangan_kegiatan,
              'keuangan_realisasi_persen_2' => $persen_realisasi_keuangan,
              'keuangan_ttb2' => $persen_tertimbang_keuangan,
              'lokasi' => '-',
              'sisa_anggaran' => $sisa_anggaran,
              'sisa_anggaran_persen' => $persen_sisa_anggaran,
              'isprogram' => false,
              'iskegiatan' => true,
              'issubkegiatan' => false,
            ];
          }
        }        
      }
      $persen_bobot=Helper::formatPersen($pagu_dana_program,$totalPaguOPD);
      $target_fisik=Helper::formatPecahan($target_fisik_program,$jumlah_uraian_program);                            
      $persen_target_fisik= $target_fisik > 100 ? 100.00 : $target_fisik;
      
      $persen_realisasi_fisik=Helper::formatPecahan($realisasi_fisik_program,$jumlah_uraian_program);
      $persen_tertimbang_fisik = 0.00;
      if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
      {
        $persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100, 2);                            
      }

      $persen_target_keuangan=Helper::formatPersen($target_keuangan_program,$pagu_dana_program);
      $persen_realisasi_keuangan=Helper::formatPersen($realisasi_keuangan_program,$pagu_dana_program);  
      $persen_tertimbang_keuangan = 0.00;
      if ($persen_realisasi_keuangan > 0 && $persen_bobot > 0)
      {
        $persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100, 2);                            
      }	

      $sisa_anggaran = $pagu_dana_program - $realisasi_keuangan_program;
      $persen_sisa_anggaran = Helper::formatPersen($sisa_anggaran,$pagu_dana_program);                            
      $data[$program_last_row]=[
        'FormBPerubahanID'=>Uuid::uuid4()->toString(),
        'RKAID' => null,
        'kode' => $kode_program,				
        'nama_uraian'=>ucwords(strtolower($data_program->Nm_Program)),
        'pagu_dana2' => $pagu_dana_program,
        'bobot2' => $persen_bobot,
        'fisik_target2' => $persen_target_fisik,
        'fisik_realisasi2' => $persen_realisasi_fisik,
        'fisik_ttb2' => $persen_tertimbang_fisik,
        'keuangan_target2' => $target_keuangan_program,
        'keuangan_target_persen_2' => $persen_target_keuangan,
        'keuangan_realisasi2' => $realisasi_keuangan_program,
        'keuangan_realisasi_persen_2' => $persen_realisasi_keuangan,
        'keuangan_ttb2' => $persen_tertimbang_keuangan,
        'lokasi' => '-',
        'sisa_anggaran' => $sisa_anggaran,
        'sisa_anggaran_persen' => $persen_sisa_anggaran,               
        'isprogram' => true,
        'iskegiatan' => false,
        'issubkegiatan' => false,
      ];
    }
    
    if ($totalPersenBobot > 100) {
      $totalPersenBobot = 100.000;
    }
    $totalPersenTargetFisik = Helper::formatPecahan($totalPersenTargetFisik,$total_sub_kegiatan);        
    $totalPersenRealisasiFisik=Helper::formatPecahan($totalPersenRealisasiFisik,$total_sub_kegiatan); 
    $totalPersenTargetKeuangan=Helper::formatPersen($totalTargetKeuanganKeseluruhan,$totalPaguOPD);                
    $totalPersenRealisasiKeuangan=Helper::formatPersen($totalRealisasiKeuanganKeseluruhan,$totalPaguOPD);
    $totalSisaAnggaran = $totalPaguOPD - $totalRealisasiKeuanganKeseluruhan;
    $totalPersenSisaAnggaran = Helper::formatPersen($totalSisaAnggaran,$totalPaguOPD);
    $totalPersenBobot=round($totalPersenBobot,2);
    $total_ttb_fisik=round($total_ttb_fisik,2);
    $total_ttb_keuangan=round($total_ttb_keuangan,2);
    $total_data=[
      'totalPaguOPD' => $totalPaguOPD,
      'totalPersenBobot' => $totalPersenBobot,
      'totalPersenTargetFisik' => $totalPersenTargetFisik,
      'totalPersenRealisasiFisik' => $totalPersenRealisasiFisik,
      'total_ttb_fisik' => $total_ttb_fisik,
      'totalTargetKeuanganKeseluruhan' => $totalTargetKeuanganKeseluruhan,
      'totalRealisasiKeuanganKeseluruhan' => $totalRealisasiKeuanganKeseluruhan,
      'totalPersenTargetKeuangan' => $totalPersenTargetKeuangan,
      'totalPersenRealisasiKeuangan' => $totalPersenRealisasiKeuangan,
      'total_ttb_keuangan' => $total_ttb_keuangan,
      'totalSisaAnggaran' => $totalSisaAnggaran,
      'totalPersenSisaAnggaran' => $totalPersenSisaAnggaran,
    ];       
    
    $statistik = Statistik2Model::where('OrgID', $OrgID)
      ->where('TA', $tahun)
      ->where('Bulan', $no_bulan)
      ->where('EntryLvl', 2)
      ->first();

    if (is_null($statistik)) 
    {
      Statistik2Model::create([
        'Statistik2ID'=>Uuid::uuid4()->toString(),
        'OrgID' => $opd->OrgID,
        'kode_organisasi' => $opd->kode_organisasi,
        'OrgNm' => $opd->Nm_Organisasi,
        'PaguDana1' => 0,
        'PaguDana2' => $totalPaguOPD,            
        'PaguDana3' => 0,            
        'JumlahKegiatan1' => 0,
        'JumlahKegiatan2' => $total_kegiatan,
        'JumlahKegiatan3' => 0,
        'JumlahUraian1' => 0,
        'JumlahUraian2' => $total_uraian,
        'JumlahUraian3' => 0,
            
        'TargetFisik1' => 0,
        'TargetFisik2' => $totalPersenTargetFisik,
        'TargetFisik3' => 0,
        'RealisasiFisik1' => 0,
        'RealisasiFisik2' => $totalPersenRealisasiFisik,
        'RealisasiFisik3' => 0,

        'TargetKeuangan1' => 0,
        'TargetKeuangan2' => $totalTargetKeuanganKeseluruhan,
        'TargetKeuangan3' => 0,
        'RealisasiKeuangan1' => 0,
        'RealisasiKeuangan2' => $totalRealisasiKeuanganKeseluruhan,
        'RealisasiKeuangan3' => 0,

        'PersenTargetKeuangan1' => 0,
        'PersenTargetKeuangan2' => $totalPersenTargetKeuangan,
        'PersenTargetKeuangan3' => 0,
        'PersenRealisasiKeuangan1' => 0,
        'PersenRealisasiKeuangan2' => $totalPersenRealisasiKeuangan,
        'PersenRealisasiKeuangan3' => 0,
            
        'SisaPaguDana2' => 0,
        'SisaPaguDana2' => $totalSisaAnggaran,
        'SisaPaguDana3' => 0,

        'PersenSisaPaguDana2' => 0,
        'PersenSisaPaguDana2' => $totalPersenSisaAnggaran,
        'PersenSisaPaguDana3' => 0,

        'Bobot1' => 0,
        'Bobot2' => $totalPersenBobot,
        'Bobot3' => 0,
        
        'Bulan' => $no_bulan,
        'TA' => $tahun,
        'EntryLvl'=>2,
      ]);
    }
    else
    {
      $statistik->PaguDana2 = $totalPaguOPD;
      $statistik->JumlahKegiatan2 = $total_kegiatan;
      $statistik->TargetFisik2 = $totalPersenTargetFisik;
      $statistik->RealisasiFisik2 = $totalPersenRealisasiFisik;
      $statistik->TargetKeuangan2 = $totalTargetKeuanganKeseluruhan;
      $statistik->RealisasiKeuangan2 = $totalRealisasiKeuanganKeseluruhan;
      $statistik->PersenTargetKeuangan2 = $totalPersenTargetKeuangan;
      $statistik->PersenRealisasiKeuangan2 = $totalPersenRealisasiKeuangan;
      $statistik->SisaPaguDana2 = $totalSisaAnggaran;
      $statistik->PersenSisaPaguDana2 = $totalPersenSisaAnggaran;
      $statistik->Bobot2 = $totalPersenBobot;		
      
      $statistik->save();
    }

    $opd->PaguDana2 = $totalPaguOPD;
    $opd->save();

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'opd' => $opd,
      'rka' => $data,
      'total_data' => $total_data,                                    
      'message' => 'Fetch data form b perubahan berhasil diperoleh'
    ], 200);    
    
  }
  public function chart(Request $request)
  {
    $this->validate($request, [            
      'tahun' => 'required|numeric',
      'no_bulan' => 'required',   
      'OrgID' => 'required|exists:tmOrg,OrgID',            
    ]);
    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    $OrgID = $request->input('OrgID');

    $data = \DB::table('trRKA')
    ->select(\DB::raw('
      RKAID,
      kode_sub_kegiatan,
      PaguDana2,
      0 AS target_fisik,
      0 AS realisasi_fisik,
      0 AS target_keuangan,
      0 AS realisasi_keuangan
    '))
    ->where('OrgID', $OrgID)
    ->where('EntryLvl', 2)
    ->orderByRaw("CASE WHEN kode_bidang = 'x.xx' THEN 1 ELSE 2 END")
    ->orderBy('kode_bidang', 'asc')
    ->orderBy('kode_sub_kegiatan', 'asc')
    ->get();

    $data->transform(function ($item, $key) use ($no_bulan) {
      //jumlah baris uraian
      $jumlahuraian = \DB::table('trRKARinc')->where('RKAID', $item->RKAID)->count();	

      $data_target=\DB::table('trRKATargetRinc')
        ->select(\DB::raw('COALESCE(SUM(target2), 0) AS totaltarget, COALESCE(SUM(fisik2), 0) AS jumlah_fisik'))
        ->where('RKAID', $item->RKAID)
        ->where('bulan2', '<=', $no_bulan)
        ->get();

      $target_fisik = Helper::formatPecahan($data_target[0]->jumlah_fisik, $jumlahuraian);                            
      $item->target_fisik = $target_fisik > 100 ? 100.00 : $target_fisik;

      $data_realisasi=\DB::table('trRKARealisasiRinc')
      ->select(\DB::raw('COALESCE(SUM(realisasi2), 0) AS realisasi2, COALESCE(SUM(fisik2), 0) AS fisik2'))
      ->where('RKAID', $item->RKAID)
      ->where('bulan2', '<=', $no_bulan)
      ->get();

      $item->realisasi_fisik = Helper::formatPecahan($data_realisasi[0]->fisik2, $jumlahuraian);

      $totalTargetKeuangan = $data_target[0]->totaltarget;
      $item->target_keuangan = Helper::formatPersen($totalTargetKeuangan, $item->PaguDana2);                            							                                 

      $totalRealisasiKeuangan = $data_realisasi[0]->realisasi2;
      $item->realisasi_keuangan = Helper::formatPersen($totalRealisasiKeuangan, $item->PaguDana2);  

      return $item;
    });

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'chart' => $data,      
      'message' => 'Fetch data chart form b murni berhasil diperoleh'
    ], 200);
  }
  public function printtoexcel (Request $request)
  {
    $this->hasPermissionTo('RENJA-FORM-B-PERUBAHAN_BROWSE');

    $this->validate($request, [            
      'tahun' => 'required',         
      'no_bulan' => 'required',   
      'OrgID' => 'required|exists:tmOrg,OrgID',            
    ]);
    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    $OrgID = $request->input('OrgID');
    
    $opd = OrganisasiModel::find($OrgID);
    if (\DB::table('trRKA')->where('OrgID', $opd->OrgID)->where('EntryLvl', 2)->where('TA', $tahun)->count() > 0)
    {
      $data_report=[
        'OrgID' => $opd->OrgID,
        'kode_organisasi' => $opd->kode_organisasi,
        'Nm_Organisasi' => $opd->Nm_Organisasi,
        'tahun' => $tahun,
        'no_bulan' => $no_bulan,
        'nama_pengguna_anggaran' => $opd->NamaKepalaOPD,
        'nip_pengguna_anggaran' => $opd->NIPKepalaOPD
      ];
      $report= new \App\Models\Renja\FormBOPDPerubahanModel ($data_report);
      $generate_date=date('Y-m-d_H_m_s');
      return $report->download("form_b_$generate_date.xlsx");
    }
    else
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',                                                                            
        'message'=>['Print excel gagal dilakukan karena tidak ada belum ada Uraian pada kegiatan ini']
      ], 422); 
    }
  }
}