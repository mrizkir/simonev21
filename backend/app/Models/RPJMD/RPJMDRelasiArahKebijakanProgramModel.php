<?php

namespace App\Models\RPJMD;

use Illuminate\Database\Eloquent\Model;

class RPJMDRelasiArahKebijakanProgramModel extends Model 
{  
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmRpjmdRelasiArahKebijakanProgram';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'ArahKebijakanProgramID',
    'PeriodeRPJMDID',
    'PrgID',
    'RpjmdArahKebijakanID',    
    'Kd_ProgramRPJMD',    
    'Nm_ProgramRPJMD',    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'ArahKebijakanProgramID';
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