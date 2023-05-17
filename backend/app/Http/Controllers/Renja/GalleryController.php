<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Exception;

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
   * digunakan untuk mengupload gambar realisasi rincian
   * @param $RKARealisasiRincID
   * @param $media berisi $request->file
   */
  public function storeMediaRealisasiRincian($RKARealisasiRincID, $media, $collection='kegiatan', $name=null)
  {
    $rincian_realisasi = \App\Models\Renja\RKARealisasiModel::find($RKARealisasiRincID);

    if (is_null($name))
    {
      $name = $media->getClientOriginalName();    
    }
    
    $custom_properties = $rincian_realisasi->toArray();

    $result = $rincian_realisasi->addMedia($media)
			->usingName($name)
      ->usingFileName($media->hashName())
      ->withCustomProperties($custom_properties)
			->toMediaCollection($collection);

    return $result;
  }
	public function index(Request $request)
	{
		try
		{
			$validator = Validator::make($request->all(), [        
				'pid'=>'required|in:realisasirincian'
			]);     

			if ($validator->stopOnFirstFailure()->fails())
			{				
				$errors = $validator->errors();				
				foreach ($errors->all() as $k=>$message) {					
					throw new Exception($message);
				}				
			}	

			switch($request->input('pid'))
			{
				case 'realisasirincian':
					$validator = Validator::make($request->all(), [
						'RKARincID' => 'required|exists:trRKARinc,RKARincID',
					]);

					if ($validator->stopOnFirstFailure()->fails())
					{				
						$errors = $validator->errors();				
						foreach ($errors->all() as $k=>$message) {					
							throw new Exception($message);
						}				
					}	
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
							// dd($media);
							$daftar_media[] = [
								'id' => $media->id,
								'publicFullUrl' => $media->getFullUrl(),
							];
						}
						else
						{
							continue;
						}						
					}					
				break;
			} 				
			return Response()->json([
				'status'=>1,
				'pid'=>'fetchdata',
				'media'=>$daftar_media,                                    
				'message'=>'Daftar media realisasi rincian berhasil diperoleh'
			], 200); 
		}
		catch(Exception $e)
		{
			return Response()->json([
				'status'=>0,
				'pid'=>'fetchdata',
				'media'=>[],                                    
				'message'=>$e->getMessage()
			], 422); 			
		}
	}
  public function store(Request $request)
  {
    try
		{
			$validator = Validator::make($request->all(), [
        'RKARealisasiRincID' => 'required|exists:trRKARealisasiRinc,RKARealisasiRincID',
				'foto'=>'required|image',	
				'pid'=>'required|in:realisasirincian'
			]);     

			if ($validator->stopOnFirstFailure()->fails())
			{				
				$errors = $validator->errors();				
				foreach ($errors->all() as $k=>$message) {					
					throw new Exception($message);
				}				
			}			
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
}
