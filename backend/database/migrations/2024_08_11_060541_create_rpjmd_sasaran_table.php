<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpjmdSasaranTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRpjmdSasaran', function (Blueprint $table) {
      $table->uuid('RpjmdSasaranID');
      $table->uuid('RpjmdTujuanID');
      $table->uuid('PeriodeRPJMDID');
      $table->string('Kd_RpjmdSasaran', 4);                  
      $table->text('Nm_RpjmdSasaran');                  
      $table->timestamps();

      $table->primary('RpjmdSasaranID');
      $table->index('RpjmdSasaranID');
      $table->index('PeriodeRPJMDID');

      $table->foreign('RpjmdTujuanID')
      ->references('RpjmdTujuanID')
      ->on('tmRpjmdTujuan')
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
    Schema::dropIfExists('tmRpjmdSasaran');
  }
}
