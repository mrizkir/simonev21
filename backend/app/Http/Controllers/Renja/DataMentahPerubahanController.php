<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\DMaster\SIPDModel;
use App\Models\Renja\RKAModel;
use App\Models\Renja\RKARencanaTargetModel;
use App\Models\Renja\RKARealisasiModel;

use Ramsey\Uuid\Uuid;

class DataMentahPerubahanController extends Controller 
{
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{             
		$this->hasPermissionTo('RENJA-RKA-MURNI_BROWSE');
		
		$this->validate($request, [            
			'tahun'=>'required',            
			'OrgID'=>'required|exists:tmOrg,OrgID',            
		]);
		$tahun = $request->input('tahun');
		$OrgID = $request->input('OrgID');		
	 
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
			keluaran1,
			tk_keluaran1,                            
			hasil1,                            
			tk_hasil1,                            
			capaian_program1,                            
			tk_capaian1,                            
			masukan1,                            
			ksk1,                            
			sifat_kegiatan1,                            
			waktu_pelaksanaan1,                            
			lokasi_kegiatan1,                            
			`PaguDana1`,                            
			`RealisasiKeuangan1`,                            
			`RealisasiFisik1`,   
			0 AS persen_keuangan1,
			nip_pa1,                            
			nip_kpa1,
			nip_ppk1,
			nip_pptk1,
			`Descr`,
			`TA`,
			`Locked`,
			\'BELUM DICOPY\' AS status,
			created_at,
			updated_at
		'))
		->where('OrgID',$OrgID)
		->where('TA',$tahun)
		->where('EntryLvl',1)
		->orderByRaw('kode_urusan="X" DESC')
		->orderBy('kode_bidang','ASC')
		->orderBy('kode_program','ASC')
		->orderBy('kode_kegiatan','ASC')
		->orderBy('kode_sub_kegiatan','ASC')
		->get();        
		
		$data->transform(function ($item, $key) {                            
			$item->persen_keuangan1=Helper::formatPersen($item->RealisasiKeuangan1,$item->PaguDana1);
			$item->status = $item->Locked == 1 ? 'SUDAH DICOPY' : 'BELUM DICOPY';
			return $item;
		});

		return Response()->json([
			'status'=>1,
			'pid'=>'fetchdata',								
			'rka'=>$data,
			'message'=>'Fetch data rka murni berhasil diperoleh'
		], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);              
	}   
	public function copyrka(Request $request)
	{
		$this->validate($request, [             
			'RKAID'=>'required|exists:trRKA,RKAID,Locked,0',            			
		]);		

		\DB::transaction(function () use ($request) {
			$RKAID = $request->input('RKAID');	
			$user_id = $this->getUserid();

			$rka = RKAModel::find($RKAID);

			$new_rka = $rka->replicate();
			$new_rka->RKAID = Uuid::uuid4()->toString();
			$new_rka->keluaran2 = $rka->keluaran1;
			$new_rka->tk_keluaran2 = $rka->tk_keluaran1;
			$new_rka->hasil2 = $rka->hasil1;
			$new_rka->tk_hasil2 = $rka->tk_hasil1;
			$new_rka->capaian_program2 = $rka->capaian_program1;
			$new_rka->tk_capaian2 = $rka->tk_capaian1;
			$new_rka->masukan2 = $rka->masukan1;
			$new_rka->ksk2 = $rka->ksk1;
			$new_rka->sifat_kegiatan2 = $rka->sifat_kegiatan1;
			$new_rka->waktu_pelaksanaan2 = $rka->waktu_pelaksanaan1;
			$new_rka->lokasi_kegiatan2 = $rka->lokasi_kegiatan1;
			$new_rka->PaguDana2 = $rka->PaguDana1;
			$new_rka->RealisasiKeuangan2 = $rka->RealisasiKeuangan1;
			$new_rka->RealisasiFisik2 = $rka->RealisasiFisik1;
			$new_rka->nip_pa2 = $rka->nip_pa1;
			$new_rka->nip_kpa2 = $rka->nip_kpa1;
			$new_rka->nip_ppk2 = $rka->nip_kpa1;
			$new_rka->nip_pptk2 = $rka->nip_pptk1;
			$new_rka->nip_pptk2 = $rka->nip_pptk1;
			$new_rka->user_id = $user_id;
			$new_rka->EntryLvl = 2;
			$new_rka->RKAID_Src = $rka->RKAID;
			$new_rka->created_at = \Carbon\Carbon::now();
			$new_rka->updated_at = \Carbon\Carbon::now();
			$new_rka->save();

			$rka->Locked=true;
			$rka->save();

			$sql_uraian = "INSERT INTO trRKARinc (
				RKARincID,
        RKAID,
        SIPDID,
        JenisPelaksanaanID,
        SumberDanaID,
        JenisPembangunanID,            
        kode_uraian1,            
        kode_uraian2,            
        NamaUraian1,            
        NamaUraian2,            
        volume1,
        volume2,
        satuan1,            
        satuan2,            
        harga_satuan1,
        harga_satuan2,
        PaguUraian1,
        PaguUraian2,
        idlok,
        ket_lok,
        rw,
        rt,
        nama_perusahaan,
        alamat_perusahaan,
        no_telepon,
        nama_direktur,
        npwp,
        no_kontrak,
        tgl_kontrak,
        tgl_mulai_pelaksanaan,
        tgl_selesai_pelaksanaan,
        status_lelang,
        EntryLvl,
        Descr,            
        TA,
        Locked,
        RKARincID_Src,
				created_at,
				updated_at
			) SELECT 
				UUID() AS RKARincID,
        '{$new_rka->RKAID}' AS RKAID,
        NULL AS SIPDID,
        JenisPelaksanaanID,
        SumberDanaID,
        JenisPembangunanID,            
        kode_uraian1,            
        kode_uraian1 AS kode_uraian2,            
        NamaUraian1,            
        NamaUraian1 AS NamaUraian2,            
        volume1,
        volume1 AS volume2,
        satuan1,            
        satuan1 AS satuan2,            
        harga_satuan1,
        harga_satuan1 AS harga_satuan2,
        PaguUraian1,
        PaguUraian1 AS PaguUraian2,
        idlok,
        ket_lok,
        rw,
        rt,
        nama_perusahaan,
        alamat_perusahaan,
        no_telepon,
        nama_direktur,
        npwp,
        no_kontrak,
        tgl_kontrak,
        tgl_mulai_pelaksanaan,
        tgl_selesai_pelaksanaan,
        status_lelang,
        2 AS EntryLvl,
        Descr,            
        TA,
        Locked,
        RKARincID AS RKARincID_Src,
				NOW(),
				NOW()
			FROM trRKARinc 
			WHERE RKAID='$RKAID' AND Locked=0";

			\DB::statement($sql_uraian);

			\DB::table('trRKARinc')
				->where('RKAID', $RKAID)
				->update([
					'Locked'=>true
				]);
				
			$sql_target = "INSERT INTO trRKATargetRinc (
					RKATargetRincID, 
					RKAID, 
					RKARincID, 
					bulan1, 
					bulan2, 
					target1,         
					target2,                
					fisik1,
					fisik2,    
					EntryLvl, 
					Descr, 
					TA, 
					Locked,
					RKATargetRincID_Src,
					created_at,
					updated_at
				) SELECT 
					UUID() AS RKATargetRincID,
					'{$new_rka->RKAID}' AS RKAID,
					A.RKARincID, 
					B.bulan1, 
					B.bulan1 AS bulan2, 
					B.target1,         
					B.target1 AS target2,                
					B.fisik1,         
					B.fisik1 AS fisik2,    
					2 AS EntryLvl, 
					B.Descr, 
					B.TA, 
					B.Locked,
					B.RKATargetRincID AS RKATargetRincID_Src,
					NOW(),
					NOW()
				FROM trRKARinc A
				JOIN trRKATargetRinc B ON (A.RKARincID_Src=B.RKARincID)
				WHERE A.RKAID='{$new_rka->RKAID}' AND B.Locked=0
			";
			
			\DB::statement($sql_target);			
			
			\DB::table('trRKATargetRinc')
				->where('RKAID', $RKAID)
				->update([
					'Locked'=>true
				]);

			$sql_realisasi = "INSERT INTO trRKARealisasiRinc (
					RKARealisasiRincID, 
					RKAID, 
					RKARincID, 
					bulan1, 
					bulan2, 
					target1, 
					target2,         
					realisasi1,  
					realisasi2,         
					target_fisik1,         
					target_fisik2,         
					fisik1,         
					fisik2,         
					EntryLvl,         
					Descr,         
					TA,         
					Locked,  
					RKARealisasiRincID_Src,
					created_at,
					updated_at
				) SELECT 
					UUID() AS RKATargetRincID,
					'{$new_rka->RKAID}' AS RKAID,
					A.RKARincID, 
					B.bulan1, 
					B.bulan1 AS bulan2, 
					B.target1, 
					B.target1 AS target2,         
					B.realisasi1,  
					B.realisasi1 AS realisasi2,         
					B.target_fisik1,         
					B.target_fisik1 AS target_fisik2,         
					B.fisik1,         
					B.fisik1 AS fisik2,         
					2 AS EntryLvl,         
					B.Descr,         
					B.TA,         
					B.Locked,  
					B.RKARealisasiRincID AS RKARealisasiRincID_Src,
					NOW(),
					NOW()
				FROM trRKARinc A
				JOIN trRKARealisasiRinc B ON (A.RKARincID_Src=B.RKARincID)
				WHERE A.RKAID='{$new_rka->RKAID}' AND B.Locked=0
			";
			\DB::statement($sql_realisasi);

			\DB::table('trRKARealisasiRinc')
				->where('RKAID', $RKAID)
				->update([
					'Locked'=>true
				]);

		});

		return Response()->json([
								'status'=>1,
								'pid'=>'store',                                
								'message'=>'Salin kegiatan dari RKA Murni ke RKA Perubahan berhasil'
							], 200);    
	}
}