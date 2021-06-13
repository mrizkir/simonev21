<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class KodefikasiSubKegiatanModel extends Model
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'tmSubKegiatan';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'SubKgtID';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'SubKgtID',
        'KgtID',
        'Kd_SubKegiatan',
        'kode_sub_kegiatan',
        'Nm_SubKegiatan',        
        'Descr',
        'TA',
        'SubKgtID_Src',
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