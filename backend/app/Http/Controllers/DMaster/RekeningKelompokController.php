<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\RekeningKelompokModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class RekeningKelompokController extends Controller {              
    /**
     * get all kelompok rekening
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-KELOMPOK_BROWSE');

        $this->validate($request, [        
            'TA'=>'required'
        ]);    
        $ta = $request->input('TA');

        $kelompok=RekeningKelompokModel::select(\DB::raw('
                                        `tmKlp`.`KlpID`,                                        
                                        `tmKlp`.`AkunID`,                                        
                                        `tmKlp`.`Kd_Rek_2`,
                                        `tmKlp`.`KlpNm`,
                                        CONCAT(`Kd_Rek_1`,\'.\',`Kd_Rek_2`) AS `kode_kelompok`,
                                        CONCAT(\'[\',`Kd_Rek_1`,\'] \',`Nm_Akun`) AS `nama_rek1`,
                                        CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'] \',`KlpNm`) AS `nama_rek2`,
                                        `tmKlp`.`Descr`,
                                        `tmKlp`.`TA`
                                    '))
                                    ->join('tmAkun','tmAkun.AkunID','tmKlp.AkunID')
                                    ->where('tmKlp.TA',$ta)
                                    ->orderBy('Kd_Rek_1','ASC')
                                    ->orderBy('Kd_Rek_2','ASC')
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'kelompok'=>$kelompok,
                                    'message'=>'Fetch data rekening kelompok berhasil.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-KELOMPOK_STORE');

        $this->validate($request, [
            'AkunID'=>'required|exists:tmAkun,AkunID',
            'Kd_Rek_2'=> [
                        Rule::unique('tmKlp')->where(function($query) use ($request){
                            return $query->where('AkunID',$request->input('AkunID'))
                                        ->where('TA',$request->input('TA'));
                        }),
                        'required',
                        'regex:/^[0-9]+$/'],
            'KlpNm'=>'required',
            'TA'=>'required'
        ]);     
            
        $ta = $request->input('TA');
        
        $kelompok = RekeningKelompokModel::create([
            'KlpID' => Uuid::uuid4()->toString(),
            'AkunID' => $request->input('AkunID'),
            'Kd_Rek_2' => $request->input('Kd_Rek_2'),
            'KlpNm' => $request->input('KlpNm'),
            'Descr' => $request->input('Descr'),
            'TA'=>$ta,
        ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'kelompok'=>$kelompok,                                    
                                    'message'=>'Data Rekening Kelompok berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-KELOMPOK_UPDATE');

        $kelompok = RekeningKelompokModel::find($id);
        
        if (is_null($kelompok))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Data Rekening Kelompok ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [    
                                        'AkunID'=>'required|exists:tmAkun,AkunID',
                                        'Kd_Rek_2'=>[
                                                    Rule::unique('tmKlp')->where(function($query) use ($request,$kelompok) {  
                                                        if ($request->input('Kd_Rek_2')==$kelompok->Kd_Rek_2) 
                                                        {
                                                            return $query->where('AkunID',$request->input('AkunID'))
                                                                        ->where('Kd_Rek_2','ignore')
                                                                        ->where('TA',$kelompok->TA);
                                                        }                 
                                                        else
                                                        {
                                                            return $query->where('Kd_Rek_2',$request->input('Kd_Rek_2'))
                                                                    ->where('AkunID',$request->input('AkunID'))
                                                                    ->where('TA',$kelompok->TA);
                                                        }                                                                                    
                                                    }),
                                                    'required',
                                                    'regex:/^[0-9]+$/'
                                                ],
                                        'KlpNm'=>'required',
                                    ]);
            
            $kelompok->AkunID = $request->input('AkunID');
            $kelompok->Kd_Rek_2 = $request->input('Kd_Rek_2');
            $kelompok->KlpNm = $request->input('KlpNm');
            $kelompok->Descr = $request->input('Descr');
            $kelompok->save();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'kelompok'=>$kelompok,                                    
                                    'message'=>'Data Rekening Kelompok '.$kelompok->KlpNm.' berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-KELOMPOK_DESTROY');

        $kelompok = RekeningKelompokModel::find($id);

        if (is_null($kelompok))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data Rekening Kelompok ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            
            $result=$kelompok->delete();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Data Rekening Kelompok dengan ID ($id) berhasil dihapus"
                                ],200);
        }
    }
}