<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeriodeRpjmdTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmPeriodeRPJMD', function (Blueprint $table) {
      $table->uuid('PeriodeRPJMDID');
      $table->year('TA_AWAL');
      $table->year('TA_AKHIR');
      $table->string('NamaPeriode');      
      $table->timestamps();

      $table->primary('PeriodeRPJMDID');
      
    });       
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {        
    Schema::dropIfExists('tmPeriodeRPJMD');
  }
}
