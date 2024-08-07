<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpjmdMisiTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRpjmdMisi', function (Blueprint $table) {
      $table->uuid('RpjmdMisiID');
      $table->uuid('RpjmdVisiID');
      $table->uuid('PeriodeRPJMDID');
      $table->string('Kd_RpjmdMisi', 4);                  
      $table->text('Nm_RpjmdMisi');                  
      $table->timestamps();

      $table->primary('RpjmdMisiID');
      $table->index('RpjmdMisiID');
      $table->index('PeriodeRPJMDID');

      $table->foreign('RpjmdVisiID')
      ->references('RpjmdVisiID')
      ->on('tmRpjmdVisi')
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
    Schema::dropIfExists('tmRpjmdMisi');
  }
}
