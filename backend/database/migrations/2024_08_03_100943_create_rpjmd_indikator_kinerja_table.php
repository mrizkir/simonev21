<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRPJMDIndikatorKinerjaTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRPJMDIndikatorKinerja', function (Blueprint $table) {
      $table->uuid('IndikatorKinerjaID');
      $table->uuid('PeriodeRPJMDID');
      $table->text('NamaIndikator');
      $table->boolean('is_iku')->default(0);
      $table->boolean('is_ikk')->default(0);
      $table->year('TA_AWAL');
      $table->year('TA_AKHIR');                    
      $table->timestamps();

      $table->primary('IndikatorKinerjaID');
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
    Schema::dropIfExists('tmRPJMDIndikatorKinerja');
  }
}
