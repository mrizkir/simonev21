<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpjmdRelasiStrategiProgramTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRpjmdRelasiStrategiProgram', function (Blueprint $table) {
      $table->uuid('StrategiProgramID');
      $table->uuid('PeriodeRPJMDID');
      $table->uuid('PrgID');
      $table->uuid('RpjmdStrategiID');
      $table->string('Kd_ProgramRPJMD', 10);                  
      $table->text('Nm_ProgramRPJMD');                  
      $table->timestamps();      
      
      $table->primary('StrategiProgramID');
      $table->index('PrgID');
      $table->index('RpjmdStrategiID');
      $table->index('PeriodeRPJMDID');

      $table->foreign('PrgID')
      ->references('PrgID')
      ->on('tmProgram')
      ->onDelete('cascade')
      ->onUpdate('cascade');

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
    Schema::dropIfExists('tmRpjmdRelasiStrategiProgram');
  }
}
