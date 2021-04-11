<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\KodefikasiUrusanModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class KodefikasiUrusanController extends Controller {              
    /**
     * get all kodefikasi urusan
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $this->hasPermissionTo('DMASTER-KODEFIKASI-URUSAN_BROWSE');

        $this->validate($request, [        
            'TA'=>'required'
        ]);    
        $ta = $request->input('TA');
        $kodefikasiurusan=KodefikasiUrusanModel::select(\DB::raw('
                                        `UrsID`,                                        
                                        `Kd_Urusan`,
                                        `Nm_Urusan`,
                                        CONCAT(\'[\',`Kd_Urusan`,\'] \',`Nm_Urusan`) AS `urusan`,
                                        `Descr`,
                                        `TA`,
                                        created_at,
                                        updated_at
                                    '))
                                    ->orderBy('Kd_Urusan','ASC')                                    
                                    ->where('TA',$ta)
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'kodefikasiurusan'=>$kodefikasiurusan,
                                    'message'=>'Fetch data kodefikasi urusan berhasil.'
                                ],200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {       
        $this->hasPermissionTo('DMASTER-KODEFIKASI-URUSAN_STORE');

        $this->validate($request, [
            'Kd_Urusan'=> [
                        Rule::unique('tmUrusan')->where(function($query) use ($request){
                            return $query->where('TA',$request->input('TA'));
                        }),
                        'required',
                        'regex:/^[0-9]+$/'],
            'Nm_Urusan'=>'required',
            'TA'=>'required'
        ]);     
            
        $ta = $request->input('TA');
        
        $kodefikasiurusan = KodefikasiUrusanModel::create([
            'UrsID' => Uuid::uuid4()->toString(),            
            'Kd_Urusan' => $request->input('Kd_Urusan'),
            'Nm_Urusan' => strtoupper($request->input('Nm_Urusan')),
            'Descr' => $request->input('Descr'),
            'TA'=>$ta,
        ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'kodefikasiurusan'=>$kodefikasiurusan,                                    
                                    'message'=>'Data Kodefikasi Urusan berhasil disimpan.'
                                ],200); 
    }               
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {        
        $this->hasPermissionTo('DMASTER-KODEFIKASI-URUSAN_UPDATE');

        $kodefikasiurusan = KodefikasiUrusanModel::find($id);
        
        if (is_null($kodefikasiurusan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Data Kodefikasi Urusan ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [    
                                        'Kd_Urusan'=>[
                                                    Rule::unique('tmUrusan')->where(function($query) use ($request,$kodefikasiurusan) {  
                                                        if ($request->input('Kd_Urusan')==$kodefikasiurusan->Kd_Urusan) 
                                                        {
                                                            return $query->where('Kd_Urusan','ignore')
                                                                        ->where('TA',$kodefikasiurusan->TA);
                                                        }                 
                                                        else
                                                        {
                                                            return $query->where('Kd_Urusan',$request->input('Kd_Urusan'))
                                                                    ->where('TA',$kodefikasiurusan->TA);
                                                        }                                                                                    
                                                    }),
                                                    'required',
                                                    'regex:/^[0-9]+$/'
                                                ],
                                        'Nm_Urusan'=>'required',
                                    ]);
            
            
            $kodefikasiurusan->Kd_Urusan = $request->input('Kd_Urusan');
            $kodefikasiurusan->Nm_Urusan = strtoupper($request->input('Nm_Urusan'));
            $kodefikasiurusan->Descr = $request->input('Descr');
            $kodefikasiurusan->save();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'kodefikasiurusan'=>$kodefikasiurusan,                                    
                                    'message'=>'Data Kodefikasi Urusan '.$kodefikasiurusan->Nm_Urusan.' berhasil diubah.'
                                ],200);
        }
        
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {   
        $this->hasPermissionTo('DMASTER-KODEFIKASI-URUSAN_DESTROY');

        $kodefikasiurusan = KodefikasiUrusanModel::find($id);

        if (is_null($kodefikasiurusan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data Kodefikasi Urusan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            
            $result=$kodefikasiurusan->delete();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Data Kodefikasi Urusan dengan ID ($id) berhasil dihapus"
                                ],200);
        }
    }
}