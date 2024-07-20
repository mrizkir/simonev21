<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class IndikatorKinerjaModel extends Model {    
	 /**
	 * nama tabel model ini.
	 *
	 * @var string
	 */
	protected $table = 'tmIndikatorKinerja';
	/**
	 * primary key tabel ini.
	 *
	 * @var string
	 */
	protected $primaryKey = 'IndikatorKinerjaID';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'IndikatorKinerjaID',
    'NamaIndikator',
    'is_iku',
    'is_ikk'
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