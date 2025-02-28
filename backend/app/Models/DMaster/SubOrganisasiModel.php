<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class SubOrganisasiModel extends Model 
{
   /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmSOrg';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'SOrgID', 
    'OrgID', 
    'kode_sub_organisasi', 
    'Kd_Sub_Organisasi', 
    'Nm_Sub_Organisasi', 
    'Alias_Sub_Organisasi',         
    'Alamat', 
    'NamaKepalaUnitKerja', 
    'NIPKepalaUnitKerja', 
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
    'SubOrgID_Src',
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'SOrgID';
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

  public function OPD ()
  {
    return $this->belongsTo('App\DMaster\OrganisasiModel','OrgID','OrgID');
  }

}
