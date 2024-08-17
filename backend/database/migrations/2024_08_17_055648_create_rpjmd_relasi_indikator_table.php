<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpjmdRelasiIndikatorTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRpjmdRelasiIndikator', function (Blueprint $table) {
      $table->uuid('RpjmdRelasiIndikatorID');
      $table->uuid('RpjmdCascadingID');     //merujuk pada tujuan,sasaran,program id 
      $table->uuid('PeriodeRPJMDID');
      $table->uuid('IndikatorKinerjaID');
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
      $table->timestamps();

      $table->primary('RpjmdRelasiIndikatorID');
      $table->index('RpjmdCascadingID');
      $table->index('PeriodeRPJMDID');

      $table->foreign('IndikatorKinerjaID')
      ->references('IndikatorKinerjaID')
      ->on('tmRPJMDIndikatorKinerja')
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
    Schema::dropIfExists('tmRpjmdRelasiIndikator');
  }
}
