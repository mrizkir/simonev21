<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Belanja\RKAModel;

use Ramsey\Uuid\Uuid;

class FormBOPDMurniController extends Controller 
{
	 /**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{             
		$this->hasPermissionTo('RENJA-FORM-B-MURNI_BROWSE');

		$this->validate($request, [            
			'tahun'=>'required',         
			'no_bulan'=>'required',   
			'OrgID'=>'required|exists:tmOrg,OrgID',            
		]);
		$tahun = $request->input('tahun');
		$no_bulan = $request->input('no_bulan');
		$OrgID = $request->input('OrgID');
		
		$opd = OrganisasiModel::find($OrgID);
		
		$totalPaguOPD = (float)\DB::table('trRKA')
									->where('OrgID',$opd->OrgID)                                            
									->where('TA',$tahun)  
									->where('EntryLvl',1)
									->sum('PaguDana1');        
		
		$total_sub_kegiatan=0;
		$total_uraian=0;
		$totalPersenBobot=0;
		$totalPersenTargetFisik=0;
		$totalPersenRealisasiFisik=0;
		$total_ttb_fisik=0;
		$totalTargetKeuanganKeseluruhan=0;
		$totalRealisasiKeuanganKeseluruhan=0;
		$total_ttb_keuangan=0;
		$totalSisaAnggaran=0;

		$daftar_program=\DB::table('trRKA')
							->select(\DB::raw('DISTINCT(kode_program), `Nm_Program`'))
							->where('OrgID',$opd->OrgID)
							->orderByRaw('kode_urusan="X" DESC')
							->orderBy('kode_bidang','ASC')
							->orderBy('kode_program','ASC')
							->orderBy('kode_kegiatan','ASC')
							->orderBy('kode_sub_kegiatan','ASC')                            
							->get();
		
		$data=[];
		$row = 0;
		foreach ($daftar_program as $data_program)
		{
			$kode_program = $data_program->kode_program;
			$daftar_kegiatan=\DB::table('trRKA')
							->select(\DB::raw('DISTINCT(kode_kegiatan), `Nm_Kegiatan`'))
							->where('kode_program',$kode_program)
							->where('OrgID', $opd->OrgID)
							->orderBy('kode_kegiatan','ASC')
							->orderBy('kode_sub_kegiatan','ASC')                            
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
					'FormBMurniID'=>Uuid::uuid4()->toString(),          
					'RKAID'=>null,      
					'kode'=>$kode_program,
					'nama_uraian'=>$data_program->Nm_Program,         
					'pagu_dana2'=>0,       
					'bobot2'=>0,
					'fisik_target2'=>0,
					'fisik_realisasi2'=>0,
					'fisik_ttb2'=>0,
					'keuangan_target2'=>0,
					'keuangan_target_persen_2'=>0,
					'keuangan_realisasi2'=>0,
					'keuangan_realisasi_persen_2'=>0,
					'keuangan_ttb2'=>0,
					'lokasi'=>0,
					'sisa_anggaran'=>0,
					'sisa_anggaran_persen'=>0,               
					'isprogram'=>true,
					'iskegiatan'=>false,
					'issubkegiatan'=>false,
				];   
				$program_last_row = $row;
				$row += 1;
				foreach ($daftar_kegiatan as $data_kegiatan)
				{
					$kode_kegiatan = $data_kegiatan->kode_kegiatan;

					$daftar_sub_kegiatan = \DB::table('trRKA')
									->select(\DB::raw('`RKAID`,`kode_sub_kegiatan`,`Nm_Sub_Kegiatan`,`PaguDana1`,`lokasi_kegiatan1`'))
									->where('kode_kegiatan',$kode_kegiatan)                                   
									->where('OrgID',$opd->OrgID)                                   
									->where('TA',$tahun)  
									->where('EntryLvl',1)                                    
									->orderBy('kode_sub_kegiatan','ASC')
									->get();

					if(isset($daftar_sub_kegiatan[0]))
					{
						$pagu_dana_kegiatan = (float)\DB::table('trRKA')
									->where('OrgID',$opd->OrgID)                                   
									->where('kode_kegiatan',$kode_kegiatan) 
									->where('EntryLvl',1)
									->sum('PaguDana1'); 

						$data[$row]=[
							'FormBMurniID'=>Uuid::uuid4()->toString(),
							'RKAID'=>null,
							'kode'=>$kode_kegiatan,
							'nama_uraian'=>$data_kegiatan->Nm_Kegiatan,
							'pagu_dana2'=>$pagu_dana_kegiatan,
							'bobot2'=>$persen_bobot,
							'fisik_target2'=>0,
							'fisik_realisasi2'=>0,
							'fisik_ttb2'=>0,
							'keuangan_target2'=>0,
							'keuangan_target_persen_2'=>0,
							'keuangan_realisasi2'=>0,
							'keuangan_realisasi_persen_2'=>0,
							'keuangan_ttb2'=>0,
							'lokasi'=>0,
							'sisa_anggaran'=>0,
							'sisa_anggaran_persen'=>0,
							'isprogram'=>false,
							'iskegiatan'=>true,
							'issubkegiatan'=>false,
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
							$pagu_dana_program += $data_sub_kegiatan->PaguDana1;
							$pagu_dana_kegiatan += $data_sub_kegiatan->PaguDana1;

							$RKAID=$data_sub_kegiatan->RKAID;
							$kode_sub_kegiatan = $data_sub_kegiatan->kode_sub_kegiatan;

							$persen_bobot=Helper::formatPersen($data_sub_kegiatan->PaguDana1,$totalPaguOPD);
							$totalPersenBobot+=$persen_bobot;

							//jumlah baris uraian
							$jumlahuraian = \DB::table('trRKARinc')->where('RKAID',$RKAID)->count();	
							$jumlah_uraian_program += $jumlahuraian;
							$jumlah_uraian_kegiatan += $jumlahuraian;

							$data_target=\DB::table('trRKATargetRinc')
											->select(\DB::raw('COALESCE(SUM(target1),0) AS totaltarget, COALESCE(SUM(fisik1),0) AS jumlah_fisik'))
											->where('RKAID',$RKAID)
											->where('bulan1','<=',$no_bulan)
											->get();

							$data_realisasi=\DB::table('trRKARealisasiRinc')
										->select(\DB::raw('COALESCE(SUM(realisasi1),0) AS realisasi1, COALESCE(SUM(fisik1),0) AS fisik1'))
										->where('RKAID',$RKAID)
										->where('bulan1','<=',$no_bulan)
										->get();

							//menghitung persen target fisik    
							$target_fisik_program += $data_target[0]->jumlah_fisik;
							$target_fisik_kegiatan += $data_target[0]->jumlah_fisik;
							$target_fisik=Helper::formatPecahan($data_target[0]->jumlah_fisik,$jumlahuraian);                            
							$persen_target_fisik= $target_fisik > 100 ?'100.00':$target_fisik;
							$totalPersenTargetFisik+=$persen_target_fisik;

							//menghitung persen realisasi fisik                
							$realisasi_fisik_program += $data_realisasi[0]->fisik1;
							$realisasi_fisik_kegiatan += $data_realisasi[0]->fisik1;
							$persen_realisasi_fisik=Helper::formatPecahan($data_realisasi[0]->fisik1,$jumlahuraian);
							$totalPersenRealisasiFisik+=$persen_realisasi_fisik; 

							$persen_tertimbang_fisik=0.00;
							if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
							{
								$persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100,2);                            
							}							
							$total_ttb_fisik+=$persen_tertimbang_fisik;

							//menghitung total target dan realisasi keuangan 
							$totalTargetKeuangan=$data_target[0]->totaltarget;
							$target_keuangan_program += $totalTargetKeuangan;
							$target_keuangan_kegiatan += $totalTargetKeuangan;
							$totalTargetKeuanganKeseluruhan+=$totalTargetKeuangan;
							$persen_target_keuangan=Helper::formatPersen($totalTargetKeuangan,$data_sub_kegiatan->PaguDana1);                            							                                 
						
							$totalRealisasiKeuangan=$data_realisasi[0]->realisasi1;
							$realisasi_keuangan_program += $totalRealisasiKeuangan;
							$realisasi_keuangan_kegiatan += $totalRealisasiKeuangan;
							$totalRealisasiKeuanganKeseluruhan+=$totalRealisasiKeuangan;
							$persen_realisasi_keuangan=Helper::formatPersen($totalRealisasiKeuangan,$data_sub_kegiatan->PaguDana1);  
							
							$persen_tertimbang_keuangan=0.00;
							if ($persen_realisasi_keuangan > 0 && $persen_bobot > 0)
							{
								$persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100,2);                            
							}	
							$total_ttb_keuangan += $persen_tertimbang_keuangan;

							$sisa_anggaran=$data_sub_kegiatan->PaguDana1-$totalRealisasiKeuangan;
							$totalSisaAnggaran+=$sisa_anggaran; 
							
							$persen_sisa_anggaran=Helper::formatPersen($sisa_anggaran,$data_sub_kegiatan->PaguDana1);                            

							$data[$row]=[
								'FormBMurniID'=>Uuid::uuid4()->toString(),
								'RKAID'=>$RKAID,
								'kode'=>$kode_sub_kegiatan,
								'nama_uraian'=>$data_sub_kegiatan->Nm_Sub_Kegiatan,                                
								'pagu_dana2'=>$data_sub_kegiatan->PaguDana1,
								'bobot2'=>$persen_bobot,
								'fisik_target2'=>$persen_target_fisik,
								'fisik_realisasi2'=>$persen_realisasi_fisik,
								'fisik_ttb2'=>$persen_tertimbang_fisik,
								'keuangan_target2'=>$totalTargetKeuangan,
								'keuangan_target_persen_2'=>$persen_target_keuangan,
								'keuangan_realisasi2'=>$totalRealisasiKeuangan,
								'keuangan_realisasi_persen_2'=>$persen_realisasi_keuangan,
								'keuangan_ttb2'=>$persen_tertimbang_keuangan,
								'lokasi'=>$data_sub_kegiatan->lokasi_kegiatan1,
								'sisa_anggaran'=>$sisa_anggaran,
								'sisa_anggaran_persen'=>$persen_sisa_anggaran,
								'isprogram'=>false,
								'iskegiatan'=>false,
								'issubkegiatan'=>true,
							];
							$row += 1;
						}
						$persen_bobot=Helper::formatPersen($pagu_dana_kegiatan,$totalPaguOPD);
						$target_fisik=Helper::formatPecahan($target_fisik_kegiatan,$jumlah_uraian_kegiatan);                            
						$persen_target_fisik= $target_fisik > 100 ?'100.00':$target_fisik;
						
						$persen_realisasi_fisik=Helper::formatPecahan($realisasi_fisik_kegiatan,$jumlah_uraian_kegiatan);
						$persen_tertimbang_fisik=0.00;
						if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
						{
							$persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100,2);                            
						}

						$persen_realisasi_keuangan=Helper::formatPersen($realisasi_keuangan_kegiatan,$pagu_dana_kegiatan);  
						$persen_tertimbang_keuangan=0.00;
						if ($persen_realisasi_keuangan > 0 && $persen_bobot > 0)
						{
							$persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100,2);                            
						}	

						$sisa_anggaran = $pagu_dana_kegiatan - $realisasi_keuangan_kegiatan;
						$persen_sisa_anggaran=Helper::formatPersen($sisa_anggaran,$pagu_dana_kegiatan);
						
						$data[$kegiatan_last_row]=[
							'FormBMurniID'=>Uuid::uuid4()->toString(),
							'RKAID'=>null,
							'kode'=>$kode_kegiatan,
							'nama_uraian'=>$data_kegiatan->Nm_Kegiatan,
							'bobot2'=>$persen_bobot,
							'pagu_dana2'=>$pagu_dana_kegiatan,
							'fisik_target2'=>$target_fisik_kegiatan,
							'fisik_realisasi2'=>$persen_target_fisik,
							'fisik_ttb2'=>$persen_tertimbang_fisik,
							'keuangan_target2'=>$target_keuangan_kegiatan,
							'keuangan_target_persen_2'=>$kegiatan_last_row+1,
							'keuangan_realisasi2'=>$realisasi_keuangan_kegiatan,
							'keuangan_realisasi_persen_2'=>$persen_realisasi_keuangan,
							'keuangan_ttb2'=>$persen_tertimbang_keuangan,
							'lokasi'=>'-',
							'sisa_anggaran'=>$sisa_anggaran,
							'sisa_anggaran_persen'=>$persen_sisa_anggaran,
							'isprogram'=>false,
							'iskegiatan'=>true,
							'issubkegiatan'=>false,
						];
					}
				}        
			}
			$persen_bobot=Helper::formatPersen($pagu_dana_program,$totalPaguOPD);
			$target_fisik=Helper::formatPecahan($target_fisik_program,$jumlah_uraian_program);                            
			$persen_target_fisik= $target_fisik > 100 ?'100.00':$target_fisik;
			
			$persen_realisasi_fisik=Helper::formatPecahan($realisasi_fisik_program,$jumlah_uraian_program);
			$persen_tertimbang_fisik=0.00;
			if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
			{
				$persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100,2);                            
			}

			$persen_realisasi_keuangan=Helper::formatPersen($realisasi_keuangan_program,$pagu_dana_program);  
			$persen_tertimbang_keuangan=0.00;
			if ($persen_realisasi_keuangan > 0 && $persen_bobot > 0)
			{
				$persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100,2);                            
			}	

			$sisa_anggaran = $pagu_dana_program - $realisasi_keuangan_program;
			$persen_sisa_anggaran=Helper::formatPersen($sisa_anggaran,$pagu_dana_program);                            
			$data[$program_last_row]=[
				'FormBMurniID'=>Uuid::uuid4()->toString(),
				'RKAID'=>null,
				'kode'=>$kode_program,
				'nama_uraian'=>$data_program->Nm_Program,         
				'pagu_dana2'=>$pagu_dana_program,
				'bobot2'=>$persen_bobot,
				'fisik_target2'=>$persen_target_fisik,
				'fisik_realisasi2'=>$persen_realisasi_fisik,
				'fisik_ttb2'=>$persen_tertimbang_fisik,
				'keuangan_target2'=>$target_keuangan_program,
				'keuangan_target_persen_2'=>$program_last_row+1,
				'keuangan_realisasi2'=>$realisasi_keuangan_program,
				'keuangan_realisasi_persen_2'=>$persen_realisasi_keuangan,
				'keuangan_ttb2'=>$persen_tertimbang_keuangan,
				'lokasi'=>'-',
				'sisa_anggaran'=>$sisa_anggaran,
				'sisa_anggaran_persen'=>$persen_sisa_anggaran,               
				'isprogram'=>true,
				'iskegiatan'=>false,
				'issubkegiatan'=>false,
			];
		}
		
		if ($totalPersenBobot > 100) {
			$totalPersenBobot = 100.000;
		}
		$totalPersenTargetFisik = Helper::formatPecahan($totalPersenTargetFisik,$total_sub_kegiatan);        
		$totalPersenRealisasiFisik=Helper::formatPecahan($totalPersenRealisasiFisik,$total_sub_kegiatan); 
		$totalPersenTargetKeuangan=Helper::formatPersen($totalTargetKeuanganKeseluruhan,$totalPaguOPD);                
		$totalPersenRealisasiKeuangan=Helper::formatPersen($totalRealisasiKeuanganKeseluruhan,$totalPaguOPD);
		$totalPersenSisaAnggaran=Helper::formatPersen($totalSisaAnggaran,$totalPaguOPD);
		$totalPersenBobot=round($totalPersenBobot,2);
		$total_ttb_fisik=round($total_ttb_fisik,2);
		$total_ttb_keuangan=round($total_ttb_keuangan,2);
		$total_data=[
			'totalPaguOPD'=>$totalPaguOPD,
			'totalPersenBobot'=>$totalPersenBobot,
			'totalPersenTargetFisik'=>$totalPersenTargetFisik,
			'totalPersenRealisasiFisik'=>$totalPersenRealisasiFisik,
			'total_ttb_fisik'=>$total_ttb_fisik,
			'totalTargetKeuanganKeseluruhan'=>$totalTargetKeuanganKeseluruhan,
			'totalRealisasiKeuanganKeseluruhan'=>$totalRealisasiKeuanganKeseluruhan,
			'totalPersenTargetKeuangan'=>$totalPersenTargetKeuangan,
			'totalPersenRealisasiKeuangan'=>$totalPersenRealisasiKeuangan,
			'total_ttb_keuangan'=>$total_ttb_keuangan,
			'totalSisaAnggaran'=>$totalSisaAnggaran,
			'totalPersenSisaAnggaran'=>$totalPersenSisaAnggaran,
		];       
		
		return Response()->json([
									'status'=>1,
									'pid'=>'fetchdata',
									'opd'=>$opd,
									'rka'=>$data,
									'total_data'=>$total_data,                                    
									'message'=>'Fetch data form b murni berhasil diperoleh'
								],200);    
		
	}
	public function printtoexcel (Request $request)
	{
		$this->hasPermissionTo('RENJA-FORM-B-MURNI_BROWSE');

		$this->validate($request, [            
			'tahun'=>'required',         
			'no_bulan'=>'required',   
			'OrgID'=>'required|exists:tmOrg,OrgID',            
		]);
		$tahun = $request->input('tahun');
		$no_bulan = $request->input('no_bulan');
		$OrgID = $request->input('OrgID');
		
		$opd = OrganisasiModel::find($OrgID);
		if (\DB::table('trRKA')->where('OrgID',$opd->OrgID)->where('EntryLvl',1)->where('TA',$tahun)->count()>0)
		{
			$data_report=[
							'OrgID'=>$opd->OrgID,
							'kode_organisasi'=>$opd->kode_organisasi,
							'Nm_Organisasi'=>$opd->Nm_Organisasi,
							'tahun'=>$tahun,
							'no_bulan'=>$no_bulan,
							'nama_pengguna_anggaran'=>$opd->NamaKepalaOPD,
							'nip_pengguna_anggaran'=>$opd->NIPKepalaOPD
						];
			$report= new \App\Models\Renja\FormBOPDMurniModel ($data_report);
			$generate_date=date('Y-m-d_H_m_s');
			return $report->download("form_b_$generate_date.xlsx");
		}
		else
		{
			return Response()->json([
									'status'=>0,
									'pid'=>'fetchdata',                                                                            
									'message'=>['Print excel gagal dilakukan karena tidak ada belum ada Uraian pada kegiatan ini']
								],422); 
		}
	}

}