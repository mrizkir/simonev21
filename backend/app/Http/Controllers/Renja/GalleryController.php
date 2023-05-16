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

class GalleryController extends Controller 
{
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
					$result = HelperKegiatan::createMediaRealisasiRincian($request->input('RKARealisasiRincID'), $request->file('foto'));
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
				'rka'=>[],                                    
				'message'=>$e->getMessage()
			], 422); 			
		}
  }
}
