<?php

namespace App\Http\Controllers\DMaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\RekeningAKunModel;

use Illuminate\Validation\Rule;

use Ramsey\Uuid\Uuid;

class RekeningAkunController extends Controller {              
  /**
   * get all akun rekening
   *
   * @return \Illuminate\Http\Response
   */
  public function index (Request $request)
  {
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-AKUN_BROWSE');

    $this->validate($request, [        
      'TA' => 'required'
    ]);    
    $ta = $request->input('TA');
    $akun=RekeningAKunModel::select(\DB::raw('
                    `AkunID`,                                        
                    `Kd_Rek_1`,
                    `Nm_Akun`,
                    CONCAT(\'[\',`Kd_Rek_1`,\'] \',`Nm_Akun`) AS `nama_rek1`,
                    `Descr`,
                    `TA`
                  '))
                  ->orderBy('Kd_Rek_1','ASC')                                    
                  ->where('TA', $ta)
                  ->get();

    return Response()->json([
                  'status' => 1,
                  'pid' => 'fetchdata',
                  'akun' => $akun,
                  'message' => 'Fetch data rekening akun berhasil.'
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-AKUN_STORE');

    $this->validate($request, [
      'Kd_Rek_1'=> [
            Rule::unique('tmAkun')->where(function($query) use ($request) {
              return $query->where('TA', $request->input('TA'));
            }),
            'required',
            'regex:/^[0-9]+$/'],
      'Nm_Akun' => 'required',
      'TA' => 'required'
    ]);     
      
    $ta = $request->input('TA');
    
    $akun = RekeningAKunModel::create([
      'AkunID' => Uuid::uuid4()->toString(),
      'Kd_Rek_1' => $request->input('Kd_Rek_1'),
      'Nm_Akun' => $request->input('Nm_Akun'),
      'Descr' => $request->input('Descr'),
      'TA' => $ta,
    ]);

    return Response()->json([
                  'status' => 1,
                  'pid' => 'store',
                  'akun' => $akun,                                    
                  'message' => 'Data Rekening Akun berhasil disimpan.'
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

		\DB::table('tmAkun')
		->where('TA', $tahun_tujuan)
		->whereRaw('AkunID_Src IS NOT NULL')
		->delete();

		$str_insert = '
			INSERT INTO `tmAkun` (
				`AkunID`,
        `Kd_Rek_1`,
        `Nm_Akun`,
        `Descr`, 
        `TA`,
        `AkunID_Src`,
        created_at,
        updated_at
			)		
			SELECT
				uuid() AS id,
				 
        t1.`Kd_Rek_1`, 
        t1.`Nm_Akun`,         
				"DI IMPOR DARI TAHUN '.$tahun_asal.'" AS `Descr`,
				'.$tahun_tujuan.' AS `TA`,
				t1.AkunID AS AkunID_Src,
				NOW() AS created_at,
				NOW() AS updated_at
			FROM tmAkun t1			
			WHERE t1.`TA`='.$tahun_asal.'      
		';        
		
    \DB::statement($str_insert);

    \DB::commit();

		return Response()->json([
			'status' => 1,
			'pid' => 'store',            
			'message'=>"Salin rekening akun dari tahun anggaran $tahun_asal berhasil."
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
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-AKUN_UPDATE');

    $akun = RekeningAKunModel::find($id);
    
    if (is_null($akun))
    {
      return Response()->json([
                  'status'=>0,
                  'pid' => 'update',                
                  'message'=>["Data Rekening Akun ($id) gagal diupdate"]
                ], 422); 
    }
    else
    {
      $this->validate($request, [    
                    'Kd_Rek_1'=>[
                          Rule::unique('tmAkun')->where(function($query) use ($request,$akun) {  
                            if ($request->input('Kd_Rek_1') == $akun->Kd_Rek_1) 
                            {
                              return $query->where('Kd_Rek_1','ignore')
                                    ->where('TA', $akun->TA);
                            }                 
                            else
                            {
                              return $query->where('Kd_Rek_1', $request->input('Kd_Rek_1'))
                                  ->where('TA', $akun->TA);
                            }                                                                                    
                          }),
                          'required',
                          'regex:/^[0-9]+$/'
                        ],
                    'Nm_Akun' => 'required',
                  ]);
      
      
      $akun->Kd_Rek_1 = $request->input('Kd_Rek_1');
      $akun->Nm_Akun = $request->input('Nm_Akun');
      $akun->Descr = $request->input('Descr');
      $akun->save();

      return Response()->json([
                  'status' => 1,
                  'pid' => 'update',
                  'akun' => $akun,                                    
                  'message' => 'Data Rekening Akun '.$akun->Nm_Akun.' berhasil diubah.'
                ], 200);
    }
    
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $uuid
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, $id)
  {   
    $this->hasPermissionTo('DMASTER-KODEFIKASI-REKENING-AKUN_DESTROY');

    $akun = RekeningAKunModel::find($id);

    if (is_null($akun))
    {
      return Response()->json([
                  'status'=>0,
                  'pid' => 'destroy',                
                  'message'=>["Data Rekening Akun ($id) gagal dihapus"]
                ], 422); 
    }
    else
    {
      
      $result = $akun->delete();

      return Response()->json([
                  'status' => 1,
                  'pid' => 'destroy',                
                  'message'=>"Data Rekening Akun dengan ID ($id) berhasil dihapus"
                ], 200);
    }
  }
}