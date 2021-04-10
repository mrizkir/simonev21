<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class KodefikasiKegiatanModel extends Model
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'tmKegiatan';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'PrgID';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'KgtID',
        'PrgID',
        'Kd_Kegiatan',
        'Nm_Kegiatan',        
        'Descr',
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