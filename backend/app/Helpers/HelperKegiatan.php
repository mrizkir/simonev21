<?php

namespace App\Helpers;
use \Codedge\Fpdf\Fpdf\Fpdf;

use App\Models\System\ConfigurationModel;

class HelperKegiatan 
{
  /**
   * dapatkan kode warna sesuai permenagri 55/2010
   * @param type $triwulan
   * @param type $value
   * @return string
   */
  public static function getKodeWarna($triwulan,$value) 
  {
    switch ($triwulan) {
      case 1 :
        if ($value >= 23) {
          $color='#29af28';
        }elseif ($value >= 20 && $value < 23 ){
          $color='#8ec189';
        }elseif ($value >= 17 && $value < 20 ){
          $color='#e1c027';
        }elseif ($value >= 13 && $value < 17 ){
          $color='#d49dc5';
        }else{
          $color='#b71b1c';
        }
      break;
      case 2 :
        if ($value >= 45) {
          $color='#29af28';
        }elseif ($value >= 39 && $value < 45 ){
          $color='#8ec189';
        }elseif ($value >= 33 && $value < 39 ){
          $color='#e1c027';
        }elseif ($value >= 26 && $value < 33 ){
          $color='#d49dc5';
        }else{
          $color='#b71b1c';
        }
      break;
      case 3 :
        if ($value >= 79) {
          $color='#29af28';
        }elseif ($value >= 70 && $value < 79 ){
          $color='#8ec189';
        }elseif ($value >= 61 && $value < 70 ){
          $color='#e1c027';
        }elseif ($value >= 51 && $value < 60 ){
          $color='#d49dc5';
        }else{
          $color='#b71b1c';
        }
      break;
      case 4 :
        if ($value >= 98) {
          $color='#29af28';
        }elseif ($value >= 95 && $value < 98 ){
          $color='#8ec189';
        }elseif ($value >= 92 && $value < 95 ){
          $color='#e1c027';
        }elseif ($value >= 88 && $value < 92 ){
          $color='#d49dc5';
        }else{
          $color='#b71b1c';
        }
      break;
      default :
        if ($value >= 91 ) {
          $color='#29af28';
        }elseif ($value >= 76 && $value < 91 ){
          $color='#8ec189';
        }elseif ($value >= 66 && $value < 76 ){
          $color='#e1c027';
        }elseif ($value >= 51 && $value < 66 ){
          $color='#d49dc5';
        }else{
          $color='#b71b1c';
        }
    }
    return $color;
  }
}