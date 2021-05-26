<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRekeningsubrincianobyekTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmSubROby', function (Blueprint $table) {
            $table->uuid('SubRObyID');
            $table->uuid('RObyID');            
            $table->string('Kd_Rek_6',4);
            $table->string('SubRObyNm');
            $table->string('Descr')->nullable();
            $table->year('TA');
            $table->uuid('RObyID_Src')->nullable();
            $table->timestamps();

            $table->primary('SubRObyID');
            $table->index('RObyID');

            $table->foreign('RObyID')
                            ->references('RObyID')
                            ->on('tmROby')
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
        Schema::dropIfExists('tmSubROby');
    }
}
