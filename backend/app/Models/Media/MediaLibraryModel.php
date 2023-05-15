<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibraryModel extends Model implements HasMedia
{
  use InteractsWithMedia;

  protected $table = 'media';

  protected $fillable = [
    'id',
    'uuid'
  ];

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
      ->height(250);
  }
}