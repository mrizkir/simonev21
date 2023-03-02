<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistik5Model extends Model {    
   /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'statistik5';
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'Statistik5ID';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'Statistik5ID',
    'SOrgID',
    'kode_sub_organisasi',
    'SOrgNm',
    'PaguDana1',
    'PaguDana2',            
    'PaguDana3',            
    'JumlahKegiatan1',
    'JumlahKegiatan2',
    'JumlahKegiatan3',
    'JumlahSubKegiatan1',
    'JumlahSubKegiatan2',
    'JumlahSubKegiatan3',
    'JumlahUraian1',
    'JumlahUraian2',
    'JumlahUraian3',
      
    'TargetFisik1',
    'TargetFisik2',
    'TargetFisik3',
    'RealisasiFisik1',
    'RealisasiFisik2',
    'RealisasiFisik3',

    'TargetKeuangan1',
    'TargetKeuangan2',
    'TargetKeuangan3',
    'RealisasiKeuangan1',
    'RealisasiKeuangan2',
    'RealisasiKeuangan3',

    'PersenTargetKeuangan1',
    'PersenTargetKeuangan2',
    'PersenTargetKeuangan3',
    'PersenRealisasiKeuangan1',
    'PersenRealisasiKeuangan2',
    'PersenRealisasiKeuangan3',
      
    'SisaPaguDana1',
    'SisaPaguDana2',
    'SisaPaguDana3',

    'PersenSisaPaguDana1',
    'PersenSisaPaguDana2',
    'PersenSisaPaguDana3',

    'Bobot1',
    'Bobot2',
    'Bobot3',
    
    'Bulan',
    'TA',
    'EntryLvl',
    'Statistik5ID_Src',
  ];
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
