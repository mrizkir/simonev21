<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Exception;
use Storage;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Helpers\HelperKegiatan;
use App\Models\DMaster\SubOrganisasiModel;
use App\Models\DMaster\SIPDModel;
use App\Models\DMaster\KodefikasiSubKegiatanModel;
use App\Models\Renja\RKAModel;
use App\Models\Renja\RKARincianModel;
use App\Models\Renja\RKARencanaTargetModel;
use App\Models\Renja\RKARealisasiModel;
use App\Models\Media\MediaLibraryModel;

class GalleryController extends Controller 
{
	/**
	 * digunakan untuk mentrigger fungsi validator exception
	*/
	public function validatorException($validator)
	{
		if ($validator->stopOnFirstFailure()->fails())
		{				
			$errors = $validator->errors();				
			foreach ($errors->all() as $k=>$message) {					
				throw new Exception($message);
			}				
		}	
	}
	/**
   * digunakan untuk mengupload gambar realisasi rincian
   * @param $RKARealisasiRincID
   * @param $media berisi $request->file
   */
  private function storeMediaRealisasiRincian($RKARealisasiRincID, $media, $collection='kegiatan', $name=null)
  {
    $rincian_realisasi = RKARealisasiModel::select(\DB::raw('
			trRKARealisasiRinc.RKARealisasiRincID_Src,		
			trRKARealisasiRinc.RKARealisasiRincID,		
			trRKARealisasiRinc.RKARincID,		
			trRKARealisasiRinc.RKAID,		
			B.kode_urusan,
			B.Nm_Bidang,
			B.kode_organisasi,
			B.Nm_Organisasi,
			B.kode_sub_organisasi,
			B.Nm_Sub_Organisasi,
			B.kode_program,
			B.Nm_Program,
			B.kode_kegiatan,
			B.Nm_Kegiatan,
			B.kode_sub_kegiatan,
			B.Nm_Sub_Kegiatan,						
			B.PaguDana1,
			B.PaguDana2,
			C.kode_uraian1,
			C.kode_uraian2,
			C.NamaUraian1,
			C.NamaUraian2,
			C.volume1,
			C.volume2,
			C.satuan1,			
			C.satuan2,			
			C.harga_satuan1,
			C.harga_satuan2,
			C.PaguUraian1,
			C.PaguUraian2,
			trRKARealisasiRinc.target1,		
			trRKARealisasiRinc.target2,					
			trRKARealisasiRinc.target_fisik1,					
			trRKARealisasiRinc.target_fisik2,		
			trRKARealisasiRinc.realisasi1,					
			trRKARealisasiRinc.realisasi2,		
			trRKARealisasiRinc.fisik1,					
			trRKARealisasiRinc.fisik2,					
			trRKARealisasiRinc.bulan1,					
			trRKARealisasiRinc.bulan2,					
			trRKARealisasiRinc.Locked,					
			trRKARealisasiRinc.Descr,					
			trRKARealisasiRinc.TA,					
			trRKARealisasiRinc.EntryLvl,					
			trRKARealisasiRinc.created_at,					
			trRKARealisasiRinc.updated_at					
		'))
		->join('trRKA AS B', 'trRKARealisasiRinc.RKAID', 'B.RKAID')
		->join('trRKARinc AS C', 'trRKARealisasiRinc.RKARincID', 'C.RKARincID')
		->leftJoin('tmSumberDana AS D','D.SumberDanaID','B.SumberDanaID')		
		->find($RKARealisasiRincID);		

    if (is_null($name))
    {
      $name = $media->getClientOriginalName();    
    }
    $custom_properties['RKARealisasiRincID_Src'] = $rincian_realisasi->RKARealisasiRincID_Src;
    $custom_properties['RKARealisasiRincID'] = $rincian_realisasi->RKARealisasiRincID;
    $custom_properties['RKARincID'] = $rincian_realisasi->RKARincID;
    $custom_properties['RKAID'] = $rincian_realisasi->RKAID;
    $custom_properties['kode_urusan'] = $rincian_realisasi->kode_urusan;
    $custom_properties['Nm_Bidang'] = $rincian_realisasi->Nm_Bidang;
    $custom_properties['kode_organisasi'] = $rincian_realisasi->kode_organisasi;
    $custom_properties['Nm_Organisasi'] = $rincian_realisasi->Nm_Organisasi;
    $custom_properties['kode_sub_organisasi'] = $rincian_realisasi->kode_sub_organisasi;
    $custom_properties['Nm_Sub_Organisasi'] = $rincian_realisasi->Nm_Sub_Organisasi;
    $custom_properties['kode_program'] = $rincian_realisasi->kode_program;
    $custom_properties['Nm_Program'] = $rincian_realisasi->Nm_Program;
    $custom_properties['kode_kegiatan'] = $rincian_realisasi->kode_kegiatan;
    $custom_properties['Nm_Kegiatan'] = $rincian_realisasi->Nm_Kegiatan;
    $custom_properties['kode_sub_kegiatan'] = $rincian_realisasi->kode_sub_kegiatan;
    $custom_properties['Nm_Sub_Kegiatan'] = $rincian_realisasi->Nm_Sub_Kegiatan;

		switch($rincian_realisasi->EntryLvl == 1)
		{
			case 1:
				$pagu_dana = $rincian_realisasi->PaguDana1;
				$kode_uraian = $rincian_realisasi->kode_uraian1;
				$nama_uraian = $rincian_realisasi->NamaUraian1;
				$volume = $rincian_realisasi->volume1;
				$satuan = $rincian_realisasi->satuan1;
				$harga_satuan = $rincian_realisasi->harga_satuan1;
				$pagu_uraian = $rincian_realisasi->PaguUraian1;
				$bulan = $rincian_realisasi->bulan1;
				$target_keuangan = $rincian_realisasi->target1;
				$target_fisik = $rincian_realisasi->target_fisik1;
				$realisasi_keuangan = $rincian_realisasi->realisasi1;
				$realisasi_fisik = $rincian_realisasi->fisik1;
			break;
			case 2:
				$pagu_dana = $rincian_realisasi->PaguDana2;
				$kode_uraian = $rincian_realisasi->kode_uraian2;
				$nama_uraian = $rincian_realisasi->NamaUraian2;
				$volume = $rincian_realisasi->volume2;
				$satuan = $rincian_realisasi->satuan2;
				$harga_satuan = $rincian_realisasi->harga_satuan2;
				$pagu_uraian = $rincian_realisasi->PaguUraian2;
				$bulan = $rincian_realisasi->bulan2;
				$target_keuangan = $rincian_realisasi->target2;
				$target_fisik = $rincian_realisasi->target_fisik2;
				$realisasi_keuangan = $rincian_realisasi->realisasi2;
				$realisasi_fisik = $rincian_realisasi->fisik2;
			break;
			default:
				$pagu_dana = 0;
				$kode_uraian = '';
				$nama_uraian = '';
				$volume = 0;
				$satuan = '';
				$harga_satuan = '';
				$pagu_uraian = 0;
				$bulan = -1;
				$target_fisik = -1;
				$target_keuangan = -1;
				$realisasi_fisik = -1;
				$realisasi_keuangan = -1;
		}    
    $custom_properties['pagu_dana'] = $pagu_dana;
    $custom_properties['kode_uraian'] = $kode_uraian;
    $custom_properties['nama_uraian'] = $nama_uraian;
    $custom_properties['volume'] = $volume;
    $custom_properties['satuan'] = $satuan;
    $custom_properties['harga_satuan'] = $harga_satuan;
    $custom_properties['pagu_uraian'] = $pagu_uraian;
    $custom_properties['target_keuangan'] = $target_keuangan;
    $custom_properties['target_fisik'] = $target_fisik;
    $custom_properties['realisasi_keuangan'] = $realisasi_keuangan;
    $custom_properties['realisasi_fisik'] = $realisasi_fisik;
    $custom_properties['bulan'] = $bulan;
    $custom_properties['Locked'] = $rincian_realisasi->Locked;
    $custom_properties['Descr'] = $rincian_realisasi->Descr;
    $custom_properties['EntryLvl'] = $rincian_realisasi->EntryLvl;
    $custom_properties['TA'] = $rincian_realisasi->TA;
    $custom_properties['created_at'] = Helper::tanggal('Y-m-d H:i:s', $rincian_realisasi->created_at);
    $custom_properties['updated_at'] = Helper::tanggal('Y-m-d H:i:s', $rincian_realisasi->updated_at);

    $result = $rincian_realisasi->addMedia($media)
			->usingName($name)
      ->usingFileName($media->hashName())
      ->withCustomProperties($custom_properties)
			->toMediaCollection($collection);

    return $result;
  }
	private function getDaftarBulan($RKARincID)
	{
		$bulan=Helper::getNamaBulan();
		
		$bulan_realisasi=RKARealisasiModel::select(\DB::raw('
			RKARealisasiRincID,
			bulan1
		'))
		->where('RKARincID', $RKARincID)
		->get()
		->pluck('bulan1', 'RKARealisasiRincID')
		->toArray();
		
		$daftar_bulan = [];
		foreach($bulan_realisasi as $k=>$v)
		{
			$daftar_bulan[]=['value'=>$k,'text'=>$bulan[$v]];			
		}
		
		return $daftar_bulan;
	}	
	public function index(Request $request)
	{
		try
		{
			$validator = Validator::make($request->all(), [        
				'pid'=>'required|in:gallery,rincian'
			]);     
			$this->validatorException($validator);
			
			$daftar_bulan = [];
			$paginate = [];
			
			switch($request->input('pid'))
			{
				case 'gallery':
					$validator = Validator::make($request->all(), [        
						'TA'=>'required|digits:4|integer|min:2020|max:'. (date('Y')),
					]);     
					$this->validatorException($validator);

					$TA = $request->input('TA');
					if ($this->hasRole('opd'))
					{
						$daftar_opd=$this->getUserOrgID($TA);

						$result = RKARealisasiModel::select(\DB::raw('
							trRKARealisasiRinc.RKARealisasiRincID
						'))
						->join('media', 'media.model_id', 'trRKARealisasiRinc.RKARealisasiRincID')
						->join('trRKA', 'trRKARealisasiRinc.RKAID', 'trRKA.RKAID')
						->whereIn('trRKA.OrgID', $daftar_opd)
						->where('trRKARealisasiRinc.TA', $TA)
						->paginate(10);
					}
					else if($this->hasRole('unitkerja'))
					{
						$daftar_unit = $this->getUserSOrgID();
						$result = RKARealisasiModel::select(\DB::raw('
							trRKARealisasiRinc.RKARealisasiRincID
						'))
						->join('media', 'media.model_id', 'trRKARealisasiRinc.RKARealisasiRincID')
						->join('trRKA', 'trRKARealisasiRinc.RKAID', 'trRKA.RKAID')
						->whereIn('trRKA.SOrgID', $daftar_unit)
						->where('trRKARealisasiRinc.TA', $TA)
						->paginate(10);
					}
					else
					{
						$result = RKARealisasiModel::select(\DB::raw('
							trRKARealisasiRinc.RKARealisasiRincID
						'))
						->join('media', 'media.model_id', 'trRKARealisasiRinc.RKARealisasiRincID')
						->where('trRKARealisasiRinc.TA', $TA)
						->paginate(10);
					}

					$daftar_media = [];
					foreach ($result as $item)
					{
						$list_media = $item->getMedia('kegiatan');	
						if (count($list_media) > 0)					
						{
							foreach($list_media as $media)
							{
								$daftar_media[] = [
									'id' => $media->id,
									'RKARealisasiRincID' => $media->model_id,
									'publicFullUrl' => $media->getFullUrl(),
									'kode_uraian' => $media->getCustomProperty('kode_uraian'),
									'nama_uraian' => $media->getCustomProperty('nama_uraian'),
									'pagu_uraian' => $media->getCustomProperty('pagu_uraian'),
									'target_fisik' => $media->getCustomProperty('target_fisik'),
									'realisasi_fisik' => $media->getCustomProperty('realisasi_fisik'),
									'target_keuangan' => $media->getCustomProperty('target_keuangan'),
									'realisasi_keuangan' => $media->getCustomProperty('realisasi_keuangan'),
									'bulan' => Helper::getNamaBulan($media->getCustomProperty('bulan')),
									'TA' => $media->getCustomProperty('TA'),
								];
							}
						}
						else
						{
							continue;
						}						
					}					
					$message = 'Daftar media foto / video realisasi berhasil diperoleh';
				break;
				case 'rincian':
					$this->hasPermissionTo('RENJA-RKA-MURNI_BROWSE');
					
					$validator = Validator::make($request->all(), [
						'RKARincID' => 'required|exists:trRKARinc,RKARincID',
					]);
					$this->validatorException($validator);

					$result = RKARealisasiModel::select(\DB::raw('
						RKARealisasiRincID
					'))										
					->where('RKARincID', $request->input('RKARincID'))
					->get();					
					
					$daftar_media = [];					
					foreach ($result as $item)
					{
						$list_media = $item->getMedia('kegiatan');	
						if (count($list_media) > 0)					
						{
							foreach($list_media as $media)
							{							
								$daftar_media[] = [
									'id' => $media->id,
									'RKARealisasiRincID' => $media->model_id,
									'publicFullUrl' => $media->getFullUrl(),
									'kode_uraian' => $media->getCustomProperty('kode_uraian'),
									'nama_uraian' => $media->getCustomProperty('nama_uraian'),
									'pagu_uraian' => $media->getCustomProperty('pagu_uraian'),
									'target_fisik' => $media->getCustomProperty('target_fisik'),
									'realisasi_fisik' => $media->getCustomProperty('realisasi_fisik'),
									'target_keuangan' => $media->getCustomProperty('target_keuangan'),
									'realisasi_keuangan' => $media->getCustomProperty('realisasi_keuangan'),
									'bulan' => Helper::getNamaBulan($media->getCustomProperty('bulan')),
									'TA' => $media->getCustomProperty('TA'),
								];
							}
						}
						else
						{
							continue;
						}						
					}

					$daftar_bulan = $this->getDaftarBulan($request->input('RKARincID'));
					$message = 'Daftar media realisasi rincian berhasil diperoleh';
				break;
			}			

			return Response()->json([
				'status'=>1,
				'pid'=>'fetchdata',
				'daftar_bulan'=>$daftar_bulan,
				'media'=>$daftar_media,
				'paginate'=>$paginate,
				'message'=>$message,
			], 200); 
		}
		catch(Exception $e)
		{
			return Response()->json([
				'status'=>0,
				'pid'=>'fetchdata',
				'media'=>[],                                    
				'daftar_bulan'=>[],                                    
				'message'=>$e->getMessage()
			], 422); 			
		}
	}
  public function store(Request $request)
  {
    try
		{
			$this->hasPermissionTo('RENJA-RKA-MURNI_STORE');

			$validator = Validator::make($request->all(), [
        'RKARealisasiRincID' => 'required|exists:trRKARealisasiRinc,RKARealisasiRincID',
				'foto'=>'required|image',	
				'pid'=>'required|in:realisasirincian'
			]);     
			$this->validatorException($validator);

			switch($request->input('pid'))
			{
				case 'realisasirincian':
					$result = $this->storeMediaRealisasiRincian($request->input('RKARealisasiRincID'), $request->file('foto'));
				break;
			}      

      return Response()->json([
				'status'=>1,
				'pid'=>'store',
				'media'=>$result,                                    
				'message'=>'Media realisasi rincian berhasil disimpan'
			], 200); 			
    }
    catch(Exception $e)
		{
			return Response()->json([
				'status'=>0,
				'pid'=>'store',
				'media'=>[],                                    
				'message'=>$e->getMessage()
			], 422); 			
		}
  }
	public function destroy(Request $request, $id)
  {
    try
		{
			$this->hasPermissionTo('RENJA-RKA-MURNI_DESTROY');

			$validator = Validator::make($request->all(), [
        'RKARealisasiRincID' => 'required|exists:trRKARealisasiRinc,RKARealisasiRincID',					
				'pid'=>'required|in:realisasirincian'
			]);     
			$this->validatorException($validator);
			
			//check record ini milik siapa? untuk user yang memiliki role opd atau unitkerja 			
			if ($this->hasRole('opd'))
			{
				$data_realisasi = RKARealisasiModel::find($request->input('RKARealisasiRincID'));
				$daftar_opd=$this->getUserOrgID($data_realisasi->TA);

				$jumlah = \DB::table('trRKARealisasiRinc AS A')
				->join('trRKA AS B', 'A.RKAID', 'B.RKAID')
				->whereIn('B.OrgID', $daftar_opd)
				->where('A.RKARealisasiRincID', $data_realisasi->RKARealisasiRincID)
				->count();

				if ($jumlah <= 0)
				{
					throw new Exception ("Tidak bisa menghapus foto ini karena bukan milik OPD.");
				}
			}
			else if($this->hasRole('unitkerja'))
			{
				$daftar_unit = $this->getUserSOrgID();
				$realisasi = \DB::table('trRKARealisasiRinc AS A')
				->join('trRKA AS B', 'A.RKAID', 'B.RKAID')
				->where('B.SOrgID', $daftar_unit)
				->where('A.RKARealisasiRincID', $request->input('RKARealisasiRincID'))
				->count();

				if ($jumlah <= 0)
				{
					throw new Exception ("Tidak bisa menghapus foto ini karena bukan milik Unit Kerja.");
				}
			}
			switch($request->input('pid'))
			{
				case 'realisasirincian':
					$RKARealisasiRincID = $request->input('RKARealisasiRincID');
					$jumlah_terhapus = HelperKegiatan::destroyMediaRealisasiRincian($RKARealisasiRincID, $id);					
				break;
			}      

      return Response()->json([
				'status'=>1,
				'pid'=>'destroy',				                               
				'message'=>'Media realisasi rincian berhasil dihapus'
			], 200); 			
    }
    catch(Exception $e)
		{
			return Response()->json([
				'status'=>0,
				'pid'=>'destroy',				                               
				'message'=>$e->getMessage()
			], 422); 			
		}
  }
}
