<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\KodefikasiProgramModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class KodefikasiProgramController extends Controller {              
    /**
     * get all kodefikasi urusan
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $this->hasPermissionTo('DMASTER-KODEFIKASI-PROGRAM_BROWSE');

        $this->validate($request, [        
            'TA'=>'required'
        ]);    
        $ta = $request->input('TA');
        $kodefikasiprogram=KodefikasiProgramModel::select(\DB::raw("
                                        tmProgram.`PrgID`,                                        
                                        tmUrusan.`Kd_Urusan`,
                                        tmBidangUrusan.`Kd_Bidang`,			 
                                        tmProgram.`Kd_Program`,
                                        CASE 
                                            WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
                                              CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`)
                                            ELSE
                                              CONCAT('X.','XX.',tmProgram.`Kd_Program`)
                                        END AS kode_program,
                                        tmUrusan.`Nm_Urusan`,
                                        tmBidangUrusan.`Nm_Bidang`,
                                        tmProgram.`Nm_Program`,
                                        tmProgram.`Jns`,
                                        tmProgram.`TA`,                                        
                                        tmProgram.`created_at`,
                                        tmProgram.`updated_at`
                                    "))
                                    ->leftJoin('tmUrusanProgram','tmProgram.PrgID','tmUrusanProgram.PrgID')
                                    ->leftJoin('tmBidangUrusan','tmBidangUrusan.BidangID','tmUrusanProgram.BidangID')
                                    ->leftJoin('tmUrusan','tmBidangUrusan.UrsID','tmUrusan.UrsID')
                                    ->orderBy('tmUrusan.Kd_Urusan','ASC')                                    
                                    ->orderBy('tmBidangUrusan.Kd_Bidang','ASC')                                    
                                    ->orderBy('tmProgram.Kd_Program','ASC')                                    
                                    ->where('tmProgram.TA',$ta)
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'kodefikasiprogram'=>$kodefikasiprogram,
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-PROGRAM_STORE');

        $this->validate($request, [
            'Kd_Bidang'=> [
                        Rule::unique('tmProgram')->where(function($query) use ($request){
                            return $query->where('UrsID',$request->input('UrsID'))
                                        ->where('TA',$request->input('TA'));
                        }),
                        'required',
                        'regex:/^[0-9]+$/'],
            'Nm_Bidang'=>'required',
            'TA'=>'required'
        ]);     
            
        $ta = $request->input('TA');
        
        $kodefikasiprogram = KodefikasiProgramModel::create([
            'BidangID' => Uuid::uuid4()->toString(),            
            'UrsID' => $request->input('UrsID'),            
            'Kd_Bidang' => $request->input('Kd_Bidang'),
            'Nm_Bidang' => strtoupper($request->input('Nm_Bidang')),
            'Descr' => $request->input('Descr'),
            'TA'=>$ta,
        ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'kodefikasiprogram'=>$kodefikasiprogram,                                    
                                    'message'=>'Data Kodefikasi Program berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-PROGRAM_UPDATE');

        $kodefikasiprogram = KodefikasiProgramModel::find($id);
        
        if (is_null($kodefikasiprogram))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Data Kodefikasi Program ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [    
                                        'Kd_Bidang'=>[
                                                    Rule::unique('tmProgram')->where(function($query) use ($request,$kodefikasiprogram) {  
                                                        if ($request->input('Kd_Bidang')==$kodefikasiprogram->Kd_Bidang) 
                                                        {
                                                            return $query->where('Kd_Bidang','ignore')
                                                                        ->where('TA',$kodefikasiprogram->TA);
                                                        }                 
                                                        else
                                                        {
                                                            return $query->where('Kd_Bidang',$request->input('Kd_Bidang'))
                                                                    ->where('TA',$kodefikasiprogram->TA);
                                                        }                                                                                    
                                                    }),
                                                    'required',
                                                    'regex:/^[0-9]+$/'
                                                ],
                                        'Nm_Bidang'=>'required',
                                    ]);
            
            
            $kodefikasiprogram->Kd_Bidang = $request->input('Kd_Bidang');
            $kodefikasiprogram->Nm_Bidang = strtoupper($request->input('Nm_Bidang'));
            $kodefikasiprogram->Descr = $request->input('Descr');
            $kodefikasiprogram->save();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'kodefikasiprogram'=>$kodefikasiprogram,                                    
                                    'message'=>'Data Kodefikasi Program '.$kodefikasiprogram->Nm_Bidang.' berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-PROGRAM_DESTROY');

        $kodefikasiprogram = KodefikasiProgramModel::find($id);

        if (is_null($kodefikasiprogram))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data Kodefikasi Program ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            
            $result=$kodefikasiprogram->delete();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Data Kodefikasi Program dengan ID ($id) berhasil dihapus"
                                ],200);
        }
    }
}