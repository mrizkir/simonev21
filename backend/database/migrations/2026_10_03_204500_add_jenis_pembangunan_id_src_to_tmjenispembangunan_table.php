<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJenisPembangunanIdSrcToTmjenispembangunanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('tmJenisPembangunan', function (Blueprint $table) {
      $table->uuid('JenisPembangunanID_Src')->nullable()->after('TA');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('tmJenisPembangunan', function (Blueprint $table) {
      $table->dropColumn('JenisPembangunanID_Src');
    });
  }
}
