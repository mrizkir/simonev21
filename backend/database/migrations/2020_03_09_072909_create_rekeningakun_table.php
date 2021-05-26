<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRekeningakunTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmAkun', function (Blueprint $table) {
            $table->uuid('AkunID');
            $table->string('Kd_Rek_1',4);
            $table->string('Nm_Akun');
            $table->string('Descr')->nullable();
            $table->year('TA');
            $table->uuid('AkunID_Src')->nullable();
            $table->timestamps();

            $table->primary('AkunID');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmAkun');
    }
}
