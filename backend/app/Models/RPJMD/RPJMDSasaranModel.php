<?php

namespace App\Models\RPJMD;

use Illuminate\Database\Eloquent\Model;

class RPJMDSasaranModel extends Model 
{  
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmRpjmdSasaran';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'RpjmdSasaranID',
    'RpjmdTujuanID',
    'PeriodeRPJMDID',
    'Kd_RpjmdSasaran',
    'Nm_RpjmdSasaran',    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'RpjmdSasaranID';
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
    return $this->belongsTo('App\Models\RPJMD\RPJMDTujuanModel', 'RpjmdTujuanID', 'RpjmdTujuanID');
  }
  public function strategi()
  {
    return $this->hasMany('App\Models\RPJMD\RPJMDStrategiModel', 'RpjmdSasaranID', 'RpjmdSasaranID')
    ->select(\DB::raw('
      tmRpjmdStrategi.*,
      CONCAT(d.Kd_RpjmdMisi,".",c.Kd_RpjmdTujuan,".",b.Kd_RpjmdSasaran,".",tmRpjmdStrategi.Kd_RpjmdStrategi) AS kode_strategi
    '))
    ->join('tmRpjmdSasaran AS b', 'b.RpjmdSasaranID', 'tmRpjmdStrategi.RpjmdSasaranID')
    ->join('tmRpjmdTujuan AS c', 'b.RpjmdTujuanID', 'c.RpjmdTujuanID')
    ->join('tmRpjmdMisi AS d', 'c.RpjmdMisiID', 'd.RpjmdMisiID');
  }
}