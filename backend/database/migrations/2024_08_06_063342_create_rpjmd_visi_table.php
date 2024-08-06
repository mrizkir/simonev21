<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpjmdVisiTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRpjmdVisi', function (Blueprint $table) {
      $table->uuid('RpjmdVisiID');
      $table->uuid('PeriodeRPJMDID');
      $table->string('Nm_RpjmdVisi');            
      $table->year('TA_AWAL');
      $table->year('TA_AKHIR');      
      $table->timestamps();

      $table->primary('RpjmdVisiID');
      $table->index('PeriodeRPJMDID');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {        
    Schema::dropIfExists('tmRpjmdVisi');
  }
}
