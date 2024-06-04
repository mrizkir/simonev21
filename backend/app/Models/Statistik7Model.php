<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistik7Model extends Model {    
   /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'statistik7';
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'Statistik7ID';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'Statistik7ID',
    'nama_rekening',
    'target',
    'realisasi',
    'jenis',        
    'Bulan',
    'TA',
    'EntryLvl',
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
