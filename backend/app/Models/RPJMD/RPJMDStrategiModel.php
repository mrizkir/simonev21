<?php

namespace App\Models\RPJMD;

use Illuminate\Database\Eloquent\Model;

class RPJMDStrategiModel extends Model 
{  
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmRpjmdStrategi';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'RpjmdStrategiID',
    'RpjmdSasaranID',
    'PeriodeRPJMDID',
    'Kd_RpjmdStrategi',
    'Nm_RpjmdStrategi',    
    'Nm_RpjmdArahKebijakan',    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'RpjmdStrategiID';
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

  public function sasaran()
  {
    return $this->belongsTo('App\Models\RPJMD\RPJMDSasaranModel', 'RpjmdSasaranID', 'RpjmdSasaranID');
  }
}