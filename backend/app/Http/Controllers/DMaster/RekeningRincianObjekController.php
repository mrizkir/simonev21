<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\RekeningRincianObjekModel;
use App\Models\DMaster\RekeningSubRincianObjekModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class RekeningRincianObjekController extends Controller {              
  /**
   * get all rincian objek
   *
   * @return \Illuminate\Http\Response
   */
  public function index (Request $request)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_BROWSE');

    $this->validate($request, [        
      'TA' => 'required'
    ]);    
    $ta = $request->input('TA');

    $rincianobjek=RekeningRincianObjekModel::select(\DB::raw('
                    `tmROby`.`RObyID`,                                        
                    `tmROby`.`ObyID`,                                        
                    `tmROby`.`Kd_Rek_5`,
                    `tmROby`.`RObyNm`,
                    CONCAT(`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'.\',`Kd_Rek_5`) AS `kode_rincian_objek`,
                    CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'] \',`ObyNm`) AS `nama_rek4`,
                    CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'.\',`Kd_Rek_5`,\'] \',`RObyNm`) AS `nama_rek5`,
                    `tmROby`.`Descr`,
                    `tmROby`.`TA`
                  '))
                  ->join('tmOby', 'tmOby.ObyID', 'tmROby.ObyID')
                  ->join('tmJns', 'tmJns.JnsID', 'tmOby.JnsID')
                  ->join('tmKlp', 'tmJns.KlpID', 'tmKlp.KlpID')
                  ->join('tmAkun', 'tmAkun.AkunID', 'tmKlp.AkunID')
                  ->where('tmROby.TA', $ta)
                  ->orderBy('Kd_Rek_1', 'ASC')
                  ->orderBy('Kd_Rek_2', 'ASC')
                  ->orderBy('Kd_Rek_3', 'ASC')
                  ->orderBy('Kd_Rek_4', 'ASC')
                  ->orderBy('Kd_Rek_5', 'ASC')
                  ->get();

    return Response()->json([
                  'status' => 1,
                  'pid' => 'fetchdata',
                  'rincianobjek' => $rincianobjek,
                  'message' => 'Fetch data rincian objek berhasil.'
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_STORE');

    $this->validate($request, [
      'ObyID' => 'required|exists:tmOby,ObyID',
      'Kd_Rek_5'=> [
            Rule::unique('tmROby')->where(function($query) use ($request) {
              return $query->where('ObyID', $request->input('ObyID'))
                    ->where('TA', $request->input('TA'));
            }),
            'required',
            'regex:/^[0-9]+$/'],
      'RObyNm' => 'required',
      'TA' => 'required'
    ]);     
      
    $ta = $request->input('TA');
    
    $rincianobjek = RekeningRincianObjekModel::create([
      'RObyID' => Uuid::uuid4()->toString(),
      'ObyID' => $request->input('ObyID'),
      'Kd_Rek_5' => $request->input('Kd_Rek_5'),
      'RObyNm' => $request->input('RObyNm'),
      'Descr' => $request->input('Descr'),
      'TA' => $ta,
    ]);

    return Response()->json([
                  'status' => 1,
                  'pid' => 'store',
                  'objek' => $rincianobjek,                                    
                  'message' => 'Data Rekening Rincian Objek berhasil disimpan.'
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

		\DB::table('tmROby')
		->where('TA', $tahun_tujuan)
		->whereRaw('RObyID_Src IS NOT NULL')
		->delete();

		$str_insert = '
			INSERT INTO `tmROby` (
				`RObyID`,
				`ObyID`,
        `Kd_Rek_5`,
        `RObyNm`,
        `Descr`, 
        `TA`,
        `RObyID_Src`,
        created_at,
        updated_at
			)		
			SELECT
				uuid() AS id,
				 
        t2.ObyID,
        t1.`Kd_Rek_5`, 
        t1.`RObyNm`,         
				"DI IMPOR DARI TAHUN '.$tahun_asal.'" AS `Descr`,
				'.$tahun_tujuan.' AS `TA`,
				t1.RObyID AS RObyID_Src,
				NOW() AS created_at,
				NOW() AS updated_at
			FROM `tmROby` t1		
      JOIN `tmOby` t2 ON t1.ObyID=t2.ObyID_Src	
			WHERE t1.`TA`='.$tahun_asal.'      
		';        
		
    \DB::statement($str_insert);

    \DB::commit();

		return Response()->json([
			'status' => 1,
			'pid' => 'store',            
			'message'=>"Salin rekening rincian objek dari tahun anggaran $tahun_asal berhasil."
		], 200);
	}
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {        
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_UPDATE');

    $rincianobjek = RekeningRincianObjekModel::find($id);
    
    if (is_null($rincianobjek))
    {
      return Response()->json([
                  'status' => 0,
                  'pid' => 'update',                
                  'message' => ["Data Rekening Rincian Objek ($id) gagal diupdate"]
                ], 422); 
    }
    else
    {
      $this->validate($request, [    
                    'ObyID' => 'required|exists:tmOby,ObyID',
                    'Kd_Rek_5' => [
                          Rule::unique('tmROby')->where(function($query) use ($request, $rincianobjek) {  
                            if ($request->input('Kd_Rek_5') == $rincianobjek->Kd_Rek_5) 
                            {
                              return $query->where('ObyID', $request->input('ObyID'))
                                    ->where('Kd_Rek_5', 'ignore')
                                    ->where('TA', $rincianobjek->TA);
                            }                 
                            else
                            {
                              return $query->where('Kd_Rek_5', $request->input('Kd_Rek_5'))
                                  ->where('ObyID', $request->input('ObyID'))
                                  ->where('TA', $rincianobjek->TA);
                            }                                                                                    
                          }),
                          'required',
                          'regex:/^[0-9]+$/'
                        ],
                    'RObyNm' => 'required',
                  ]);
      
      
      $rincianobjek->ObyID = $request->input('ObyID');
      $rincianobjek->Kd_Rek_5 = $request->input('Kd_Rek_5');
      $rincianobjek->RObyNm = $request->input('RObyNm');
      $rincianobjek->Descr = $request->input('Descr');
      $rincianobjek->save();

      return Response()->json([
                  'status' => 1,
                  'pid' => 'update',
                  'objek' => $rincianobjek,                                    
                  'message' => 'Data Rekening Rincian Objek '.$rincianobjek->RObyNm.' berhasil diubah.'
                ], 200);
    }
    
  }
  public function subrincianobjekrka (Request $request, $id)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_BROWSE');

    $subrincianobjek=RekeningSubRincianObjekModel::select(\DB::raw('
                    `tmSubROby`.`SubRObyID`,                                        
                    CONCAT(`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'.\',`Kd_Rek_5`,\'.\',`Kd_Rek_6`) AS `kode_uraian`,
                    CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'.\',`Kd_Rek_5`,\'.\',`Kd_Rek_6`,\'] \',`SubRObyNm`) AS `nama_rek6`,
                    `tmSubROby`.`SubRObyNm`
                  '))
                  ->join('tmROby', 'tmROby.RObyID', 'tmSubROby.RObyID')
                  ->join('tmOby', 'tmOby.ObyID', 'tmROby.ObyID')
                  ->join('tmJns', 'tmJns.JnsID', 'tmOby.JnsID')
                  ->join('tmKlp', 'tmJns.KlpID', 'tmKlp.KlpID')
                  ->join('tmAkun', 'tmAkun.AkunID', 'tmKlp.AkunID')
                  ->where('tmROby.RObyID', $id)
                  ->orderBy('Kd_Rek_1', 'ASC')
                  ->orderBy('Kd_Rek_2', 'ASC')
                  ->orderBy('Kd_Rek_3', 'ASC')
                  ->orderBy('Kd_Rek_4', 'ASC')
                  ->orderBy('Kd_Rek_5', 'ASC')
                  ->orderBy('Kd_Rek_6', 'ASC')
                  ->get();

    return Response()->json([
                  'status' => 1,
                  'pid' => 'fetchdata',
                  'subrincianobjek' => $subrincianobjek,
                  'message'=>"Fetch data sub rincian objek dari rincian objek ($id) berhasil."
                ], 200);

  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $uuid
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {   
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_DESTROY');

    $rincianobjek = RekeningRincianObjekModel::find($id);

    if (is_null($rincianobjek))
    {
      return Response()->json([
                  'status' => 0,
                  'pid' => 'destroy',                
                  'message' => ["Data Rekening Rincian Objek ($id) gagal dihapus"]
                ], 422); 
    }
    else
    {
      
      $result = $rincianobjek->delete();

      return Response()->json([
                  'status' => 1,
                  'pid' => 'destroy',                
                  'message'=>"Data Rekening Rincian Objek dengan ID ($id) berhasil dihapus"
                ], 200);
    }
  }
}