<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRKPDTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RKPD', function (Blueprint $table) {
            $table->uuid('RKPDID');
            $table->uuid('OrgID')->nullable();
            $table->string('kode_organisasi')->nullable();
            $table->string('OrgNm')->nullable();
            $table->string('kode')->nullable();
            $table->string('nama')->nullable();
            $table->decimal('target_renstra',15,2)->default(0);
            $table->tinyInteger('level');
            $table->integer('no_urut');
            $table->tinyInteger('BulanLaporan');            
            $table->year('TA');            
            $table->timestamps();

            $table->primary('RKPDID');            
            $table->index('OrgID');
            $table->index('kode_organisasi');
            $table->index('TA');
            $table->index('BulanLaporan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('RKPD');
    }
}
