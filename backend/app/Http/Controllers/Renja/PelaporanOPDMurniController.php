<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Belanja\RKAModel;

use Ramsey\Uuid\Uuid;

class PelaporanOPDMurniController extends Controller 
{
	 /**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{             
		$this->hasPermissionTo('RENJA-PELAPORAN-OPD_BROWSE');

		$this->validate($request, [
      'tahun'=>'required|numeric',
			'OrgID'=>'required|exists:tmOrg,OrgID',            
		]);
		$data = [];
    $data_opd = OrganisasiModel::find($request->input('OrgID'));

		return Response()->json([
									'status'=>1,
									'pid'=>'fetchdata',									
                  'data_opd'=>$data_opd,
                  'laporanopd'=>$data,
									'message'=>'Fetch data pelaporan opd murni berhasil diperoleh'
								], 200);    
		
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