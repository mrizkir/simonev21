<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use App\Models\DMaster\JenisPelaksanaanModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class JenisPelaksanaanController extends Controller
{   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->hasPermissionTo('DMASTER-JENIS-PELAKSANAAN_BROWSE');
        
        $tahun = $request->input('tahun');
        $this->validate($request, [            
            'tahun' => 'required',
            
        ]);     
        
        $data = JenisPelaksanaanModel::where('TA', $tahun)
                                        ->get();
        return Response()->json([
                                'status' => 1,
                                'pid' => 'fetchdata',
                                'jenispelaksanaan' => $data,
                                'message' => 'Fetch data jenis pelaksanaan berhasil diperoleh'
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
        $this->hasPermissionTo('DMASTER-JENIS-PELAKSANAAN_STORE'); 
        $this->validate($request, [
            'NamaJenis' => 'required|unique:tmJenisPelaksanaan',
            'TA' => 'required',
        ]);

       
        $jenispelaksanaan = JenisPelaksanaanModel::create ([
            'JenisPelaksanaanID'=> Uuid::uuid4()->toString(),
            'NamaJenis' => $request->input('NamaJenis'),
            'Descr' => $request->input('Descr'),
            'TA' => $request->input('TA'),
        ]);     
        
        return Response()->json([
                                'status' => 1,
                                'pid' => 'store',
                                'jenispelaksanaan' => $jenispelaksanaan,                                    
                                'message' => 'Data Jenis Pelaksanaan berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-JENIS-PELAKSANAAN_UPDATE'); 
        $jenispelaksanaan = JenisPelaksanaanModel::find($id);
        
        $this->validate($request, [
            'NamaJenis' => 'required',
        ]);

        $jenispelaksanaan->NamaJenis = $request->input('NamaJenis');
        $jenispelaksanaan->Descr = $request->input('Descr');
        $jenispelaksanaan->save();

        return Response()->json([
                                'status' => 1,
                                'pid' => 'store',
                                'jenispelaksanaan' => $jenispelaksanaan,                                    
                                'message' => 'Data Jenis Pelaksanaan berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-JENIS-PELAKSANAAN_DESTROY'); 
        $jenispelaksanaan = JenisPelaksanaanModel::find($id);
        $result = $jenispelaksanaan->delete();
        return Response()->json([
                                    'status' => 1,
                                    'pid' => 'destroy',                
                                    'message'=>"Nama Jenis Pelaksanaan berhasil dihapus"
                                ], 200);
    }
}
