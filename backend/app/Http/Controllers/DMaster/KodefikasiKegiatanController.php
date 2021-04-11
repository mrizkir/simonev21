<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\KodefikasiKegiatanModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class KodefikasiKegiatanController extends Controller {              
    /**
     * get all kodefikasi urusan
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $this->hasPermissionTo('DMASTER-KODEFIKASI-KEGIATAN_BROWSE');

        $this->validate($request, [        
            'TA'=>'required'
        ]);    
        $ta = $request->input('TA');
        $kodefikasikegiatan=KodefikasiKegiatanModel::select(\DB::raw("
                                      tmKegiatan.`KgtID`,
                                      tmKegiatan.`PrgID`,
                                      tmBidangUrusan.BidangID,
                                      tmUrusan.`Kd_Urusan`,
                                      tmBidangUrusan.`Kd_Bidang`,			 
                                      tmProgram.`Kd_Program`,
                                      tmKegiatan.`Kd_Kegiatan`,
                                      CASE 
                                          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
                                            CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`)
                                          ELSE
                                            CONCAT('X.','XX.',tmProgram.`Kd_Program`)
                                      END AS kode_program,
                                      CASE 
                                          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
                                            CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`)
                                          ELSE
                                            CONCAT('X.','XX.',tmProgram.`Kd_Program`,`tmKegiatan`.`Kd_Kegiatan`)
                                      END AS kode_kegiatan,
                                      COALESCE(tmUrusan.`Nm_Urusan`,'SEMUA BIDANG URUSAN') AS Nm_Urusan,
                                      COALESCE(tmBidangUrusan.`Nm_Bidang`,'SEMUA BIDANG URUSAN') AS Nm_Bidang,
                                      tmProgram.`Nm_Program`,
                                      tmKegiatan.`Nm_Kegiatan`,
                                      tmKegiatan.`TA`,                                        
                                      tmKegiatan.`Descr`,                                        
                                      tmKegiatan.`created_at`,
                                      tmKegiatan.`updated_at`
                                    "))
                                    ->join('tmProgram','tmKegiatan.PrgID','tmProgram.PrgID')
                                    ->leftJoin('tmUrusanProgram','tmProgram.PrgID','tmUrusanProgram.PrgID')
                                    ->leftJoin('tmBidangUrusan','tmBidangUrusan.BidangID','tmUrusanProgram.BidangID')
                                    ->leftJoin('tmUrusan','tmBidangUrusan.UrsID','tmUrusan.UrsID')
                                    ->orderBy('kode_kegiatan','ASC')                                    
                                    ->where('tmKegiatan.TA',$ta)
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'kodefikasikegiatan'=>$kodefikasikegiatan,
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-KEGIATAN_STORE');

        $this->validate($request, [
            'Kd_Kegiatan'=> [
                        Rule::unique('tmKegiatan')->where(function($query) use ($request){
                            return $query->where('PrgID',$request->input('PrgID'))
                                        ->where('TA',$request->input('TA'));
                        }),
                        'required',
                        'regex:/^[0-9]+$/'],
            'Nm_Kegiatan'=>'required',
            'TA'=>'required'
        ]);     
            
        $ta = $request->input('TA');
        
        $kodefikasikegiatan = KodefikasiKegiatanModel::create([
            'KgtID' => Uuid::uuid4()->toString(),            
            'PrgID' => $request->input('PrgID'),            
            'Kd_Kegiatan' => $request->input('Kd_Kegiatan'),
            'Nm_Kegiatan' => strtoupper($request->input('Nm_Kegiatan')),
            'Descr' => $request->input('Descr'),
            'TA'=>$ta,
        ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'kodefikasikegiatan'=>$kodefikasikegiatan,                                    
                                    'message'=>'Data Kodefikasi Kegiatan berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-KEGIATAN_UPDATE');

        $kodefikasikegiatan = KodefikasiKegiatanModel::find($id);
        
        if (is_null($kodefikasikegiatan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Data Kodefikasi Kegiatan ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [    
                                        'Kd_Kegiatan'=>[
                                                    Rule::unique('tmKegiatan')->where(function($query) use ($request,$kodefikasikegiatan) {  
                                                        if ($request->input('Kd_Kegiatan')==$kodefikasikegiatan->Kd_Kegiatan) 
                                                        {
                                                            return $query->where('Kd_Kegiatan','ignore')
                                                                        ->where('TA',$kodefikasikegiatan->TA);
                                                        }                 
                                                        else
                                                        {
                                                            return $query->where('Kd_Kegiatan',$request->input('Kd_Kegiatan'))
                                                                    ->where('PrgID',$kodefikasikegiatan->PrgID)
                                                                    ->where('TA',$kodefikasikegiatan->TA);
                                                        }                                                                                    
                                                    }),
                                                    'required',
                                                    'regex:/^[0-9]+$/'
                                                ],
                                        'Nm_Kegiatan'=>'required',
                                    ]);
            
            
            $kodefikasikegiatan->Kd_Kegiatan = $request->input('Kd_Kegiatan');
            $kodefikasikegiatan->Nm_Kegiatan = strtoupper($request->input('Nm_Kegiatan'));
            $kodefikasikegiatan->Descr = $request->input('Descr');
            $kodefikasikegiatan->save();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'kodefikasikegiatan'=>$kodefikasikegiatan,                                    
                                    'message'=>'Data Kodefikasi Kegiatan '.$kodefikasikegiatan->Nm_Kegiatan.' berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-KEGIATAN_DESTROY');

        $kodefikasikegiatan = KodefikasiKegiatanModel::find($id);

        if (is_null($kodefikasikegiatan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data Kodefikasi Kegiatan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            
            $result=$kodefikasikegiatan->delete();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Data Kodefikasi Kegiatan dengan ID ($id) berhasil dihapus"
                                ],200);
        }
    }
}