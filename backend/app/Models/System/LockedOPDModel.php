<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LockedOPDModel extends Model {    
   /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'lockedopd';
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'lockedid';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'lockedid', 'OrgID', 'Bulan', 'TA', 'Locked'
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