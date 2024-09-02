<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\KodefikasiBidangUrusanModel;
use App\Models\DMaster\KodefikasiProgramModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class KodefikasiBidangUrusanController extends Controller {              
	/**
	 * get all kodefikasi urusan
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index (Request $request)
	{
		$this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_BROWSE');

		$this->validate($request, [        
			'TA' => 'required'
		]);    
		$ta = $request->input('TA');
		$kodefikasibidangurusan=KodefikasiBidangUrusanModel::select(\DB::raw("
			`BidangID`,
			`tmBidangUrusan`.`UrsID`,
			`Kd_Bidang`,
			`Nm_Bidang`,
			CONCAT(`tmUrusan`.`Kd_Urusan`,'.',`tmBidangUrusan`.`Kd_Bidang`) AS `kode_bidang`,
			CONCAT('[',`tmUrusan`.`Kd_Urusan`,'.',`tmBidangUrusan`.`Kd_Bidang`,'] ',`Nm_Bidang`) AS `bidangurusan`,
			`tmBidangUrusan`.`Descr`,
			`tmBidangUrusan`.`TA`,
			`tmBidangUrusan`.`created_at`,
			`tmBidangUrusan`.`updated_at`
		"))
		->join('tmUrusan','tmBidangUrusan.UrsID','tmUrusan.UrsID')
		->orderBy('kode_bidang','ASC')                                    
		->where('tmBidangUrusan.TA', $ta)
		->get();

		return Response()->json([
									'status' => 1,
									'pid' => 'fetchdata',
									'kodefikasibidangurusan'=>$kodefikasibidangurusan,
									'message' => 'Fetch data kodefikasi urusan berhasil.'
								], 200);
	}
	/**
	 * get all kodefikasi program
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function program (Request $request,$id)
	{
		$this->hasPermissionTo('DMASTER-KODEFIKASI-PROGRAM_BROWSE');

		$this->validate($request, [        
			'TA' => 'required',            
		]);        
		if ($id == 'all')
		{
			$ta = $request->input('TA');
			$program = KodefikasiProgramModel::select(\DB::raw('
								`PrgID`,
								CONCAT("[X.XX.",`Kd_Program`,"] ",`Nm_Program`) AS nama_program	
							'))
							->where('TA', $ta)
							->where('Jns',0)
							->orderBy('Kd_Program','ASC')                                    
							->get();
		}
		else
		{
			$program=KodefikasiProgramModel::select(\DB::raw("
					tmProgram.`PrgID`,
					CASE 
						WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
						CONCAT('[',tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`,'] ',tmProgram.Nm_Program)
						ELSE
						CONCAT('[X.','XX.',tmProgram.`Kd_Program`,'] ',tmProgram.Nm_Program)
					END AS nama_program
				"))
				->leftJoin('tmUrusanProgram','tmProgram.PrgID','tmUrusanProgram.PrgID')
				->leftJoin('tmBidangUrusan','tmBidangUrusan.BidangID','tmUrusanProgram.BidangID')
				->leftJoin('tmUrusan','tmBidangUrusan.UrsID','tmUrusan.UrsID')
				->orderBy('tmUrusan.Kd_Urusan','ASC')                                    
				->orderBy('tmBidangUrusan.Kd_Bidang','ASC')                                    
				->orderBy('tmProgram.Kd_Program','ASC')                                    
				->where('tmBidangUrusan.BidangID', $id)
				->get();
		}
		return Response()->json([
			'status' => 1,
			'pid' => 'fetchdata',
			'program'=>$program,
			'message' => 'Fetch data kodefikasi program rka berhasil.'
		], 200);
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function salin(Request $request)
	{       
		$this->validate($request, [            
			'tahun_asal' => 'required|numeric',
			'tahun_tujuan' => 'required|numeric|gt:tahun_asal',
		]);

		$tahun_asal = $request->input('tahun_asal');
		$tahun_tujuan = $request->input('tahun_tujuan');

		\DB::table('tmBidangUrusan')
		->where('TA', $tahun_tujuan)
		->whereRaw('BidangID_Src IS NOT NULL')
		->delete();

		$str_insert = '
			INSERT INTO `tmBidangUrusan` (
				`BidangID`,
				`UrsID`,
				`Kd_Bidang`,
				`Nm_Bidang`,
				`Descr`,
				`TA`,
				`BidangID_Src`,			
				created_at,
				updated_at
			)		
			SELECT
				uuid() AS id,
				t2.UrsID,
				t1.Kd_Bidang,
				t1.Nm_Bidang,
				"DI IMPOR DARI TAHUN '.$tahun_asal.'" AS `Descr`,
				'.$tahun_tujuan.' AS `TA`,
				t1.BidangID AS BidangID_Src,
				NOW() AS created_at,
				NOW() AS updated_at
			FROM tmBidangUrusan t1
			JOIN tmUrusan t2 ON (t1.UrsID=t2.UrsID_Src)
			WHERE t1.TA='.$tahun_asal.'        
		';    

		\DB::statement($str_insert); 

		return Response()->json([
			'status' => 1,
			'pid' => 'store',            
			'message'=>"Salin bidang urusan dari tahun anggaran $tahun_asal berhasil."
		], 200);
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{       
		$this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_STORE');

		$this->validate($request, [
			'Kd_Bidang'=> [
						Rule::unique('tmBidangUrusan')->where(function($query) use ($request) {
							return $query->where('UrsID', $request->input('UrsID'))
										->where('TA', $request->input('TA'));
						}),
						'required',
						'regex:/^[0-9]+$/'],
			'Nm_Bidang' => 'required',
			'TA' => 'required'
		]);     
			
		$ta = $request->input('TA');
		
		$kodefikasibidangurusan = KodefikasiBidangUrusanModel::create([
			'BidangID' => Uuid::uuid4()->toString(),
			'UrsID' => $request->input('UrsID'),            
			'Kd_Bidang' => $request->input('Kd_Bidang'),
			'Nm_Bidang' => strtoupper($request->input('Nm_Bidang')),
			'Descr' => $request->input('Descr'),
			'TA'=>$ta,
		]);

		return Response()->json([
									'status' => 1,
									'pid' => 'store',
									'kodefikasibidangurusan'=>$kodefikasibidangurusan,                                    
									'message' => 'Data Kodefikasi Bidang Urusan berhasil disimpan.'
								], 200); 
	}               
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request,$id)
	{        
		$this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_UPDATE');

		$kodefikasibidangurusan = KodefikasiBidangUrusanModel::find($id);
		
		if (is_null($kodefikasibidangurusan))
		{
			return Response()->json([
									'status'=>0,
									'pid' => 'update',                
									'message'=>["Data Kodefikasi Bidang Urusan ($id) gagal diupdate"]
								], 422); 
		}
		else
		{
			$this->validate($request, [    
										'Kd_Bidang'=>[
													Rule::unique('tmBidangUrusan')->where(function($query) use ($request,$kodefikasibidangurusan) {  
														if ($request->input('Kd_Bidang')= = $kodefikasibidangurusan->Kd_Bidang) 
														{
															return $query->where('Kd_Bidang','ignore')
																		->where('TA', $kodefikasibidangurusan->TA);
														}                 
														else
														{
															return $query->where('Kd_Bidang', $request->input('Kd_Bidang'))
																	->where('UrsID', $kodefikasibidangurusan->UrsID)
																	->where('TA', $kodefikasibidangurusan->TA);
														}                                                                                    
													}),
													'required',
													'regex:/^[0-9]+$/'
												],
										'Nm_Bidang' => 'required',
									]);
			
			
			$kodefikasibidangurusan->Kd_Bidang = $request->input('Kd_Bidang');
			$kodefikasibidangurusan->Nm_Bidang = strtoupper($request->input('Nm_Bidang'));
			$kodefikasibidangurusan->Descr = $request->input('Descr');
			$kodefikasibidangurusan->save();

			return Response()->json([
									'status' => 1,
									'pid' => 'update',
									'kodefikasibidangurusan'=>$kodefikasibidangurusan,                                    
									'message' => 'Data Kodefikasi Bidang Urusan '.$kodefikasibidangurusan->Nm_Bidang.' berhasil diubah.'
								], 200);
		}
		
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $uuid
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request,$id)
	{   
		$this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_DESTROY');

		$kodefikasibidangurusan = KodefikasiBidangUrusanModel::find($id);

		if (is_null($kodefikasibidangurusan))
		{
			return Response()->json([
									'status'=>0,
									'pid' => 'destroy',                
									'message'=>["Data Kodefikasi Bidang Urusan ($id) gagal dihapus"]
								], 422); 
		}
		else
		{
			
			$result = $kodefikasibidangurusan->delete();

			return Response()->json([
									'status' => 1,
									'pid' => 'destroy',                
									'message'=>"Data Kodefikasi Bidang Urusan dengan ID ($id) berhasil dihapus"
								], 200);
		}
	}
}