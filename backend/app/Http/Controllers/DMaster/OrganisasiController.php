<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Statistik1Model;
use App\Helpers\Helper;

use Ramsey\Uuid\Uuid;

class OrganisasiController extends Controller {     
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {                
        $this->hasPermissionTo('DMASTER-OPD_BROWSE');

        $this->validate($request, [            
            'tahun'=>'required',            
        ]);             
        $tahun=$request->input('tahun');

        if ($this->hasRole(['superadmin','bapelitbang']))
        {
            $data = OrganisasiModel::where('TA',$tahun)
                                ->orderBy('kode_organisasi','ASC')
                                ->get();            
        }       
        else if ($this->hasRole('opd'))
        {
            $daftar_opd=json_decode($user->payload,true);
            $data = OrganisasiModel::where('TA',$tahun)
                                ->whereIn('OrgID',$daftar_opd)
                                ->orderBy('kode_organisasi','ASC')
                                ->get();
        }        

        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'opd'=>$data,
                                'jumlah_apbd'=>$data->sum('PaguDana1'),
                                'jumlah_apbdp'=>$data->sum('PaguDana2'),
                                'message'=>'Fetch data opd berhasil diperoleh'
                            ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);    
        
    }
    /**
     * load data opd 
     *
     * @return \Illuminate\Http\Response
     */
    public function loadpaguapbdp(Request $request)
    {        
        $this->hasPermissionTo('DMASTER-OPD_STORE');
        
        $this->validate($request, [            
            'tahun'=>'required',            
        ]); 
        $tahun=$request->input('tahun');
        
        $str_statistik_opd1 = '
            UPDATE `tmOrg` SET `PaguDana2`=level1.`PaguUraian2` FROM
            (SELECT kode_organisasi,SUM(`PaguUraian2`) AS `PaguUraian2` FROM simda WHERE `TA`='.$tahun.' AND `EntryLvl`=2 GROUP BY kode_organisasi) AS level1
            WHERE level1.kode_organisasi=`tmOrg`.kode_organisasi AND `TA`='.$tahun.'
        ';
        
        \DB::statement($str_statistik_opd1); 

        $str_update_jumlah_program = 'UPDATE `tmOrg` SET `JumlahProgram2`=level3.jumlah_program FROM ( 
            SELECT kode_organisasi, COUNT(kode_program) jumlah_program FROM (
                SELECT * FROM 
                    (SELECT kode_organisasi, kode_program FROM simda WHERE `TA`='.$tahun.' AND `EntryLvl`=2 GROUP BY `kode_program`,kode_organisasi ORDER BY kode_program ASC) AS level1
            ) AS level2 GROUP BY kode_organisasi ORDER BY kode_organisasi
        ) AS level3 WHERE level3.kode_organisasi=`tmOrg`.kode_organisasi';

        \DB::statement($str_update_jumlah_program); 
        
        $str_update_jumlah_kegiatan = 'UPDATE `tmOrg` SET `JumlahKegiatan2`=level2.jumlah_kegiatan FROM 
        (
            SELECT kode_organisasi,COUNT(kode_kegiatan) AS jumlah_kegiatan FROM 
            (
                SELECT 
                    DISTINCT(kode_kegiatan),				
                    `kode_organisasi`				
                FROM simda WHERE `TA`='.$tahun.' AND `EntryLvl`=2
                ORDER BY kode_organisasi ASC
            ) AS level1 GROUP BY kode_organisasi
        ) AS level2 WHERE level2.kode_organisasi=`tmOrg`.kode_organisasi';
        
        \DB::statement($str_update_jumlah_kegiatan);

        $data = OrganisasiModel::where('TA',$tahun)->get();
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',
                                'opd'=>$data,
                                'jumlah_apbd'=>$data->sum('PaguDana1'),
                                'jumlah_apbdp'=>$data->sum('PaguDana2'),
                                'message'=>'Fetch data opd berhasil diperoleh'
                            ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
    }    
    /**
     * STORE the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('DMASTER-OPD_STORE');

        $this->validate($request, [
            'BidangID_1'=>'required|exists:tmBidangUrusan,BidangID',
            'kode_bidang_1'=>'required',
            'Nm_Bidang_1'=>'required',            

            'kode_organisasi'=>'required',
            'Kd_Organisasi'=>'required',
            'Nm_Organisasi'=>'required',
            'Alias_Organisasi'=>'required',
            'Alamat'=>'required|min:5',

            'NamaKepalaSKPD'=>'required|min:5',
            'NIPKepalaSKPD'=>'required|min:5',

            'TA'=>'required|numeric',
        ]);
        
        $BidangID_2 = null;
        $kode_bidang_2 = null;
        $Nm_Bidang_2 = null;

        $BidangID_3 = null;
        $kode_bidang_3 = null;
        $Nm_Bidang_3 = null;

        if ($request->filled('BidangID_2')) {
            $BidangID_2 = $request->input('BidangID_2');
            $kode_bidang_2 = $request->input('kode_bidang_2');
            $Nm_Bidang_2 = $request->input('Nm_Bidang_2');
        }

        if ($request->filled('BidangID_3')) {
            $BidangID_3 = $request->input('BidangID_3');
            $kode_bidang_3 = $request->input('kode_bidang_3');
            $Nm_Bidang_3 = $request->input('Nm_Bidang_3');
        }
        $organisasi = OrganisasiModel::create([
            'OrgID' => Uuid::uuid4()->toString(), 
        
            'BidangID_1'=>$request->input('BidangID_1'),         
            'kode_bidang_1'=>$request->input('kode_bidang_1'),
            'Nm_Bidang_1'=>$request->input('Nm_Bidang_1'),
            
            'BidangID_2'=>$BidangID_2,         
            'kode_bidang_2'=>$kode_bidang_2,         
            'Nm_Bidang_2'=>$Nm_Bidang_2,         

            'BidangID_3'=>$BidangID_3,         
            'kode_bidang_3'=>$kode_bidang_3,         
            'Nm_Bidang_3'=>$Nm_Bidang_3,         

            'kode_organisasi'=>$request->input('kode_organisasi'), 
            'Kd_Organisasi'=>$request->input('Kd_Organisasi'), 
            'Nm_Organisasi'=>$request->input('Nm_Organisasi'), 
            'Alias_Organisasi'=>$request->input('Alias_Organisasi'),                
            'Alamat'=>$request->input('Alamat'), 
            'NamaKepalaSKPD'=>$request->input('NamaKepalaSKPD'), 
            'NIPKepalaSKPD'=>$request->input('NIPKepalaSKPD'), 

            'TA'=>$request->input('TA'), 
        ]);        
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',
                                'opd'=>$organisasi,                                    
                                'message'=>'Data organisasi '.$organisasi->OrgNm.' berhasil disimpan.'
                            ], 200); 
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->hasPermissionTo('DMASTER-OPD_UPDATE');

        $this->validate($request, [            
            'BidangID_1'=>'required|exists:tmBidangUrusan,BidangID',
            'kode_bidang_1'=>'required',
            'Nm_Bidang_1'=>'required',            

            'kode_organisasi'=>'required',
            'Kd_Organisasi'=>'required',
            'Nm_Organisasi'=>'required',
            'Alias_Organisasi'=>'required',
            'Alamat'=>'required|min:5',

            'NamaKepalaSKPD'=>'required|min:5',
            'NIPKepalaSKPD'=>'required|min:5',
        ]);

        $organisasi = OrganisasiModel::find($id);
        
        $organisasi->BidangID_1 = $request->input('BidangID_1');
        $organisasi->kode_bidang_1 = $request->input('kode_bidang_1');
        $organisasi->Nm_Bidang_1 = $request->input('Nm_Bidang_1');
        
        $BidangID_2 = null;
        $kode_bidang_2 = null;
        $Nm_Bidang_2 = null;

        $BidangID_3 = null;
        $kode_bidang_3 = null;
        $Nm_Bidang_3 = null;

        if ($request->filled('BidangID_2')) {
            $BidangID_2 = $request->input('BidangID_2');
            $kode_bidang_2 = $request->input('kode_bidang_2');
            $Nm_Bidang_2 = $request->input('Nm_Bidang_2');
        }

        if ($request->filled('BidangID_3')) {
            $BidangID_3 = $request->input('BidangID_3');
            $kode_bidang_3 = $request->input('kode_bidang_3');
            $Nm_Bidang_3 = $request->input('Nm_Bidang_3');
        }

        $organisasi->BidangID_2 = $BidangID_2;
        $organisasi->kode_bidang_2 = $kode_bidang_2;
        $organisasi->Nm_Bidang_2 = $Nm_Bidang_2;
        
        $organisasi->BidangID_3 = $BidangID_3;
        $organisasi->kode_bidang_3 = $kode_bidang_3;
        $organisasi->Nm_Bidang_3 = $Nm_Bidang_3;
        
        $organisasi->kode_organisasi = $request->input('kode_organisasi');
        $organisasi->Kd_Organisasi = $request->input('Kd_Organisasi');
        $organisasi->Nm_Organisasi = $request->input('Nm_Organisasi');
        $organisasi->Alias_Organisasi = $request->input('Alias_Organisasi');
        $organisasi->Alamat = $request->input('Alamat');
        $organisasi->NamaKepalaSKPD = $request->input('NamaKepalaSKPD');
        $organisasi->NIPKepalaSKPD = $request->input('NIPKepalaSKPD');
        
        $organisasi->Descr = $request->input('Descr');
        $organisasi->save();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'opd'=>$organisasi,                                    
                                    'message'=>'Data organisasi '.$organisasi->Nm_Organisasi.' berhasil diubah.'
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
        $this->hasPermissionTo('DMASTER-OPD_DESTROY');

        $organisasi = OrganisasiModel::find($id);

        if (is_null($organisasi))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data OPD ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            
            $result=$organisasi->delete();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Data OPD dengan ID ($id) berhasil dihapus"
                                ],200);
        }
    }
    /**
     * digunakan untuk mendapat unit kerja berdasarkan OrgID
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function opdunitkerja ($id)
    {
        $organisasi = OrganisasiModel::find($id);
        $unitkerja = $organisasi->unitkerja()->orderBy('kode_sub_organisasi','ASC')->get();        
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'organisasi'=>$organisasi,
                                    'unitkerja'=>$unitkerja,                                    
                                    'message'=>'Data unit kerja berdasarkan id '.$organisasi->OrgNm.' berhasil diubah.'
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
    }
    /**
     * digunakan untuk mendapat pejabat  berdasarkan OrgID
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pejabatopd ($id)
    {
        $pejabat = \DB::table('tmASN')
                        ->select(\DB::raw('`trRiwayatJabatanASN`.`ASNID`,`tmASN`.`Nm_ASN`,`Jenis_Jabatan`'))
                        ->join('trRiwayatJabatanASN','trRiwayatJabatanASN.ASNID','tmASN.ASNID')
                        ->where('trRiwayatJabatanASN.OrgID',$id)
                        ->get();

        $pa=[];
        $kpa=[];
        $ppk=[];
        $pptk=[];
        foreach ($pejabat as $item)
        {
            switch ($item->Jenis_Jabatan)
            {
                case 'pa' :
                    $pa[]=[
                        'text'=>$item->Nm_ASN,
                        'value'=>$item->ASNID
                    ];
                break;                    
                case 'kpa' :
                    $kpa[]=[
                        'text'=>$item->Nm_ASN,
                        'value'=>$item->ASNID
                    ];;
                break;                    
                case 'ppk' :
                    $ppk[]=[
                        'text'=>$item->Nm_ASN,
                        'value'=>$item->ASNID
                    ];
                break;                    
                case 'pptk' :
                    $pptk[]=[
                        'text'=>$item->Nm_ASN,
                        'value'=>$item->ASNID
                    ];
                break;                   
            }
        }        
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',                                    
                                    'pejabat'=>[
                                        'pa'=>$pa,
                                        'kpa'=>$kpa,
                                        'ppk'=>$ppk,
                                        'pptk'=>$pptk,
                                    ],                                    
                                    'message'=>'Data unit kerja berdasarkan id '.$id.' berhasil diubah.'
                                ],200); 
    }
}