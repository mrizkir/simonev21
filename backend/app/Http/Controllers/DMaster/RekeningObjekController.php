<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\RekeningObjekModel;
use App\Models\DMaster\RekeningRincianObjekModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class RekeningObjekController extends Controller {              
    /**
     * get all objek
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-OBJEK_BROWSE');

        $this->validate($request, [        
            'TA'=>'required'
        ]);    
        $ta = $request->input('TA');

        $objek=RekeningObjekModel::select(\DB::raw('
                                        `tmOby`.`ObyID`,                                        
                                        `tmOby`.`JnsID`,                                        
                                        `tmOby`.`Kd_Rek_4`,
                                        `tmOby`.`ObyNm`,        
                                        CONCAT(`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`) AS `kode_objek`,
                                        CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',\'] \',`ObyNm`) AS `nama_rek3`,
                                        CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'] \',`ObyNm`) AS `nama_rek4`,
                                        `tmOby`.`Descr`,
                                        `tmOby`.`TA`
                                    '))                                    
                                    ->join('tmJns','tmJns.JnsID','tmOby.JnsID')
                                    ->join('tmKlp','tmJns.KlpID','tmKlp.KlpID')
                                    ->join('tmAkun','tmAkun.AkunID','tmKlp.AkunID')
                                    ->where('tmOby.TA',$ta)
                                    ->orderBy('Kd_Rek_1','ASC')
                                    ->orderBy('Kd_Rek_2','ASC')
                                    ->orderBy('Kd_Rek_3','ASC')
                                    ->orderBy('Kd_Rek_4','ASC')                                    
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'objek'=>$objek,
                                    'message'=>'Fetch data objek berhasil.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-OBJEK_STORE');

        $this->validate($request, [
            'JnsID'=>'required|exists:tmJns,JnsID',
            'Kd_Rek_4'=> [
                        Rule::unique('tmOby')->where(function($query) use ($request){
                            return $query->where('JnsID',$request->input('JnsID'))
                                        ->where('TA',$request->input('TA'));
                        }),
                        'required',
                        'regex:/^[0-9]+$/'],
            'ObyNm'=>'required',
            'TA'=>'required'
        ]);     
            
        $ta = $request->input('TA');
        
        $objek = RekeningObjekModel::create([
            'ObyID' => Uuid::uuid4()->toString(),
            'JnsID' => $request->input('JnsID'),
            'Kd_Rek_4' => $request->input('Kd_Rek_4'),
            'ObyNm' => $request->input('ObyNm'),
            'Descr' => $request->input('Descr'),
            'TA'=>$ta,
        ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'objek'=>$objek,                                    
                                    'message'=>'Data Rekening Objek berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-OBJEK_UPDATE');

        $objek = RekeningObjekModel::find($id);
        
        if (is_null($objek))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Data Rekening Objek ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [    
                                        'JnsID'=>'required|exists:tmJns,JnsID',
                                        'Kd_Rek_4'=>[
                                                    Rule::unique('tmOby')->where(function($query) use ($request,$objek) {  
                                                        if ($request->input('Kd_Rek_4')==$objek->Kd_Rek_4) 
                                                        {
                                                            return $query->where('JnsID',$request->input('JnsID'))
                                                                        ->where('Kd_Rek_4','ignore')
                                                                        ->where('TA',$objek->TA);
                                                        }                 
                                                        else
                                                        {
                                                            return $query->where('Kd_Rek_4',$request->input('Kd_Rek_4'))
                                                                    ->where('JnsID',$request->input('JnsID'))
                                                                    ->where('TA',$objek->TA);
                                                        }                                                                                    
                                                    }),
                                                    'required',
                                                    'regex:/^[0-9]+$/'
                                                ],
                                        'ObyNm'=>'required',
                                    ]);
            
            
            $objek->JnsID = $request->input('JnsID');
            $objek->Kd_Rek_4 = $request->input('Kd_Rek_4');
            $objek->ObyNm = $request->input('ObyNm');
            $objek->Descr = $request->input('Descr');
            $objek->save();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'objek'=>$objek,                                    
                                    'message'=>'Data Rekening Objek '.$objek->ObyNm.' berhasil diubah.'
                                ],200);
        }
        
    }
    public function rincianobjekrka (Request $request, $id)
    {
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_BROWSE');

        $rincianobjek=RekeningRincianObjekModel::select(\DB::raw('
                                        `tmROby`.`RObyID`,                                        
                                        CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'.\',`Kd_Rek_5`,\'] \',`RObyNm`) AS `nama_rek5`                                        
                                    '))
                                    ->join('tmOby','tmOby.ObyID','tmROby.ObyID')
                                    ->join('tmJns','tmJns.JnsID','tmOby.JnsID')
                                    ->join('tmKlp','tmJns.KlpID','tmKlp.KlpID')
                                    ->join('tmAkun','tmAkun.AkunID','tmKlp.AkunID')
                                    ->where('tmROby.ObyID',$id)
                                    ->orderBy('Kd_Rek_1','ASC')
                                    ->orderBy('Kd_Rek_2','ASC')
                                    ->orderBy('Kd_Rek_3','ASC')
                                    ->orderBy('Kd_Rek_4','ASC')
                                    ->orderBy('Kd_Rek_5','ASC')
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'rincianobjek'=>$rincianobjek,
                                    'message'=>"Fetch data rincian objek dari objek ($id) berhasil."
                                ],200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {   
        $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-OBJEK_DESTROY');

        $objek = RekeningObjekModel::find($id);

        if (is_null($objek))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data Rekening Objek ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            
            $result=$objek->delete();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Data Rekening Objek dengan ID ($id) berhasil dihapus"
                                ],200);
        }
    }
}