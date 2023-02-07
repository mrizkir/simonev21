<?php

namespace App\Http\Controllers\Statistik;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Statistik2Model;
use App\Models\System\ConfigurationModel;

class PelaporanOPDController extends Controller {     
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function front(Request $request)
	{
		$this->validate($request, [            
			'tahun'=>'required',                     
		]);

		$tahun = $request->input('tahun');
		
		$config = ConfigurationModel::getCache();
		
		if ($config['DEFAULT_MASA_PELAPORAN'] == 'murni')
		{
			$EntryLvl = 1;
			$sql = '                            
				statistik2.`OrgID`,
				statistik2.kode_organisasi,
				statistik2.`OrgNm`,
				statistik2.`RealisasiFisik1` AS RealisasiFisik,
				statistik2.`PersenRealisasiKeuangan1` AS PersenRealisasiKeuangan
			';
		}
		else
		{
			$EntryLvl = 2;
			$sql = '                            
				statistik2.`OrgID`,
				statistik2.kode_organisasi,
				statistik2.`OrgNm`,
				statistik2.`RealisasiFisik2` AS RealisasiFisik,
				statistik2.`PersenRealisasiKeuangan2` AS PersenRealisasiKeuangan
			';
		}
		$subquery = \DB::table('statistik2')
		->select(\DB::raw('`OrgID`,MAX(`Bulan`) AS `Bulan`'))
		->where('TA', $tahun)
		->where('EntryLvl', $EntryLvl)
		->groupBy('OrgID');

		$peringkat=\DB::table('statistik2')
			->select(\DB::raw($sql))
			->joinSub($subquery,'B',function($join){
				$join->on('statistik2.OrgID','=','B.OrgID');
				$join->on('statistik2.Bulan','=','B.Bulan');
			})  
			->where('EntryLvl', $EntryLvl)
			->where('statistik2.TA', $tahun)
			->orderBy('RealisasiFisik','DESC')
			->orderBy('PersenRealisasiKeuangan','DESC')
			->get();
		
		return Response()->json([
			'status'=>1,
			'pid'=>'fetchdata',
			'peringkat'=>$peringkat,
			'message'=>'Fetch data untuk peringkat opd berhasil diperoleh'
		], 200);    
		
	}    
}