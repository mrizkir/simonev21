<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatistik3Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistik3', function (Blueprint $table) {
            $table->uuid('Statistik3ID');
            $table->uuid('OrgID');
            $table->string('kode_organisasi');
            $table->string('OrgNm');
            $table->decimal('PaguDana',15,2)->default(0);
            $table->decimal('RealisasiKeuangan',15,2)->default(0);
            $table->decimal('RealisasiFisik',15,2)->default(0);
            $table->decimal('Kontrak',15,2)->default(0);
            $table->integer('PekerjaanSelesai')->default(0);
            $table->integer('PekerjaanBerjalan')->default(0);
            $table->integer('PekerjaanTerhenti')->default(0);
            $table->integer('PekerjaanBelumBerjalan')->default(0);
            $table->tinyInteger('BulanLaporan');            
            $table->year('TA');
            $table->boolean('isverified')->default(0);
            $table->timestamps();

            $table->primary('Statistik3ID');            
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
        Schema::dropIfExists('statistik3');
    }
}
