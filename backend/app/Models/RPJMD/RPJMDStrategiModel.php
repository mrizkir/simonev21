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

  public function arahkebijakan()
  {
    return $this->hasMany('App\Models\RPJMD\RPJMDArahKebijakanModel', 'RpjmdStrategiID', 'RpjmdStrategiID')
    ->select(\DB::raw('
      tmRpjmdArahKebijakan.*,
      CONCAT(e.Kd_RpjmdMisi,".",d.Kd_RpjmdTujuan,".",c.Kd_RpjmdSasaran,".",b.Kd_RpjmdStrategi,".",tmRpjmdArahKebijakan.Kd_RpjmdArahKebijakan) AS kode_arah_kebijakan
    '))
    ->join('tmRpjmdStrategi AS b', 'b.RpjmdStrategiID', 'tmRpjmdArahKebijakan.RpjmdStrategiID')
    ->join('tmRpjmdSasaran AS c', 'c.RpjmdSasaranID', 'b.RpjmdSasaranID')
    ->join('tmRpjmdTujuan AS d', 'd.RpjmdTujuanID', 'c.RpjmdTujuanID')
    ->join('tmRpjmdMisi AS e', 'e.RpjmdMisiID', 'd.RpjmdMisiID');
  }

  public function sasaran()
  {
    return $this->belongsTo('App\Models\RPJMD\RPJMDSasaranModel', 'RpjmdSasaranID', 'RpjmdSasaranID');
  }  
}