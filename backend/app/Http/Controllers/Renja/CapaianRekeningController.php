<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;

use App\Models\Statistik7Model;

class CapaianRekeningController extends Controller 
{
  public function index(Request $request)
  {
    $this->validate($request, [            
      'ta'=>'required',
      'tw'=>'required|in:1,2,3,4',
      'mode'=>'required|in:fisik,keuangan',
    ]);
    
    $tahun = $request->input('ta');
    $tw = $request->input('tw');
    $mode = $request->input('mode');

    switch($tw)
    {
      case 1:
        $bulan = [1,2,3];
        $target = [
          1 => 0,
          2 => 0,
          3 => 0,
        ];
        $realisasi = [
          1 => 0,
          2 => 0,
          3 => 0,
        ];
      break;
      case 2:
        $bulan = [4,5,6];
        $target = [
          4 => 0,
          5 => 0,
          6 => 0,
        ];
        $realisasi = [
          4 => 0,
          5 => 0,
          6 => 0,
        ];
      break;
      case 3:
        $bulan = [7,8,9];
        $target = [
          7 => 0,
          8 => 0,
          9 => 0,
        ];
        $realisasi = [
          7 => 0,
          8 => 0,
          9 => 0,
        ];
      break;
      case 4:
        $bulan = [10,11,12];
        $target = [
          10 => 0,
          11 => 0,
          12 => 0,
        ];
        $realisasi = [
          10 => 0,
          11 => 0,
          12 => 0,
        ];
      break;
    }

    $dr = Statistik7Model::where('TA', $tahun)
      ->select(\DB::raw("
        DISTINCT nama_rekening
      "))
      ->where('EntryLvl', 1)
      ->where('jenis', $mode)
      ->whereIn('Bulan', $bulan)
      ->orderBy('nama_rekening', 'ASC')
      ->orderBy('Bulan', 'ASC');

    if($this->hasRole(['opd', 'unitkerja']))
    {
      $dr = $dr->where('user_id', $this->getUserid());
    }
    else
    {
      $dr = $dr->whereNull('user_id');
    }
    $dr = $dr->get();
      
    $data_rekening = [];

    if(count($dr) > 0)
    {
      foreach($dr as $k => $v)
      {
        $dr2 = Statistik7Model::where('TA', $tahun)        
        ->where('EntryLvl', 1)
        ->where('jenis', $mode)
        ->whereIn('Bulan', $bulan)
        ->where('nama_rekening', $v->nama_rekening)
        ->orderBy('nama_rekening', 'ASC')
        ->orderBy('Bulan', 'ASC')
        ->get();
        
        foreach($dr2 as $v2)
        {
          $target[$v2->Bulan] = $v2->target;
          $realisasi[$v2->Bulan] = $v2->realisasi;
        }
        $data_rekening[$k] = [
          'rekening_id' => Uuid::uuid4()->toString(),
          'nama_rekening' => $v->nama_rekening,
          'target' => $target,
          'realisasi' => $realisasi,
        ];
      }
    }
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata', 
      'result' => $data_rekening,   
      'message' => "Fetch data target realisasi rekening TW ke $tw berhasil diperoleh"
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  public function reloadcapaianrek(Request $request)
  {
    $this->validate($request, [            
      'ta'=>'required',
      'tw'=>'required|in:1,2,3,4',
      'mode'=>'required|in:fisik,keuangan',
    ]);
    
    $tahun = $request->input('ta');
    $tw = $request->input('tw');
    $mode = $request->input('mode');

    $user_id = null;

    if($this->hasRole(['opd', 'unitkerja']))
    {
      $user_id = $this->getUserid();
    }

    $data_rekening = [
      [
        'rekening_id' => Uuid::uuid4()->toString(),
        'kode_rekening' => ['5.1.01'],
        'nama_rekening' => 'Belanja Pegawai',                
      ],
      [
        'rekening_id' => Uuid::uuid4()->toString(),
        'kode_rekening' => ['5.1.02'],
        'nama_rekening' => 'Belanja Barang dan Jasa',        
      ],
      [
        'rekening_id' => Uuid::uuid4()->toString(),
        'kode_rekening' => ['5.2.01','5.2.02','5.2.03','5.2.04','5.2.05'],
        'nama_rekening' => 'Belanja Modal',        
      ],
    ];

    switch($tw)
    {
      case 1:
        $bulan = [1,2,3];
      break;
      case 2:
        $bulan = [4,5,6];
      break;
      case 3:
        $bulan = [7,8,9];
      break;
      case 4:
        $bulan = [10,11,12];     
      break;
    }
    
    $statistik7_delete = Statistik7Model::where('TA', $tahun)
    ->where('EntryLvl', 1)
    ->where('jenis', $mode)
    ->whereIn('Bulan', $bulan);

    if($this->hasRole(['opd', 'unitkerja']))
    {
      $statistik7_delete = $statistik7_delete->where('user_id', $user_id);
    }
    else
    {
      $statistik7_delete = $statistik7_delete->whereNull('user_id');
    }

    $statistik7_delete->delete();

    $dr = $data_rekening;
    foreach ($dr as $k => $v)
    { 
      $jumlah_total = $mode == 'fisik' ? $this->getJumlahUraianByRekening($tahun, $v['kode_rekening']) : $this->getJumlahPaguByRekening($tahun, $v['kode_rekening']);      
      
      switch($tw)
      {
        case 1:
          for($i = 1; $i <= 3; $i++)
          {
            $target = $this->getDataTargetByRekening($tahun, $i, $v['kode_rekening'], $mode);
            $data_rekening[$k]['target'][$i] = $mode == 'fisik' ? Helper::formatPecahan($target, $jumlah_total) : Helper::formatPersen($target, $jumlah_total);
            $realisasi = $this->getDataRealisasiByRekening($tahun, $i, $v['kode_rekening'], $mode);
            $data_rekening[$k]['realisasi'][$i] = $mode == 'fisik' ? Helper::formatPecahan($realisasi, $jumlah_total) : Helper::formatPersen($realisasi, $jumlah_total);
            Statistik7Model::create([
              'Statistik7ID'=>Uuid::uuid4()->toString(),
              'user_id'=>$user_id,
              'nama_rekening' => $v['nama_rekening'],
              'target' => $data_rekening[$k]['target'][$i],
              'realisasi' => $data_rekening[$k]['realisasi'][$i],
              'jenis' => $mode,
              'Bulan' => $i,
              'TA' => $tahun,
              'EntryLvl' => 1,
            ]);
          }          
        break;
        case 2:          
          for($i = 4; $i <= 6; $i++)
          {
            $target = $this->getDataTargetByRekening($tahun, $i, $v['kode_rekening'], $mode);
            $data_rekening[$k]['target'][$i] = $mode == 'fisik' ? Helper::formatPecahan($target, $jumlah_total) : Helper::formatPersen($target, $jumlah_total);
            $realisasi = $this->getDataRealisasiByRekening($tahun, $i, $v['kode_rekening'], $mode);
            $data_rekening[$k]['realisasi'][$i] = $mode == 'fisik' ? Helper::formatPecahan($realisasi, $jumlah_total) : Helper::formatPersen($realisasi, $jumlah_total);
            Statistik7Model::create([
              'Statistik7ID'=>Uuid::uuid4()->toString(),
              'user_id'=>$user_id,
              'nama_rekening' => $v['nama_rekening'],
              'target' => $data_rekening[$k]['target'][$i],
              'realisasi' => $data_rekening[$k]['realisasi'][$i],
              'jenis' => $mode,
              'Bulan' => $i,
              'TA' => $tahun,
              'EntryLvl' => 1,
            ]);
          }          
        break;
        case 3:
          for($i = 7; $i <= 9; $i++)
          {
            $target = $this->getDataTargetByRekening($tahun, $i, $v['kode_rekening'], $mode);
            $data_rekening[$k]['target'][$i] = $mode == 'fisik' ? Helper::formatPecahan($target, $jumlah_total) : Helper::formatPersen($target, $jumlah_total);
            $realisasi = $this->getDataRealisasiByRekening($tahun, $i, $v['kode_rekening'], $mode);
            $data_rekening[$k]['realisasi'][$i] = $mode == 'fisik' ? Helper::formatPecahan($realisasi, $jumlah_total) : Helper::formatPersen($realisasi, $jumlah_total);
            Statistik7Model::create([
              'Statistik7ID'=>Uuid::uuid4()->toString(),
              'user_id'=>$user_id,
              'nama_rekening' => $v['nama_rekening'],
              'target' => $data_rekening[$k]['target'][$i],
              'realisasi' => $data_rekening[$k]['realisasi'][$i],
              'jenis' => $mode,
              'Bulan' => $i,
              'TA' => $tahun,
              'EntryLvl' => 1,
            ]);
          }          
        break;
        case 4:
          for($i = 10; $i <= 12; $i++)
          {
            $target = $this->getDataTargetByRekening($tahun, $i, $v['kode_rekening'], $mode);
            $data_rekening[$k]['target'][$i] = $mode == 'fisik' ? Helper::formatPecahan($target, $jumlah_total) : Helper::formatPersen($target, $jumlah_total);
            $realisasi = $this->getDataRealisasiByRekening($tahun, $i, $v['kode_rekening'], $mode);
            $data_rekening[$k]['realisasi'][$i] = $mode == 'fisik' ? Helper::formatPecahan($realisasi, $jumlah_total) : Helper::formatPersen($realisasi, $jumlah_total);
            Statistik7Model::create([
              'Statistik7ID'=>Uuid::uuid4()->toString(),
              'user_id'=>$user_id,
              'nama_rekening' => $v['nama_rekening'],
              'target' => $data_rekening[$k]['target'][$i],
              'realisasi' => $data_rekening[$k]['realisasi'][$i],
              'jenis' => $mode,
              'Bulan' => $i,
              'TA' => $tahun,
              'EntryLvl' => 1,
            ]);
          }          
        break;
      }
    }    
    return Response()->json([
      'status' => 1,
      'pid' => 'update', 
      'result' => $data_rekening,   
      'message' => "Update data target realisasi rekening TW ke $tw berhasil diproses"
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  /**
  * digunakan untuk mendapatkan jumlah uraian berdasarkan kode rekening dan ta
  * @param tahun tahun anggaran
  * @param kode_rekening dalam array
  */
  private function getJumlahUraianByRekening($tahun, $kode_rekening)
  {
    //jumlah baris uraian        
    $jumlahuraian = \DB::table('trRKARinc AS A');

    if($this->hasRole(['opd', 'unitkerja']))
    {
      $daftar_opd = $this->getUserOrgID($tahun);
      $jumlahuraian = $jumlahuraian->join('trRKA AS B', 'A.RKAID', 'B.RKAID')
      ->whereIn('B.OrgID', $daftar_opd);
    }

    $jumlahuraian = $jumlahuraian->where('A.TA', $tahun)    
    ->whereIn('kode_rek_3', $kode_rekening)
    ->count();	

    return $jumlahuraian;
  }
  /**
  * digunakan untuk mendapatkan jumlah uraian berdasarkan kode rekening dan ta
  * @param tahun tahun anggaran
  * @param no_bulan bulan realisasi
  * @param kode_rekening dalam array
  * @param mode [fisik atau keuangan]
  */
  private function getDataTargetByRekening($tahun, $no_bulan, $kode_rekening, $mode = 'fisik')
  {
    $select = $mode == 'fisik' ? 'A.fisik1' : 'A.target1';

    $jumlah = \DB::table('trRKATargetRinc AS A')
    ->select(\DB::raw($select))
    ->join('trRKARinc AS B', 'A.RKARincID', 'B.RKARincID');

    if($this->hasRole(['opd', 'unitkerja']))
    {
      $daftar_opd = $this->getUserOrgID($tahun);
      $jumlah = $jumlah->join('trRKA AS C', 'B.RKAID', 'C.RKAID')
      ->whereIn('C.OrgID', $daftar_opd);
    }

    $jumlah = $jumlah->where('A.TA', $tahun)
    ->where('A.bulan1', '<=', $no_bulan)           
    ->whereIn('kode_rek_3', $kode_rekening)
    ->sum($select);

    return $jumlah;
  }
  /**
  * digunakan untuk mendapatkan jumlah uraian berdasarkan kode rekening dan ta
  * @param tahun tahun anggaran
  * @param no_bulan bulan realisasi
  * @param kode_rekening dalam array
  * @param mode [fisik atau keuangan]
  */
  private function getDataRealisasiByRekening($tahun, $no_bulan, $kode_rekening, $mode = 'fisik')
  {
    $select = $mode == 'fisik' ? 'A.fisik1' : 'A.realisasi1';

    $jumlah = \DB::table('trRKARealisasiRinc AS A')
    ->select(\DB::raw($select))
    ->join('trRKARinc AS B', 'A.RKARincID', 'B.RKARincID');

    if($this->hasRole(['opd', 'unitkerja']))
    {
      $daftar_opd = $this->getUserOrgID($tahun);
      $jumlah = $jumlah->join('trRKA AS C', 'B.RKAID', 'C.RKAID')
      ->whereIn('C.OrgID', $daftar_opd);
    }

    $jumlah = $jumlah->where('A.TA', $tahun)
    ->where('A.bulan1', '<=', $no_bulan)     
    ->whereIn('kode_rek_3', $kode_rekening)   
    ->sum($select);
    
    return $jumlah;
  }
  /**
  * digunakan untuk mendapatkan jumlah uraian berdasarkan kode rekening dan ta
  * @param tahun tahun anggaran  
  * @param kode_rekening dalam array  
  */
  private function getJumlahPaguByRekening($tahun, $kode_rekening)
  {
    $jumlahpagu = \DB::table('trRKARinc AS A');

    if($this->hasRole(['opd', 'unitkerja']))
    {
      $daftar_opd = $this->getUserOrgID($tahun);
      $jumlahpagu = $jumlahpagu->join('trRKA AS B', 'B.RKAID', 'A.RKAID')
      ->whereIn('B.OrgID', $daftar_opd);
    }

    $jumlahpagu = $jumlahpagu->where('A.TA', $tahun)    
    ->whereIn('kode_rek_3', $kode_rekening)
    ->sum('PaguUraian1');

    return $jumlahpagu;
  }
}