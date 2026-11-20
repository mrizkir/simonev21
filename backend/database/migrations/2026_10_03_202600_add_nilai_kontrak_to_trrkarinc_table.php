<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNilaiKontrakToTrrkarincTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('trRKARinc', function (Blueprint $table) {
      $table->decimal('nilai_kontrak', 15, 2)->nullable()->default(0)->after('no_kontrak');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('trRKARinc', function (Blueprint $table) {
      $table->dropColumn('nilai_kontrak');
    });
  }
}
