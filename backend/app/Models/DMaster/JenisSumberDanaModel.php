<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class JenisSumberDanaModel extends Model 
{
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmJenisSumberDana';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'Id_Jenis_SumberDana',
    'Nm_Alias',
    'Nm_Jenis_SumberDana',    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'Id_Jenis_SumberDana';
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