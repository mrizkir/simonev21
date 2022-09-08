<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistik3Model extends Model {
   /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'statistik3';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'Statistik3ID',
    'OrgID',
    'kode_organisasi',
    'OrgNm',
    'PaguDana',
    'RealisasiKeuangan',
    'RealisasiFisik',
    'Kontrak',
    'PekerjaanSelesai',
    'PekerjaanBerjalan',
    'PekerjaanTerhenti',
    'PekerjaanBelumBerjalan',
    'BulanLaporan',
    'BuktiCetak',
    'TA',
    'isverified',
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'Statistik3ID';
  /**
   * enable auto_increment.
   *
   * @var string
   */
  public $incrementing = false;
  /**
   * activated timestamps.
   *
   * @var string
   */
  public $timestamps = true;    
}
