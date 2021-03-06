<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\KodefikasiProgramModel;
use App\Models\DMaster\KodefikasiKegiatanModel;
use App\Models\DMaster\KodefikasiUrusanProgramModel;

use App\Rules\KodefikasiKodeProgramRule;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class KodefikasiProgramController extends Controller {              
    /**
     * get all kodefikasi program
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
                                        tmBidangUrusan.BidangID,
                                        tmUrusan.`Kd_Urusan`,
                                        tmBidangUrusan.`Kd_Bidang`,			 
                                        tmProgram.`Kd_Program`,
                                        CASE 
                                            WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
                                              CONCAT(tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`)
                                            ELSE
                                              CONCAT('X.','XX.',tmProgram.`Kd_Program`)
                                        END AS kode_program,                                        
                                        COALESCE(tmUrusan.`Nm_Urusan`,'SEMUA BIDANG URUSAN') AS Nm_Urusan,
                                        COALESCE(tmBidangUrusan.`Nm_Bidang`,'SEMUA BIDANG URUSAN') AS Nm_Bidang,
                                        tmProgram.`Nm_Program`,
                                        CASE 
                                            WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
                                              CONCAT('[',tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`,'] ',tmProgram.Nm_Program)
                                            ELSE
                                              CONCAT('[X.','XX.',tmProgram.`Kd_Program`,'] ',tmProgram.Nm_Program)
                                        END AS nama_program,                                        
                                        tmProgram.`Jns`,
                                        tmProgram.`TA`,                                        
                                        tmProgram.`Descr`,                                        
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
                                    'message'=>'Fetch data kodefikasi program berhasil.'
                                ],200);
    }
    /**
     * get all kodefikasi program
     *
     * @return \Illuminate\Http\Response
     */
    public function rka (Request $request)
    {
        $this->hasPermissionTo('DMASTER-KODEFIKASI-PROGRAM_BROWSE');

        $this->validate($request, [        
            'TA'=>'required',
            'BidangID_1'=>'required',
        ]);    
        $ta = $request->input('TA');
        $BidangID_1=$request->input('BidangID_1');
        $BidangID_2=$request->input('BidangID_2');
        $BidangID_3=$request->input('BidangID_3');

        $program = KodefikasiProgramModel::select(\DB::raw('
                                `PrgID`,
                                CONCAT("[X.XX.",`Kd_Program`,"] ",`Nm_Program`) AS nama_program	
                            '))
                            ->where('TA',$ta)
                            ->where('Jns',0)
                            ->orderBy('Kd_Program','ASC')                                    
                            ->get();

        $kodefikasiprogram=KodefikasiProgramModel::select(\DB::raw('
                                        `tmProgram`.`PrgID`,
	                                    CONCAT("[",D.Kd_Urusan,".",C.Kd_Bidang,".",tmProgram.Kd_Program,"] ",tmProgram.Nm_Program) AS nama_program	
                                    '))
                                    ->join('tmUrusanProgram AS B','tmProgram.PrgID','B.PrgID')
                                    ->join('tmBidangUrusan AS C','C.BidangID','B.BidangID')
                                    ->join('tmUrusan AS D','D.UrsID','D.UrsID')                                    
                                    ->where('tmProgram.TA',$ta)
                                    ->where('B.BidangID',$BidangID_1)
                                    ->orWhere('B.BidangID',$BidangID_2)
                                    ->orWhere('B.BidangID',$BidangID_3)
                                    ->orderBy('D.Kd_Urusan','ASC')                                    
                                    ->orderBy('C.Kd_Bidang','ASC')                                    
                                    ->orderBy('tmProgram.Kd_Program','ASC')                                    
                                    ->get();

        foreach ($kodefikasiprogram as $item)
        {
            $program->add($item);
        }
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'programrka'=>$program,
                                    'message'=>'Fetch data kodefikasi program rka berhasil.'
                                ],200);
    }
    /**
     * digunakan untuk mendapatkan daftar kegiatan dari sebuah program
     */
    public function kegiatan(Request $request, $id)
    {       
        $this->hasPermissionTo('DMASTER-KODEFIKASI-KEGIATAN_BROWSE');

        $programkegiatan=KodefikasiKegiatanModel::select(\DB::raw("
                                      tmKegiatan.`KgtID`,                                      
                                      CASE 
                                          WHEN tmBidangUrusan.`UrsID` IS NOT NULL OR tmBidangUrusan.`BidangID` IS NOT NULL THEN
                                            CONCAT('[',tmUrusan.`Kd_Urusan`,'.',tmBidangUrusan.`Kd_Bidang`,'.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`,'] ',`tmKegiatan`.`Nm_Kegiatan`)
                                          ELSE
                                            CONCAT('[','X.','XX.',tmProgram.`Kd_Program`,'.',`tmKegiatan`.`Kd_Kegiatan`,'] ',`tmKegiatan`.`Nm_Kegiatan`)
                                      END AS nama_kegiatan
                                    "))
                                    ->join('tmProgram','tmKegiatan.PrgID','tmProgram.PrgID')
                                    ->leftJoin('tmUrusanProgram','tmProgram.PrgID','tmUrusanProgram.PrgID')
                                    ->leftJoin('tmBidangUrusan','tmBidangUrusan.BidangID','tmUrusanProgram.BidangID')
                                    ->leftJoin('tmUrusan','tmBidangUrusan.UrsID','tmUrusan.UrsID')
                                    ->orderBy('tmKegiatan.Kd_Kegiatan','ASC')                                    
                                    ->orderBy('tmProgram.Kd_Program','ASC')                                    
                                    ->orderBy('tmBidangUrusan.Kd_Bidang','ASC')                                    
                                    ->orderBy('tmUrusan.Kd_Urusan','ASC')                                    
                                    ->where('tmKegiatan.PrgID',$id)
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'programkegiatan'=>$programkegiatan,
                                    'message'=>"Fetch data kegiatan dari program $id berhasil."
                                ],200);   
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {       
        $this->hasPermissionTo('DMASTER-KODEFIKASI-PROGRAM_STORE');
        
        $this->validate($request, [            
            'Jns'=>'required|in:1,0',
            'Nm_Program'=>'required',
            'TA'=>'required',
            'Kd_Program'=> [                        
                'required',
                'regex:/^[0-9]+$/', 
                new KodefikasiKodeProgramRule($request,'unique')
            ],
        ]);     
        $kodefikasiprogram = \DB::transaction(function () use ($request) {
            $ta = $request->input('TA');
            $jns = $request->input('Jns');

            $kodefikasiprogram = KodefikasiProgramModel::create([
                'PrgID' => Uuid::uuid4()->toString(),                                              
                'Kd_Program' => $request->input('Kd_Program'),
                'Nm_Program' => strtoupper($request->input('Nm_Program')),
                'Jns' => $request->input('Jns'),
                'Descr' => $request->input('Descr'),
                'TA'=>$ta,
            ]);
            if ($jns == 1)  // per urusan
            {
                KodefikasiUrusanProgramModel::create ([
                    'UrsPrgID'=>Uuid::uuid4()->toString(),
                    'BidangID'=>$request->input('BidangID'),
                    'PrgID'=>$kodefikasiprogram->PrgID,
                    'Descr'=>$kodefikasiprogram->Descr,
                    'TA'=>$kodefikasiprogram->TA,
                ]);
            }
            return $kodefikasiprogram;
        });
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
                                        'Kd_Program'=>[                                                    
                                                    'required',
                                                    'regex:/^[0-9]+$/',
                                                    new KodefikasiKodeProgramRule($request,'ignore',$kodefikasiprogram)
                                                ],
                                        'Nm_Program'=>'required',
                                    ]);
            
            $kodefikasiprogram = \DB::transaction(function () use ($request, $kodefikasiprogram) {
                $kodefikasiprogram->Jns = $request->input('Jns');
                $kodefikasiprogram->Kd_Program = $request->input('Kd_Program');
                $kodefikasiprogram->Nm_Program = strtoupper($request->input('Nm_Program'));
                $kodefikasiprogram->Descr = $request->input('Descr');
                $kodefikasiprogram->save();
                
                \DB::table('tmUrusanProgram')
                        ->where('PrgID', $kodefikasiprogram->PrgID)
                        ->delete();

                if ($request->input('Jns') == 1)
                {
                    KodefikasiUrusanProgramModel::create ([
                        'UrsPrgID'=>Uuid::uuid4()->toString(),
                        'BidangID'=>$request->input('BidangID'),
                        'PrgID'=>$kodefikasiprogram->PrgID,
                        'Descr'=>$kodefikasiprogram->Descr,
                        'TA'=>$kodefikasiprogram->TA,
                    ]);
                }            
                return $kodefikasiprogram;
            });
            
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'kodefikasiprogram'=>$kodefikasiprogram,                                    
                                    'message'=>'Data Kodefikasi Program '.$kodefikasiprogram->Nm_Program.' berhasil diubah.'
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