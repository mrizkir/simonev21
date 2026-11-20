<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJenisPelaksanaanIdSrcToTmjenispelaksanaanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('tmJenisPelaksanaan', function (Blueprint $table) {
      $table->uuid('JenisPelaksanaanID_Src')->nullable()->after('TA');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('tmJenisPelaksanaan', function (Blueprint $table) {
      $table->dropColumn('JenisPelaksanaanID_Src');
    });
  }
}
