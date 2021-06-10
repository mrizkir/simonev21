<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class RekeningSubRincianObjekModel extends Model
{
    /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'tmSubROby';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'SubRObyID';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'SubRObyID', 'RObyID', 'Kd_Rek_6', 'SubRObyNm', 'Descr', 'TA', 'SubRObyID_Src'
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