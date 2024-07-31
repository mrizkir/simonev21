<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use App\Models\DMaster\IndikatorKinerjaModel;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;

class KodefikasiIndikatorKinerjaController extends Controller
{    
  /**
   * mendapatkan daftar seluruh ASN
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-INDIKATOR-KINERJA_BROWSE');
    
    $data = IndikatorKinerjaModel::orderBy('NamaIndikator', 'ASC')
      ->get();
    
    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'payload'=>$data,
      'message'=>'Fetch data indikator kinerja berhasil diperoleh'
    ], 200);  

  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {        
    $this->hasPermissionTo('DMASTER-ASN_STORE');
    $this->validate($request, [           
      'TA'=>'required'
    ]);

    $ta = $request->input('TA');

    $this->validate($request, [
      'NIP_ASN'=> [
        Rule::unique('tmASN')->where(function($query) use ($request) {
          return $query->where('NIP_ASN',$request->input('NIP_ASN'))
            ->where('TA',$request->input('TA'));
        }),
        'required',
        'regex:/^[0-9]+$/'
      ],
      'Nm_ASN'=>'required',
    ]);
        
    $asn = ASNModel::create ([
      'ASNID'=> Uuid::uuid4()->toString(),
      'NIP_ASN' => $request->input('NIP_ASN'),
      'Nm_ASN' => $request->input('Nm_ASN'),
      'Descr' => $request->input('Descr'),
      'TA'=>$ta,
    ]);  
    
    return Response()->json([
      'status'=>1,
      'pid'=>'store',
      'asn'=>$asn,                                    
      'message'=>'Data ASN berhasil disimpan.'
    ], 200); 		
  }
  /**
   * menyalin asli
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function salin(Request $request)
  {
    $this->validate($request, [            
      'tahun_asal'=>'required|numeric',
      'tahun_tujuan'=>'required|numeric|gt:tahun_asal',
    ]);

    $tahun_asal = $request->input('tahun_asal');
    $tahun_tujuan = $request->input('tahun_tujuan');

    $str_insert = '
      INSERT INTO `tmASN` (
        `ASNID`,
        `NIP_ASN`,
        `Nm_ASN`,
        `Descr`,
        `TA`,
        `Active`,        
        created_at,
        updated_at
      )		
      SELECT
        uuid() AS id,
        
        `NIP_ASN`,
        `Nm_ASN`,
        "DI IMPOR DARI TAHUN '.$tahun_asal.'" AS `Descr`,
        '.$tahun_tujuan.' AS `TA`,
        `Active`,                
        NOW() AS created_at,
        NOW() AS updated_at
      FROM tmASN t1
      WHERE `TA`='.$tahun_asal.'
      AND `NIP_ASN` NOT IN (SELECT `NIP_ASN` FROM `tmASN` WHERE `TA`='.$tahun_tujuan.')      			
    ';    
    
    \DB::statement($str_insert); 
    
    return Response()->json([
      'status'=>1,
      'pid'=>'store',            
      'message'=>"Salin ASN dari tahun anggaran $tahun_asal berhasil.",
      'sql_insert'=>$str_insert,
    ], 200);
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request,$id)
  {        
    $this->hasPermissionTo('DMASTER-ASN_UPDATE');
    $asn = ASNModel::find($id);
    
    $this->validate($request, [    
      'NIP_ASN'=>[
        Rule::unique('tmASN')->where(function($query) use ($request,$asn) {  
          if ($request->input('NIP_ASN')==$asn->NIP_ASN) 
          {
            return $query->where('NIP_ASN','ignore');
          }                 
          else
          {
            return $query->where('NIP_ASN',$request->input('NIP_ASN'))
                ->where('TA',$asn->TA);
          }                                                                                    
        }),
        'required',
        'regex:/^[0-9]+$/'
      ],
      'Nm_ASN'=>'required',
    ]);		
    
    $asn->NIP_ASN = $request->input('NIP_ASN');
    $asn->Nm_ASN = $request->input('Nm_ASN');
    $asn->Descr = $request->input('Descr');
    $asn->save();

    return Response()->json([
      'status'=>1,
      'pid'=>'update',
      'asn'=>$asn,                                    
      'message'=>'Data ASN '.$asn->Nm_ASN.' berhasil diubah.'
    ], 200);
    
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $uuid
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request,$id)
  {   
    $this->hasPermissionTo('DMASTER-ASN_DESTROY');
    $asn = ASNModel::find($id);
    $result=$asn->delete();
    return Response()->json([
      'status'=>1,
      'pid'=>'destroy',                
      'message'=>"Data ASN dengan ID ($id) berhasil dihapus"
    ], 200);
  }
}
