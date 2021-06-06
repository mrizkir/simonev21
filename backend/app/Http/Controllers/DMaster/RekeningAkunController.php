<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\RekeningAkunModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class RekeningAKunController extends Controller {              
    /**
     * get all akun rekening
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-AKUN_BROWSE');

        $this->validate($request, [        
            'TA'=>'required'
        ]);    
        $ta = $request->input('TA');
        $akun=RekeningAkunModel::select(\DB::raw('
                                        `AkunID`,                                        
                                        `Kd_Rek_1`,
                                        `Nm_Akun`,
                                        CONCAT(\'[\',`Kd_Rek_1`,\'] \',`Nm_Akun`) AS `nama_rek1`,
                                        `Descr`,
                                        `TA`
                                    '))
                                    ->orderBy('Kd_Rek_1','ASC')                                    
                                    ->where('TA',$ta)
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'akun'=>$akun,
                                    'message'=>'Fetch data rekening akun berhasil.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-AKUN_STORE');

        $this->validate($request, [
            'Kd_Rek_1'=> [
                        Rule::unique('tmAkun')->where(function($query) use ($request){
                            return $query->where('TA',$request->input('TA'));
                        }),
                        'required',
                        'regex:/^[0-9]+$/'],
            'Nm_Akun'=>'required',
            'TA'=>'required'
        ]);     
            
        $ta = $request->input('TA');
        
        $akun = RekeningAkunModel::create([
            'AkunID' => Uuid::uuid4()->toString(),
            'Kd_Rek_1' => $request->input('Kd_Rek_1'),
            'Nm_Akun' => $request->input('Nm_Akun'),
            'Descr' => $request->input('Descr'),
            'TA'=>$ta,
        ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'akun'=>$akun,                                    
                                    'message'=>'Data Rekening Akun berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-AKUN_UPDATE');

        $akun = RekeningAkunModel::find($id);
        
        if (is_null($akun))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Data Rekening Akun ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [    
                                        'Kd_Rek_1'=>[
                                                    Rule::unique('tmAkun')->where(function($query) use ($request,$akun) {  
                                                        if ($request->input('Kd_Rek_1')==$akun->Kd_Rek_1) 
                                                        {
                                                            return $query->where('Kd_Rek_1','ignore')
                                                                        ->where('TA',$akun->TA);
                                                        }                 
                                                        else
                                                        {
                                                            return $query->where('Kd_Rek_1',$request->input('Kd_Rek_1'))
                                                                    ->where('TA',$akun->TA);
                                                        }                                                                                    
                                                    }),
                                                    'required',
                                                    'regex:/^[0-9]+$/'
                                                ],
                                        'Nm_Akun'=>'required',
                                    ]);
            
            
            $akun->Kd_Rek_1 = $request->input('Kd_Rek_1');
            $akun->Nm_Akun = $request->input('Nm_Akun');
            $akun->Descr = $request->input('Descr');
            $akun->save();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'akun'=>$akun,                                    
                                    'message'=>'Data Rekening Akun '.$akun->Nm_Akun.' berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-AKUN_DESTROY');

        $akun = RekeningAkunModel::find($id);

        if (is_null($akun))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data Rekening Akun ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            
            $result=$akun->delete();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Data Rekening Akun dengan ID ($id) berhasil dihapus"
                                ],200);
        }
    }
}