<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatistik7Table extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('statistik7', function (Blueprint $table) {
      $table->uuid('Statistik7ID');      
      $table->string('nama_rekening');
      $table->decimal('target',5,2)->default(0);
      $table->decimal('realisasi',5,2)->default(0);      
      $table->enum('jenis', ['fisik', 'keuangan'])->default('fisik');
      $table->tinyInteger('Bulan');
      $table->year('TA');
      $table->tinyInteger('EntryLvl')->default(0);
      $table->timestamps();

      $table->primary('Statistik7ID');
      $table->index('TA');
      $table->index('Bulan');
      $table->index('EntryLvl');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('statistik7');
  }
}
