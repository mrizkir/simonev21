<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\KodefikasiBidangUrusanModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class KodefikasiBidangUrusanController extends Controller {              
    /**
     * get all kodefikasi urusan
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_BROWSE');

        $this->validate($request, [        
            'TA'=>'required'
        ]);    
        $ta = $request->input('TA');
        $kodefikasibidangurusan=KodefikasiBidangUrusanModel::select(\DB::raw("
                                        `BidangID`,
                                        `tmBidangUrusan`.`UrsID`,
                                        `Kd_Bidang`,
                                        `Nm_Bidang`,
                                        CONCAT(`tmUrusan`.`Kd_Urusan`,'.',`tmBidangUrusan`.`Kd_Bidang`) AS `kode_bidang`,
                                        CONCAT('[',`tmUrusan`.`Kd_Urusan`,'.',`tmBidangUrusan`.`Kd_Bidang`,'] ',`Nm_Bidang`) AS `bidangurusan`,
                                        `tmBidangUrusan`.`Descr`,
                                        `tmBidangUrusan`.`TA`
                                    "))
                                    ->join('tmUrusan','tmBidangUrusan.UrsID','tmUrusan.UrsID')
                                    ->orderBy('kode_bidang','ASC')                                    
                                    ->where('tmBidangUrusan.TA',$ta)
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'kodefikasibidangurusan'=>$kodefikasibidangurusan,
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_STORE');

        $this->validate($request, [
            'Kd_Bidang'=> [
                        Rule::unique('tmBidangUrusan')->where(function($query) use ($request){
                            return $query->where('UrsID',$request->input('UrsID'))
                                        ->where('TA',$request->input('TA'));
                        }),
                        'required',
                        'regex:/^[0-9]+$/'],
            'Nm_Bidang'=>'required',
            'TA'=>'required'
        ]);     
            
        $ta = $request->input('TA');
        
        $kodefikasibidangurusan = KodefikasiBidangUrusanModel::create([
            'BidangID' => Uuid::uuid4()->toString(),            
            'UrsID' => $request->input('UrsID'),            
            'Kd_Bidang' => $request->input('Kd_Bidang'),
            'Nm_Bidang' => $request->input('Nm_Bidang'),
            'Descr' => $request->input('Descr'),
            'TA'=>$ta,
        ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'kodefikasibidangurusan'=>$kodefikasibidangurusan,                                    
                                    'message'=>'Data Kodefikasi Bidang Urusan berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_UPDATE');

        $kodefikasibidangurusan = KodefikasiBidangUrusanModel::find($id);
        
        if (is_null($kodefikasibidangurusan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Data Kodefikasi Bidang Urusan ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [    
                                        'Kd_Bidang'=>[
                                                    Rule::unique('tmBidangUrusan')->where(function($query) use ($request,$kodefikasibidangurusan) {  
                                                        if ($request->input('Kd_Bidang')==$kodefikasibidangurusan->Kd_Bidang) 
                                                        {
                                                            return $query->where('Kd_Bidang','ignore')
                                                                        ->where('TA',$kodefikasibidangurusan->TA);
                                                        }                 
                                                        else
                                                        {
                                                            return $query->where('Kd_Bidang',$request->input('Kd_Bidang'))
                                                                    ->where('TA',$kodefikasibidangurusan->TA);
                                                        }                                                                                    
                                                    }),
                                                    'required',
                                                    'regex:/^[0-9]+$/'
                                                ],
                                        'Nm_Bidang'=>'required',
                                    ]);
            
            
            $kodefikasibidangurusan->Kd_Bidang = $request->input('Kd_Bidang');
            $kodefikasibidangurusan->Nm_Bidang = $request->input('Nm_Bidang');
            $kodefikasibidangurusan->Descr = $request->input('Descr');
            $kodefikasibidangurusan->save();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'kodefikasibidangurusan'=>$kodefikasibidangurusan,                                    
                                    'message'=>'Data Kodefikasi Bidang Urusan '.$kodefikasibidangurusan->Nm_Bidang.' berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-BIDANG-URUSAN_DESTROY');

        $kodefikasibidangurusan = KodefikasiBidangUrusanModel::find($id);

        if (is_null($kodefikasibidangurusan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data Kodefikasi Bidang Urusan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            
            $result=$kodefikasibidangurusan->delete();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Data Kodefikasi Bidang Urusan dengan ID ($id) berhasil dihapus"
                                ],200);
        }
    }
}