<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\KabupatenModel;
use App\Models\DMaster\ProvinsiModel;
use App\Rules\CheckRecordIsExistValidation;
use App\Rules\IgnoreIfDataIsEqualValidation;

class ProvinsiController extends Controller {        
    /**
     * digunakan untuk mendapatkan daftar kecamatan
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = ProvinsiModel::orderBy('nama','ASC')
                                ->get();
                                
        return Response()->json([
                                'status' => 1,
                                'pid' => 'fetchdata',
                                'provinsi' => $data,
                                'message' => 'Fetch data provinsi berhasil diperoleh'
                            ], 200);  
    }
    public function kabupaten (Request $request,$id)
    {
        
        $kota = KabupatenModel::where('provinsi_id', $id)
                            ->orderBy('nama', 'asc')
                            ->get();

        return Response()->json([
                                'status' => 1,
                                'pid' => 'fetchdata',                                
                                'kota' => $kota,
                                'message' => 'Fetch data kabupaten/kota dari provinsi berhasil diperoleh'
                            ], 200);  

    }
   
}