<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOPD extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'usersopd';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'user_id', 
        'OrgID',
        'kode_organisasi',
        'Nm_Organisasi',
        'Alias_Organisasi',
        'ta',
        'locked',
    ];
    /**
     * enable auto_increment.
     *
     * @var string
     */
    public $incrementing = true;
    /**
     * activated timestamps.
     *
     * @var string
     */
    public $timestamps = true;
}