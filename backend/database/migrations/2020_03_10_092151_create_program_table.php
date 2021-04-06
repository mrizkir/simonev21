<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmProgram', function (Blueprint $table) {
            $table->uuid('PrgID');            
            $table->string('Kd_Program',4);
            $table->string('Nm_Program');
            $table->tinyInteger('Jns')->default(1);            
            $table->string('Descr')->nullable();
            $table->year('TA');                 
            $table->timestamps();

            $table->primary('PrgID');
            $table->index('Kd_Program');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmProgram');
    }
}
