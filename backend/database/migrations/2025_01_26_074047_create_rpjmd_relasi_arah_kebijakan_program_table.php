<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpjmdRelasiArahKebijakanProgramTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRpjmdRelasiArahKebijakanProgram', function (Blueprint $table) {
      $table->uuid('ArahKebijakanProgramID');
      $table->uuid('PeriodeRPJMDID');
      $table->uuid('PrgID');
      $table->uuid('RpjmdArahKebijakanID');
      $table->string('Kd_ProgramRPJMD', 10);                  
      $table->text('Nm_ProgramRPJMD');                  
      $table->timestamps();      
      
      $table->primary('ArahKebijakanProgramID');
      $table->index('PrgID');
      $table->index('RpjmdArahKebijakanID');
      $table->index('PeriodeRPJMDID');

      $table->foreign('PrgID')
      ->references('PrgID')
      ->on('tmProgram')
      ->onDelete('cascade')
      ->onUpdate('cascade');

      $table->foreign('RpjmdArahKebijakanID', 'abp')
      ->references('RpjmdArahKebijakanID')
      ->on('tmRpjmdArahKebijakan')
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
    Schema::dropIfExists('tmRpjmdRelasiArahKebijakanProgram');
  }
}
