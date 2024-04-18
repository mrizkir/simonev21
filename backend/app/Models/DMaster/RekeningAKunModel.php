<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class RekeningAKunModel extends Model
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'tmAkun';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'AkunID';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'AkunID', 'Kd_Rek_1', 'Nm_Akun', 'Descr', 'TA', 'AkunID_Src'
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