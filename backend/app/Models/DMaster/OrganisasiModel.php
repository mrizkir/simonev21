<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class OrganisasiModel extends Model {
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'tmOrg';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'OrgID', 
        'BidangID', 
        'Kd_Organisasi', 
        'Nm_Organisasi', 
        'Alias_Organisasi',         
        'Alamat', 
        'NamaKepalaSKPD', 
        'NIPKepalaSKPD', 
        'PaguDana1',
        'PaguDana2',
        'JumlahProgram',
        'JumlahProgram2',        
        'JumlahKegiatan',
        'JumlahKegiatan2',        
        'RealisasiKeuangan1',            
        'RealisasiKeuangan2',        
        'RealisasiFisik1',        
        'RealisasiFisik2',        
        'Descr', 
        'TA'
    ];
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'OrgID';
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

    public function unitkerja ()
    {
        return $this->hasMany('App\Models\DMaster\SubOrganisasiModel','OrgID');
    }
}
