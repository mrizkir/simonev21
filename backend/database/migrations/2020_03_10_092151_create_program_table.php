<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmProgram', function (Blueprint $table) {
      $table->uuid('PrgID');            
      $table->string('Kd_Program',4);
      $table->string('Nm_Program');
      $table->tinyInteger('Jns')->default(1);    
      $table->decimal('PaguDana1',15,2)->default(0);
      $table->decimal('PaguDana2',15,2)->default(0);
      $table->decimal('RealisasiKeuangan1',15,2)->default(0);
      $table->decimal('RealisasiKeuangan2',15,2)->default(0);
      $table->decimal('RealisasiFisik1',5,2)->default(0);
      $table->decimal('RealisasiFisik2',5,2)->default(0);        
      $table->string('Descr')->nullable();
      $table->year('TA');   
      $table->boolean('Locked')->default(1);   
      $table->uuid('PrgID_Src')->nullable();           
      $table->timestamps();

      $table->primary('PrgID');
      $table->index('Kd_Program');
      $table->index('PrgID_Src');
    });        
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('tmProgram');
  }
}
