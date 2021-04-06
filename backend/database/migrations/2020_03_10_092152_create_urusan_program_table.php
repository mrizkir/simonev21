<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrusanProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmUrusanProgram', function (Blueprint $table) {
            $table->uuid('UrsPrgID');            
            $table->uuid('BidangID');
            $table->uuid('PrgID');                        
            $table->year('TA');                 
            $table->timestamps();

            $table->primary('UrsPrgID');
            $table->index('BidangID');
            $table->index('PrgID');

            $table->foreign('BidangID')
                ->references('BidangID')
                ->on('tmBidangUrusan')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('PrgID')
                ->references('PrgID')
                ->on('tmProgram')
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
        Schema::dropIfExists('tmUrusanProgram');
    }
}
