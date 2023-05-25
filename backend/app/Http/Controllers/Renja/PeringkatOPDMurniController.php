<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Statistik2Model;

class PeringkatOPDMurniController extends Controller {     
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$this->hasPermissionTo('RENJA-STATISTIK-PERINGKAT-OPD_BROWSE');
		
		$this->validate($request, [            
			'tahun'=>'required',                     
		]);

		$tahun=$request->input('tahun');
		
		$subquery = \DB::table('statistik2')
			->select(\DB::raw('`OrgID`,MAX(`Bulan`) AS `Bulan`'))
			->where('TA', $tahun)
			->where('EntryLvl', 1)
			->groupBy('OrgID');

		$data=\DB::table('statistik2 AS A')
			->select(\DB::raw('                            
				A.`OrgID`,
				A.kode_organisasi,
				A.`OrgNm`,
				A.`RealisasiFisik1`,
				A.`PersenRealisasiKeuangan1`,
				C.`TA`
			'))
			->joinSub($subquery,'B',function($join){
				$join->on('A.OrgID','=','B.OrgID');
				$join->on('A.Bulan','=','B.Bulan');
			})  
			->join('tmOrg AS C', 'A.OrgID', 'C.OrgID')
			->where('A.EntryLvl', 1)
			->where('A.TA', $tahun)
			->orderBy('A.RealisasiFisik1','DESC')
			->orderBy('A.PersenRealisasiKeuangan1','DESC')
			->get();		
	
		$peringkat_temp = [];		
		foreach ($data as $v)
		{
			if (!isset($peringkat_temp[$v->kode_organisasi]))
			{
				$peringkat_temp[$v->kode_organisasi] = $v;
			}					
		}
		$k=0;
		$peringkat = [];
		foreach ($peringkat_temp as $v)
		{
			$peringkat[$k] = $v;
			$k+=1;
		}		
		return Response()->json([
			'status'=>1,
			'pid'=>'fetchdata',
			'peringkat'=>$peringkat,
			'message'=>'Fetch data untuk peringkat opd berhasil diperoleh'
		], 200);    
		
	}    
}