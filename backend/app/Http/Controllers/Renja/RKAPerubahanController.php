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

class RKAPerubahanController extends Controller 
{
	private function recalculate($RKAID)
	{
		$paguuraian = \DB::table('trRKARinc')                            
					->where('RKAID',$RKAID)
					->sum('PaguUraian2');

		$jumlah_uraian = \DB::table('trRKARinc')                            
							->where('RKAID',$RKAID)
							->count('RKARincID');

		$data_realisasi = \DB::table('trRKARealisasiRinc')
							->select(\DB::raw('
								COALESCE(SUM(realisasi2),0) AS jumlah_realisasi,
								COALESCE(SUM(fisik2),0) AS jumlah_fisik
							'))
							->where('RKAID',$RKAID)
							->get();
		
		$rka = RKAModel::find($RKAID);
		$rka->PaguDana2 = $paguuraian;
		$rka->RealisasiKeuangan2=$data_realisasi[0]->jumlah_realisasi;
		$rka->RealisasiFisik2=Helper::formatPecahan($data_realisasi[0]->jumlah_fisik,$jumlah_uraian);
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
											`trRKA`.`lokasi_kegiatan2`,
											`trRKA`.`SumberDanaID`,
											`tmSumberDana`.`Nm_SumberDana`,
											`trRKA`.`tk_capaian2`,
											`trRKA`.`capaian_program2`,
											`trRKA`.`masukan2`,
											`trRKA`.`tk_keluaran2`,
											`trRKA`.`keluaran2`,
											`trRKA`.`tk_hasil2`,
											`trRKA`.`hasil2`,
											`trRKA`.`ksk2`,
											`trRKA`.`sifat_kegiatan2`,
											`trRKA`.`waktu_pelaksanaan2`,
											`trRKA`.`PaguDana2`,
											`trRKA`.`Descr`,
											`trRKA`.`EntryLvl`,
											`trRKA`.`Locked`,
											`trRKA`.`created_at`,
											`trRKA`.`updated_at`
											'))
							->leftJoin('tmSumberDana','tmSumberDana.SumberDanaID','trRKA.SumberDanaID')
							->where('trRKA.EntryLvl',2)
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
												`bulan2`,
												`target2`,
												`realisasi2`,
												target_fisik2,
												fisik2,
												`TA`,
												`Descr`,
												`created_at`,
												`updated_at`
											'))
							->where('RKARincID',$RKARincID)
							->orderBy('bulan2','ASC')
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
								->where('bulan2','<=',$item->bulan2)
								->sum('realisasi2');

				$sisa_anggaran=$datauraian->PaguUraian2-$sum_realisasi;            
				$daftar_realisasi[]=[
					'RKARealisasiRincID'=>$item->RKARealisasiRincID,
					'bulan2'=>$item->bulan2,
					'NamaBulan'=>Helper::getNamaBulan($item->bulan2),
					'target2'=>$item->target2,
					'realisasi2'=>$item->realisasi2,
					'target_fisik2'=>$item->target_fisik2,
					'fisik2'=>$item->fisik2,
					'sisa_anggaran'=>$sisa_anggaran,
					'Descr'=>$item->Descr,
					'TA'=>$item->TA,
					'created_at'=>$item->created_at,
					'updated_at'=>$item->updated_at,
				];
				
				$totalanggarankas+=$item->target2;
				$totalrealisasi+=$item->realisasi2;
				$totaltargetfisik+=$item->target_fisik2;
				$totalfisik+=$item->fisik2;
			}
			
			$data['datarealisasi']=$daftar_realisasi;
			$data['totalanggarankas']=$totalanggarankas;
			$data['totalrealisasi']=$totalrealisasi;
			$data['totaltargetfisik']=round($totaltargetfisik,2);
			$data['totalfisik']=round($totalfisik,2);
			$data['sisa_anggaran']=$datauraian->PaguUraian2-$totalrealisasi;
			
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
		$tahun=$request->input('tahun');
		$this->validate($request, [            
			'tahun'=>'required',
			'SOrgID'=>'required|exists:tmSOrg,SOrgID',
		]); 
		
		$SOrgID=$request->input('SOrgID');
		$unitkerja = SubOrganisasiModel::find($SOrgID);

		$str_insert = '
		INSERT INTO `trRKA` (
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
			`Locked`,
			`RKAID_Src`,
			created_at,
			updated_at
		)
		SELECT 
			uuid() AS `RKAID_New`, 
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
			`keluaran1` AS `keluaran2`,         
			`tk_keluaran1`,         
			`tk_keluaran1` AS `tk_keluaran2`,         
			`hasil1`,         
			`hasil1` AS `hasil2`,         
			`tk_hasil1`,         
			`tk_hasil1` AS `tk_hasil2`,         
			`capaian_program1`,  
			`capaian_program1` AS `capaian_program2`,  
			`tk_capaian1`,
			`tk_capaian1` AS `tk_capaian2`,
			`masukan1`,         
			`masukan1` AS `masukan2`,         
			`ksk1`,         
			`ksk1` AS `ksk2`,         
			`sifat_kegiatan1`,   
			`sifat_kegiatan1` AS `sifat_kegiatan2`,   
			`waktu_pelaksanaan1`,         
			`waktu_pelaksanaan1` AS `waktu_pelaksanaan2`,         
			`lokasi_kegiatan1`,      
			`lokasi_kegiatan1` AS `lokasi_kegiatan2`,      
			`PaguDana1`, 
			`PaguDana1` AS `PaguDana2`,        
			`RealisasiKeuangan1`, 
			`RealisasiKeuangan1` AS `RealisasiKeuangan2`,        
			`RealisasiFisik1`,        
			`RealisasiFisik1` AS `RealisasiFisik2`,        
			`nip_pa1`, 
			`nip_pa1` AS `nip_pa2`, 
			`nip_kpa1`, 
			`nip_kpa1` AS `nip_kpa2`, 
			`nip_ppk1`, 
			`nip_ppk1` AS `nip_ppk2`, 
			`nip_pptk1`, 
			`nip_pptk1` AS `nip_pptk2`, 
			\''.$this->getUserid().'\' AS `user_id`,
			2 AS `EntryLvl`,
			\'IMPORTED FROM RKA\' AS `Descr`,
			`TA`, 
			`Locked`,
			`RKAID` AS `RKAID_Src`,
			NOW() AS created_at,
			NOW() AS updated_at
		FROM
			trRKA
		WHERE `TA`='.$tahun.' 
		AND `EntryLvl`=1
		AND `Locked`=0
		AND `SOrgID`=\''.$unitkerja->SOrgID.'\'
		AND `RKAID` NOT IN (
			SELECT
				`RKAID_Src`
			FROM 
				`trRKA`
			WHERE `TA`='.$tahun.' 
			AND `EntryLvl`=2
			AND `SOrgID`=\''.$unitkerja->SOrgID.'\'		
		)
		';        
		\DB::statement($str_insert); 
		
		\DB::table('trRKA')				
				->where('SOrgID', $unitkerja->SOrgID)
				->where('EntryLvl', 1)
				->update([
					'Locked'=>true
				]);

		$data = RKAModel::where('SOrgID',$unitkerja->SOrgID)
							->where('TA',$tahun)
							->where('EntryLvl', 2)
							->get();
							
		return Response()->json([
								'status'=>1,
								'pid'=>'fetchdata',
								'unitkerja'=>$unitkerja,
								'rka'=>$data,
								'message'=>'Fetch data rka perubahan berhasil diperoleh'
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
			`Locked`,
			`RKARincID_Src`,
			created_at,
			updated_at
		) 
		SELECT
			uuid() AS `RKARincID`,
			\''.$rka->RKAID.'\' AS `RKAID`,
			`SIPDID`,
			`JenisPelaksanaanID`,
			`SumberDanaID`,
			`JenisPembangunanID`,            
			`kode_uraian1`,            
			`kode_uraian1` AS `kode_uraian2`,            
			`NamaUraian1`,            
			`NamaUraian1` AS `NamaUraian2`,            
			`volume1`,
			`volume1` AS `volume2`,
			`satuan1`,            
			`satuan1` AS `satuan2`,            
			`harga_satuan1`,
			`harga_satuan1` AS `harga_satuan2`,
			`PaguUraian1`,
			`PaguUraian1` AS `PaguUraian2`,
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
			2 AS `EntryLvl`,
			\'IMPORTED FROM RKARinc murni\' AS `Descr`,            
			`TA`,
			`Locked`,
			`RKARincID` AS `RKARincID_Src`,
			NOW() AS created_at,
			NOW() AS updated_at
		FROM trRKARinc
		WHERE RKAID = \''.$rka->RKAID_Src.'\'
		AND `Locked`=0
		AND `RKARincID` NOT IN (
			SELECT
				`RKARincID_Src`
			FROM 
				`trRKARinc`	
			WHERE RKAID = \''.$rka->RKAID.'\'
		)	
		';
		\DB::statement($str_insert); 
		
		\DB::table('trRKARinc')				
			->where('RKAID', $rka->RKAID_Src)
			->update([
				'Locked'=>true
			]);

		$data = RKARincianModel::select(\DB::raw('
									`RKARincID`,
									`SIPDID`,
									kode_uraian2 AS kode_uraian,
									`NamaUraian2` AS nama_uraian,
									CONCAT(volume2,\' \',satuan2) AS volume,
									`volume2`,
									`satuan2`,
									`harga_satuan2`,
									`PaguUraian2`,
									0 AS `realisasi2`,
									0 AS `fisik2`,
									`JenisPelaksanaanID`,
									`TA`,
									`RKARincID_Src`,
									created_at,
									updated_at
								'))                                
								->where('RKAID',$rka->RKAID)
								->get();
		
		$rka->PaguDana2 = $data->sum('PaguUraian2');        
		$rka->save();
		
		foreach ($data as $v) 
		{
			$sql_insert = '
				INSERT INTO `trRKATargetRinc` (
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
					`Locked`,
					`RKATargetRincID_Src`,
					created_at,
					updated_at
				)
				SELECT 
					uuid() AS `RKATargetRincID`, 
					\''.$rka->RKAID.'\' AS `RKAID`, 
					\''.$v->RKARincID.'\' AS `RKARincID`, 
					`bulan1`, 
					`bulan1` AS `bulan2`, 
					`target1`,         
					`target1` AS `target2`,
					`fisik1`,         
					`fisik1` AS `fisik2`,    
					2 AS `EntryLvl`, 
					\'imported from rka target murni\' AS `Descr`, 
					`TA`, 
					`Locked`,
					`RKATargetRincID` AS `RKATargetRincID_Src`,
					NOW() AS created_at,
					NOW() AS updated_at
				FROM `trRKATargetRinc`
				WHERE `RKARincID` = \''.$v->RKARincID_Src.'\'
				AND `Locked`=0
				AND `RKATargetRincID` NOT IN (
					SELECT
						`RKATargetRincID_Src`
					FROM 
						`trRKATargetRinc`	
					WHERE RKARincID = \''.$v->RKARincID.'\'
				)
			';
			\DB::statement($sql_insert);
			
			\DB::table('trRKATargetRinc')				
				->where('RKARincID', $v->RKARincID_Src)
				->update([
					'Locked'=>true
				]);

			$sql_insert = '
				INSERT INTO `trRKARealisasiRinc` (
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
					`Locked`,
					`RKARealisasiRincID_Src`,
					created_at,
					updated_at
				)
				SELECT 
					uuid() AS `RKARealisasiRincID`, 
					\''.$rka->RKAID.'\' AS `RKAID`, 
					\''.$v->RKARincID.'\' AS `RKARincID`, 
					`bulan1`, 
					`bulan1` AS `bulan2`, 
					`target1`,         
					`target1` AS `target2`,
					`realisasi1`,
					`realisasi1` AS `realisasi2`,  
					`target_fisik1`,
					`target_fisik1` AS `target_fisik2`,                              
					`fisik1`,         
					`fisik1` AS `fisik2`,    
					2 AS `EntryLvl`, 
					\'IMPORTED FROM REALISASI MURNI\'`Descr`, 
					`TA`, 
					`Locked`,
					`RKARealisasiRincID` AS `RKARealisasiRincID_Src`,
					NOW() AS created_at,
					NOW() AS updated_at
				FROM `trRKARealisasiRinc`
				WHERE `RKARincID` = \''.$v->RKARincID_Src.'\'
				AND `Locked`=0
				AND `RKARealisasiRincID` NOT IN (
					SELECT
						`RKARealisasiRincID_Src`
					FROM 
						`trRKARealisasiRinc`
					WHERE RKARincID = \''.$v->RKARincID.'\'
				)
			';
			\DB::statement($sql_insert);

			\DB::table('trRKARealisasiRinc')				
				->where('RKARincID', $v->RKARincID_Src)
				->update([
					'Locked'=>true
				]);

		}
		return Response()->json([
								'status'=>1,
								'pid'=>'fetchdata',
								'rka'=>$rka,
								'uraian'=>$data,
								'message'=>'Fetch data uraian rka perubahan berhasil diperoleh'
							], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
		
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{             
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_BROWSE');
		
		$this->validate($request, [            
			'tahun'=>'required',            
			'SOrgID'=>'required|exists:tmSOrg,SOrgID',            
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
							0 AS `PaguUraian2`,
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
							`RKAID_Src`,
							created_at,
							updated_at
						'))
						->where('SOrgID',$unitkerja->SOrgID)
						->where('TA',$tahun)
						->where('EntryLvl',2)
						->orderByRaw('kode_urusan="X" DESC')
						->orderBy('kode_bidang','ASC')
						->orderBy('kode_program','ASC')
						->orderBy('kode_kegiatan','ASC')
						->orderBy('kode_sub_kegiatan','ASC')
						->get();        
					
		$data->transform(function ($item,$key) {                            
			$item->persen_keuangan2=Helper::formatPersen($item->RealisasiKeuangan2,$item->PaguDana2);
			$item->PaguUraian2=\DB::table('trRKARinc')
			->where('RKAID', $item->RKAID)
			->where('EntryLvl', 2)
			->sum('PaguUraian2');
			
			return $item;
		});
		$jumlah_sub_kegiatan2 = $data->count();
		$unitkerja->RealisasiKeuangan2=$data->sum('RealisasiKeuangan2');
		$jumlah_realisasi_fisik=$data->sum('RealisasiFisik2');
		$unitkerja->RealisasiFisik2=Helper::formatPecahan($jumlah_realisasi_fisik,$jumlah_sub_kegiatan2);
		$unitkerja->JumlahSubKegiatan2=$jumlah_sub_kegiatan2;
		$unitkerja->save();

		return Response()->json([
								'status'=>1,
								'pid'=>'fetchdata',
								'unitkerja'=>$unitkerja,
								'rka'=>$data,
								'message'=>'Fetch data rka perubahan berhasil diperoleh'
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_STORE');

		$this->validate($request, [
			'OrgID'=>'required|exists:tmOrg,OrgID',
			'SOrgID'=>'required|exists:tmSOrg,SOrgID',
			'SubKgtID'=> [
				'required',
				'exists:tmSubKegiatan,SubKgtID',				
			]
		]);     
			
		$SubKgtID = $request->input('SubKgtID');
		
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
									->where('tmSubKegiatan.SubKgtID',$SubKgtID)
									->first();

		$organisasi = SubOrganisasiModel::select(\DB::raw('
									tmSOrg.kode_sub_organisasi,
									tmSOrg.Nm_Sub_Organisasi,
									tmOrg.kode_organisasi,
									tmOrg.Nm_Organisasi
								'))
								->join('tmOrg','tmOrg.OrgID','tmSOrg.OrgID')
								->where('tmSOrg.SOrgID',$request->input('SOrgID'))                                
								->first();
								
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
			'EntryLvl' => 2,

			'TA'=>$kodefikasisubkegiatan->TA,
		]);

		return Response()->json([
									'status'=>1,
									'pid'=>'store',
									'rka'=>$rka,                    
									'message'=>'Data RKA berhasil disimpan.'
								], 200); 
	}               
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function storeuraian(Request $request)
	{       
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_STORE');

		$this->validate($request, [
			'RKAID'=>'required|exists:trRKA,RKAID',
			'SubRObyID'=>'required|exists:tmSubROby,SubRObyID',
			'kode_uraian2'=> 'required',
			'nama_uraian2'=> 'required',
			'volume2'=> 'required|numeric',
			'satuan2'=> 'required',
			'harga_satuan2'=> 'required|numeric',
			'PaguUraian2'=> 'required',            
		]);     

		$rka = RKAModel::find($request->input('RKAID'));

		$uraian = RKARincianModel::create([
			'RKARincID' => Uuid::uuid4()->toString(),
			'RKAID' => $request->input('RKAID'),
			'kode_uraian2' => $request->input('kode_uraian2'),
			'NamaUraian2' => $request->input('nama_uraian2'),
			'volume2' => $request->input('volume2'),
			'satuan2' => $request->input('satuan2'),
			'harga_satuan2' => $request->input('harga_satuan2'),
			'PaguUraian2' => $request->input('PaguUraian2'),
			'Descr' => $request->input('Descr'),
			'EntryLvl' => 2,
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_UPDATE');

		$kegiatan = RKAModel::find($id);
		
		if (is_null($kegiatan) )
		{
			return Response()->json([
									'status'=>0,
									'pid'=>'fetchdata',
									'message'=>["Kegiatan dengan dengan ($id) gagal diperoleh"]
								],422); 
		}
		else if ($kegiatan->Locked)
		{
			return Response()->json([
									'status'=>0,
									'pid'=>'fetchdata',
									'message'=>["Kegiatan dengan dengan ($id) tidak bisa diubah karena sudah dikunci, saat copy data ke Perubahan."]
								],422); 
		}
		else
		{
			$this->validate($request, [
				'PaguDana2'=>'required',
				'SumberDanaID'=>'required',
				'keluaran2'=>'required',
				'tk_keluaran2'=>'required',
				'hasil2'=>'required',
				'tk_hasil2'=>'required',
				'capaian_program2'=>'required',
				'tk_capaian2'=>'required',
				'masukan2'=>'required',
				'ksk2'=>'required',
				'sifat_kegiatan2'=>'required',
				'waktu_pelaksanaan2'=>'required',
				'lokasi_kegiatan2'=>'required',                
				'nip_pa2'=>'required',
				'nip_kpa2'=>'required',
				'nip_ppk2'=>'required',
				'nip_pptk2'=>'required', 
			]);
			                
			$kegiatan->SumberDanaID=$request->input('SumberDanaID');                
			$kegiatan->keluaran2=$request->input('keluaran2');                
			$kegiatan->tk_keluaran2=$request->input('tk_keluaran2');                
			$kegiatan->hasil2=$request->input('hasil2');                
			$kegiatan->tk_hasil2=$request->input('tk_hasil2');                
			$kegiatan->capaian_program2=$request->input('capaian_program2');                
			$kegiatan->tk_capaian2=$request->input('tk_capaian2');                
			$kegiatan->masukan2=$request->input('masukan2');                
			$kegiatan->ksk2=$request->input('ksk2');                
			$kegiatan->sifat_kegiatan2=$request->input('sifat_kegiatan2');                
			$kegiatan->waktu_pelaksanaan2=$request->input('waktu_pelaksanaan2');                
			$kegiatan->lokasi_kegiatan2=$request->input('lokasi_kegiatan2');                                
			$kegiatan->nip_pa2=$request->input('nip_pa2');                
			$kegiatan->nip_kpa2=$request->input('nip_kpa2');                
			$kegiatan->nip_ppk2=$request->input('nip_ppk2');                
			$kegiatan->nip_pptk2=$request->input('nip_pptk2'); 
			$kegiatan->Descr=$request->input('Descr'); 
			$kegiatan->save();

			$PaguDana2 = $request->input('PaguDana2');
			\DB::statement("UPDATE `trRKA` SET `PaguDana2`='$PaguDana2' WHERE `RKAID`='$id'");
			
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
								],422); 
		}
		else if ($kegiatan->Locked)
		{
			return Response()->json([
									'status'=>0,
									'pid'=>'fetchdata',
									'message'=>["Kegiatan dengan dengan ($id) tidak bisa diubah karena sudah dikunci, saat copy data ke Perubahan."]
								],422); 
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_UPDATE');

		$rinciankegiatan = RKARincianModel::find($id);
		if (is_null($rinciankegiatan) )
		{
			return Response()->json([
									'status'=>0,
									'pid'=>'fetchdata',
									'message'=>["Rincian Kegiatan dengan dengan ($id) gagal diperoleh"]
								],422); 
		}
		else if ($rinciankegiatan->Locked)
		{
			return Response()->json([
									'status'=>0,
									'pid'=>'fetchdata',
									'message'=>["Rincian Kegiatan dengan dengan ($id) tidak bisa diubah karena sudah dikunci, saat copy data ke Perubahan."]
								],422); 
		}
		else
		{
			$this->validate($request, [
				'volume2'=>'required',
				'satuan2'=>'required',
				'harga_satuan2'=>'required',
				'PaguUraian2'=>'required',
			]);
			
			$rinciankegiatan = \DB::transaction(function () use ($request,$rinciankegiatan) {
				$rinciankegiatan->volume2=$request->input('volume2');
				$rinciankegiatan->satuan2=$request->input('satuan2');
				$rinciankegiatan->harga_satuan2=$request->input('harga_satuan2');
				$rinciankegiatan->PaguUraian2=$request->input('PaguUraian2');
				$rinciankegiatan->JenisPelaksanaanID = $request->input('JenisPelaksanaanID');                   
				$rinciankegiatan->save();

				\DB::table('sipd')
					->where('SIPDID',$rinciankegiatan->SIPDID)
					->update(['PaguUraian2'=>$request->input('PaguUraian2')]);                

				$paguuraian=RKARincianModel::where('RKAID',$rinciankegiatan->RKAID)                                 
											->sum('PaguUraian2');                
				
				\DB::table('trRKA')
					->where('RKAID', $rinciankegiatan->RKAID)
					->update([
						'PaguDana2'=>$paguuraian
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_UPDATE');

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
	public function bulanrealisasi(Request $request,$id)
	{ 
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_BROWSE');

		$bulan=Helper::getNamaBulan();
		$bulan_realisasi=RKARealisasiModel::select('bulan2')
													->where('RKARincID',$id)
													->get()
													->pluck('bulan2','bulan2')
													->toArray();
		$data = [];
		foreach($bulan as $k=>$v)
		{
			if (!array_key_exists($k,$bulan_realisasi))
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_STORE');

		$this->validate($request, [
			'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
			'bulan_fisik.*'=>'required',
		]);

		
		$bulan_fisik= $request->input('bulan_fisik');      
		$data = [];
		$now = \Carbon\Carbon::now('utc')->toDateTimeString();
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
				'fisik1'=>0,
				'fisik2'=>$bulan_fisik[$i],
				'EntryLvl'=>2,
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_UPDATE');

		$this->validate($request, [
			'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
			'bulan_fisik.*'=>'required',
		]);

		$bulan_fisik= $request->input('bulan_fisik');      
		$data = [];
		$now = \Carbon\Carbon::now('utc')->toDateTimeString();
		for ($i=0;$i < 12; $i+=1)
		{
			\DB::table('trRKATargetRinc')
				->where('RKARincID',$request->input('RKARincID'))
				->where('bulan2',$i+1)
				->update(['fisik2'=>$bulan_fisik[$i]]);
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_STORE');

		$this->validate($request, [
			'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
			'bulan_fisik.*'=>'required',
		]);

		$bulan_anggaran= $request->input('bulan_anggaran');      
		$data = [];
		$now = \Carbon\Carbon::now('utc')->toDateTimeString();
		for ($i=0;$i < 12; $i+=1)
		{
			$data[]=[
				'RKATargetRincID'=>Uuid::uuid4()->toString(),
				'RKAID'=>$request->input('RKAID'),
				'RKARincID'=>$request->input('RKARincID'),
				'bulan1'=>$i+1,
				'bulan2'=>$i+1,
				'fisik1'=>0,
				'fisik2'=>0,
				'target1'=>0,
				'target2'=>$bulan_anggaran[$i],
				'EntryLvl'=>2,
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
								'message'=>'Rencana target anggaran kas uraian berhasil disimpan.'
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_UPDATE');

		$this->validate($request, [
			'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
			'bulan_fisik.*'=>'required',
		]);

		$bulan_anggaran= $request->input('bulan_anggaran');      
		$data = [];
		$now = \Carbon\Carbon::now('utc')->toDateTimeString();
		for ($i=0;$i < 12; $i+=1)
		{
			\DB::table('trRKATargetRinc')
				->where('RKARincID',$request->input('RKARincID'))
				->where('bulan2',$i+1)
				->update(['target2'=>$bulan_anggaran[$i]]);
		}

		return Response()->json([
								'status'=>1,
								'pid'=>'update',
								'message'=>'Rencana target anggaran kas uraian berhasil diubah.'
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_STORE');

		$this->validate($request, [
			'RKARincID'=>'required',
			'RKAID'=>'required',
			'bulan2'=>'required',            
			'target2'=>'required',
			'realisasi2'=>'required',
			'target_fisik2'=>'required',
			'fisik2'=>'required',      
		]);
		$RKAID=$request->input('RKAID');
		$realisasi = RKARealisasiModel::create([
			'RKARealisasiRincID' => Uuid::uuid4()->toString(),
			'RKAID' => $RKAID,
			'RKARincID' => $request->input('RKARincID'),            
			'bulan2' => $request->input('bulan2'),            
			'target2' => $request->input('target2'),        
			'realisasi2' => $request->input('realisasi2'),        
			'target_fisik2' => $request->input('target_fisik2'),       
			'fisik2' => $request->input('fisik2'),       
			'EntryLvl' => 2,
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_UPDATE');
		
		$this->validate($request, [                    
			'target2'=>'required',
			'realisasi2'=>'required',
			'target_fisik2'=>'required',
			'fisik2'=>'required',      
		]);

		$realisasi = RKARealisasiModel::find($id);    
		$realisasi->target2 = $request->input('target2');
		$realisasi->realisasi2 = $request->input('realisasi2');
		$realisasi->target_fisik2 = $request->input('target_fisik2');
		$realisasi->fisik2 = $request->input('fisik2');        
		$realisasi->Descr = $request->input('Descr');
		$realisasi->save();                     

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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_SHOW');

		$rka = $this->getDataRKA($id);

		if (is_null($rka))
		{
			return Response()->json([
				'status'=>0,
				'pid'=>'fetchdata',
				'message'=>"Fetch data kegiatan perubahan dengan id ($id) gagal diperoleh"
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
									`trRKARinc`.kode_uraian2 AS kode_uraian,
									`trRKARinc`.`NamaUraian2` AS nama_uraian,
									`trRKARinc`.`volume2`,
									`trRKARinc`.`satuan2`,
									CONCAT(`trRKARinc`.volume2,\' \',`trRKARinc`.satuan2) AS volume,
									`trRKARinc`.`harga_satuan2`,
									`trRKARinc`.`PaguUraian2`,
									0 AS `realisasi2`,
									0 AS `persen_keuangan2`,
									0 AS `fisik2`,                                                        
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
									`trRKARinc`.`RKARincID_Src`,
									`trRKARinc`.created_at,
									`trRKARinc`.updated_at
								'))                                
								->where('RKAID',$rka->RKAID)
								->orderBy('trRKARinc.kode_uraian2','ASC')
								->get();
			
			$data->transform(function ($item,$key) {
				$item->realisasi2=\DB::table('trRKARealisasiRinc')->where('RKARincID',$item->RKARincID)->sum('realisasi2');    
				$item->fisik2=\DB::table('trRKARealisasiRinc')->where('RKARincID',$item->RKARincID)->sum('fisik2');
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_SHOW');

		$this->validate($request, [            
			'mode'=>'required',            
			'RKARincID'=>'required|exists:trRKARinc,RKARincID',            
		]);
		$mode = $request->input('mode');
		$RKARincID = $request->input('RKARincID');
		
		$data_uraian = RKARincianModel::select(\DB::raw('
										`SIPDID`,
										kode_uraian2 AS kode_uraian,
										NamaUraian2 AS nama_uraian,
										`PaguUraian2`
									'))
									->find($RKARincID);
		
		$data_realisasi = \DB::table('trRKARealisasiRinc')
					->select(\DB::raw('
						COALESCE(SUM(target2),0) AS jumlah_targetanggarankas,
						COALESCE(SUM(realisasi2),0) AS jumlah_realisasi,
						COALESCE(SUM(target_fisik2),0) AS jumlah_targetfisik,
						COALESCE(SUM(fisik2),0) AS jumlah_fisik
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
									TRAILING '}' FROM JSON_OBJECT(CONCAT('fisik_',`trRKATargetRinc`.`bulan2`),`trRKATargetRinc`.`fisik2`)
								)
							)
						),
					'}') AS `fisik2`					
				"))
				->where('RKARincID',$RKARincID)
				->groupBy('RKARincID')
				->get();                  		

			$target=isset($data[0]) ? json_decode($data[0]->fisik2, true) : [];
		}
		else if ($mode == 'targetanggarankas')
		{            
			$data = \DB::table('trRKATargetRinc')
				->select(\DB::raw("					
					CONCAT('{',
						GROUP_CONCAT(
							TRIM(
								LEADING '{' FROM TRIM(
									TRAILING '}' FROM JSON_OBJECT(CONCAT('anggaran_',`trRKATargetRinc`.`bulan2`),`trRKATargetRinc`.`target2`)
								)
							)
						),
					'}') AS `anggaran2`
				"))
				->where('RKARincID',$RKARincID)
				->groupBy('RKARincID')
				->get();                  		

			$target=isset($data[0]) ? json_decode($data[0]->anggaran2, true) : [];
		}
		else if ($mode == 'bulan' && $request->has('bulan2'))
		{
			$bulan2 = $request->input('bulan2');
			
			$data = \DB::table('trRKATargetRinc')
				->select(\DB::raw("
					CONCAT('{',
						GROUP_CONCAT(
							TRIM(
								LEADING '{' FROM TRIM(
									TRAILING '}' FROM JSON_OBJECT(CONCAT('fisik_',`trRKATargetRinc`.`bulan2`),`trRKATargetRinc`.`fisik2`)
								)
							)
						),
					'}') AS `fisik2`,
					CONCAT('{',
						GROUP_CONCAT(
							TRIM(
								LEADING '{' FROM TRIM(
									TRAILING '}' FROM JSON_OBJECT(CONCAT('anggaran_',`trRKATargetRinc`.`bulan2`),`trRKATargetRinc`.`target2`)
								)
							)
						),
					'}') AS `anggaran2`
				"))
				->where('RKARincID',$RKARincID)
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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_SHOW');

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
		$this->hasPermissionTo('RENJA-RKA-PERUBAHAN_DESTROY');
		$message = \DB::transaction(function () use ($request, $id) {
			$pid=$request->input('pid');
			$message = "PID tidak dikenali";
			switch ($pid)
			{          
				case 'datarka' :
					$rka = RKAModel::find($id);
					$RKAID_Src = $rka->RKAID_Src;
					$rka->delete();
					\DB::table('trRKA')
					->where('RKAID', $RKAID_Src)
					->update([
						'Locked'=>0
					]);
					\DB::table('trRKARinc')
					->where('RKAID', $RKAID_Src)
					->update([
						'Locked'=>0
					]);
					\DB::table('trRKATargetRinc')
					->where('RKAID', $RKAID_Src)
					->update([
						'Locked'=>0
					]);
					\DB::table('trRKARealisasiRinc')
					->where('RKAID', $RKAID_Src)
					->update([
						'Locked'=>0
					]);
					$message="data rka perubahan dengan ID ($id) Berhasil di Hapus";                 
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
			return $message;          
		});

		return Response()->json([
									'status'=>1,
									'pid'=>'destroy',
									'message'=>$message
								], 200);  
			  
	}
}