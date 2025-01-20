<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\System\ConfigurationModel;
use App\Models\System\LockedOPDModel;

use Ramsey\Uuid\Uuid;

class VariablesController extends Controller 
{    
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {   
    $this->hasPermissionTo('SYSTEM-SETTING-VARIABLES_BROWSE'); 
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'setting'=>ConfigurationModel::getCache(),
      'message' => 'Fetch data seluruh setting variabel'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  public function show(Request $request, $id)
  {
    $code = 422;
    $status = 0;
    $data = null;

    $message = "Isi variables ($id) gagal diperoleh";
    switch($id)
    {
      case 203:
        $this->validate($request, [            
          'tahun' => 'required',          
        ]);

        $tahun = $request->input('tahun');

        $data = \DB::table('lockedopd')
          ->select(\DB::raw('`OrgID`, Locked'))
          ->where('TA', $tahun)
          ->where('Bulan', 0)
          ->first();
        
        $masa_pelaporan = 'murni';
        if (!is_null($data))
        {
          $masa_pelaporan = ($data->Locked == 10 || $data->Locked = 0) ? 'murni' : 'perubahan';
        }

        $status = 1;
        $data = [
          'masa_pelaporan' => $masa_pelaporan,
        ];
        $code = 200;
        $message = "Isi variables ($id) berhasil diperoleh";
      break;
    }
    return Response()->json([
      'status' => $status,
      'pid' => 'fetch',  
      'result' => $data,              
      'message'=> $message,
    ], $code); 
  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    $this->hasPermissionTo('SYSTEM-SETTING-VARIABLES_UPDATE');

    $this->validate($request, [ 
      'pid' => 'required',                               
      'setting' => [
        'required',                
      ],                     
    ],
    [
      'name.required' => 'Setting mohon untuk di isi',
    ]);        
    $pid = $request->input('pid');
    $config=json_decode($request->input('setting'), true);
    
    foreach($config as $k=>$v)
    {
      \DB::table('configuration')->where('config_id', $k)->update(['config_value' => $v]);      
    }

    ConfigurationModel::clear();
    ConfigurationModel::toCache();

    $config = ConfigurationModel::getCache(); 
    $tahun = $config['DEFAULT_TA'];

    $daftar_opd = \DB::table('tmOrg')
    ->where('TA', $tahun)
    ->select(\DB::raw('
      OrgID
    '))
    ->orderBy('kode_organisasi', 'ASC')
    ->get();   

    foreach($daftar_opd as $opd)
    {
      \DB::table('lockedopd')
        ->where('OrgID', $opd->OrgID)
        ->where('TA', $tahun)
        ->where('Bulan', 0)
        ->delete();

      LockedOPDModel::create([
        'lockedid' => Uuid::uuid4()->toString(), 
        'OrgID' => $opd->OrgID,
        'TA' => $tahun,
        'Bulan' => 0,
        'Locked' => $config['DEFAULT_MASA_PELAPORAN'] == 'murni' ? 10 : 11,
      ]);   
    }
    return Response()->json([
      'status' => 1,
      'pid' => 'update',       
      'config' => $config,                                                               
      'message'=>"Data setting $pid berhasil diubah."
    ], 200); 
  }
  public function clear(Request $request)
  {
    ConfigurationModel::clear();
    ConfigurationModel::toCache();
    return Response()->json([
      'status' => 1,
      'pid' => 'update',                                                                    
      'message'=>"Cache sudah dikosongkan dan direload ulang setting berhasil."
    ], 200); 
  }
}