<?php

namespace App\Helpers;
use \Codedge\Fpdf\Fpdf\Fpdf;

use App\Models\System\ConfigurationModel;
use App\Models\System\LockedOPDModel;
use App\Models\Media\MediaLibraryModel;
use App\Models\Renja\RKARealisasiModel;

class HelperKegiatan 
{
  /**
   * dapatkan kode warna sesuai permenagri 55/2010
   * @param type $triwulan
   * @param type $value
   * @return string
   */
  public static function getKodeWarna($triwulan,$value) 
  {
    switch ($triwulan) {
      case 1 :
        if ($value >= 23) {
          $color='#29af28';
        }elseif ($value >= 20 && $value < 23 ){
          $color='#8ec189';
        }elseif ($value >= 17 && $value < 20 ){
          $color='#e1c027';
        }elseif ($value >= 13 && $value < 17 ){
          $color='#d49dc5';
        }else{
          $color='#b71b1c';
        }
      break;
      case 2 :
        if ($value >= 45) {
          $color='#29af28';
        }elseif ($value >= 39 && $value < 45 ){
          $color='#8ec189';
        }elseif ($value >= 33 && $value < 39 ){
          $color='#e1c027';
        }elseif ($value >= 26 && $value < 33 ){
          $color='#d49dc5';
        }else{
          $color='#b71b1c';
        }
      break;
      case 3 :
        if ($value >= 79) {
          $color='#29af28';
        }elseif ($value >= 70 && $value < 79 ){
          $color='#8ec189';
        }elseif ($value >= 61 && $value < 70 ){
          $color='#e1c027';
        }elseif ($value >= 51 && $value < 60 ){
          $color='#d49dc5';
        }else{
          $color='#b71b1c';
        }
      break;
      case 4 :
        if ($value >= 98) {
          $color='#29af28';
        }elseif ($value >= 95 && $value < 98 ){
          $color='#8ec189';
        }elseif ($value >= 92 && $value < 95 ){
          $color='#e1c027';
        }elseif ($value >= 88 && $value < 92 ){
          $color='#d49dc5';
        }else{
          $color='#b71b1c';
        }
      break;
      default :
        if ($value >= 91 ) {
          $color='#29af28';
        }elseif ($value >= 76 && $value < 91 ){
          $color='#8ec189';
        }elseif ($value >= 66 && $value < 76 ){
          $color='#e1c027';
        }elseif ($value >= 51 && $value < 66 ){
          $color='#d49dc5';
        }else{
          $color='#b71b1c';
        }
    }
    return $color;
  }
  /**
   * digunakan untuk mendapatkan masa pelaporan saat ini
   */
  public static function getMasaPelaporan($tahun)
  {
    $config = ConfigurationModel::getCache();
		$masa_pelaporan = $config['DEFAULT_MASA_PELAPORAN'];
    
    $data = \DB::table('lockedopd')
			->select(\DB::raw('`OrgID`, Locked'))
			->where('TA', $tahun)
			->where('Bulan', 0)
			->first();		
		
		if (!is_null($data))
		{
			$masa_pelaporan = ($data->Locked == 10 || $data->Locked = 0) ? 'murni' : 'perubahan';
		}

    return $masa_pelaporan;
  }
  /**
   * digunakan untuk mengecek apakah sudah dikunci atau belum kegiatannya
   */
  public static function isLocked($OrgID, $bulan, $tahun, $masa = NULL)
  {
    if ($masa === NULL || $masa != 'murni' || $masa != 'perubahan')
    {
      $masa = HelperKegiatan::getMasaPelaporan($tahun);
    }      
    
    $locked = LockedOPDModel::where('OrgID', $OrgID)
    ->select('Locked')
    ->where('Bulan', $bulan)
    ->where('TA', $tahun)
    ->where('masa', $masa)
    ->first();
    
    return is_null($locked) ? 0 : $locked->Locked;    
  }
  /**
   * digunakan untuk menghapus foto realisasi 
   * @id = NULL maknanya semuanya dihapus
   *  
  */ 
  public static function destroyMediaRealisasiRincian($RKARealisasiRincID, $id = NULL)
  {
    $realisasi_ = RKARealisasiModel::find($RKARealisasiRincID);
    $list_media = $realisasi_->getMedia('kegiatan');
    
    $jumlah_terhapus = 0;
    if (count($list_media) > 0)
    {
      if ($id === NULL)
      {
        foreach($list_media as $k=>$media)
        {	
          $fullPathOnDisk = preg_replace('#/+#','/', $media->getPath());		                  						
          $list_media[$k]->delete();								
          Helper::deleteDirectory(dirname($fullPathOnDisk));     
          $jumlah_terhapus += 1;                 
        }
      }
      else
      {
        foreach($list_media as $k=>$media)
        {	
          if ($media->id == $id)
          {
            $fullPathOnDisk = preg_replace('#/+#','/', $media->getPath());		          						
            $list_media[$k]->delete();								
            Helper::deleteDirectory(dirname($fullPathOnDisk));
            $jumlah_terhapus += 1;
            break;
          }
        }
      }      
    }
    
    return $jumlah_terhapus;
  }

  /**
   * Ambil prefix kode rekening sampai level (jumlah segment).
   * Parsing berbasis segment (split by '.') agar tidak bergantung panjang digit per tahun.
   * Contoh: getRekeningPrefixByLevel("5.1.01.01.001.00001", 1) => "5"
   * @param string $kode Kode rekening (mis. 5.1.01.01.01.0001 atau 5.1.01.01.001.00001)
   * @param int $level Jumlah segment (1=Akun, 2=Kelompok, 3=Jenis, 4=Objek, 5=Rincian, 6=Sub Rincian)
   * @return string
   */
  public static function getRekeningPrefixByLevel($kode, $level)
  {
    $segments = explode('.', (string) $kode);
    $slice = array_slice($segments, 0, max(1, (int) $level));
    return implode('.', $slice);
  }

  /**
   * Normalisasi kode rekening untuk perbandingan (hilangkan leading zero per segment).
   * Agar "5" dan "05", "5.1" dan "05.01" dianggap sama (mis. beda tahun/master).
   * @param string $kode
   * @return string
   */
  public static function normalizeRekeningForCompare($kode)
  {
    $segments = explode('.', (string) $kode);
    $normalized = array_map(function ($s) {
      $s = trim($s);
      return $s === '' ? '0' : (ltrim($s, '0') ?: '0');
    }, $segments);
    return implode('.', $normalized);
  }

  /**
   * Cek apakah kode anak diawali oleh kode induk (prefix by segment).
   * Pakai normalisasi agar leading zero tidak mempengaruhi (tahun 2025 vs 2026).
   * @param string $kodeAnak Kode rekening anak (lebih rinci)
   * @param string $kodeInduk Kode rekening induk
   * @return bool
   */
  public static function rekeningIsAnakOf($kodeAnak, $kodeInduk)
  {
    $level = count(explode('.', (string) $kodeInduk));
    $prefixAnak = self::getRekeningPrefixByLevel($kodeAnak, $level);
    return self::normalizeRekeningForCompare($prefixAnak) === self::normalizeRekeningForCompare($kodeInduk);
  }
}