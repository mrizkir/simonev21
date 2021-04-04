<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class KodefikasiBidangUrusanModel extends Model
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'tmBidangUrusan';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'BidangID';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'BidangID',
        'UrsID',
        'Kd_Bidang',
        'Nm_Bidang',
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