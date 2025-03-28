<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\RekeningJenisModel;
use App\Models\DMaster\RekeningObjekModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class RekeningJenisController extends Controller {              
  /**
   * get all jenis rekening
   *
   * @return \Illuminate\Http\Response
   */
  public function index (Request $request)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-JENIS_BROWSE');

    $this->validate($request, [        
      'TA' => 'required'
    ]);    
    $ta = $request->input('TA');

    $jenis=RekeningJenisModel::select(\DB::raw('
                    `tmJns`.`JnsID`,                                        
                    `tmJns`.`KlpID`,                                        
                    `tmJns`.`Kd_Rek_3`,
                    `tmJns`.`JnsNm`,    
                    CONCAT(`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`) AS `kode_jenis`,                                    
                    CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'] \',`KlpNm`) AS `nama_rek2`,
                    CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'] \',`JnsNm`) AS `nama_rek3`,
                    `tmJns`.`Descr`,
                    `tmJns`.`TA`
                  '))
                  ->join('tmKlp', 'tmJns.KlpID', 'tmKlp.KlpID')
                  ->join('tmAkun', 'tmAkun.AkunID', 'tmKlp.AkunID')
                  ->where('tmKlp.TA', $ta)
                  ->orderBy('Kd_Rek_1', 'ASC')
                  ->orderBy('Kd_Rek_2', 'ASC')
                  ->orderBy('Kd_Rek_3', 'ASC')
                  ->get();

    return Response()->json([
                  'status' => 1,
                  'pid' => 'fetchdata',
                  'jenis' => $jenis,
                  'message' => 'Fetch data rekening jenis berhasil.'
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-JENIS_STORE');

    $this->validate($request, [
      'KlpID' => 'required|exists:tmKlp,KlpID',
      'Kd_Rek_3'=> [
            Rule::unique('tmJns')->where(function($query) use ($request) {
              return $query->where('KlpID', $request->input('KlpID'))
                    ->where('TA', $request->input('TA'));
            }),
            'required',
            'regex:/^[0-9]+$/'],
      'JnsNm' => 'required',
      'TA' => 'required'
    ]);     
      
    $ta = $request->input('TA');
    
    $jenis = RekeningJenisModel::create([
      'JnsID' => Uuid::uuid4()->toString(),
      'KlpID' => $request->input('KlpID'),
      'Kd_Rek_3' => $request->input('Kd_Rek_3'),
      'JnsNm' => $request->input('JnsNm'),
      'Descr' => $request->input('Descr'),
      'TA' => $ta,
    ]);

    return Response()->json([
                  'status' => 1,
                  'pid' => 'store',
                  'jenis' => $jenis,                                    
                  'message' => 'Data Rekening Jenis berhasil disimpan.'
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

		\DB::table('tmJns')
		->where('TA', $tahun_tujuan)
		->whereRaw('JnsID_Src IS NOT NULL')
		->delete();

		$str_insert = '
			INSERT INTO `tmJns` (
				`JnsID`,
				`KlpID`,
        `Kd_Rek_3`,
        `JnsNm`,
        `Descr`, 
        `TA`,
        `JnsID_Src`,
        created_at,
        updated_at
			)		
			SELECT
				uuid() AS id,
				 
        t2.KlpID,
        t1.`Kd_Rek_3`, 
        t1.`JnsNm`,         
				"DI IMPOR DARI TAHUN '.$tahun_asal.'" AS `Descr`,
				'.$tahun_tujuan.' AS `TA`,
				t1.JnsID AS JnsID_Src,
				NOW() AS created_at,
				NOW() AS updated_at
			FROM `tmJns` t1		
      JOIN `tmKlp` t2 ON t1.KlpID=t2.KlpID_Src	
			WHERE t1.`TA`='.$tahun_asal.'      
		';        
		
    \DB::statement($str_insert);

    \DB::commit();

		return Response()->json([
			'status' => 1,
			'pid' => 'store',            
			'message'=>"Salin rekening jenis dari tahun anggaran $tahun_asal berhasil."
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-JENIS_UPDATE');

    $jenis = RekeningJenisModel::find($id);
    
    if (is_null($jenis))
    {
      return Response()->json([
                  'status' => 0,
                  'pid' => 'update',                
                  'message' => ["Data Rekening Jenis ($id) gagal diupdate"]
                ], 422); 
    }
    else
    {
      $this->validate($request, [    
                    'KlpID' => 'required|exists:tmKlp,KlpID',
                    'Kd_Rek_3' => [
                          Rule::unique('tmJns')->where(function($query) use ($request, $jenis) {  
                            if ($request->input('Kd_Rek_3') == $jenis->Kd_Rek_3) 
                            {
                              return $query->where('KlpID', $request->input('KlpID'))
                                    ->where('Kd_Rek_3', 'ignore')
                                    ->where('TA', $jenis->TA);
                            }                 
                            else
                            {
                              return $query->where('Kd_Rek_3', $request->input('Kd_Rek_3'))
                                  ->where('KlpID', $request->input('KlpID'))
                                  ->where('TA', $jenis->TA);
                            }                                                                                    
                          }),
                          'required',
                          'regex:/^[0-9]+$/'
                        ],
                    'JnsNm' => 'required',
                  ]);
      
      
      $jenis->KlpID = $request->input('KlpID');
      $jenis->Kd_Rek_3 = $request->input('Kd_Rek_3');
      $jenis->JnsNm = $request->input('JnsNm');
      $jenis->Descr = $request->input('Descr');
      $jenis->save();

      return Response()->json([
                  'status' => 1,
                  'pid' => 'update',
                  'jenis' => $jenis,                                    
                  'message' => 'Data Rekening Jenis '.$jenis->JnsNm.' berhasil diubah.'
                ], 200);
    }
    
  }
  public function objekrka (Request $request, $id)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-OBJEK_BROWSE');

    $objek=RekeningObjekModel::select(\DB::raw('
                    `tmOby`.`ObyID`,                                        
                    CONCAT(\'[\',`Kd_Rek_1`,\'.\',`Kd_Rek_2`,\'.\',`Kd_Rek_3`,\'.\',`Kd_Rek_4`,\'] \',`ObyNm`) AS `nama_rek4`                                        
                  '))                                    
                  ->join('tmJns', 'tmJns.JnsID', 'tmOby.JnsID')
                  ->join('tmKlp', 'tmJns.KlpID', 'tmKlp.KlpID')
                  ->join('tmAkun', 'tmAkun.AkunID', 'tmKlp.AkunID')
                  ->where('tmOby.JnsID', $id)
                  ->orderBy('Kd_Rek_1', 'ASC')
                  ->orderBy('Kd_Rek_2', 'ASC')
                  ->orderBy('Kd_Rek_3', 'ASC')
                  ->orderBy('Kd_Rek_4', 'ASC')                                    
                  ->get();

    return Response()->json([
                  'status' => 1,
                  'pid' => 'fetchdata',
                  'objek' => $objek,
                  'message'=>"Fetch data objek dari rekening jenis ($id) berhasil."
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-JENIS_DESTROY');

    $jenis = RekeningJenisModel::find($id);

    if (is_null($jenis))
    {
      return Response()->json([
                  'status' => 0,
                  'pid' => 'destroy',                
                  'message' => ["Data Rekening Jenis ($id) gagal dihapus"]
                ], 422); 
    }
    else
    {
      
      $result = $jenis->delete();

      return Response()->json([
                  'status' => 1,
                  'pid' => 'destroy',                
                  'message'=>"Data Rekening Jenis dengan ID ($id) berhasil dihapus"
                ], 200);
    }
  }
}