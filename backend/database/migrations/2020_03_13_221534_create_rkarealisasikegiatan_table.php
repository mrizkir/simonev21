<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRkarealisasikegiatanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('trRKARealisasiRinc', function (Blueprint $table) {
      $table->uuid('RKARealisasiRincID');
      $table->uuid('RKAID');
      $table->uuid('RKARincID');
      $table->tinyInteger('bulan1');
      $table->tinyInteger('bulan2');
      $table->decimal('target1',15,2);
      $table->decimal('target2',15,2);
      $table->decimal('realisasi1',15,2);            
      $table->decimal('realisasi2',15,2);            
      $table->decimal('target_fisik1',5,2);            
      $table->decimal('target_fisik2',5,2);            
      $table->decimal('fisik1',5,2);            
      $table->decimal('fisik2',5,2);            
      $table->tinyInteger('EntryLvl')->default(0);
      $table->string('Descr')->nullable();            
      $table->year('TA'); 
      $table->boolean('Locked')->default(0);
      $table->uuid('RKARealisasiRincID_Src')->nullable();
      $table->timestamps();

      $table->primary('RKARealisasiRincID');
      $table->index('RKAID');
      $table->index('RKARincID');
      $table->index('RKARealisasiRincID_Src');

      $table->foreign('RKARincID')
          ->references('RKARincID')
          ->on('trRKARinc')
          ->onDelete('cascade')
          ->onUpdate('cascade');    

      $table->foreign('RKAID')
          ->references('RKAID')
          ->on('trRKA')
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
    Schema::dropIfExists('trRKARealisasiRinc');
  }
}
