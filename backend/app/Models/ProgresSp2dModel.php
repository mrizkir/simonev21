<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresSp2dModel extends Model {
   /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'progressp2d';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'progres_id',
    'OrgID',
    'PaguDana1',
    'PaguDana2',
    'JumlahProgram1',
    'JumlahProgram2',
    'JumlahKegiatan1',
    'JumlahKegiatan2',
    'JumlahSubKegiatan1',
    'JumlahSubKegiatan2',
    'RealisasiKeuangan1',
    'RealisasiKeuangan2',
    'RealisasiFisik1',
    'RealisasiFisik2',
    'Sp2d1',
    'Sp2d2',
    'Bulan',
    'TA',
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'progres_id';
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
