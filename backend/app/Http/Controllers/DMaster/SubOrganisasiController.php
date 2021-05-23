<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\SubOrganisasiModel;
use App\Models\DMaster\OrganisasiModel;

use Ramsey\Uuid\Uuid;

class SubOrganisasiController extends Controller {     
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {                
        $this->hasPermissionTo('DMASTER-UNIT-KERJA_BROWSE');

        $tahun=$request->input('tahun');
        $this->validate($request, [            
            'tahun'=>'required',            
        ]);     
        
        $select = \DB::raw('
            tmSOrg.SOrgID,            
            tmOrg.OrgID,          
            tmSOrg.kode_sub_organisasi,
            tmSOrg.Kd_Sub_Organisasi,
            tmSOrg.Nm_Sub_Organisasi,
            tmSOrg.Alias_Sub_Organisasi,
            tmSOrg.Alamat,
            tmSOrg.NamaKepalaUnitKerja,
            tmSOrg.NIPKepalaUnitKerja,
            tmOrg.Nm_Bidang_1,
            tmOrg.Nm_Bidang_2,
            tmOrg.Nm_Bidang_3,
            tmSOrg.PaguDana1,
            tmSOrg.PaguDana2,
            tmSOrg.Descr,
            tmSOrg.created_at,
            tmSOrg.updated_at
        ');
        if ($this->hasRole(['superadmin','bapelitbang']))
        {
            $data = SubOrganisasiModel::select($select)
                                ->join('tmOrg','tmOrg.OrgID','tmSOrg.OrgID')
                                ->where('tmSOrg.TA',$tahun)
                                ->orderBy('kode_sub_organisasi','ASC')
                                ->get();
        }       
        else if ($this->hasRole('opd'))
        {
            $daftar_unitkerja=json_decode($user->payload,true);
            $data = SubOrganisasiModel::select($select)
                                ->join('tmOrg','tmOrg.OrgID','tmSOrg.OrgID')
                                ->where('tmSOrg.TA',$tahun)
                                ->whereIn('OrgID',$daftar_unitkerja)
                                ->orderBy('kode_sub_organisasi','ASC')
                                ->get();
        }
        

        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'unitkerja'=>$data,
                                'jumlah_apbd'=>$data->sum('PaguDana1'),
                                'jumlah_apbdp'=>$data->sum('PaguDana2'),
                                'message'=>'Fetch data unit kerjaberhasil diperoleh'
                            ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);    
        
    }
    /**
     * load data unit kerja 
     *
     * @return \Illuminate\Http\Response
     */
    public function loadpaguapbdp(Request $request)
    {        
        $this->hasPermissionTo('DMASTER-UNIT-KERJA_STORE');

        $this->validate($request, [            
            'tahun'=>'required',            
        ]);
        $tahun=$request->input('tahun');
        
        $str_update_jumlah_program = 'UPDATE `tmSOrg` SET `JumlahProgram2`=level3.jumlah_program FROM ( 
            SELECT kode_sub_organisasi, COUNT(kode_program) jumlah_program FROM (
                SELECT * FROM 
                    (SELECT kode_sub_organisasi, kode_program FROM sipd WHERE `TA`='.$tahun.' AND `EntryLvl`=2 GROUP BY `kode_program`,kode_sub_organisasi ORDER BY kode_program ASC) AS level1
            ) AS level2 GROUP BY kode_sub_organisasi ORDER BY kode_sub_organisasi
        ) AS level3 WHERE level3.kode_sub_organisasi=`tmSOrg`.kode_sub_organisasi';

        \DB::statement($str_update_jumlah_program); 
        
        $str_update_jumlah_kegiatan = 'UPDATE `tmSOrg` SET `JumlahKegiatan2`=level2.jumlah_kegiatan FROM 
        (
            SELECT kode_sub_organisasi,COUNT(kode_kegiatan) AS jumlah_kegiatan FROM 
            (
                SELECT 
                    DISTINCT(kode_kegiatan),				
                    `kode_sub_organisasi`				
                FROM sipd WHERE `TA`='.$tahun.' AND `EntryLvl`=2
                ORDER BY kode_sub_organisasi ASC
            ) AS level1 GROUP BY kode_sub_organisasi
        ) AS level2 WHERE level2.kode_sub_organisasi=`tmSOrg`.kode_sub_organisasi';

        \DB::statement($str_update_jumlah_kegiatan); 
        
        $str_statistik_unitkerja1 = '
            UPDATE `tmSOrg` SET `PaguDana2`=level1.`PaguUraian2` FROM
            (SELECT kode_sub_organisasi,SUM(`PaguUraian2`) AS `PaguUraian2` FROM sipd WHERE `TA`='.$tahun.' AND `EntryLvl`=2 GROUP BY kode_sub_organisasi) AS level1
            WHERE level1.kode_sub_organisasi=`tmSOrg`.kode_sub_organisasi AND `TA`='.$tahun.'
        ';

        \DB::statement($str_statistik_unitkerja1); 

        $data = SubOrganisasiModel::where('TA',$tahun)->get();
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',
                                'unitkerja'=>$data,
                                'jumlah_apbd'=>$data->sum('PaguDana1'),
                                'jumlah_apbdp'=>$data->sum('PaguDana2'),
                                'message'=>'Fetch data unit kerja berhasil diperoleh'
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
        $this->hasPermissionTo('DMASTER-UNIT-KERJA_STORE');

    $this->validate($request, [
            'OrgID'=>'required|exists:tmOrg,OrgID',
            'Kd_Sub_Organisasi'=>'required',
            'Nm_Sub_Organisasi'=>'required',
            'Alias_Sub_Organisasi'=>'required',
            'Alamat'=>'required|min:5',

            'NamaKepalaUnitKerja'=>'required|min:5',
            'NIPKepalaUnitKerja'=>'required|min:5',

            'TA'=>'required|numeric',
        ]);
        
        $organisasi = OrganisasiModel::find($request->input('OrgID'));
        $sub_organisasi = SubOrganisasiModel::create([
            'SOrgID' => Uuid::uuid4()->toString(), 
            'OrgID' => $request->input('OrgID'), 
            'kode_sub_organisasi'=>$organisasi->kode_organisasi.'.'.$request->input('Kd_Sub_Organisasi'), 
            'Kd_Sub_Organisasi'=>$request->input('Kd_Sub_Organisasi'), 
            'Nm_Sub_Organisasi'=>$request->input('Nm_Sub_Organisasi'), 
            'Alias_Sub_Organisasi'=>$request->input('Alias_Sub_Organisasi'),                
            'Alamat'=>$request->input('Alamat'), 
            'NamaKepalaUnitKerja'=>$request->input('NamaKepalaUnitKerja'), 
            'NIPKepalaUnitKerja'=>$request->input('NIPKepalaUnitKerja'), 

            'Descr'=>$request->input('Descr'), 
            'TA'=>$request->input('TA'), 
        ]);        
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',
                                'unitkerja'=>$sub_organisasi,                                    
                                'message'=>'Data sub organisasi '.$sub_organisasi->OrgNm.' berhasil disimpan.'
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
        $this->hasPermissionTo('DMASTER-UNIT-KERJA_UPDATE');

        $this->validate($request, [            
            'OrgID'=>'required|exists:tmOrg,OrgID',
            'Kd_Sub_Organisasi'=>'required',
            'Nm_Sub_Organisasi'=>'required',
            'Alias_Sub_Organisasi'=>'required',
            'Alamat'=>'required|min:5',

            'NamaKepalaUnitKerja'=>'required|min:5',
            'NIPKepalaUnitKerja'=>'required|min:5',
        ]);

        $sub_organisasi = SubOrganisasiModel::find($id);
        $sub_organisasi->OrgID = $request->input('OrgID');
        $sub_organisasi->Kd_Sub_Organisasi = $request->input('Kd_Sub_Organisasi');
        $sub_organisasi->Nm_Sub_Organisasi = $request->input('Nm_Sub_Organisasi');
        $sub_organisasi->Alias_Sub_Organisasi = $request->input('Alias_Sub_Organisasi');
        $sub_organisasi->Alamat = $request->input('Alamat');
        $sub_organisasi->NamaKepalaUnitKerja = $request->input('NamaKepalaUnitKerja');
        $sub_organisasi->NIPKepalaUnitKerja = $request->input('NIPKepalaUnitKerja');
        $sub_organisasi->Descr = $request->input('Descr');
        $sub_organisasi->save();
        
        // $str_update_jumlah_program = 'UPDATE `tmSOrg` SET `JumlahProgram1`=level3.jumlah_program FROM ( 
        //     SELECT kode_sub_organisasi, COUNT(kode_program) jumlah_program FROM (
        //         SELECT * FROM 
        //             (SELECT kode_sub_organisasi, kode_program FROM sipd WHERE `TA`='.$sub_organisasi->TA.' AND kode_sub_organisasi=\''.$sub_organisasi->kode_sub_organisasi.'\' AND `EntryLvl`=1 GROUP BY `kode_program`,kode_sub_organisasi ORDER BY kode_program ASC) AS level1
        //     ) AS level2 GROUP BY kode_sub_organisasi ORDER BY kode_sub_organisasi
        // ) AS level3 WHERE level3.kode_sub_organisasi=`tmSOrg`.kode_sub_organisasi';

        // \DB::statement($str_update_jumlah_program); 

        // $str_update_jumlah_kegiatan = 'UPDATE `tmSOrg` SET `JumlahKegiatan1`=level2.jumlah_kegiatan FROM 
        // (
        //     SELECT kode_sub_organisasi,COUNT(kode_kegiatan) AS jumlah_kegiatan FROM 
        //     (
        //         SELECT 
        //             DISTINCT(kode_kegiatan),				
        //             `kode_sub_organisasi`				
        //         FROM sipd WHERE `TA`='.$sub_organisasi->TA.' AND kode_sub_organisasi=\''.$sub_organisasi->kode_sub_organisasi.'\' AND `EntryLvl`=1
        //         ORDER BY kode_sub_organisasi ASC
        //     ) AS level1 GROUP BY kode_sub_organisasi
        // ) AS level2 WHERE level2.kode_sub_organisasi=`tmSOrg`.kode_sub_organisasi';

        // \DB::statement($str_update_jumlah_kegiatan);
        
        // $str_statistik_unitkerja1 = '
        //     UPDATE `tmSOrg` SET 
        //         `PaguDana1`=level1.`PaguUraian1`                
        //     FROM
        //         (
        //             SELECT 
        //                 kode_sub_organisasi,
        //                 SUM(`PaguUraian1`) AS `PaguUraian1`                                               
        //             FROM sipd 
        //             WHERE kode_sub_organisasi=\''.$sub_organisasi->kode_sub_organisasi.'\' 
        //             GROUP BY kode_sub_organisasi
        //         ) AS level1
        //     WHERE `tmSOrg`.kode_sub_organisasi=level1.kode_sub_organisasi
        // ';
        // \DB::statement($str_statistik_unitkerja1); 
        
        // $sub_organisasi = SubOrganisasiModel::find($id);

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'unitkerja'=>$sub_organisasi,                                    
                                    'message'=>'Data unit kerja '.$sub_organisasi->SOrgNm.' berhasil diubah.'
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
    
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {   
        $this->hasPermissionTo('DMASTER-UNIT-KERJA_DESTROY');

        $sub_organisasi = SubOrganisasiModel::find($id);

        if (is_null($sub_organisasi))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'destroy',                
                                    'message'=>["Data Unit Kerja dengan ($id) gagal dihapus"]
                                ], 422); 
        }
        else
        {
            
            $result=$sub_organisasi->delete();

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>"Data Unit Kerja dengan ID ($id) berhasil dihapus"
                                ],200);
        }
    }
}