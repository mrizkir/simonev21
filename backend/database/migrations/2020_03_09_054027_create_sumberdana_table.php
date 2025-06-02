<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSumberdanaTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tmSumberDana', function (Blueprint $table) {
      $table->uuid('SumberDanaID');
      $table->tinyInteger('Kd_SumberDana');
      $table->string('Nm_SumberDana');
      $table->tinyInteger('Id_Jenis_SumberDana')->nullable();
      $table->string('Descr')->nullable();
      $table->year('TA');
      $table->uuid('SumberDanaID_Src')->nullable();
      $table->timestamps();

      $table->primary('SumberDanaID');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('tmSumberDana');
  }
}
