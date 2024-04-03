<?php

namespace App\Http\Controllers\Renja;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;

class CapaianRekeningController extends Controller 
{
  public function index(Request $request)
  {
    $this->validate($request, [            
      'ta'=>'required',
      'tw'=>'required|in:1,2,3,4',
      'mode'=>'required|in:fisik,keuangan',
    ]);
    
    $tahun=$request->input('ta');
    $tw=$request->input('tw');
    $mode=$request->input('mode');

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
          }          
        break;
        case 2:          
          for($i = 4; $i <= 6; $i++)
          {
            $data_rekening[$k]['target'][$i] = 0;
            $data_rekening[$k]['realisasi'][$i] = 0;
          }          
        break;
        case 3:
          for($i = 7; $i <= 9; $i++)
          {
            $data_rekening[$k]['target'][$i] = 0;
            $data_rekening[$k]['realisasi'][$i] = 0;
          }          
        break;
        case 4:
          for($i = 10; $i <= 12; $i++)
          {
            $data_rekening[$k]['target'][$i] = 0;
            $data_rekening[$k]['realisasi'][$i] = 0;
          }          
        break;
      }
    }
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata', 
      'result' => $data_rekening,   
      'message' => "Fetch data target realisasi rekening TW ke $tw berhasil diperoleh"
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