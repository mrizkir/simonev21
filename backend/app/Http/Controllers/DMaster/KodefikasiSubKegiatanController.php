<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\KodefikasiKegiatanModel;
use App\Models\DMaster\KodefikasiSubKegiatanModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class KodefikasiSubKegiatanController extends Controller {              
    /**
     * get all kodefikasi urusan
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request)
    {
        $this->hasPermissionTo('DMASTER-KODEFIKASI-SUB-KEGIATAN_BROWSE');

        $this->validate($request, [        
            'TA'=>'required'
        ]);    
        $ta = $request->input('TA');
        $kodefikasisubkegiatan=KodefikasiSubKegiatanModel::select(\DB::raw("
                                      tmSubKegiatan.`SubKgtID`,
                                      tmKegiatan.`KgtID`,
                                      tmKegiatan.`PrgID`,
                                      tmBidangUrusan.BidangID,
                                      tmUrusan.`Kd_Urusan`,
                                      tmBidangUrusan.`Kd_Bidang`,			 
                                      tmProgram.`Kd_Program`,
                                      tmKegiatan.`Kd_Kegiatan`,
                                      tmSubKegiatan.`Kd_SubKegiatan`,
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
                                            CONCAT('X.','XX.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`)
                                      END AS kode_kegiatan,
                                      CASE 
                                          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
                                            CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`,'.',`tmSubKegiatan`.`Kd_SubKegiatan`)
                                          ELSE
                                            CONCAT('X.','XX.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`,'.',`tmSubKegiatan`.`Kd_SubKegiatan`)
                                      END AS kode_sub_kegiatan,
                                      COALESCE(tmUrusan.`Nm_Urusan`,'SEMUA URUSAN') AS Nm_Urusan,
                                      COALESCE(tmBidangUrusan.`Nm_Bidang`,'SEMUA BIDANG URUSAN') AS Nm_Bidang,
                                      `tmProgram`.`Nm_Program`,
                                      `tmKegiatan`.`Nm_Kegiatan`,
                                      `tmSubKegiatan`.`Nm_SubKegiatan`,
                                      `tmSubKegiatan`.`TA`,                                        
                                      `tmSubKegiatan`.`Descr`,                                        
                                      `tmSubKegiatan`.`created_at`,
                                      `tmSubKegiatan`.`updated_at`
                                    "))
                                    ->join('tmKegiatan','tmKegiatan.KgtID','tmSubKegiatan.KgtID')
                                    ->join('tmProgram','tmKegiatan.PrgID','tmProgram.PrgID')
                                    ->leftJoin('tmUrusanProgram','tmProgram.PrgID','tmUrusanProgram.PrgID')
                                    ->leftJoin('tmBidangUrusan','tmBidangUrusan.BidangID','tmUrusanProgram.BidangID')
                                    ->leftJoin('tmUrusan','tmBidangUrusan.UrsID','tmUrusan.UrsID')
                                    ->orderBy('kode_sub_kegiatan','ASC')                                    
                                    ->where('tmSubKegiatan.TA',$ta)
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'kodefikasisubkegiatan'=>$kodefikasisubkegiatan,
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-SUB-KEGIATAN_STORE');

        $this->validate($request, [
            'Kd_SubKegiatan'=> [
                        Rule::unique('tmSubKegiatan')->where(function($query) use ($request){
                            return $query->where('KgtID',$request->input('KgtID'))
                                        ->where('TA',$request->input('TA'));
                        }),
                        'required',
                        'regex:/^[0-9]+$/'],
            'Nm_SubKegiatan'=>'required',
            'TA'=>'required'
        ]);     
            
        $ta = $request->input('TA');
        
        $KgtID = $request->input('KgtID');

        $kegiatan = KodefikasiKegiatanModel::select(\DB::raw("                                      
                                      CASE 
                                          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
                                            CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`,'.')
                                          ELSE
                                            CONCAT('X.','XX.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`,'.')
                                      END AS kode_kegiatan                                      
                                    "))
                                    ->join('tmProgram','tmKegiatan.PrgID','tmProgram.PrgID')
                                    ->leftJoin('tmUrusanProgram','tmProgram.PrgID','tmUrusanProgram.PrgID')
                                    ->leftJoin('tmBidangUrusan','tmBidangUrusan.BidangID','tmUrusanProgram.BidangID')
                                    ->leftJoin('tmUrusan','tmBidangUrusan.UrsID','tmUrusan.UrsID')                                                                       
                                    ->where('tmKegiatan.KgtID',$KgtID)
                                    ->first();

        $kodefikasisubkegiatan = KodefikasiSubKegiatanModel::create([
            'SubKgtID' => Uuid::uuid4()->toString(),            
            'KgtID' => $request->input('KgtID'),            
            'Kd_SubKegiatan' => $request->input('Kd_SubKegiatan'),
            'kode_sub_kegiatan' => $kegiatan->kode_kegiatan . $request->input('Kd_SubKegiatan'),
            'Nm_SubKegiatan' => strtoupper($request->input('Nm_SubKegiatan')),
            'Descr' => $request->input('Descr'),
            'TA'=>$ta,
        ]);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'kodefikasisubkegiatan'=>$kodefikasisubkegiatan,                                    
                                    'message'=>'Data Kodefikasi Sub Kegiatan berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-SUB-KEGIATAN_UPDATE');

        $kodefikasisubkegiatan = KodefikasiSubKegiatanModel::find($id);
        
        if (is_null($kodefikasisubkegiatan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Data Kodefikasi Sub Kegiatan ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $this->validate($request, [    
                                        'Kd_SubKegiatan'=>[
                                                    Rule::unique('tmSubKegiatan')->where(function($query) use ($request,$kodefikasisubkegiatan) {  
                                                        if ($request->input('Kd_SubKegiatan')==$kodefikasisubkegiatan->Kd_SubKegiatan) 
                                                        {
                                                            return $query->where('Kd_SubKegiatan','ignore')
                                                                        ->where('TA',$kodefikasisubkegiatan->TA);
                                                        }                 
                                                        else
                                                        {
                                                            return $query->where('Kd_SubKegiatan',$request->input('Kd_SubKegiatan'))
                                                                    ->where('KgtID',$kodefikasisubkegiatan->KgtID)
                                                                    ->where('TA',$kodefikasisubkegiatan->TA);
                                                        }                                                                                    
                                                    }),
                                                    'required',
                                                    'regex:/^[0-9]+$/'
                                                ],
                                        'Nm_SubKegiatan'=>'required',
                                    ]);
            
            $KgtID = $request->input('KgtID');

            $kegiatan = KodefikasiKegiatanModel::select(\DB::raw("                                      
                                      CASE 
                                          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
                                            CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`,'.')
                                          ELSE
                                            CONCAT('X.','XX.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`,'.')
                                      END AS kode_kegiatan                                      
                                    "))
                                    ->join('tmProgram','tmKegiatan.PrgID','tmProgram.PrgID')
                                    ->leftJoin('tmUrusanProgram','tmProgram.PrgID','tmUrusanProgram.PrgID')
                                    ->leftJoin('tmBidangUrusan','tmBidangUrusan.BidangID','tmUrusanProgram.BidangID')
                                    ->leftJoin('tmUrusan','tmBidangUrusan.UrsID','tmUrusan.UrsID')                                                                       
                                    ->where('tmKegiatan.KgtID',$KgtID)
                                    ->first();

            $kodefikasisubkegiatan->Kd_SubKegiatan = $request->input('Kd_SubKegiatan');
            $kodefikasisubkegiatan->kode_sub_kegiatan = $kegiatan->kode_kegiatan . $request->input('Kd_SubKegiatan');
            $kodefikasisubkegiatan->Nm_SubKegiatan = strtoupper($request->input('Nm_SubKegiatan'));
            $kodefikasisubkegiatan->Descr = $request->input('Descr');
            $kodefikasisubkegiatan->save();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'kodefikasisubkegiatan'=>$kodefikasisubkegiatan,                                    
                                    'message'=>'Data Kodefikasi Sub Kegiatan '.$kodefikasisubkegiatan->Nm_SubKegiatan.' berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-KODEFIKASI-SUB-KEGIATAN_DESTROY');

        $kodefikasisubkegiatan = KodefikasiSubKegiatanModel::find($id);

        if (is_null($kodefikasisubkegiatan))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data Kodefikasi Sub Kegiatan ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            
            $result=$kodefikasisubkegiatan->delete();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Data Kodefikasi Sub Kegiatan dengan ID ($id) berhasil dihapus"
                                ],200);
        }
    }
}