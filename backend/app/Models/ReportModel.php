<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Helpers\Helper;

class ReportModel extends Model
{   
	/**
	* data report
	*/
	protected $dataReport = [];
   /**
	* object spreadsheet
	*/
	protected $spreadsheet;
	/**
	 * digunakan untuk menyimpan data satu kegiatan
	 */
	protected $dataKegiatan = [];
	/**
	 * digunakan untuk menyimpan data RKA atau uraian
	 */
	protected $dataRKA = [];

	public function __construct($dataReport)
	{
		$this->dataReport = $dataReport;
		$this->spreadsheet = new Spreadsheet();         
	}
   /**
	 * digunakan untuk mengeset data report dan inisialisasi object spreadsheet
	 */
	public function setObjReport($dataReport)
	{   
		$this->dataReport = $dataReport;
		$this->spreadsheet = new Spreadsheet();         
	}
	public function download(string $filename)
	{
		$pathToFile = Helper::exported_path().'excel/'.$filename;
		$this->spreadsheet->getProperties()->setCreator(config('simonev.nama_institusi'));
		$this->spreadsheet->getProperties()->setLastModifiedBy(config('simonev.nama_institusi'));         
		$writer = new Xlsx($this->spreadsheet);
		$writer->save($pathToFile);        
		return response()->download($pathToFile)->deleteFileAfterSend(false);
	}    
	/**
	* digunakan untuk mendapatkan data RKA
	*/
	public function getDataRKA ($id,$no_bulan,$entryLvl)
	{      
		$dataAkhir=[];
		switch ($entryLvl)
		{
			case 1 :
				$sql = \DB::raw('`trRKA`.`RKAID`,
								`trRKA`.`kode_urusan`,
								`trRKA`.`Nm_Urusan`,
								`trRKA`.`kode_bidang`,
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
								`C`.`Nm_SumberDana`,
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
								`trRKA`.`TA`,
								`trRKA`.`EntryLvl`,
								`A`.`NIP_ASN` AS nip_pa,
								`A`.`Nm_ASN` AS nama_pa,
								`B`.`NIP_ASN` AS nip_pptk,
								`B`.`Nm_ASN` AS nama_pptk,
								`trRKA`.`created_at`,
								`trRKA`.`updated_at`
							');

				$rka = \App\Models\Renja\RKAModel::select($sql)                              
							->leftJoin('tmASN AS A','A.ASNID','trRKA.nip_pa1')     
							->leftJoin('tmASN AS B','B.ASNID','trRKA.nip_pptk1')     
							->leftJoin('tmSumberDana AS C','C.SumberDanaID','trRKA.SumberDanaID')                                 
							->where('trRKA.EntryLvl',$entryLvl)
							->find($id);

				if (!is_null($rka))
				{
					$this->dataKegiatan=$rka->toArray();        
					$totalPaguUraian = $rka->PaguDana1;
					$this->dataKegiatan['total_pagu_uraian']=$totalPaguUraian;
					$data_akhir = \DB::table('trRKARinc')
									->select(\DB::raw("
										`trRKARinc`.`RKARincID`,
										`trRKARinc`.`RKAID`,
										tmAkun.`Kd_Rek_1`, 
										tmAkun.`Nm_Akun`, 
										CONCAT(tmAkun.`Kd_Rek_1`,'.',tmKlp.`Kd_Rek_2`) AS `Kd_Rek_2`, tmKlp.`KlpNm`, 
										CONCAT(tmAkun.`Kd_Rek_1`,'.',tmKlp.`Kd_Rek_2`,'.',tmJns.`Kd_Rek_3`) AS `Kd_Rek_3`, tmJns.`JnsNm`, 
										CONCAT(tmAkun.`Kd_Rek_1`,'.',tmKlp.`Kd_Rek_2`,'.',tmJns.`Kd_Rek_3`,'.',tmOby.`Kd_Rek_4`) AS `Kd_Rek_4`, tmOby.`ObyNm`,
										CONCAT(tmAkun.`Kd_Rek_1`,'.',tmKlp.`Kd_Rek_2`,'.',tmJns.`Kd_Rek_3`,'.',tmOby.`Kd_Rek_4`,'.',tmROby.`Kd_Rek_5`) AS `Kd_Rek_5`, tmROby.`RObyNm`, 
										CONCAT(tmAkun.`Kd_Rek_1`,'.',tmKlp.`Kd_Rek_2`,'.',tmJns.`Kd_Rek_3`,'.',tmOby.`Kd_Rek_4`,'.',tmROby.`Kd_Rek_5`,'.',tmSubROby.`Kd_Rek_6`) AS `Kd_Rek_6`, 
										`tmSubROby`.`SubRObyNm`, 
										`trRKARinc`.`NamaUraian1`, 
										`trRKARinc`.`PaguUraian1`, 
										`trRKARinc`.`volume1`, 
										`trRKARinc`.`satuan1`, 
										`trRKARinc`.`harga_satuan1` 
									"))
									->where('RKAID',$rka->RKAID)
									->join('tmSubROby','tmSubROby.kode_rek_6','trRKARinc.kode_uraian1')
									->join('tmROby','tmROby.RobyID','tmSubROby.RobyID')
									->join('tmOby','tmOby.ObyID','tmROby.ObyID')
									->join('tmJns','tmJns.JnsID','tmOby.JnsID')
									->join('tmKlp','tmKlp.KlpID','tmJns.KlpID')
									->join('tmAkun','tmAkun.AkunID','tmKlp.AkunID')
									->get();   
					
					foreach ($data_akhir as $k=>$v)
					{
						$RKARincID=$v->RKARincID;                      
						$nama_uraian=$v->NamaUraian1;
						$target=(float)\DB::table('trRKATargetRinc')
											->where('RKARincID',$RKARincID)
											->where('bulan1','<=',$no_bulan)
											->sum('target1');  
						
						$data_realisasi=\DB::table('trRKARealisasiRinc')
											->select(\DB::raw('COALESCE(SUM(realisasi1),0) AS realisasi1, COALESCE(SUM(fisik1),0) AS fisik1'))
											->where('RKARincID',$RKARincID)
											->where('bulan1','<=',$no_bulan)
											->get();
						
						$realisasi=(float)$data_realisasi[0]->realisasi1;
						$fisik=(float)$data_realisasi[0]->fisik1;            
						$persen_fisik=number_format((($fisik > 100) ? 100:$fisik),2);
						$no_rek6=$v->Kd_Rek_6;            
						if (array_key_exists ($no_rek6, $dataAkhir)) 
						{
							$persenbobot=Helper::formatPersen($v->PaguUraian1,$totalPaguUraian); 
							$persen_target=Helper::formatPersen($target,$totalPaguUraian);   
							$persen_realisasi=Helper::formatPersen($realisasi,$totalPaguUraian);
							$persen_tertimbang_realisasi=number_format(($persen_realisasi*$persenbobot)/100,2);   
							$persen_tertimbang_fisik=number_format(($persen_fisik*$persenbobot)/100,2);

							$dataAkhir[$no_rek5]['child'][]=[
													'RKARincID'=>$v->RKARincID,
													'Kd_Rek_1'=>$v->Kd_Rek_1,
													'Nm_Akun'=>$v->Nm_Akun,
													'Kd_Rek_2'=>$v->Kd_Rek_2,
													'KlpNm'=>$v->KlpNm,
													'Kd_Rek_3'=>$v->Kd_Rek_3,
													'JnsNm'=>$v->JnsNm,
													'Kd_Rek_4'=>$v->Kd_Rek_4,
													'ObyNm'=>$v->ObyNm,
													'Kd_Rek_5'=>$v->Kd_Rek_5,
													'RObyNm'=>$v->RObyNm,
													'Kd_Rek_6'=>$v->Kd_Rek_6,
													'SubRObyNm'=>$v->SubRObyNm,
													'nama_uraian'=>$nama_uraian,                                        
													'pagu_uraian'=>$v->PaguUraian1,
													'persen_bobot'=>$persenbobot,
													'target'=>$target,
													'persen_target'=>$persen_target,
													'realisasi'=>$realisasi,
													'persen_realisasi'=>$persen_realisasi,
													'persen_tertimbang_realisasi'=>$persen_tertimbang_realisasi,
													'fisik'=>$fisik,
													'persen_fisik'=>$persen_fisik,
													'persen_tertimbang_fisik'=>$persen_tertimbang_fisik,
													'volume'=>$v->volume1,
													'harga_satuan'=>(float)$v->harga_satuan1,
													'satuan'=>$v->satuan1
												];
						}
						else
						{
							$persenbobot=Helper::formatPersen($v->PaguUraian1,$totalPaguUraian); 
							$persen_target=Helper::formatPersen($target,$totalPaguUraian);   
							$persen_realisasi=Helper::formatPersen($realisasi,$totalPaguUraian);
							$persen_tertimbang_realisasi=number_format(($persen_realisasi*$persenbobot)/100,2);   
							$persen_tertimbang_fisik=number_format(($persen_fisik*$persenbobot)/100,2);
							
							$dataAkhir[$no_rek6]=[
													'RKARincID'=>$v->RKARincID,
													'Kd_Rek_1'=>$v->Kd_Rek_1,
													'Nm_Akun'=>$v->Nm_Akun,
													'Kd_Rek_2'=>$v->Kd_Rek_2,
													'KlpNm'=>$v->KlpNm,
													'Kd_Rek_3'=>$v->Kd_Rek_3,
													'JnsNm'=>$v->JnsNm,
													'Kd_Rek_4'=>$v->Kd_Rek_4,
													'ObyNm'=>$v->ObyNm,
													'Kd_Rek_5'=>$v->Kd_Rek_5,
													'RObyNm'=>$v->RObyNm,
													'Kd_Rek_6'=>$v->Kd_Rek_6,
													'SubRObyNm'=>$v->SubRObyNm,
													'nama_uraian'=>$nama_uraian,                                        
													'pagu_uraian'=>$v->PaguUraian1,
													'persen_bobot'=>$persenbobot,
													'target'=>$target,
													'persen_target'=>$persen_target,
													'realisasi'=>$realisasi,
													'persen_realisasi'=>$persen_realisasi,
													'persen_tertimbang_realisasi'=>$persen_tertimbang_realisasi,
													'fisik'=>$fisik,
													'persen_fisik'=>$persen_fisik,
													'persen_tertimbang_fisik'=>$persen_tertimbang_fisik,
													'volume'=>$v->volume1,
													'harga_satuan'=>(float)$v->harga_satuan1,
													'satuan'=>$v->satuan1
												];
							
						}

					}       	
				}
			break;
			case 2 :
				$sql = \DB::raw('`trRKA`.`RKAID`,
								`trRKA`.`kode_urusan`,
								`trRKA`.`Nm_Urusan`,
								`trRKA`.`kode_bidang`,
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
								`C`.`Nm_SumberDana`,
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
								`trRKA`.`TA`,
								`trRKA`.`EntryLvl`,
								`A`.`NIP_ASN` AS nip_pa,
								`A`.`Nm_ASN` AS nama_pa,
								`B`.`NIP_ASN` AS nip_pptk,
								`B`.`Nm_ASN` AS nama_pptk,
								`trRKA`.`created_at`,
								`trRKA`.`updated_at`
							');

				$rka = \App\Models\Renja\RKAModel::select($sql)                              
							->leftJoin('tmASN AS A','A.ASNID','trRKA.nip_pa2')     
							->leftJoin('tmASN AS B','B.ASNID','trRKA.nip_pptk2')     
							->leftJoin('tmSumberDana AS C','C.SumberDanaID','trRKA.SumberDanaID')                                 
							->where('trRKA.EntryLvl',$entryLvl)
							->find($id);

				if (!is_null($rka))
				{
					$this->dataKegiatan=$rka->toArray();        
					$totalPaguUraian = $rka->PaguDana2;
					$this->dataKegiatan['total_pagu_uraian']=$totalPaguUraian;
					$data_akhir = \DB::table('trRKARinc')
									->select(\DB::raw("
										`trRKARinc`.`RKARincID`,
										`trRKARinc`.`RKAID`,
										tmAkun.`Kd_Rek_1`, 
										tmAkun.`Nm_Akun`, 
										CONCAT(tmAkun.`Kd_Rek_1`,'.',tmKlp.`Kd_Rek_2`) AS `Kd_Rek_2`, tmKlp.`KlpNm`, 
										CONCAT(tmAkun.`Kd_Rek_1`,'.',tmKlp.`Kd_Rek_2`,'.',tmJns.`Kd_Rek_3`) AS `Kd_Rek_3`, tmJns.`JnsNm`, 
										CONCAT(tmAkun.`Kd_Rek_1`,'.',tmKlp.`Kd_Rek_2`,'.',tmJns.`Kd_Rek_3`,'.',tmOby.`Kd_Rek_4`) AS `Kd_Rek_4`, tmOby.`ObyNm`,
										CONCAT(tmAkun.`Kd_Rek_1`,'.',tmKlp.`Kd_Rek_2`,'.',tmJns.`Kd_Rek_3`,'.',tmOby.`Kd_Rek_4`,'.',tmROby.`Kd_Rek_5`) AS `Kd_Rek_5`, tmROby.`RObyNm`, 
										CONCAT(tmAkun.`Kd_Rek_1`,'.',tmKlp.`Kd_Rek_2`,'.',tmJns.`Kd_Rek_3`,'.',tmOby.`Kd_Rek_4`,'.',tmROby.`Kd_Rek_5`,'.',tmSubROby.`Kd_Rek_6`) AS `Kd_Rek_6`, 
										`tmSubROby`.`SubRObyNm`, 
										`trRKARinc`.`NamaUraian2`, 
										`trRKARinc`.`PaguUraian2`, 
										`trRKARinc`.`volume2`, 
										`trRKARinc`.`satuan2`, 
										`trRKARinc`.`harga_satuan2` 
									"))
									->where('RKAID',$rka->RKAID)
									->join('tmSubROby','tmSubROby.kode_rek_6','trRKARinc.kode_uraian2')
									->join('tmROby','tmROby.RobyID','tmSubROby.RobyID')
									->join('tmOby','tmOby.ObyID','tmROby.ObyID')
									->join('tmJns','tmJns.JnsID','tmOby.JnsID')
									->join('tmKlp','tmKlp.KlpID','tmJns.KlpID')
									->join('tmAkun','tmAkun.AkunID','tmKlp.AkunID')
									->get();   
					
					foreach ($data_akhir as $k=>$v)
					{
						$RKARincID=$v->RKARincID;                      
						$nama_uraian=$v->NamaUraian1;
						$target=(float)\DB::table('trRKATargetRinc')
											->where('RKARincID',$RKARincID)
											->where('bulan2','<=',$no_bulan)
											->sum('target2');  
						
						$data_realisasi=\DB::table('trRKARealisasiRinc')
											->select(\DB::raw('COALESCE(SUM(realisasi2),0) AS realisasi2, COALESCE(SUM(fisik2),0) AS fisik2'))
											->where('RKARincID',$RKARincID)
											->where('bulan2','<=',$no_bulan)
											->get();
						
						$realisasi=(float)$data_realisasi[0]->realisasi2;
						$fisik=(float)$data_realisasi[0]->fisik2;            
						$persen_fisik=number_format((($fisik > 100) ? 100:$fisik),2);
						$no_rek6=$v->Kd_Rek_6;            
						if (array_key_exists ($no_rek6, $dataAkhir)) 
						{
							$persenbobot=Helper::formatPersen($v->PaguUraian2,$totalPaguUraian); 
							$persen_target=Helper::formatPersen($target,$totalPaguUraian);   
							$persen_realisasi=Helper::formatPersen($realisasi,$totalPaguUraian);
							$persen_tertimbang_realisasi=number_format(($persen_realisasi*$persenbobot)/100,2);   
							$persen_tertimbang_fisik=number_format(($persen_fisik*$persenbobot)/100,2);

							$dataAkhir[$no_rek5]['child'][]=[
													'RKARincID'=>$v->RKARincID,
													'Kd_Rek_1'=>$v->Kd_Rek_1,
													'Nm_Akun'=>$v->Nm_Akun,
													'Kd_Rek_2'=>$v->Kd_Rek_2,
													'KlpNm'=>$v->KlpNm,
													'Kd_Rek_3'=>$v->Kd_Rek_3,
													'JnsNm'=>$v->JnsNm,
													'Kd_Rek_4'=>$v->Kd_Rek_4,
													'ObyNm'=>$v->ObyNm,
													'Kd_Rek_5'=>$v->Kd_Rek_5,
													'RObyNm'=>$v->RObyNm,
													'Kd_Rek_6'=>$v->Kd_Rek_6,
													'SubRObyNm'=>$v->SubRObyNm,
													'nama_uraian'=>$nama_uraian,                                        
													'pagu_uraian'=>$v->PaguUraian2,
													'persen_bobot'=>$persenbobot,
													'target'=>$target,
													'persen_target'=>$persen_target,
													'realisasi'=>$realisasi,
													'persen_realisasi'=>$persen_realisasi,
													'persen_tertimbang_realisasi'=>$persen_tertimbang_realisasi,
													'fisik'=>$fisik,
													'persen_fisik'=>$persen_fisik,
													'persen_tertimbang_fisik'=>$persen_tertimbang_fisik,
													'volume'=>$v->volume2,
													'harga_satuan'=>(float)$v->harga_satuan2,
													'satuan'=>$v->satuan2
												];
						}
						else
						{
							$persenbobot=Helper::formatPersen($v->PaguUraian2,$totalPaguUraian); 
							$persen_target=Helper::formatPersen($target,$totalPaguUraian);   
							$persen_realisasi=Helper::formatPersen($realisasi,$totalPaguUraian);
							$persen_tertimbang_realisasi=number_format(($persen_realisasi*$persenbobot)/100,2);   
							$persen_tertimbang_fisik=number_format(($persen_fisik*$persenbobot)/100,2);
							
							$dataAkhir[$no_rek6]=[
													'RKARincID'=>$v->RKARincID,
													'Kd_Rek_1'=>$v->Kd_Rek_1,
													'Nm_Akun'=>$v->Nm_Akun,
													'Kd_Rek_2'=>$v->Kd_Rek_2,
													'KlpNm'=>$v->KlpNm,
													'Kd_Rek_3'=>$v->Kd_Rek_3,
													'JnsNm'=>$v->JnsNm,
													'Kd_Rek_4'=>$v->Kd_Rek_4,
													'ObyNm'=>$v->ObyNm,
													'Kd_Rek_5'=>$v->Kd_Rek_5,
													'RObyNm'=>$v->RObyNm,
													'Kd_Rek_6'=>$v->Kd_Rek_6,
													'SubRObyNm'=>$v->SubRObyNm,
													'nama_uraian'=>$nama_uraian,                                        
													'pagu_uraian'=>$v->PaguUraian2,
													'persen_bobot'=>$persenbobot,
													'target'=>$target,
													'persen_target'=>$persen_target,
													'realisasi'=>$realisasi,
													'persen_realisasi'=>$persen_realisasi,
													'persen_tertimbang_realisasi'=>$persen_tertimbang_realisasi,
													'fisik'=>$fisik,
													'persen_fisik'=>$persen_fisik,
													'persen_tertimbang_fisik'=>$persen_tertimbang_fisik,
													'volume'=>$v->volume2,
													'harga_satuan'=>(float)$v->harga_satuan2,
													'satuan'=>$v->satuan2
												];
							
						}

					}       	
				}
			break;
			
		}          
		$this->dataRKA=$dataAkhir;
		return $dataAkhir;
	}
	/**
	 * digunakan untuk mendapatkan data kegiatan
	*/  
	public function gerDataKegiatan()
	{
		return $this->dataKegiatan;
	}
	/**
	* digunakan untuk mendapatkan tingkat rekening Form A	
	*/
	public function getRekeningProyek () {		 
		$a=$this->dataRKA;        
		$tingkat=[];
		foreach ($a as $v) {					
			$tingkat[1][$v['Kd_Rek_1']]=$v['Nm_Akun'];
			$tingkat[2][$v['Kd_Rek_2']]=$v['KlpNm'];
			$tingkat[3][$v['Kd_Rek_3']]=$v['JnsNm'];
			$tingkat[4][$v['Kd_Rek_4']]=$v['ObyNm'];
			$tingkat[5][$v['Kd_Rek_5']]=$v['RObyNm'];				
			$tingkat[6][$v['Kd_Rek_6']]=$v['SubRObyNm'];				
		}
		return $tingkat;
	}
	/**
	* digunakan untuk menghitung jumlah setiap level rekening Form A 
	*/
	public static function calculateEachLevel ($dataproyek,$k,$no_rek) {        
		$totalpagu=0;
		$totaltarget=0;
		$totalrealisasi=0;        
		$totalfisik=0;
		$totalpersenbobot='0.00';
		$totalpersentarget=0;
		$totalpersenrealisasi=0;
		$totalpersentertimbangrealisasi=0;
		$totalpersentertimbangfisik=0;
		$totalbaris=0;        
		foreach ($dataproyek as $de) {                        
			if ($k==$de[$no_rek]) {
				$totalpagu+=$de['pagu_uraian'];
				$totaltarget+=$de['target'];
				$totalrealisasi+=$de['realisasi'];
				$totalfisik+=$de['persen_fisik'];
				$totalpersenbobot+=$de['persen_bobot'];
				$totalpersentarget+=$de['persen_target'];
				$totalpersenrealisasi+=$de['persen_realisasi'];
				$totalpersentertimbangrealisasi+=$de['persen_tertimbang_realisasi'];
				$totalpersentertimbangfisik+=$de['persen_tertimbang_fisik'];
				$totalbaris+=1;
				if (isset($dataproyek[$de['Kd_Rek_5']]['child'][0])) {                    
					$child=$dataproyek[$de['Kd_Rek_5']]['child'];                    
					foreach ($child as $n) {                       
						$totalbaris+=1;
						$totalpagu+=$n['pagu_uraian'];
						$totaltarget+=$n['target'];
						$totalrealisasi+=$n['realisasi'];
						$totalfisik+=$n['persen_fisik'];
						$totalpersenbobot+=$n['persen_bobot'];                                                        
						$totalpersentertimbangfisik+=$n['persen_tertimbang_fisik'];
					}
				}
			}
		}         
		$totalpersentarget=Helper::formatPersen($totaltarget,$totalpagu);                
		$totalpersenrealisasi=Helper::formatPersen($totalrealisasi,$totalpagu);            
		$totalpersentertimbangrealisasi=number_format(($totalpersenrealisasi*$totalpersenbobot)/100,2);
		$result=['totalpagu'=>$totalpagu,
				'totaltarget'=>$totaltarget,
				'totalrealisasi'=>$totalrealisasi,
				'totalfisik'=>$totalfisik,
				'totalpersenbobot'=>$totalpersenbobot,
				'totalpersentarget'=>$totalpersentarget,
				'totalpersenrealisasi'=>$totalpersenrealisasi,
				'totalpersentertimbangrealisasi'=>$totalpersentertimbangrealisasi,
				'totalpersentertimbangfisik'=>$totalpersentertimbangfisik,
				'totalbaris'=>$totalbaris];        
		return $result;
	}
}