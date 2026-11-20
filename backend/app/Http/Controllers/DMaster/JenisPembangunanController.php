<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use App\Models\DMaster\JenisPembangunanModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class JenisPembangunanController extends Controller
{ 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('DMASTER-JENIS-PEMBANGUNAN_BROWSE');
        
        $tahun = $request->input('tahun');
        $this->validate($request, [            
            'tahun' => 'required',
            
        ]);     
        
        $data = JenisPembangunanModel::where('TA', $tahun)
                                        ->get();
        return Response()->json([
                                'status' => 1,
                                'pid' => 'fetchdata',
                                'jenispembangunan' => $data,
                                'message' => 'Fetch data jenis pembangunan berhasil diperoleh'
                            ], 200);  

    }    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {      
        $this->hasPermissionTo('DMASTER-JENIS-PEMBANGUNAN_STORE');  
        $this->validate($request, [
            'NamaJenis' => 'required|unique:tmJenisPembangunan',
            'TA' => 'required',
        ]);

       
        $jenispembangunan = JenisPembangunanModel::create ([
            'JenisPembangunanID'=> Uuid::uuid4()->toString(),
            'NamaJenis' => $request->input('NamaJenis'),
            'Descr' => $request->input('Descr'),
            'TA' => $request->input('TA'),
        ]);     
        
        return Response()->json([
                                'status' => 1,
                                'pid' => 'store',
                                'jenispembangunan' => $jenispembangunan,                                    
                                'message' => 'Data Jenis Pembangunan berhasil disimpan.'
                            ], 200); 
               
    }
    /**
     * Salin jenis pembangunan dari tahun anggaran asal ke tahun tujuan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function salin(Request $request)
    {
        $this->validate($request, [
            'tahun_asal' => 'required|numeric',
            'tahun_tujuan' => 'required|numeric|gt:tahun_asal',
        ]);

        $tahun_asal = $request->input('tahun_asal');
        $tahun_tujuan = $request->input('tahun_tujuan');

        \DB::table('tmJenisPembangunan')
            ->where('TA', $tahun_tujuan)
            ->whereRaw('JenisPembangunanID_Src IS NOT NULL')
            ->delete();

        $str_insert = '
            INSERT INTO `tmJenisPembangunan` (
                `JenisPembangunanID`,
                `NamaJenis`,
                `Descr`,
                `TA`,
                `JenisPembangunanID_Src`,
                created_at,
                updated_at
            )
            SELECT
                uuid() AS id,
                NamaJenis,
                "DI IMPOR DARI TAHUN '.$tahun_asal.'" AS `Descr`,
                '.$tahun_tujuan.' AS `TA`,
                JenisPembangunanID AS JenisPembangunanID_Src,
                NOW() AS created_at,
                NOW() AS updated_at
            FROM tmJenisPembangunan
            WHERE TA='.$tahun_asal.'
        ';

        \DB::statement($str_insert);

        return Response()->json([
            'status' => 1,
            'pid' => 'store',
            'message' => "Salin jenis pembangunan dari tahun anggaran $tahun_asal berhasil."
        ], 200);
    }
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {        
        $this->hasPermissionTo('DMASTER-JENIS-PEMBANGUNAN_UPDATE');  
        $jenispembangunan = JenisPembangunanModel::find($id);
        
        $this->validate($request, [
            'NamaJenis' => 'required',
        ]);

        $jenispembangunan->NamaJenis = $request->input('NamaJenis');
        $jenispembangunan->Descr = $request->input('Descr');
        $jenispembangunan->save();

        return Response()->json([
                                'status' => 1,
                                'pid' => 'store',
                                'jenispembangunan' => $jenispembangunan,                                    
                                'message' => 'Data Jenis Pembangunan berhasil diubah.'
                            ], 200); 
                                
    } 
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {  
        $this->hasPermissionTo('DMASTER-JENIS-PEMBANGUNAN_DESTROY');  
        $jenispembangunan = JenisPembangunanModel::find($id);
        $result = $jenispembangunan->delete();
        return Response()->json([
                                'status' => 1,
                                'pid' => 'destroy',                
                                'message'=>"Nama Jenis Pembangunan berhasil dihapus"
                            ], 200);
    }
}
