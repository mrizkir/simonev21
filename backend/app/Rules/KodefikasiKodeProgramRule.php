<?php 

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class KodefikasiKodeProgramRule implements Rule 
{
  /**
   * object request
   */
  private $request;  
  /**
   * old value request
   */
  private $oldvalue;  
  /**
   * proses apakah unique, exists, dan lain sebagainya
   */
  private $pid = 'unique';
  public function __construct($request,$pid,$oldvalue=null)
  {
    $this->request = $request;    
    $this->pid = $pid;
    $this->oldvalue = $oldvalue;
  }
  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attributes
   * @param  mixed  $value
   * @return bool
   */
  public function passes ($attributes, $value) 
  { 
    switch($this->pid)
    {
      case 'unique':
        if ($this->request->input('jns') == 1)
        {
          $table = \DB::table('tmProgram')
                  ->join('tmUrusanProgram','tmProgram.PrgID','tmUrusanProgram.PrgID')
                  ->where('tmUrusanProgram.BidangID',$this->request->input('BidangID'))              
                  ->where('tmProgram.TA',$this->request->input('TA'));
        }
        else
        {
          $table = \DB::table('tmProgram')
                        ->where('TA',$this->request->input('TA'));
        }    
        $bool = !($table->where($attributes,$value)->count() > 0);
        return $bool;   
      break;
      case 'ignore' :
        if (
          (
            strtolower($value) == strtolower($this->oldvalue->Kd_Program)
          ) 
          && 
          (
            $this->request->input('BidangID') == $this->oldvalue->BidangID
          )
          &&
          $this->request->input('Jns') == 1) 
        {
          return true;
        }
        else if ((strtolower($value) == strtolower($this->oldvalue->Kd_Program)) && $this->request->input('Jns') == 0)
        {
          return true;
        }
        else
        {
          if ($this->request->input('jns') == 1)
          {
            $table = \DB::table('tmProgram')
                    ->join('tmUrusanProgram','tmProgram.PrgID','tmUrusanProgram.PrgID')
                    ->where('tmUrusanProgram.BidangID',$this->request->input('BidangID'))              
                    ->where('tmProgram.TA',$this->request->input('TA'));
          }
          else
          {
            $table = \DB::table('tmProgram')
                          ->where('TA',$this->request->input('TA'));
          }    
          $bool = !($table->where($attributes,$value)->count() > 0);
          return $bool; 
        }
      break;
    }    
}
  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message () 
  {
    return "Mohon maaf data untuk Kode Program yang di inputkan sudah tersedia. Mohon ganti dengan yang lain";
  }
}