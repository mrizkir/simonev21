<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\OrganisasiModel;
use App\Models\DMaster\SIPDModel;

class DataMentahMurniController extends Controller 
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {             
        $this->hasPermissionTo('RENJA-RKA-MURNI_BROWSE');
        
        $this->validate($request, [            
            'tahun'=>'required',            
            'OrgID'=>'required|exists:tmOrg,OrgID',            
        ]);
        $tahun = $request->input('tahun');
        $OrgID = $request->input('OrgID');
        $organisasi = OrganisasiModel::find($OrgID);
     
        $data = \DB::table('sipd')
                        ->select(\DB::raw('                            
                            DISTINCT(kd_keg_gabung) AS kode_kegiatan,                            
                            `Kd_Urusan1` AS `Kd_Urusan`,
                            `Nm_Urusan`,
                            `Kd_Bidang`,
                            `Nm_Bidang_Urusan` AS `Nm_Bidang`,
                            nm_kegiatan AS `KgtNm`,
                            kd_prog_gabungan AS kode_program,                                                        
                            nm_program AS `PrgNm`,
                            0 AS `PaguDana1`,
                            \'BELUM DICOPY\' AS status,
                            `TA`
                        '))
                        ->where('OrgID',$OrgID)
                        ->where('TA',$tahun)
                        ->where('EntryLevel',1)
                        ->orderBy('kode_program','ASC')
                        ->orderBy('kode_kegiatan','ASC')
                        ->get();        
        
        $data->transform(function($item,$key) use ($organisasi) {
            $rka = \DB::table('trRKA')
                        ->where('OrgID',$organisasi->OrgID)
                        ->where('TA',$organisasi->TA)
                        ->where('EntryLvl',1)
                        ->where('kode_kegiatan',$item->kode_kegiatan)
                        ->get();
            $item->Kd_Urusan=$item->Kd_Urusan;
            $item->Kd_Bidang=$item->Kd_Urusan.'.'.$item->Kd_Bidang;
            if (isset($rka[0]))
            {
                $item->status='SUDAH DICOPY';
            }
            $item->PaguDana1=\DB::table('sipd')
                                ->where('EntryLevel',1)
                                ->where('TA',$organisasi->TA)
                                ->where('kd_keg_gabung',$item->kode_kegiatan)
                                ->sum('PaguUraian1');
                            
            return $item;
        });
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'organisasi'=>$organisasi,
                                'rka'=>$data,
                                'message'=>'Fetch data rka perubahan berhasil diperoleh'
                            ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);              
    }   
    public function copyrka(Request $request)
    {
        $this->validate($request, [             
            'OrgID'=>'required|exists:tmOrg,OrgID',            
            'kode_kegiatan'=>'required',            
        ]);
        
        $OrgID=$request->input('OrgID');
        $opd = OrganisasiModel::find($OrgID);
        $kode_kegiatan = $request->input('kode_kegiatan');
        
        $user_id=$this->getUserid();

        $str_insert = '
        INSERT INTO `trRKA` (
            `RKAID`,
            `OrgID`,
		    `SOrgID`,
            kode_urusan,
            kode_bidang,
            kode_organisasi,
            `Nm_Organisasi`,
            kode_sub_organisasi,
            `Nm_Sub_Organisasi`,
            kode_program,
            kode_kegiatan,
            kode_sub_kegiatan,
            `Nm_Urusan`,
            `Nm_Bidang`,
            `Nm_Program`,
            `Nm_Kegiatan`,
            `Nm_Sub_Kegiatan`,
            `PaguDana1`,
            `RealisasiKeuangan1`,
            `RealisasiKeuangan2`,
            `user_id`,
            `EntryLvl`,
            `Descr`,
            `TA`,
            `Locked`,
            created_at,
            updated_at
        )
        SELECT
            uuid() AS `RKAID`,
            `OrgID`,
		    `SOrgID`,
            kode_urusan,
            kode_bidang,
            kode_organisasi,
            `Nm_Organisasi`,
            kode_sub_organisasi,
            `Nm_Sub_Organisasi`,
            kode_program,
            kode_kegiatan,
            kode_sub_kegiatan,
            `Nm_Urusan`,
            `Nm_Bidang_Urusan`,
            `Nm_Program`,
            `Nm_Kegiatan`,
            `Nm_Sub_Kegiatan`,
            `PaguDana1`,
            `RealisasiKeuangan1`,
            `RealisasiKeuangan2`,
            `user_id`,
            `EntryLevel`,
            `Descr`,
            `TA`,
            `Locked`,
            created_at,
            updated_at
        FROM
        (
            SELECT 
                DISTINCT(kd_sub_keg_gabung) AS kode_sub_kegiatan,
                `OrgID`,
                `SOrgID`,
                `kd_Urusan1` AS kode_urusan,
                CONCAT(`kd_Urusan1`,\'.\',`kd_Bidang`) AS kode_bidang,
                kode_organisasi,
                `Nm_Organisasi`,
                kode_sub_organisasi,
                `Nm_Sub_Organisasi`,
                kd_prog_gabungan AS kode_program,
                kd_keg_gabung AS kode_kegiatan,		
                `Nm_Urusan`,
                `Nm_Bidang_Urusan`,
                `Nm_Program`,
                `Nm_Kegiatan`,
                `Nm_Sub_Kegiatan`,
                0 AS `PaguDana1`,
                0 AS `RealisasiKeuangan1`,
                0 AS `RealisasiKeuangan2`,
                \''.$this->getUserid().'\' AS `user_id`,
                1 AS `EntryLevel`,
                \'IMPORTED FROM SIPD\' AS `Descr`,
                '.$opd->TA.' AS `TA`,
                false AS `Locked`,
                NOW() AS created_at,
                NOW() AS updated_at
            FROM sipd WHERE kd_keg_gabung=\''.$kode_kegiatan.'\' AND 
                            `OrgID`=\''.$OrgID.'\' AND
                            `TA`='.$opd->TA.' AND 
                            `EntryLevel`=1
        ) AS temp
        ';
        \DB::statement($str_insert); 
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',                                
                                'message'=>'Salin kegiatan dari data mentar ke RKA Murni berhasil'
                            ], 200);    
    }
}