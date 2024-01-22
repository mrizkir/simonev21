<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\DMaster\SubOrganisasiModel;
use App\Models\Renja\RKAModel;
use App\Models\Renja\FormAPerubahanModel;
use App\Models\Statistik6Model;;

use Ramsey\Uuid\Uuid;

class FormAPerubahanController extends Controller
{
   /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('RENJA-FORM-A-PERUBAHAN_BROWSE');

    $this->validate($request, [
      'RKAID'=>'required',
      'no_bulan'=>'required',
    ]);
    $RKAID = $request->input('RKAID');
    $no_bulan = $request->input('no_bulan');

    $forma=new FormAPerubahanModel([],false);
    $rka = $forma->getDataRKA($RKAID,$no_bulan,2);
    $tingkat = $forma->getRekeningProyek();
    $data=[];
    $total_data=[
      'totalPaguDana'=>0,
      'totalPersenBobot'=>0,
      'totalRealisasiFisik'=>0,
      'totalPersenTertimbangFisikSatuKegiatan'=>0,
      'totalTargetSatuKegiatan'=>0,
      'totalRealisasiSatuKegiatan'=>0,
      'total_persen_rata2_realisasi'=>0,
      'totalPersenTertimbangRealisasiSatuKegiatan'=>0,
      'sisa_anggaran'=>0
    ];

    $chart_keuangan=[
      [
        0,0,0,0,0,0,0,0,0,0,0,0
      ],
      [
        0,0,0,0,0,0,0,0,0,0,0,0
      ]
    ];
    $chart_fisik=[
      [
        0,0,0,0,0,0,0,0,0,0,0,0
      ],
      [
        0,0,0,0,0,0,0,0,0,0,0,0
      ]
    ];

    if (is_null($rka))
    {
      return Response()->json([
        'status'=>0,
        'pid'=>'fetchdata',
        'message'=>'Fetch data form a perubahan gagal diperoleh'
      ], 422);
    }
    else if (isset($tingkat[1]) )
    {
      $tingkat_1=$tingkat[1];
      $tingkat_2=$tingkat[2];
      $tingkat_3=$tingkat[3];
      $tingkat_4=$tingkat[4];
      $tingkat_5=$tingkat[5];
      $tingkat_6=$tingkat[6];
      $totalPaguDana=0;
      $totalUraian=0;
      $totalTargetSatuKegiatan=0;
      $totalRealisasiSatuKegiatan=0;
      $totalPersenBobotSatuKegiatan=0;
      $totalPersenTargetSatuKegiatan=0;
      $totalPersenRealisasiSatuKegiatan=0;
      $totalPersenFisikSatuKegiatan=0;
      $totalPersenTertimbangFisikSatuKegiatan=0;
      foreach ($tingkat_1 as $k1=>$v1)
      {
        foreach ($tingkat_6 as $k6=>$v6)
        {
          $rek1=substr($k6,0,1);
          if ($rek1 == $k1)
          {
            //tingkat i

            $totalPaguDana_Rek1=\App\Models\Renja\FormAPerubahanModel::calculateEachLevel($rka,$k1,'Kd_Rek_1');
            $totalPaguDana=$totalPaguDana_Rek1['totalpagu'];
            $data[]=[
              'FormAPerubahanDetailID'=>Uuid::uuid4()->toString(),
              'tingkat'=>1,
              'kode'=>$k1,
              'nama_uraian'=>$v1,
              'totalPaguDana'=>$totalPaguDana,
              'persen_bobot'=>'',
              'persen_rata2_fisik'=>'',
              'persen_tertimbang_fisik'=>'',
              'total_target'=>'',
              'total_realisasi'=>'',
              'persen_realisasi'=>'',
              'persen_tertimbang_realisasi'=>'',
              'sisa_anggaran'=>'',
            ];
            //tingkat ii
            foreach ($tingkat_2 as $k2=>$v2)
            {
              $rek2_tampil=[];
              foreach ($tingkat_5 as $k5_level2=>$v5_level2) {
                $rek2=substr($k5_level2,0,3);
                if ($rek2 == $k2) {
                  if (!array_key_exists($k2,$rek2_tampil)) {
                    $rek2_tampil[$rek2]=$v2;
                  }
                }
              }
              foreach ($rek2_tampil as $a=>$b)
              {
                $totalPaguDana_Rek2=\App\Models\Renja\FormAPerubahanModel::calculateEachLevel($rka,$a,'Kd_Rek_2');
                $no_=explode ('.',$a);
                $data[]=[
                  'FormAPerubahanDetailID'=>Uuid::uuid4()->toString(),
                  'tingkat'=>2,
                  'kode'=>$no_[0].'.'.$no_[1],
                  'nama_uraian'=>$b,
                  'totalPaguDana'=>$totalPaguDana_Rek2['totalpagu'],
                  'persen_bobot'=>'',
                  'persen_rata2_fisik'=>'',
                  'persen_tertimbang_fisik'=>'',
                  'total_target'=>'',
                  'total_realisasi'=>'',
                  'persen_realisasi'=>'',
                  'persen_tertimbang_realisasi'=>'',
                  'sisa_anggaran'=>'',
                ];
                //tingkat iii
                foreach ($tingkat_3 as $k3=>$v3)
                {
                  $rek3=substr($k3,0,3);
                  if ($a==$rek3)
                  {
                    $totalPaguDana_Rek3=\App\Models\Renja\FormAPerubahanModel::calculateEachLevel($rka,$k3,'Kd_Rek_3');
                    $no_=explode (".",$k3);
                    $persen_bobot_rek3=Helper::formatPersen($totalPaguDana_Rek3['totalpersenbobot'],100);
                    $persen_target_rek3=Helper::formatPecahan($totalPaguDana_Rek3['totalpersentarget'],1);
                    $persen_realisasi_rek3=Helper::formatPecahan($totalPaguDana_Rek3['totalpersenrealisasi'],1);
                    $persen_tertimbang_realisasi_rek3=Helper::formatPecahan($totalPaguDana_Rek3['totalpersentertimbangrealisasi'],1);
                    $persen_rata2_fisik_rek3=Helper::formatPecahan($totalPaguDana_Rek3['totalfisik'],$totalPaguDana_Rek3['totalbaris']);
                    $persen_tertimbang_fisik_rek3=Helper::formatPecahan($totalPaguDana_Rek3['totalpersentertimbangfisik'],1);

                    $data[]=[
                      'FormAPerubahanDetailID'=>Uuid::uuid4()->toString(),
                      'tingkat'=>3,
                      'kode'=>$no_[0].'.'.$no_[1].'.'.$no_[2],
                      'nama_uraian'=>$v3,
                      'totalPaguDana'=>$totalPaguDana_Rek3['totalpagu'],
                      'persen_bobot'=>$persen_bobot_rek3,
                      'persen_rata2_fisik'=>$persen_rata2_fisik_rek3,
                      'persen_tertimbang_fisik'=>$persen_tertimbang_fisik_rek3,
                      'total_target'=>$totalPaguDana_Rek3['totaltarget'],
                      'total_realisasi'=>$totalPaguDana_Rek3['totalrealisasi'],
                      'persen_realisasi'=>$persen_realisasi_rek3,
                      'persen_tertimbang_realisasi'=>$persen_tertimbang_realisasi_rek3,
                      'sisa_anggaran'=>$totalPaguDana_Rek3['totalpagu']-$totalPaguDana_Rek3['totalrealisasi'],
                    ];
                    foreach ($tingkat_4 as $k4=>$v4)
                    {
                      if (preg_match("/^$k3/", $k4))
                      {
                        $totalPaguDana_Rek4=\App\Models\Renja\FormAPerubahanModel::calculateEachLevel($rka,$k4,'Kd_Rek_4');
                        $no_=explode (".",$k4);
                        $persen_bobot_rek4=Helper::formatPersen($totalPaguDana_Rek4['totalpersenbobot'],100);
                        $persen_target_rek4=Helper::formatPecahan($totalPaguDana_Rek4['totalpersentarget'],1);
                        $persen_realisasi_rek4=Helper::formatPecahan($totalPaguDana_Rek4['totalpersenrealisasi'],1);
                        $persen_tertimbang_realisasi_rek4=Helper::formatPecahan($totalPaguDana_Rek4['totalpersentertimbangrealisasi'],1);
                        $persen_rata2_fisik_rek4=Helper::formatPecahan($totalPaguDana_Rek4['totalfisik'],$totalPaguDana_Rek4['totalbaris']);
                        $persen_tertimbang_fisik_rek4=Helper::formatPecahan($totalPaguDana_Rek4['totalpersentertimbangfisik'],1);

                        $data[]=[
                          'FormAPerubahanDetailID'=>Uuid::uuid4()->toString(),
                          'tingkat'=>4,
                          'kode'=>$no_[0].'.'.$no_[1].'.'.$no_[2].'.'.$no_[3],
                          'nama_uraian'=>$v4,
                          'totalPaguDana'=>$totalPaguDana_Rek4['totalpagu'],
                          'persen_bobot'=>$persen_bobot_rek4,
                          'persen_rata2_fisik'=>$persen_rata2_fisik_rek4,
                          'persen_tertimbang_fisik'=>$persen_tertimbang_fisik_rek4,
                          'total_target'=>$totalPaguDana_Rek4['totaltarget'],
                          'total_realisasi'=>$totalPaguDana_Rek4['totalrealisasi'],
                          'persen_realisasi'=>$persen_realisasi_rek4,
                          'persen_tertimbang_realisasi'=>$persen_tertimbang_realisasi_rek4,
                          'sisa_anggaran'=>$totalPaguDana_Rek4['totalpagu']-$totalPaguDana_Rek4['totalrealisasi'],
                        ];
                        foreach ($tingkat_5 as $k5=>$v5)
                        {
                          if (preg_match("/^$k4/", $k5))
                          {
                            $totalPaguDana_Rek5=\App\Models\Renja\FormAPerubahanModel::calculateEachLevel($rka,$k5,'Kd_Rek_5');
                            $no_=explode (".",$k5);
                            $persen_bobot_rek5=Helper::formatPersen($totalPaguDana_Rek5['totalpersenbobot'],100);
                            $persen_target_rek5=Helper::formatPecahan($totalPaguDana_Rek5['totalpersentarget'],1);
                            $persen_realisasi_rek5=Helper::formatPecahan($totalPaguDana_Rek5['totalpersenrealisasi'],1);
                            $persen_tertimbang_realisasi_rek5=Helper::formatPecahan($totalPaguDana_Rek5['totalpersentertimbangrealisasi'],1);
                            $persen_rata2_fisik_rek5=Helper::formatPecahan($totalPaguDana_Rek5['totalfisik'],$totalPaguDana_Rek5['totalbaris']);
                            $persen_tertimbang_fisik_rek5=Helper::formatPecahan($totalPaguDana_Rek5['totalpersentertimbangfisik'],1);

                            $data[]=[
                              'FormAPerubahanDetailID'=>Uuid::uuid4()->toString(),
                              'tingkat'=>5,
                              'kode'=>$no_[0].'.'.$no_[1].'.'.$no_[2].'.'.$no_[3].'.'.$no_[4],
                              'nama_uraian'=>$v5,
                              'totalPaguDana'=>$totalPaguDana_Rek5['totalpagu'],
                              'persen_bobot'=>$persen_bobot_rek5,
                              'persen_rata2_fisik'=>$persen_rata2_fisik_rek5,
                              'persen_tertimbang_fisik'=>$persen_tertimbang_fisik_rek5,
                              'total_target'=>$totalPaguDana_Rek5['totaltarget'],
                              'total_realisasi'=>$totalPaguDana_Rek5['totalrealisasi'],
                              'persen_realisasi'=>$persen_realisasi_rek5,
                              'persen_tertimbang_realisasi'=>$persen_tertimbang_realisasi_rek5,
                              'sisa_anggaran'=>$totalPaguDana_Rek5['totalpagu']-$totalPaguDana_Rek5['totalrealisasi'],
                            ];

                            foreach ($tingkat_6 as $k6=>$v6)
                            {
                              if (preg_match("/^$k5/", $k6))
                              {
                                $totalUraian+=1;
                                $totalPaguDana_Rek6=\App\Models\Renja\FormAPerubahanModel::calculateEachLevel($rka,$k6,'Kd_Rek_6');
                                $RKARincID=$rka[$k6]['RKARincID'];
                                $nama_uraian=$rka[$k6]['nama_uraian'];
                                $no_=explode (".",$k6);
                                $persen_bobot_rek6=Helper::formatPersen($totalPaguDana_Rek6['totalpersenbobot'],100);
                                $persen_target_rek6=Helper::formatPecahan($totalPaguDana_Rek6['totalpersentarget'],1);
                                $persen_realisasi_rek6=Helper::formatPecahan($totalPaguDana_Rek6['totalpersenrealisasi'],1);
                                $persen_tertimbang_realisasi_rek6=Helper::formatPecahan($totalPaguDana_Rek6['totalpersentertimbangrealisasi'],1);
                                $persen_rata2_fisik_rek6=Helper::formatPecahan($totalPaguDana_Rek6['totalfisik'],$totalPaguDana_Rek6['totalbaris']);
                                $persen_tertimbang_fisik_rek6=Helper::formatPecahan($totalPaguDana_Rek6['totalpersentertimbangfisik'],1);

                                $data[]=[
                                  'FormAPerubahanDetailID'=>Uuid::uuid4()->toString(),
                                  'tingkat'=>6,
                                  'kode'=>$no_[0].'.'.$no_[1].'.'.$no_[2].'.'.$no_[3].'.'.$no_[4].'.'.$no_[5],
                                  'nama_uraian'=>$v6,
                                  'totalPaguDana'=>$totalPaguDana_Rek6['totalpagu'],
                                  'persen_bobot'=>$persen_bobot_rek6,
                                  'persen_rata2_fisik'=>$persen_rata2_fisik_rek6,
                                  'persen_tertimbang_fisik'=>$persen_tertimbang_fisik_rek6,
                                  'total_target'=>$totalPaguDana_Rek6['totaltarget'],
                                  'total_realisasi'=>$totalPaguDana_Rek6['totalrealisasi'],
                                  'persen_realisasi'=>$persen_realisasi_rek6,
                                  'persen_tertimbang_realisasi'=>$persen_tertimbang_realisasi_rek6,
                                  'sisa_anggaran'=>$totalPaguDana_Rek6['totalpagu']-$totalPaguDana_Rek6['totalrealisasi'],
                                ];

                                $nilaiuraian=$rka[$k6]['pagu_uraian'];
                                $target=$rka[$k6]['target'];
                                $totalTargetSatuKegiatan+=$target;
                                $realisasi=$rka[$k6]['realisasi'];
                                $totalRealisasiSatuKegiatan+=$realisasi;
                                $fisik=$rka[$k6]['fisik'];
                                $volume=$rka[$k6]['volume'];
                                $persen_bobot=Helper::formatPecahan($rka[$k6]['persen_bobot'],1);
                                $totalPersenBobotSatuKegiatan+=$persen_bobot;
                                $persen_target=Helper::formatPecahan($rka[$k6]['persen_target'],1);
                                $totalPersenTargetSatuKegiatan+=$persen_target;
                                $persen_realisasi=Helper::formatPecahan($rka[$k6]['persen_realisasi'],1);
                                $totalPersenRealisasiSatuKegiatan+=$persen_realisasi;
                                $persen_tertimbang_realisasi=Helper::formatPecahan($rka[$k6]['persen_tertimbang_realisasi'],1);
                                $persen_fisik=Helper::formatPecahan($rka[$k6]['persen_fisik'],1);
                                $totalPersenFisikSatuKegiatan+=$persen_fisik;
                                $persen_tertimbang_fisik=Helper::formatPecahan($rka[$k6]['persen_tertimbang_fisik'],1);
                                $totalPersenTertimbangFisikSatuKegiatan+=$persen_tertimbang_fisik;
                                $sisa_anggaran=$nilaiuraian-$realisasi;

                                if (isset($rka[$k6]['child'][0]))
                                {
                                  $child=$rka[$k6]['child'];
                                  foreach ($child as $n)
                                  {
                                    $totalUraian+=1;
                                    $RKARincID=$n['RKARincID'];
                                    $nama_uraian=$n['nama_uraian'];
                                    $nilaiuraian=$n['pagu_uraian'];
                                    $target=$n['target'];
                                    $totalTargetSatuKegiatan+=$target;
                                    $realisasi=$n['realisasi'];
                                    $totalRealisasiSatuKegiatan+=$realisasi;
                                    $fisik=$n['fisik'];
                                    $volume=$n['volume'];
                                    $persen_bobot=Helper::formatPecahan($n['persen_bobot'],1);
                                    $totalPersenBobotSatuKegiatan+=$persen_bobot;
                                    $persen_target=Helper::formatPecahan($n['persen_target'],1);
                                    $totalPersenTargetSatuKegiatan+=$persen_target;
                                    $persen_realisasi=Helper::formatPecahan($n['persen_realisasi'],1);
                                    $totalPersenRealisasiSatuKegiatan+=$persen_realisasi;
                                    $persen_tertimbang_realisasi=Helper::formatPecahan($n['persen_tertimbang_realisasi'],1);
                                    $persen_fisik=Helper::formatPecahan($n['persen_fisik'],1);
                                    $totalPersenFisikSatuKegiatan+=$persen_fisik;
                                    $persen_tertimbang_fisik=Helper::formatPecahan($n['persen_tertimbang_fisik'],1);
                                    $totalPersenTertimbangFisikSatuKegiatan+=$persen_tertimbang_fisik;
                                    $sisa_anggaran=$nilaiuraian-$realisasi;

                                    $data[]=[
                                      'FormAPerubahanDetailID'=>Uuid::uuid4()->toString(),
                                      'tingkat'=>7,
                                      'kode'=>'',
                                      'nama_uraian'=>$nama_uraian,
                                      'totalPaguDana'=>$nilaiuraian,
                                      'persen_bobot'=>$persen_bobot,
                                      'persen_rata2_fisik'=>$persen_fisik,
                                      'persen_tertimbang_fisik'=>$persen_tertimbang_fisik,
                                      'total_target'=>$target,
                                      'total_realisasi'=>$realisasi,
                                      'persen_realisasi'=>$persen_realisasi,
                                      'persen_tertimbang_realisasi'=>$persen_tertimbang_realisasi,
                                      'sisa_anggaran'=>$sisa_anggaran,
                                    ];
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            break;
          }
          continue;
        }
      }
      $total_persen_rata2_realisasi=Helper::formatPersen($totalRealisasiSatuKegiatan,$totalPaguDana);
      $total_data=[
        'totalPaguDana'=>$totalPaguDana,
        'totalPersenBobot'=>Helper::formatPersen($totalPersenBobotSatuKegiatan,100),
        'totalRealisasiFisik'=>Helper::formatPecahan($totalPersenFisikSatuKegiatan,$totalUraian),
        'totalPersenTertimbangFisikSatuKegiatan'=>$totalPersenTertimbangFisikSatuKegiatan,
        'totalTargetSatuKegiatan'=>$totalTargetSatuKegiatan,
        'totalRealisasiSatuKegiatan'=>$totalRealisasiSatuKegiatan,
        'total_persen_rata2_realisasi'=>Helper::formatPersen($total_persen_rata2_realisasi,100),
        'totalPersenTertimbangRealisasiSatuKegiatan'=>Helper::formatPecahan($total_persen_rata2_realisasi,$totalPersenBobotSatuKegiatan),
        'sisa_anggaran'=>$totalPaguDana-$totalRealisasiSatuKegiatan
      ];

      $this->generateStatistik6($RKAID);
      
      $statistik6=Statistik6Model::select(\DB::raw('
        `Bulan`,
        SUM(`PersenTargetKeuangan2`) AS `PersenTargetKeuangan2`,
        SUM(`PersenRealisasiKeuangan2`) AS `PersenRealisasiKeuangan2`,                                                
        SUM(`TargetFisik2`) AS `TargetFisik2`,
        SUM(`RealisasiFisik2`) AS `RealisasiFisik2`
      '))
      ->where('RKAID',$RKAID)                                                                                       
      ->where('EntryLvl', 2)        
      ->groupBy('Bulan')                                
      ->get();

      foreach($statistik6 as $v)
      {
        switch($v->Bulan)
        {
          case 1 :
            $chart_keuangan[0][0] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][0] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);

            $chart_fisik[0][0] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][0] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 2 :
            $chart_keuangan[0][1] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][1] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);

            $chart_fisik[0][1] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][1] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 3 :
            $chart_keuangan[0][2] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][2] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);

            $chart_fisik[0][2] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][2] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 4 :
            $chart_keuangan[0][3] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][3] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);

            $chart_fisik[0][3] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][3] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 5 :
            $chart_keuangan[0][4] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][4] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);

            $chart_fisik[0][4] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][4] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 6 :
            $chart_keuangan[0][5] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][5] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);

            $chart_fisik[0][5] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][5] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 7 :
            $chart_keuangan[0][6] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][6] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);
            
            $chart_fisik[0][6] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][6] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 8 :
            $chart_keuangan[0][7] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][7] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);
            
            $chart_fisik[0][7] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][7] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 9 :
            $chart_keuangan[0][8] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][8] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);
            
            $chart_fisik[0][8] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][8] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 10 :
            $chart_keuangan[0][9] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][9] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);
            
            $chart_fisik[0][9] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][9] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 11 :
            $chart_keuangan[0][10] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][10] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);
            
            $chart_fisik[0][10] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][10] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
          case 12 :
            $chart_keuangan[0][11] = Helper::formatPecahan($v->PersenTargetKeuangan2, 1);
            $chart_keuangan[1][11] = Helper::formatPecahan($v->PersenRealisasiKeuangan2, 1);
            
            $chart_fisik[0][11] = Helper::formatPecahan($v->TargetFisik2, 1);
            $chart_fisik[1][11] = Helper::formatPecahan($v->RealisasiFisik2, 1);
          break;
        }
      }

      return Response()->json([
        'status'=>1,
        'pid'=>'fetchdata',
        'rka'=>$data,
        'total_data'=>$total_data,
        'chart_keuangan'=>$chart_keuangan,
        'chart_fisik'=>$chart_fisik,
        'message'=>'Fetch data form a perubahan berhasil diperoleh'
      ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
    else
    {
      return Response()->json([
        'status'=>1,
        'pid'=>'fetchdata',
        'rka'=>$data,
        'total_data'=>$total_data,
        'chart_keuangan'=>$chart_keuangan,
        'chart_fisik'=>$chart_fisik,
        'message'=>'Fetch data form a perubahan berhasil diperoleh'
      ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

  }
  public function printtoexcel (Request $request)
  {
    $this->hasPermissionTo('RENJA-FORM-A-PERUBAHAN_BROWSE');

    $this->validate($request, [
      'no_bulan'=>'required',
      'SOrgID'=>'required',
      'RKAID'=>'required|exists:trRKA,RKAID',
    ]);
    $SOrgID = $request->input('SOrgID');
    $tahun = $request->input('tahun');
    $no_bulan = $request->input('no_bulan');
    $RKAID = $request->input('RKAID');

    if (\DB::table('trRKARinc')->where('RKAID',$RKAID)->count() > 0)
    {
      $unitkerja = SubOrganisasiModel::find($SOrgID);
      $data_report=[
        'RKAID'=>$RKAID,
        'kode_subkegiatan'=>$unitkerja->kode_subkegiatan,
        'SOrgNm'=>$unitkerja->SOrgNm,
        'tahun'=>$tahun,
        'no_bulan'=>$no_bulan,
        'nama_pengguna_anggaran'=>$unitkerja->NamaKepalaUnitKerja,
        'nip_pengguna_anggaran'=>$unitkerja->NIPKepalaUnitKerja
      ];
      $report= new \App\Models\Renja\FormAPerubahanModel ($data_report);
      $generate_date=date('Y-m-d_H_m_s');
      return $report->download("forma_a_$generate_date.xlsx");
    }
    else
    {
      return Response()->json([
        'status'=>0,
        'pid'=>'fetchdata',
        'message'=>['Print excel gagal dilakukan karena tidak ada belum ada Uraian pada kegiatan ini']
      ], 422);
    }
  }
  /**
   * digunakan untuk mendapatkan rangkuman masing-masing kegiatan 
  */
  public function generateStatistik6($RKAID)
  {
    $jumlahuraian = \DB::table('trRKARinc')->where('RKAID', $RKAID)->count();	
    $data_sub_kegiatan = RKAModel::select(\DB::raw('`RKAID`,`kode_kegiatan`,`kode_sub_kegiatan`,`Nm_Kegiatan`,`Nm_Sub_Kegiatan`,`PaguDana2`,`lokasi_kegiatan2`, `TA`'))    
    ->where('RKAID',$RKAID)                                       
    ->first();

    for($no_bulan = 1; $no_bulan <= 12; $no_bulan += 1)
    {
      $data_target=\DB::table('trRKATargetRinc')
        ->select(\DB::raw('COALESCE(SUM(target2),0) AS totaltarget, COALESCE(SUM(fisik2),0) AS jumlah_fisik'))
        ->where('RKAID',$RKAID)
        ->where('bulan2','<=',$no_bulan)
        ->get();

      $data_realisasi=\DB::table('trRKARealisasiRinc')
        ->select(\DB::raw('COALESCE(SUM(realisasi2),0) AS realisasi2, COALESCE(SUM(fisik2),0) AS fisik2'))
        ->where('RKAID',$RKAID)
        ->where('bulan2','<=',$no_bulan)
        ->get();

      $target_fisik=Helper::formatPecahan($data_target[0]->jumlah_fisik,$jumlahuraian);
      $persen_realisasi_fisik=Helper::formatPecahan($data_realisasi[0]->fisik2,$jumlahuraian);
      $totalTargetKeuangan=$data_target[0]->totaltarget;
      $totalRealisasiKeuangan=$data_realisasi[0]->realisasi2;

      $persen_target_keuangan=Helper::formatPersen($totalTargetKeuangan, $data_sub_kegiatan->PaguDana2);
      $persen_realisasi_keuangan=Helper::formatPersen($totalRealisasiKeuangan,$data_sub_kegiatan->PaguDana2); 
      
      $sisa_anggaran=$data_sub_kegiatan->PaguDana2-$totalRealisasiKeuangan;							
              
      $persen_sisa_anggaran=Helper::formatPersen($sisa_anggaran, $data_sub_kegiatan->PaguDana2);                            

      $statistik = Statistik6Model::where('RKAID', $RKAID)      
      ->where('Bulan', $no_bulan)
      ->first();

      if (is_null($statistik)) 
      {
        Statistik6Model::create([
          'Statistik6ID'=>Uuid::uuid4()->toString(),
          'RKAID'=>$RKAID,
          'kode_kegiatan'=>$data_sub_kegiatan->kode_kegiatan,
          'kode_sub_kegiatan'=>$data_sub_kegiatan->kode_sub_kegiatan,
          'Nm_Kegiatan'=>$data_sub_kegiatan->Nm_Kegiatan,
          'Nm_Sub_Kegiatan'=>$data_sub_kegiatan->Nm_Sub_Kegiatan,
          
          'PaguDana1'=>0,
          'PaguDana2'=>$data_sub_kegiatan->PaguDana2,            
          'PaguDana3'=>0,            
          'JumlahKegiatan1'=>0,
          'JumlahKegiatan2'=>1,
          'JumlahKegiatan3'=>0,

          'JumlahSubKegiatan1'=>0,
          'JumlahSubKegiatan2'=>1,
          'JumlahSubKegiatan1'=>0,

          'JumlahUraian1'=>0,
          'JumlahUraian2'=>$jumlahuraian,
          'JumlahUraian3'=>0,
              
          'TargetFisik1'=>0,
          'TargetFisik2'=>$target_fisik,
          'TargetFisik3'=>0,
          'RealisasiFisik1'=>0,
          'RealisasiFisik2'=>$persen_realisasi_fisik,
          'RealisasiFisik3'=>0,

          'TargetKeuangan1'=>0,
          'TargetKeuangan2'=>$totalTargetKeuangan,
          'TargetKeuangan3'=>0,
          'RealisasiKeuangan1'=>0,
          'RealisasiKeuangan2'=>$totalRealisasiKeuangan,
          'RealisasiKeuangan3'=>0,

          'PersenTargetKeuangan1'=>0,
          'PersenTargetKeuangan2'=>$persen_target_keuangan,
          'PersenTargetKeuangan3'=>0,
          'PersenRealisasiKeuangan1'=>0,
          'PersenRealisasiKeuangan2'=>$persen_realisasi_keuangan,
          'PersenRealisasiKeuangan3'=>0,
              
          'SisaPaguDana1'=>0,
          'SisaPaguDana2'=>$sisa_anggaran,
          'SisaPaguDana3'=>0,

          'PersenSisaPaguDana1'=>0,
          'PersenSisaPaguDana2'=>$persen_sisa_anggaran,
          'PersenSisaPaguDana3'=>0,

          'Bobot1'=>0,
          'Bobot2'=>100,
          'Bobot3'=>0,
          
          'Bulan'=>$no_bulan,
          'TA'=>$data_sub_kegiatan->TA,
          'EntryLvl'=>2,
        ]);
      }
      else
      {
        $statistik->PaguDana2 = $data_sub_kegiatan->PaguDana2;
        $statistik->JumlahKegiatan2 = 1;
        $statistik->TargetFisik2 = $target_fisik;
        $statistik->RealisasiFisik2 = $persen_realisasi_fisik;
        $statistik->TargetKeuangan2 = $totalTargetKeuangan;
        $statistik->RealisasiKeuangan2 = $totalRealisasiKeuangan;
        $statistik->PersenTargetKeuangan2 = $persen_target_keuangan;
        $statistik->PersenRealisasiKeuangan2 = $persen_realisasi_keuangan;
        $statistik->SisaPaguDana2 = $sisa_anggaran;
        $statistik->PersenSisaPaguDana2 = $persen_sisa_anggaran;
        $statistik->Bobot2 = 100;		

        $statistik->save();
      }
    }
  }
}
