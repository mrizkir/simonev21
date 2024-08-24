<?php

namespace App\Models\RPJMD;

use Illuminate\Database\Eloquent\Model;

class RPJMDMisiModel extends Model 
{  
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmRpjmdMisi';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'RpjmdMisiID',
    'RpjmdVisiID',
    'PeriodeRPJMDID',
    'Kd_RpjmdMisi',
    'Nm_RpjmdMisi',    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'RpjmdMisiID';
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

  public function tujuan()
  {
    return $this->hasMany('App\Models\RPJMD\RPJMDTujuanModel', 'RpjmdMisiID', 'RpjmdMisiID')
    ->select(\DB::raw('
      tmRpjmdTujuan.*,
      CONCAT(b.Kd_RpjmdMisi,".",tmRpjmdTujuan.Kd_RpjmdTujuan) AS kode_tujuan
    '))
    ->join('tmRpjmdMisi AS b', 'b.RpjmdMisiID', 'tmRpjmdTujuan.RpjmdMisiID');
  }

  public function visi()
  {
    return $this->belongsTo('App\Models\RPJMD\RPJMDVisiModel', 'RpjmdVisiID', 'RpjmdVisiID');
  }
}