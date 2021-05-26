<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRekeningjenisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmJns', function (Blueprint $table) {
            $table->uuid('JnsID');
            $table->uuid('KlpID');
            $table->string('Kd_Rek_3',4);
            $table->string('JnsNm');
            $table->string('Descr')->nullable();
            $table->year('TA');
            $table->uuid('JnsID_Src')->nullable();
            $table->timestamps();

            $table->primary('JnsID');
            $table->index('KlpID');

            $table->foreign('KlpID')
                            ->references('KlpID')
                            ->on('tmKlp')
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
        Schema::dropIfExists('tmJns');
    }
}
