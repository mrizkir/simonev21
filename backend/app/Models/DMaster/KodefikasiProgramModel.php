<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class KodefikasiProgramModel extends Model
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'tmProgram';
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
        'PrgID',
        'Kd_Program',
        'Nm_Program',
        'Jns',
        'Descr',
        'TA',
        'Locked',
        'PrgID_Src',
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

    public function kegiatan() 
    {
        return $this->hasMany('App\Models\DMaster\KodefikasiKegiatanModel','PrgID','PrgID');
    }
}