<?php

namespace App\Models\RPJMD;

use Illuminate\Database\Eloquent\Model;

class RPJMDTujuanModel extends Model 
{  
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmRpjmdTujuan';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'RpjmdTujuanID',
    'RpjmdMisiID',
    'PeriodeRPJMDID',
    'Kd_RpjmdTujuan',
    'Nm_RpjmdTujuan',    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'RpjmdTujuanID';
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

  public function misi()
  {
    return $this->belongsTo('App\Models\RPJMD\RPJMDMisiModel', 'RpjmdMisiID', 'RpjmdMisiID');
  }
  public function sasaran()
  {
    return $this->hasMany('App\Models\RPJMD\RPJMDSasaranModel', 'RpjmdTujuanID', 'RpjmdTujuanID')
    ->select(\DB::raw('
      tmRpjmdSasaran.*,
      CONCAT(c.Kd_RpjmdMisi,".",b.Kd_RpjmdTujuan,".",tmRpjmdSasaran.Kd_RpjmdSasaran) AS kode_sasaran
    '))
    ->join('tmRpjmdTujuan AS b', 'b.RpjmdTujuanID', 'tmRpjmdSasaran.RpjmdTujuanID')
    ->join('tmRpjmdMisi AS c', 'c.RpjmdMisiID', 'b.RpjmdMisiID');
  }
}