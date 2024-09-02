<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\RekeningRincianObjekModel;
use App\Models\DMaster\RekeningSubRincianObjekModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class RekeningSubRincianObjekController extends Controller {              
  /**
   * get all sub rincian objek
   *
   * @return \Illuminate\Http\Response
   */
  public function index (Request $request)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_BROWSE');

    $this->validate($request, [        
      'TA' => 'required'
    ]);    
    $ta = $request->input('TA');

    $subrincianobjek=RekeningSubRincianObjekModel::select(\DB::raw('
                    `tmSubROby`.`SubRObyID`,                                        
                    `tmSubROby`.`RObyID`,                                        
                    `tmSubROby`.`Kd_Rek_6`,
                    `tmSubROby`.`SubRObyNm`,
                    CONCAT(`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'.\',`Kd_Rek_5`,\'.\',`Kd_Rek_6`) AS `kode_sub_rincian_objek`,
                    CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'.\',`Kd_Rek_5`,\'] \',`RObyNm`) AS `nama_rek5`,
                    CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'.\',`Kd_Rek_5`,\'.\',`Kd_Rek_6`,\'] \',`SubRObyNm`) AS `nama_rek6`,
                    `tmSubROby`.`Descr`,
                    `tmSubROby`.`TA`
                  '))
                  ->join('tmROby','tmROby.RObyID','tmSubROby.RObyID')
                  ->join('tmOby','tmOby.ObyID','tmROby.ObyID')
                  ->join('tmJns','tmJns.JnsID','tmOby.JnsID')
                  ->join('tmKlp','tmJns.KlpID','tmKlp.KlpID')
                  ->join('tmAkun','tmAkun.AkunID','tmKlp.AkunID')
                  ->where('tmROby.TA', $ta)
                  ->orderBy('Kd_Rek_1','ASC')
                  ->orderBy('Kd_Rek_2','ASC')
                  ->orderBy('Kd_Rek_3','ASC')
                  ->orderBy('Kd_Rek_4','ASC')
                  ->orderBy('Kd_Rek_5','ASC')
                  ->orderBy('Kd_Rek_6','ASC')
                  ->get();

    return Response()->json([
                  'status' => 1,
                  'pid' => 'fetchdata',
                  'subrincianobjek'=>$subrincianobjek,
                  'message' => 'Fetch data sub rincian objek berhasil.'
                ], 200);
  }
  /**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function salin(Request $request)
	{       
		$this->validate($request, [            
			'tahun_asal' => 'required|numeric',
			'tahun_tujuan' => 'required|numeric|gt:tahun_asal',
		]);

		$tahun_asal = $request->input('tahun_asal');
		$tahun_tujuan = $request->input('tahun_tujuan');

    \DB::beginTransaction();

		\DB::table('tmSubROby')
		->where('TA', $tahun_tujuan)
		->whereRaw('SubRObyID_Src IS NOT NULL')
		->delete();

		$str_insert = '
			INSERT INTO `tmSubROby` (
				`SubRObyID`,
				`RObyID`,
        `Kd_Rek_6`,
        `kode_rek_6`,
        `SubRObyNm`,
        `Descr`, 
        `TA`,
        `SubRObyID_Src`,
        created_at,
        updated_at
			)		
			SELECT
				uuid() AS id,
				 
        t2.RObyID,
        t1.`Kd_Rek_6`, 
        t1.`kode_rek_6`, 
        t1.`SubRObyNm`,         
				"DI IMPOR DARI TAHUN '.$tahun_asal.'" AS `Descr`,
				'.$tahun_tujuan.' AS `TA`,
				t1.SubRObyID AS SubRObyID_Src,
				NOW() AS created_at,
				NOW() AS updated_at
			FROM `tmSubROby` t1		
      JOIN `tmROby` t2 ON t1.RObyID=t2.RObyID_Src	
			WHERE t1.`TA`='.$tahun_asal.'      
		';        
		
    \DB::statement($str_insert);

    \DB::commit();

		return Response()->json([
			'status' => 1,
			'pid' => 'store',            
			'message'=>"Salin rekening sub rincian objek dari tahun anggaran $tahun_asal berhasil."
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_STORE');

    $this->validate($request, [
      'RObyID' => 'required|exists:tmROby,RObyID',
      'Kd_Rek_6'=> [
            Rule::unique('tmSubROby')->where(function($query) use ($request) {
              return $query->where('RObyID', $request->input('RObyID'))
                    ->where('TA', $request->input('TA'));
            }),
            'required',
            'regex:/^[0-9]+$/'],
      'SubRObyNm' => 'required',
      'TA' => 'required'
    ]);     
      
    $ta = $request->input('TA');
    $RObyID = $request->input('RObyID');

    $rincianobjek=RekeningRincianObjekModel::select(\DB::raw('                                        
                    CONCAT(`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'.\',`Kd_Rek_5`,\'.\') AS `kode_rincian_objek`                                        
                  '))
                  ->join('tmOby','tmOby.ObyID','tmROby.ObyID')
                  ->join('tmJns','tmJns.JnsID','tmOby.JnsID')
                  ->join('tmKlp','tmJns.KlpID','tmKlp.KlpID')
                  ->join('tmAkun','tmAkun.AkunID','tmKlp.AkunID')
                  ->where('tmROby.RObyID', $RObyID)                                    
                  ->first();

    $subrincianobjek = RekeningSubRincianObjekModel::create([
      'SubRObyID' => Uuid::uuid4()->toString(),
      'RObyID' => $request->input('RObyID'),
      'Kd_Rek_6' => $request->input('Kd_Rek_6'),
      'kode_rek_6' => $rincianobjek->kode_rincian_objek.$request->input('Kd_Rek_6'),
      'SubRObyNm' => $request->input('SubRObyNm'),
      'Descr' => $request->input('Descr'),
      'TA'=>$ta,
    ]);

    return Response()->json([
                  'status' => 1,
                  'pid' => 'store',
                  'subrincianobjek'=>$subrincianobjek,                                    
                  'message' => 'Data Rekening Rincian Objek berhasil disimpan.'
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_UPDATE');

    $subrincianobjek = RekeningSubRincianObjekModel::find($id);
    
    if (is_null($subrincianobjek))
    {
      return Response()->json([
                  'status'=>0,
                  'pid' => 'update',                
                  'message'=>["Data Rekening Rincian Objek ($id) gagal diupdate"]
                ], 422); 
    }
    else
    {
      $this->validate($request, [    
                    'RObyID' => 'required|exists:tmROby,RObyID',
                    'Kd_Rek_6'=>[
                          Rule::unique('tmSubROby')->where(function($query) use ($request,$subrincianobjek) {  
                            if ($request->input('Kd_Rek_6')= = $subrincianobjek->Kd_Rek_6) 
                            {
                              return $query->where('RObyID', $request->input('RObyID'))
                                    ->where('Kd_Rek_6','ignore')
                                    ->where('TA', $subrincianobjek->TA);
                            }                 
                            else
                            {
                              return $query->where('Kd_Rek_6', $request->input('Kd_Rek_6'))
                                  ->where('RObyID', $request->input('RObyID'))
                                  ->where('TA', $subrincianobjek->TA);
                            }                                                                                    
                          }),
                          'required',
                          'regex:/^[0-9]+$/'
                        ],
                    'SubRObyNm' => 'required',
                  ]);
      
      $RObyID = $request->input('RObyID');

      $rincianobjek=RekeningRincianObjekModel::select(\DB::raw('                                        
                      CONCAT(`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'.\',`Kd_Rek_5`,\'.\') AS `kode_rincian_objek`                                        
                    '))
                    ->join('tmOby','tmOby.ObyID','tmROby.ObyID')
                    ->join('tmJns','tmJns.JnsID','tmOby.JnsID')
                    ->join('tmKlp','tmJns.KlpID','tmKlp.KlpID')
                    ->join('tmAkun','tmAkun.AkunID','tmKlp.AkunID')
                    ->where('tmROby.RObyID', $RObyID)                                    
                    ->first();
                    
      $subrincianobjek->RObyID = $request->input('RObyID');
      $subrincianobjek->Kd_Rek_6 = $request->input('Kd_Rek_6');
      $subrincianobjek->kode_rek_6 = $rincianobjek->kode_rincian_objek.$request->input('Kd_Rek_6');
      $subrincianobjek->SubRObyNm = $request->input('SubRObyNm');
      $subrincianobjek->Descr = $request->input('Descr');
      $subrincianobjek->save();

      return Response()->json([
                  'status' => 1,
                  'pid' => 'update',
                  'subrincianobjek'=>$subrincianobjek,                                    
                  'message' => 'Data Rekening Rincian Objek '.$subrincianobjek->SubRObyNm.' berhasil diubah.'
                ], 200);
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_DESTROY');

    $subrincianobjek = RekeningSubRincianObjekModel::find($id);

    if (is_null($subrincianobjek))
    {
      return Response()->json([
                  'status'=>0,
                  'pid' => 'destroy',                
                  'message'=>["Data Rekening Rincian Objek ($id) gagal dihapus"]
                ], 422); 
    }
    else
    {
      
      $result = $subrincianobjek->delete();

      return Response()->json([
                  'status' => 1,
                  'pid' => 'destroy',                
                  'message'=>"Data Rekening Rincian Objek dengan ID ($id) berhasil dihapus"
                ], 200);
    }
  }
}