<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class KodefikasiUrusanModel extends Model
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'tmUrusan';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'UrsID';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'UrsID',
        'Kd_Urusan',
        'Nm_Urusan',
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