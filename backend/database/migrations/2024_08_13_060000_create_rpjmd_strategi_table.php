<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRpjmdStrategiTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmRpjmdStrategi', function (Blueprint $table) {
      $table->uuid('RpjmdStrategiID');
      $table->uuid('RpjmdSasaranID');
      $table->uuid('PeriodeRPJMDID');
      $table->string('Kd_RpjmdStrategi', 4);                  
      $table->text('Nm_RpjmdStrategi');                  
      $table->timestamps();

      $table->primary('RpjmdStrategiID');
      $table->index('RpjmdStrategiID');
      $table->index('PeriodeRPJMDID');

      $table->foreign('RpjmdSasaranID')
      ->references('RpjmdSasaranID')
      ->on('tmRpjmdSasaran')
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
    Schema::dropIfExists('tmRpjmdStrategi');
  }
}
