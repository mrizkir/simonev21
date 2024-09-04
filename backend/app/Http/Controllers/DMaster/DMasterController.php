<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DMasterController extends Controller
{ 
  public function index(Request $request)
  {
    $this->validate($request, [            
			'ta' => 'required',            			
		]);
    $ta = $request->input('ta');

    $jumlah_urusan = \DB::table('tmBidangUrusan')
    ->where('TA', $ta)
    ->count();

    $jumlah_program =  \DB::table('tmProgram')
    ->where('TA', $ta)
    ->count();

    $jumlah_kegiatan =  \DB::table('tmKegiatan')
    ->where('TA', $ta)
    ->count();

    $jumlah_sub_kegiatan =  \DB::table('tmSubKegiatan')
    ->where('TA', $ta)
    ->count();

    $dmaster = [
      'jumlah_urusan' => $jumlah_urusan,
      'jumlah_program' => $jumlah_program,
      'jumlah_kegiatan' => $jumlah_kegiatan,
      'jumlah_sub_kegiatan' => $jumlah_sub_kegiatan,
    ];
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'dmaster' => $dmaster,
      'message' => 'Fetch data master berhasil diperoleh'
  ], 200); 
  }
}