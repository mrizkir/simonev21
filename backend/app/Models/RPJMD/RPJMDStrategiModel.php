<?php

namespace App\Models\RPJMD;

use Illuminate\Database\Eloquent\Model;

class RPJMDStrategiModel extends Model 
{  
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmRpjmdStrategi';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'RpjmdStrategiID',
    'RpjmdSasaranID',
    'PeriodeRPJMDID',
    'Kd_RpjmdStrategi',
    'Nm_RpjmdStrategi',    
    'Nm_RpjmdArahKebijakan',    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'RpjmdStrategiID';
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

  public function sasaran()
  {
    return $this->belongsTo('App\Models\RPJMD\RPJMDSasaranModel', 'RpjmdSasaranID', 'RpjmdSasaranID');
  }
  public function program()
  {
    return $this->hasMany('App\Models\RPJMD\RPJMDRelasiStrategiProgramModel', 'RpjmdStrategiID', 'RpjmdStrategiID')
    ->select(\DB::raw("
      tmRpjmdRelasiStrategiProgram.`StrategiProgramID`,
      b.`PrgID`,
      d.BidangID,
      e.`Kd_Urusan`,
      d.`Kd_Bidang`,			 
      b.`Kd_Program`,
      CASE 
        WHEN d.`UrsID` IS NOT NULL OR d.`BidangID` IS NOT NULL THEN
          CONCAT(e.`Kd_Urusan`,'.',d.`Kd_Bidang`,'.',b.`Kd_Program`)
        ELSE
          CONCAT('X.','XX.',b.`Kd_Program`)
      END AS kode_program,                                        
      COALESCE(e.`Nm_Urusan`,'SEMUA URUSAN') AS Nm_Urusan,
      COALESCE(d.`Nm_Bidang`,'SEMUA BIDANG URUSAN') AS Nm_Bidang,
      b.`Nm_Program`,
      CASE 
        WHEN d.`UrsID` IS NOT NULL OR d.`BidangID` IS NOT NULL THEN
          CONCAT('[',e.`Kd_Urusan`,'.',d.`Kd_Bidang`,'.',b.`Kd_Program`,'] ',b.Nm_Program)
        ELSE
          CONCAT('[X.','XX.',b.`Kd_Program`,'] ',b.Nm_Program)
      END AS nama_program,
      tmRpjmdRelasiStrategiProgram.Kd_ProgramRPJMD,
      tmRpjmdRelasiStrategiProgram.Nm_ProgramRPJMD,
      b.`Jns`,
      b.`TA`,                                        
      b.`Descr`,
      b.`Locked`,
      tmRpjmdRelasiStrategiProgram.`created_at`,
      tmRpjmdRelasiStrategiProgram.`updated_at`
    "))
    ->join('tmProgram AS b', 'b.PrgID', 'tmRpjmdRelasiStrategiProgram.PrgID')
    ->leftJoin('tmUrusanProgram AS c','b.PrgID','c.PrgID')
    ->leftJoin('tmBidangUrusan AS d','d.BidangID','c.BidangID')
    ->leftJoin('tmUrusan AS e','d.UrsID','e.UrsID');    
  }
}