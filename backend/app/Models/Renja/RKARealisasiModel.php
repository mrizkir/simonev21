<?php

namespace App\Models\Renja;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RKARealisasiModel extends Model implements HasMedia
{
  use InteractsWithMedia;
  /**
   * nama tabel model ini.
   *
   * @var string
  */
  protected $table = 'trRKARealisasiRinc';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
  */
  protected $fillable = [
    'RKARealisasiRincID', 
    'RKAID', 
    'RKARincID', 
    'bulan1', 
    'bulan2', 
    'target1', 
    'target2',         
    'realisasi1',  
    'realisasi2',         
    'target_fisik1',         
    'target_fisik2',         
    'fisik1',         
    'fisik2',         
    'EntryLvl',         
    'Descr',         
    'TA',         
    'Locked',  
    'RKARealisasiRincID_Src',                       
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
  */
  protected $primaryKey = 'RKARealisasiRincID';
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

  public function registerMediaConversions(Media $media = null): void
  {
    $this->addMediaConversion('thumb')
      ->width(350)
      ->height(350);
  }
}
