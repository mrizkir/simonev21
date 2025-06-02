<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJenisSumberdanaTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmJenisSumberDana', function (Blueprint $table) {
      $table->tinyInteger('Id_Jenis_SumberDana');
      $table->string('Nm_Jenis_SumberDana');                 
      $table->string('Nm_Alias')->nullable();                 
      $table->timestamps();

      $table->primary('Id_Jenis_SumberDana');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('tmJenisSumberDana');
  }
}
