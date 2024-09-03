<?php

namespace App\Models\RPJMD;

use Illuminate\Database\Eloquent\Model;

class RPJMDRelasiStrategiProgramModel extends Model 
{  
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmRpjmdRelasiStrategiProgram';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'StrategiProgramID',
    'PrgID',
    'RpjmdStrategiID',    
    'Kd_ProgramRPJMD',    
    'Nm_ProgramRPJMD',    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'StrategiProgramID';
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