<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpjmdTujuanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRpjmdTujuan', function (Blueprint $table) {
      $table->uuid('RpjmdTujuanID');
      $table->uuid('RpjmdMisiID');
      $table->uuid('PeriodeRPJMDID');
      $table->string('Kd_RpjmdTujuan', 4);                  
      $table->text('Nm_RpjmdTujuan');                  
      $table->timestamps();

      $table->primary('RpjmdTujuanID');
      $table->index('RpjmdTujuanID');
      $table->index('PeriodeRPJMDID');

      $table->foreign('RpjmdMisiID')
      ->references('RpjmdMisiID')
      ->on('tmRpjmdMisi')
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
    Schema::dropIfExists('tmRpjmdTujuan');
  }
}
