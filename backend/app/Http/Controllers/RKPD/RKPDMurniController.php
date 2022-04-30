<?php

namespace App\Http\Controllers\RKPD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;

use Ramsey\Uuid\Uuid;

class RKPDMurniController extends Controller 
{
  private function load(Request $request)
  {
    $tahun=$request->input('tahun');
		$no_bulan=$request->input('no_bulan');

    $urusan=\DB::table('tmUrusan')
    ->select(\DB::raw('
      `UrsID`,                                        
      `Kd_Urusan`,
      `Nm_Urusan`        
    '))
    ->orderBy('Kd_Urusan','ASC')                                    
    ->where('TA',$tahun)
    ->get();
    
    $now = \Carbon\Carbon::now('Asia/Jakarta')->toDateTimeString();
    
    \DB::beginTransaction();

    \DB::table('RKPD')
    ->where('TA', $tahun)
    ->delete();

    $no_urut = 1;
    foreach($urusan as $item_u)
    {
      //urusan
      $data=[
        'RKPDID' => Uuid::uuid4()->toString(),
        'kode' => $item_u->Kd_Urusan,
        'nama' => $item_u->Nm_Urusan,
        'level' => 1,
        'no_urut' => $no_urut,
        'TA' => $tahun,
        'BulanLaporan' => $no_bulan,
        'created_at' => $now,
        'updated_at' => $now,
      ];
      \DB::table('RKPD')->insert($data);
      $no_urut = $no_urut + 1;
      
      //bidang urusan
      $bidang_urusan = \DB::table('tmBidangUrusan')
      ->select(\DB::raw('
        `BidangID`,
        `Kd_Bidang`,
        `Nm_Bidang`        
      '))
      ->where('UrsID', $item_u->UrsID)
      ->orderBy('Kd_Bidang','ASC')                                    
      ->get();

      if ($bidang_urusan->count() > 0)
      {
        foreach($bidang_urusan as $item_bu)
        {
          $data=[
            'RKPDID' => Uuid::uuid4()->toString(),
            'kode' => $item_u->Kd_Urusan .'.'.$item_bu->Kd_Bidang,
            'nama' => $item_bu->Nm_Bidang,
            'level' => 2,
            'no_urut' => $no_urut,
            'TA' => $tahun,
            'BulanLaporan' => $no_bulan,
            'created_at' => $now,
            'updated_at' => $now,
          ];
          \DB::table('RKPD')->insert($data);
          $no_urut = $no_urut + 1;

          //program
          $program = \DB::table('tmProgram AS A')
          ->select(\DB::raw("
            A.`PrgID`,
            CONCAT(D.`Kd_Urusan`,'.',C.`Kd_Bidang`,'.',A.`Kd_Program`) AS kode_program,        
            A.`Nm_Program`        
          "))
          ->join('tmUrusanProgram AS B','A.PrgID','B.PrgID')
          ->join('tmBidangUrusan AS C','C.BidangID','B.BidangID')
          ->join('tmUrusan AS D','C.UrsID','D.UrsID')
          ->where('B.BidangID', $item_bu->BidangID)
          ->orderBy('D.Kd_Urusan','ASC')                                    
          ->orderBy('C.Kd_Bidang','ASC')                                    
          ->orderBy('A.Kd_Program','ASC')                                    
          ->get();
          
          if ($program->count() > 0)
          {
            foreach($program as $item_p)
            {
              $data=[
                'RKPDID' => Uuid::uuid4()->toString(),
                'kode' => $item_p->kode_program,
                'nama' => $item_p->Nm_Program,
                'level' => 3,
                'no_urut' => $no_urut,
                'TA' => $tahun,
                'BulanLaporan' => $no_bulan,
                'created_at' => $now,
                'updated_at' => $now,
              ];
              \DB::table('RKPD')->insert($data);
              $no_urut = $no_urut + 1;

              //kegiatan
              $kegiatan = \DB::table('tmKegiatan AS A')
              ->select(\DB::raw("
                A.`KgtID`,
                CONCAT(D.`Kd_Urusan`,'.',C.`Kd_Bidang`,'.',A1.`Kd_Program`,'.',`A`.`Kd_Kegiatan`) AS kode_kegiatan,
                A.`Nm_Kegiatan`        
              "))
              ->join('tmProgram AS A1','A.PrgID','A1.PrgID')
              ->join('tmUrusanProgram AS B','A1.PrgID','B.PrgID')
              ->join('tmBidangUrusan AS C','C.BidangID','B.BidangID')
              ->join('tmUrusan AS D','C.UrsID','D.UrsID')
              ->where('A.PrgID', $item_p->PrgID)
              ->orderBy('D.Kd_Urusan','ASC')                                    
              ->orderBy('C.Kd_Bidang','ASC')                                    
              ->orderBy('A1.Kd_Program','ASC')                                    
              ->orderBy('A.Kd_Kegiatan','ASC')                                    
              ->get();

              if ($kegiatan->count() > 0)
              {
                foreach($kegiatan as $item_k)
                {
                  $data=[
                    'RKPDID' => Uuid::uuid4()->toString(),
                    'kode' => $item_k->kode_kegiatan,
                    'nama' => $item_k->Nm_Kegiatan,
                    'level' => 4,
                    'no_urut' => $no_urut,
                    'TA' => $tahun,
                    'BulanLaporan' => $no_bulan,
                    'created_at' => $now,
                    'updated_at' => $now,
                  ];
                  \DB::table('RKPD')->insert($data);
                  $no_urut = $no_urut + 1;  
                  
                  //sub kegiatan
                  $sub_kegiatan = \DB::table('tmSubKegiatan AS A')
                    ->select(\DB::raw("
                      A.`SubKgtID`,
                      CONCAT(F.`Kd_Urusan`,'.',E.`Kd_Bidang`,'.',C.`Kd_Program`,'.',`B`.`Kd_Kegiatan`,'.',`A`.`Kd_SubKegiatan`) AS kode_sub_kegiatan,
                      A.`Nm_SubKegiatan`        
                    "))
                    ->join('tmKegiatan AS B','A.KgtID','B.KgtID')
                    ->join('tmProgram AS C','B.PrgID','C.PrgID')
                    ->join('tmUrusanProgram AS D','C.PrgID','D.PrgID')
                    ->join('tmBidangUrusan AS E','D.BidangID','E.BidangID')
                    ->join('tmUrusan AS F','F.UrsID','E.UrsID')
                    ->where('A.KgtID', $item_k->KgtID)
                    ->orderBy('F.Kd_Urusan','ASC')                                    
                    ->orderBy('E.Kd_Bidang','ASC')                                    
                    ->orderBy('C.Kd_Program','ASC')                                    
                    ->orderBy('B.Kd_Kegiatan','ASC')                                    
                    ->orderBy('A.Kd_SubKegiatan','ASC')                                    
                    ->get();

                  if ($sub_kegiatan->count() > 0)
                  {
                    foreach($sub_kegiatan as $item_s)
                    {
                      $data=[
                        'RKPDID' => Uuid::uuid4()->toString(),
                        'kode' => $item_s->kode_sub_kegiatan,
                        'nama' => $item_s->Nm_SubKegiatan,
                        'level' => 5,
                        'no_urut' => $no_urut,
                        'TA' => $tahun,
                        'BulanLaporan' => $no_bulan,
                        'created_at' => $now,
                        'updated_at' => $now,
                      ];
                      \DB::table('RKPD')->insert($data);
                      $no_urut = $no_urut + 1;
                    }
                  }
                  else
                  {
                    $data=[
                      'RKPDID' => Uuid::uuid4()->toString(),
                      'kode' => 'N.A',
                      'nama' => 'TIDAK MEMILIKI SUB KEGIATAN',
                      'level' => 10,
                      'no_urut' => $no_urut,
                      'TA' => $tahun,
                      'BulanLaporan' => $no_bulan,
                      'created_at' => $now,
                      'updated_at' => $now,
                    ];
                    \DB::table('RKPD')->insert($data);
                    $no_urut = $no_urut + 1;   
                  }
                }                
              }
              else
              {
                $data=[
                  'RKPDID' => Uuid::uuid4()->toString(),
                  'kode' => 'N.A',
                  'nama' => 'TIDAK MEMILIKI KEGIATAN',
                  'level' => 10,
                  'no_urut' => $no_urut,
                  'TA' => $tahun,
                  'BulanLaporan' => $no_bulan,
                  'created_at' => $now,
                  'updated_at' => $now,
                ];
                \DB::table('RKPD')->insert($data);
                $no_urut = $no_urut + 1;   
              }
            }            
          }  
          else
          {
            $data=[
              'RKPDID' => Uuid::uuid4()->toString(),
              'kode' => 'N.A',
              'nama' => 'TIDAK MEMILIKI PROGRAM',
              'level' => 10,
              'no_urut' => $no_urut,
              'TA' => $tahun,
              'BulanLaporan' => $no_bulan,
              'created_at' => $now,
              'updated_at' => $now,
            ];
            \DB::table('RKPD')->insert($data);
            $no_urut = $no_urut + 1;
          }      
        }
      } // endif bidang urusan
      else
      {
        $data=[
          'RKPDID' => Uuid::uuid4()->toString(),
          'kode' => 'N.A',
          'nama' => 'TIDAK MEMILIKI BIDANG URUSAN',
          'level' => 10,
          'no_urut' => $no_urut,
          'TA' => $tahun,
          'BulanLaporan' => $no_bulan,
          'created_at' => $now,
          'updated_at' => $now,
        ];
        \DB::table('RKPD')->insert($data);
        $no_urut = $no_urut + 1;
      }
    }     

    \DB::commit();
    
  }
  public function index(Request $request)
  {
    $this->hasPermissionTo('RKPD-GROUP');

    $this->validate($request, [            
			'tahun'=>'required',
			'no_bulan'=>'required'            
		]);

    $tahun=$request->input('tahun');
		$no_bulan=$request->input('no_bulan');

    $this->load($request);

    $data = \DB::table('RKPD')
    ->where('TA',$tahun)
    ->where('BulanLaporan', $no_bulan)
    ->orderByRaw('no_urut + 1')
    ->get();
    
    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata', 
      'rkpd'=>$data,
      'message'=>'Fetch data rkpd murni berhasil diperoleh'
    ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);      
  }
}