<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndikatorKinerjaTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmIndikatorKinerja', function (Blueprint $table) {
      $table->uuid('IndikatorKinerjaID');
      $table->text('NamaIndikator');
      $table->boolean('is_iku')->default(0);
      $table->boolean('is_ikk')->default(0);                  
      $table->timestamps();

      $table->primary('IndikatorKinerjaID');
      
    });       
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {        
    Schema::dropIfExists('tmIndikatorKinerja');
  }
}
