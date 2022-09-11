<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\TAModel;

use Illuminate\Validation\Rule;

class TAController extends Controller {              
    /**
     * get all Tahun Anggaran
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $ta=TAModel::all();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'ta'=>$ta,
                                    'message'=>'Fetch data tahun anggaran berhasil.'
                                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        $this->hasPermissionTo('DMASTER-TA_STORE');
        
        $this->validate($request, [
            'tahun'=> [
                        Rule::unique('ta')->where(function($query) {
                            return $query;
                        }),
                        'required',
                        'regex:/^[0-9]+$/'],
            'tahun_anggaran'=>'required',
        ]);

              
        $ta = TAModel::create ([
                                'tahun' => $request->input('tahun'),
                                'tahun_anggaran' => $request->input('tahun_anggaran'),                                
                            ]);  
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',
                                'ta'=>$ta,                                    
                                'message'=>'Data TA berhasil disimpan.'
                            ], 200); 
        
    }  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {        
        $this->hasPermissionTo('DMASTER-TA_UPDATE');
        $ta = TAModel::find($id);
        
        $this->validate($request, [
                                    'tahun'=>[
                                                Rule::unique('ta')->where(function($query) use ($request,$ta) {  
                                                    if ($request->input('tahun')==$ta->tahun) 
                                                    {
                                                        return $query->where('tahun',0);
                                                    }                 
                                                    else
                                                    {
                                                        return $query->where('tahun',$request->input('tahun'))
                                                                ->where('TA',$ta->tahun);
                                                    }                                                                                    
                                                }),
                                                'required',
                                                'regex:/^[0-9]+$/'
                                            ],
                                    'tahun_anggaran'=>'required',
                                ]);
        
        
        $ta->tahun = $request->input('tahun');
        $ta->tahun_anggaran = $request->input('tahun_anggaran');        
        $ta->save();

        return Response()->json([
                                'status'=>1,
                                'pid'=>'update',
                                'ta'=>$ta,                                    
                                'message'=>'Data TA '.$ta->tahun_anggaran.' berhasil diubah.'
                            ], 200);
        
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {   
        $this->hasPermissionTo('DMASTER-TA_DESTROY');
        $ta = TAModel::find($id);
        $result=$ta->delete();
        return Response()->json([
                                'status'=>1,
                                'pid'=>'destroy',                
                                'message'=>"Data TA dengan ID ($id) berhasil dihapus"
                            ], 200);
    }
}