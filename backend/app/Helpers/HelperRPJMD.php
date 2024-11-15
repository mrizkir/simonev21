<?php

namespace App\Helpers;

use App\Models\System\ConfigurationModel;

class HelperRPJMD 
{
  /**
   * dapatkan kriteria realisasi kinerja   
   * @param type $value
   * @return string
  */
  public static function getKriteriaPenilaianRealisasi($value) 
  {
    if ($value >= 91)
    {
      $kriteria = 'SANGAT TINGGI';
    }
    elseif ($value >= 76 && $value < 91 )
    {
      $kriteria = 'TINGGI';
    }
    elseif ($value >= 66 && $value < 76 )
    {
      $kriteria = 'SEDANG';
    }
    elseif ($value >= 51 && $value < 66 )
    {
      $kriteria = 'RENDAH';
    }
    else
    {
      $kriteria = 'SANGAT RENDAH';
    }

    return $kriteria;
  }
  /**
   * dapatkan kriteria realisasi kinerja   
   * @param type $value
   * @return string
  */
  public static function getWarnaPenilaianRealisasi($value) 
  {
    if ($value >= 91)
    {
      $color = '#29af28';
    }
    elseif ($value >= 76 && $value < 91 )
    {
      $color = '#8ec189';
    }
    elseif ($value >= 66 && $value < 76 )
    {
      $color = '#e1c027';
    }
    elseif ($value >= 51 && $value < 66 )
    {
      $color = '#d49dc5';
    }
    else
    {
      $color = '#b71b1c';
    }

    return $color;
  }
}