<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpjmdArahKebijakanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRpjmdArahKebijakan', function (Blueprint $table) {
      $table->uuid('RpjmdArahKebijakanID');
      $table->uuid('RpjmdStrategiID');
      $table->uuid('PeriodeRPJMDID');
      $table->string('Kd_RpjmdArahKebijakan', 4);                               
      $table->text('Nm_RpjmdArahKebijakan');                  
      $table->timestamps();

      $table->primary('RpjmdArahKebijakanID');
      $table->index('RpjmdStrategiID');
      $table->index('PeriodeRPJMDID');

      $table->foreign('RpjmdStrategiID')
      ->references('RpjmdStrategiID')
      ->on('tmRpjmdStrategi')
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
    Schema::dropIfExists('tmRpjmdArahKebijakan');
  }
}
