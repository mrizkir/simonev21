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
				A.`OrgID`,
				A.kode_organisasi,
				A.`OrgNm`,
				A.`RealisasiFisik1` AS RealisasiFisik,
				A.`PersenRealisasiKeuangan1` AS PersenRealisasiKeuangan,
				C.`TA`
			';
		}
		else
		{
			$EntryLvl = 2;
			$sql = '                            
				A.`OrgID`,
				A.kode_organisasi,
				A.`OrgNm`,
				A.`RealisasiFisik2` AS RealisasiFisik,
				A.`PersenRealisasiKeuangan2` AS PersenRealisasiKeuangan,
				C.`TA`
			';
		}
		$subquery = \DB::table('statistik2')
		->select(\DB::raw('`OrgID`,MAX(`Bulan`) AS `Bulan`'))
		->where('TA', $tahun)
		->where('EntryLvl', $EntryLvl)
		->groupBy('OrgID');

		$peringkat=\DB::table('statistik2 AS A')
			->select(\DB::raw($sql))
			->joinSub($subquery,'B',function($join){
				$join->on('A.OrgID','=','B.OrgID');
				$join->on('A.Bulan','=','B.Bulan');
			})  
			->join('tmOrg AS C', 'A.OrgID', 'C.OrgID')
			->where('A.EntryLvl', $EntryLvl)
			->where('A.TA', $tahun)
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