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
    
    'BidangID_1',         
    'kode_bidang_1',         
    'Nm_Bidang_1',         
    
    'BidangID_2',         
    'kode_bidang_2',         
    'Nm_Bidang_2',         

    'BidangID_3',         
    'kode_bidang_3',         
    'Nm_Bidang_3',         

    'kode_organisasi', 
    'Kd_Organisasi', 
    'Nm_Organisasi', 
    'Alias_Organisasi',                
    'Alamat', 
    'NamaKepalaSKPD', 
    'NIPKepalaSKPD', 
    'PaguDana1',
    'PaguDana2',
    'JumlahProgram1',
    'JumlahProgram2',        
    'JumlahKegiatan1',
    'JumlahKegiatan2',        
    'JumlahSubKegiatan1',
    'JumlahSubKegiatan2',
    'RealisasiKeuangan1',            
    'RealisasiKeuangan2',        
    'RealisasiFisik1',        
    'RealisasiFisik2',        
    'Descr', 
    'TA',
    'Locked',
    'OrgID_Src'
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
