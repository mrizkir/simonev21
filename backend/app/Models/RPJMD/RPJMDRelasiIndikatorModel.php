<?php

namespace App\Models\RPJMD;

use Illuminate\Database\Eloquent\Model;

class RPJMDRelasiIndikatorModel extends Model 
{  
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmRpjmdRelasiIndikator';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'RpjmdRelasiIndikatorID',
    'RpjmdCascadingID',
    'PeriodeRPJMDID',
    'IndikatorKinerjaID',
    'data_1',    
    'data_2',    
    'data_3',    
    'data_4',    
    'data_5',    
    'data_6',    
    'data_7',    
    'data_8',    
    'data_9',    
    'data_10',    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'RpjmdRelasiIndikatorID';
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