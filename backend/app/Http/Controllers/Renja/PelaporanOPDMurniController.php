<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Statistik3Model;

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
		$OrgID = $request->input('OrgID');
		$data_opd = OrganisasiModel::find($OrgID);

		$data = Statistik3Model::where('OrgID', $OrgID)
			->get();    

		return Response()->json([
			'status'=>1,
			'pid'=>'fetchdata',									
			'data_opd'=>$data_opd,
			'laporanopd'=>$data,
			'message'=>'Fetch data pelaporan opd murni berhasil diperoleh'
		], 200);
	}
	/**
	 * Show the form for creating a new resource. [menambah realisasi uraian]
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function bulanpelaporan(Request $request,$id)
	{ 
		$this->hasPermissionTo('RENJA-PELAPORAN-OPD_BROWSE');

		$bulan=Helper::getNamaBulan();
		$bulan_realisasi = Statistik3Model::select('BulanLaporan')
			->where('OrgID', $id)
			->get()
			->pluck('BulanLaporan','BulanLaporan')
			->toArray();
		$data = [];
		foreach($bulan as $k=>$v)
		{
			if (!array_key_exists($k, $bulan_realisasi))
			{
				$data[$k]=['value'=>$k,'text'=>$v];
			}
		}
		return Response()->json([
			'status'=>1,
			'pid'=>'fetchdata',
			'bulan'=>$data,
			'message'=>'Fetch data bulan pelaporan berhasil diperoleh'
		], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
	}
	public function store(Request $request)
	{
		$this->hasPermissionTo('RENJA-PELAPORAN-OPD_STORE');

		$this->validate($request, [
			'BulanLaporan' => 'required',
			'BuktiCetak' => 'required|mimetypes:application/pdf',
		]);
		
		$bukti_laporan = $request->input('BulanLaporan');
		$bukti_cetak = $request->file('BuktiCetak');

		$mime_type=$foto->getMimeType();
		if ($mime_type=='image/png' || $mime_type=='image/jpeg')
		{
			Statistik3Model::create([				
				'Statistik3ID' => Uuid::uuid4()->toString(),
				'OrgID' => $request->input('OrgID'),
				'kode_organisasi' => $request->input('kode_organisasi'),
				'OrgNm' => $request->input('OrgNm'),
				'PaguDana' => $request->input('OrgNm'),
				'RealisasiKeuangan' => $request->input('OrgNm'),
				'RealisasiFisik' => $request->input('OrgNm'),
				'Kontrak' => $request->input('OrgNm'),
				'PekerjaanSelesai' => $request->input('OrgNm'),
				'PekerjaanBerjalan' => $request->input('OrgNm'),
				'PekerjaanTerhenti' => $request->input('OrgNm'),
				'PekerjaanBelumBerjalan' => $request->input('OrgNm'),
				'BulanLaporan' => $request->input('OrgNm'),
				'BuktiCetak' => $request->input('OrgNm'),
				'TA' => $request->input('OrgNm'),
				'isverified' => 0,
			]);
		}
		return Response()->json([
			'status'=>1,
			'pid'=>'store',
			'message'=>'Pelaporan OPD Renja Murni berhasil disimpan'
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
								], 422); 
		}
	}

}