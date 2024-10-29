<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpjmdRealisasiIndikatorTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRpjmdRealisasiIndikator', function (Blueprint $table) {
      $table->uuid('RpjmdRealisasiIndikatorID');
      $table->uuid('RpjmdRelasiIndikatorID');
      $table->uuid('RpjmdCascadingID');     //merujuk pada tujuan,sasaran,program id 
      $table->uuid('PeriodeRPJMDID');
      $table->uuid('IndikatorKinerjaID')->nullable();
      $table->string('TipeCascading', 20); //merujuk pada tujuan,sasarab,program id
      $table->string('data_1');
      $table->string('data_2');
      $table->string('data_3');
      $table->string('data_4');
      $table->string('data_5');
      $table->string('data_6');
      $table->string('data_7');
      $table->string('data_8');
      $table->string('data_9');
      $table->string('data_10');
      $table->string('data_11');
      $table->string('data_12');
      $table->string('data_13');
      $table->string('data_14');
      $table->string('data_15');
      $table->string('data_16');
      $table->string('data_17');
      $table->string('data_18');
      $table->string('data_19');
      $table->string('data_20');
      $table->timestamps();

      $table->primary('RpjmdRealisasiIndikatorID');
      $table->index('RpjmdCascadingID');
      $table->index('RpjmdRelasiIndikatorID');
      $table->index('PeriodeRPJMDID');
      $table->index('IndikatorKinerjaID');

      $table->foreign('IndikatorKinerjaID')
      ->references('IndikatorKinerjaID')
      ->on('tmRPJMDIndikatorKinerja')
      ->onDelete('cascade')
      ->onUpdate('cascade');
      
      $table->foreign('RpjmdRelasiIndikatorID')
      ->references('RpjmdRelasiIndikatorID')
      ->on('tmRpjmdRelasiIndikator')
      ->onDelete('cascade')
      ->onUpdate('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {        
    Schema::dropIfExists('tmRpjmdRealisasiIndikator');
  }
}
