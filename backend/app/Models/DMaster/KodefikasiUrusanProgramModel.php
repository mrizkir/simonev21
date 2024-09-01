<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class KodefikasiUrusanProgramModel extends Model
{
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmUrusanProgram';
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'UrsPrgID';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'UrsPrgID',
    'BidangID',
    'PrgID',
    'TA',
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