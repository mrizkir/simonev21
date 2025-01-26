<?php

namespace App\Models\RPJMD;

use Illuminate\Database\Eloquent\Model;

class RPJMDArahKebijakanModel extends Model 
{  
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'tmRpjmdArahKebijakan';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'RpjmdArahKebijakanID',
    'RpjmdStrategiID',    
    'PeriodeRPJMDID',
    'Kd_RpjmdArahKebijakan',    
    'Nm_RpjmdArahKebijakan',    
  ];
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'RpjmdArahKebijakanID';
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

  public function strategi()
  {
    return $this->belongsTo('App\Models\RPJMD\RPJMDStrategiModel', 'RpjmdStrategiID', 'RpjmdStrategiID');
  }
  public function program()
  {
    return $this->hasMany('App\Models\RPJMD\RPJMDRelasiArahKebijakanProgramModel', 'RpjmdArahKebijakanID', 'RpjmdArahKebijakanID')
    ->select(\DB::raw("
      tmRpjmdRelasiArahKebijakanProgram.`ArahKebijakanProgramID`,
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
      tmRpjmdRelasiArahKebijakanProgram.Kd_ProgramRPJMD,
      tmRpjmdRelasiArahKebijakanProgram.Nm_ProgramRPJMD,
      b.`Jns`,
      b.`TA`,                                        
      b.`Descr`,
      b.`Locked`,
      tmRpjmdRelasiArahKebijakanProgram.`created_at`,
      tmRpjmdRelasiArahKebijakanProgram.`updated_at`
    "))
    ->join('tmProgram AS b', 'b.PrgID', 'tmRpjmdRelasiArahKebijakanProgram.PrgID')
    ->leftJoin('tmUrusanProgram AS c','b.PrgID','c.PrgID')
    ->leftJoin('tmBidangUrusan AS d','d.BidangID','c.BidangID')
    ->leftJoin('tmUrusan AS e','d.UrsID','e.UrsID');    
  }
}