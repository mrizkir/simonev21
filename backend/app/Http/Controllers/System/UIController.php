<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\System\ConfigurationModel;
use App\Models\DMaster\TAModel;

use App\Helpers\Helper;

class UIController extends Controller {
    /**
     * digunakan untuk mendapatkan setting variabel ui frontend
     */
    public function frontend ()
    {
        $config = ConfigurationModel::getCache();        
        $identitas['nama_app']=$config['NAMA_APP'];
        $identitas['nama_app_alias']=$config['NAMA_APP_ALIAS'];
        $identitas['nama_opd']=$config['NAMA_OPD'];
        $identitas['nama_opd_alias']=$config['NAMA_OPD_ALIAS'];       

        $theme=[
            'V-SYSTEM-BAR-CSS-CLASS'=>$config['V-SYSTEM-BAR-CSS-CLASS'],
            'V-APP-BAR-CSS-CLASS'=>$config['V-APP-BAR-CSS-CLASS'],
            'V-APP-BAR-COLOR'=>$config['V-APP-BAR-COLOR'],
            'V-APP-BAR-NAV-ICON-CSS-CLASS'=>$config['V-APP-BAR-NAV-ICON-CSS-CLASS'],
            'V-NAVIGATION-DRAWER-CSS-CLASS'=>$config['V-NAVIGATION-DRAWER-CSS-CLASS'],
            'V-LIST-ITEM-BOARD-CSS-CLASS'=>$config['V-LIST-ITEM-BOARD-CSS-CLASS'],
            'V-LIST-ITEM-BOARD-COLOR'=>$config['V-LIST-ITEM-BOARD-COLOR'],
            'V-LIST-ITEM-ACTIVE-CSS-CLASS'=>$config['V-LIST-ITEM-ACTIVE-CSS-CLASS'],            
        ];
        $daftar_ta=TAModel::select(\DB::raw('tahun AS value,tahun AS text'))
                                ->orderBy('tahun','asc')
                                ->get();

        $bulan=Helper::getNamaBulan();
        $daftar_bulan = [];
        foreach ($bulan as $k=>$v)
        {
            $daftar_bulan[]=[
                'text'=>$v,
                'value'=>$k,
            ];
        }
        $tahun_anggaran = $config['DEFAULT_TA'];
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',                                    
                                    'tahun_anggaran'=>$tahun_anggaran,
                                    'bulan_realisasi'=>$tahun_anggaran < date('Y') ? 12 : date('m'),
                                    'identitas'=>$identitas,       
                                    'daftar_ta'=>$daftar_ta,                             
                                    'daftar_bulan'=>$daftar_bulan,                             
                                    'theme'=>$theme,
                                    'message'=>'Fetch data ui untuk front berhasil diperoleh'
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
    /**
     * digunakan untuk mendapatkan setting variabel ui admin
     */
    public function admin ()
    {
        return Response()->json([
            'status'=>1,
            'pid'=>'fetchdata',                                                                           
            'message'=>'Fetch data ui untuk admin berhasil diperoleh'
        ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
