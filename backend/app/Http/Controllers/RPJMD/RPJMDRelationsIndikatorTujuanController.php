<?php

namespace App\Http\Controllers\RPJMD;

use App\Http\Controllers\Controller;
use App\Models\RPJMD\RPJMDRelasiIndikatorModel;
use Illuminate\Http\Request;

use App\Models\RPJMD\RPJMDTujuanModel;

use Ramsey\Uuid\Uuid;

class RPJMDRelationsIndikatorTujuanController extends Controller
{
  /**
   * mendapatkan daftar seluruh indikator
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_BROWSE');

    $this->validate($request, [      
      'PeriodeRPJMDID' => 'required|exists:tmRPJMDPeriode,PeriodeRPJMDID',
      'pid' => 'required|in:realisasi',      
    ]);

    $PeriodeRPJMDID = $request->input('PeriodeRPJMDID');
    $pid = $request->input('pid');
    
    $totalRecords = RPJMDTujuanModel::where('PeriodeRPJMDID', $PeriodeRPJMDID)->count('RpjmdMisiID');
    
    $data = RPJMDTujuanModel::select(\DB::raw('
      tmRpjmdTujuan.*,
      CONCAT(b.Kd_RpjmdMisi,".",tmRpjmdTujuan.Kd_RpjmdTujuan) AS kode_tujuan,
      "{}" AS indikator
    '))
    ->join('tmRpjmdMisi AS b', 'b.RpjmdMisiID', 'tmRpjmdTujuan.RpjmdMisiID')
    ->where('tmRpjmdTujuan.PeriodeRPJMDID', $PeriodeRPJMDID);
    
    if($request->filled('offset'))
    {
      $this->validate($request, [              
        'offset' => 'required|numeric',      
      ]);

      $offset = $request->input('offset');
      $data = $data->offset($offset);
    }

    if($request->filled('limit'))
    {
      $this->validate($request, [              
        'limit' => 'required|numeric|gt:0',   
      ]);

      $limit = $request->input('limit');
      $data = $data->limit($limit);
    }

    $indikatorkinerja = $data
    ->orderBy('kode_tujuan', 'asc')
    ->get()
    ->transform(function($item, $key) use ($pid) {
      switch($pid)
      {
        case 'realisasi':
          $data = \DB::table('tmRpjmdRelasiIndikator AS a')->select(\DB::raw('
            a.RpjmdRelasiIndikatorID,
            b.IndikatorKinerjaID,
            b.NamaIndikator,
            b.Satuan,
            b.Operasi,
            a.data_1 AS target_1,
            a.data_2 AS target_2,
            a.data_3 AS target_3,
            a.data_4 AS target_4,
            a.data_5 AS target_5,
            a.data_6 AS target_6,
            a.data_7 AS target_7,
            a.data_8 AS target_8,
            a.data_9 AS target_9,
            a.data_10 AS target_10,
            a.data_11 AS target_11,
            a.data_12 AS target_12,
            a.data_13 AS target_13,
            a.data_14 AS target_14,
            a.data_15 AS target_15,
            a.data_16 AS target_16,
            c.RpjmdRealisasiIndikatorID,
            c.data_1 AS realisasi_1,
            c.data_2 AS realisasi_2,
            c.data_3 AS realisasi_3,
            c.data_4 AS realisasi_4,
            c.data_5 AS realisasi_5,
            c.data_6 AS realisasi_6,
            c.data_7 AS realisasi_7,
            c.data_8 AS realisasi_8,
            c.data_9 AS realisasi_9,
            c.data_10 AS realisasi_10,
            c.data_11 AS realisasi_11,
            c.data_12 AS realisasi_12,
            c.data_13 AS realisasi_13,
            c.data_14 AS realisasi_14,
            c.data_15 AS realisasi_15,
            c.data_16 AS realisasi_16,
            a.created_at AS created_at_target,
            a.updated_at AS updated_at_target,
            c.created_at AS created_at_realisasi,
            c.updated_at AS updated_at_realisasi
          '))
          ->join('tmRPJMDIndikatorKinerja AS b', 'a.IndikatorKinerjaID', 'b.IndikatorKinerjaID')
          ->join('tmRpjmdRealisasiIndikator AS c', 'a.RpjmdRelasiIndikatorID', 'c.RpjmdRelasiIndikatorID');          
        break;
        default:
          $data = \DB::table('tmRpjmdRelasiIndikator AS a')->select(\DB::raw('
            a.RpjmdRelasiIndikatorID,
            b.IndikatorKinerjaID,
            b.NamaIndikator,
            b.Satuan,
            b.Operasi,
            a.data_1,
            a.data_2,
            a.data_3,
            a.data_4,
            a.data_5,
            a.data_6,
            a.data_7,
            a.data_8,
            a.data_9 ,
            a.data_10,
            a.data_11,
            a.data_12,
            a.data_13,
            a.data_14,
            a.data_15,
            a.data_16,          
            a.created_at,
            a.updated_at
          '))
          ->join('tmRPJMDIndikatorKinerja AS b', 'a.IndikatorKinerjaID', 'b.IndikatorKinerjaID');          
      }
      
      $item->indikator = $data->where('a.RpjmdCascadingID', $item->RpjmdTujuanID)
      ->get();

      return $item;
    });

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => [
        'data' => $indikatorkinerja,
        'totalRecords' => $totalRecords,
      ],
      'message' => 'Fetch data Indikator Tujuan berhasil diperoleh'
    ], 200);
  }
  public function show(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_SHOW');

    $indikatortujuan = [];

    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $indikatortujuan,                                    
      'message' => 'Data Indikator Tujuan berhasil disimpan.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  public function indikatortujuan(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_SHOW');

    $indikatortujuan = \DB::table('');
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'payload' => $indikatortujuan,                                    
      'message' => 'Data Indikator Tujuan berhasil diperoleh.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  public function store(Request $request)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_STORE');

    $this->validate($request, [
      'Operasi' => 'required|in:MAX,MIN,RANGE'
    ]);

    $Operasi = $request->input('Operasi');
    
    $rules = [      
      'IndikatorKinerjaID' => 'required|exists:tmRPJMDIndikatorKinerja,IndikatorKinerjaID',      
      'RpjmdCascadingID' => 'required|exists:tmRpjmdTujuan,RpjmdTujuanID',      
      'data_1' => 'required|numeric',      
      'data_2' => 'required|numeric',      
      'data_3' => 'required|numeric',      
      'data_4' => 'required|numeric',      
      'data_5' => 'required|numeric',      
      'data_6' => 'required|numeric',      
      'data_7' => 'required|numeric',      
      'data_8' => 'required|numeric',
    ];

    if($Operasi == 'RANGE')
    {
      $rules['data_9'] = 'required|numeric';
      $rules['data_10'] = 'required|numeric';
      $rules['data_11'] = 'required|numeric';
      $rules['data_12'] = 'required|numeric';
      $rules['data_13'] = 'required|numeric';
      $rules['data_14'] = 'required|numeric';
      $rules['data_15'] = 'required|numeric';

      $this->validate($request, $rules);

      $indikatortujuan = RPJMDRelasiIndikatorModel::create([
        'RpjmdRelasiIndikatorID' => Uuid::uuid4()->toString(),
        'IndikatorKinerjaID' => $request->input('IndikatorKinerjaID'),
        'RpjmdCascadingID' => $request->input('RpjmdCascadingID'),
        'PeriodeRPJMDID' => $request->input('PeriodeRPJMDID'),      
        'TipeCascading' => 'tujuan',      
        'data_1' => $request->input('data_1'),    
        'data_2' => $request->input('data_2'),    
        'data_3' => $request->input('data_3'),    
        'data_4' => $request->input('data_4'),    
        'data_5' => $request->input('data_5'),    
        'data_6' => $request->input('data_6'),    
        'data_7' => $request->input('data_7'),    
        'data_8' => $request->input('data_8'),    
        'data_9' => $request->input('data_9'),    
        'data_10' => $request->input('data_10'),    
        'data_11' => $request->input('data_11'),    
        'data_12' => $request->input('data_12'),    
        'data_13' => $request->input('data_13'),    
        'data_14' => $request->input('data_14'),            
        'data_15' => $request->input('data_15'),            
        'data_16' => 0,                            
        'data_17' => 0,
        'data_18' => 0,
        'data_19' => 0,
        'data_20' => 0,
      ]);
    }
    else
    {
      $this->validate($request, $rules); 

      $indikatortujuan = RPJMDRelasiIndikatorModel::create([
        'RpjmdRelasiIndikatorID' => Uuid::uuid4()->toString(),
        'IndikatorKinerjaID' => $request->input('IndikatorKinerjaID'),
        'RpjmdCascadingID' => $request->input('RpjmdCascadingID'),
        'PeriodeRPJMDID' => $request->input('PeriodeRPJMDID'),      
        'TipeCascading' => 'tujuan',      
        'data_1' => $request->input('data_1'),    
        'data_2' => $request->input('data_2'),    
        'data_3' => $request->input('data_3'),    
        'data_4' => $request->input('data_4'),    
        'data_5' => $request->input('data_5'),    
        'data_6' => $request->input('data_6'),    
        'data_7' => $request->input('data_7'),    
        'data_8' => $request->input('data_8'),    
        'data_9' => 0,    
        'data_10' => 0,
        'data_11' => 0,
        'data_12' => 0,
        'data_13' => 0,
        'data_14' => 0,
        'data_15' => 0,    
        'data_16' => 0,
        'data_17' => 0,
        'data_18' => 0,
        'data_19' => 0,
        'data_20' => 0,
      ]);
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'store',
      'payload' => $indikatortujuan,                                    
      'message' => 'Data Indikator Tujuan berhasil disimpan.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_UPDATE');

    $indikatortujuan = RPJMDRelasiIndikatorModel::find($id);

    if(is_null($indikatortujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Indikator Tujuan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    else
    {
      $this->validate($request, [
        'Operasi' => 'required|in:MAX,MIN,RANGE'
      ]);
  
      $Operasi = $request->input('Operasi');
      
      
      $rules = [
        'data_1' => 'required|numeric',      
        'data_2' => 'required|numeric',      
        'data_3' => 'required|numeric',      
        'data_4' => 'required|numeric',      
        'data_5' => 'required|numeric',      
        'data_6' => 'required|numeric',      
        'data_7' => 'required|numeric',      
        'data_8' => 'required|numeric',
      ];

      if($Operasi == 'RANGE')
      {
        $rules['data_9'] = 'required|numeric';
        $rules['data_10'] = 'required|numeric';
        $rules['data_11'] = 'required|numeric';
        $rules['data_12'] = 'required|numeric';
        $rules['data_13'] = 'required|numeric';
        $rules['data_14'] = 'required|numeric';
        $rules['data_15'] = 'required|numeric';

        $this->validate($request, $rules); 

        $indikatortujuan->data_1 = $request->input('data_1');
        $indikatortujuan->data_2 = $request->input('data_2');
        $indikatortujuan->data_3 = $request->input('data_3');
        $indikatortujuan->data_4 = $request->input('data_4');
        $indikatortujuan->data_5 = $request->input('data_5');
        $indikatortujuan->data_6 = $request->input('data_6');
        $indikatortujuan->data_7 = $request->input('data_7');
        $indikatortujuan->data_8 = $request->input('data_8');
        $indikatortujuan->data_9 = $request->input('data_9');
        $indikatortujuan->data_10 = $request->input('data_10');
        $indikatortujuan->data_11 = $request->input('data_11');
        $indikatortujuan->data_12 = $request->input('data_12');
        $indikatortujuan->data_13 = $request->input('data_13');
        $indikatortujuan->data_14 = $request->input('data_14');
        $indikatortujuan->data_15 = $request->input('data_15');
      }
      else
      {
        $this->validate($request, $rules); 

        $indikatortujuan->data_1 = $request->input('data_1');
        $indikatortujuan->data_2 = $request->input('data_2');
        $indikatortujuan->data_3 = $request->input('data_3');
        $indikatortujuan->data_4 = $request->input('data_4');
        $indikatortujuan->data_5 = $request->input('data_5');
        $indikatortujuan->data_6 = $request->input('data_6');
        $indikatortujuan->data_7 = $request->input('data_7');
        $indikatortujuan->data_8 = $request->input('data_8');
      }
      $indikatortujuan->save();
      
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'payload' => $indikatortujuan,                                    
        'message' => 'Data indikator tujuan berhasil disimpan.'
      ], 200); 
    }
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {
    $this->hasPermissionTo('RPJMD-INDIKASI-TUJUAN_DESTROY');

    $indikatortujuan = RPJMDRelasiIndikatorModel::find($id);

    if(is_null($indikatortujuan))
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => ["Data Indikator Tujuan dengan dengan ($id) gagal diperoleh"]
      ], 422); 
    }
    // else if($visi->misi->count('RpjmdMisiID') > 0)
    // {
    //   return Response()->json([
    //     'status' => 0,
    //     'pid' => 'fetchdata',
    //     'message' => ["RPJMD Misi dengan dengan ($id) gagal dihapus karena masih terhubung ke Misi"]
    //   ], 422); 
    // }
    else
    {
      $indikatortujuan->delete();

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Data Indikator Tujuan dengan ID ($id) berhasil dihapus"
      ], 200);
    }    
  }
}