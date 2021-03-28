<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrusanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmUrusan', function (Blueprint $table) {
            $table->uuid('UrsID');            
            $table->string('Kd_Urusan',5);
            $table->string('Nm_Urusan');                        
            $table->string('Descr');
            $table->year('TA');       
            $table->timestamps();

            $table->primary('UrsID'); 
            $table->index('Kd_Urusan'); 
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmUrusan');
    }
}
