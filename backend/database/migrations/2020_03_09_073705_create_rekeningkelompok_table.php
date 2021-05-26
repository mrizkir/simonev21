<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRekeningkelompokTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmKlp', function (Blueprint $table) {
            $table->uuid('KlpID');
            $table->uuid('AkunID');
            $table->string('Kd_Rek_2',4);
            $table->string('KlpNm');
            $table->string('Descr')->nullable();
            $table->year('TA');
            $table->string('KlpID_Src')->nullable();
            $table->timestamps();

            $table->primary('KlpID');
            $table->index('AkunID');

            $table->foreign('AkunID')
                            ->references('AkunID')
                            ->on('tmAkun')
                            ->onDelete('cascade')
                            ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmKlp');
    }
}
