<?php

namespace App\Models\RPJMD;

use Illuminate\Database\Eloquent\Model;

class RPJMDPeriodeModel extends Model {    
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmRPJMDPeriode';
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'PeriodeRPJMDID';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'PeriodeRPJMDID', 
      'TA_AWAL',                 
      'TA_AKHIR',                 
      'NamaPeriode',                 
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