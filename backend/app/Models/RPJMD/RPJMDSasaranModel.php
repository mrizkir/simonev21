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

  public function misi()
  {
    return $this->belongsTo('App\Models\RPJMD\RPJMDMisiModel', 'RpjmdTujuanID', 'RpjmdTujuanID');
  }
}