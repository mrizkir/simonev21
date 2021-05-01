<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBidangUrusanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmBidangUrusan', function (Blueprint $table) {
            $table->uuid('BidangID');            
            $table->uuid('UrsID');            
            $table->string('Kd_Bidang',5);
            $table->string('Nm_Bidang');                        
            $table->string('Descr');
            $table->year('TA');
            $table->uuid('BidangID_Src')->nullable();
            $table->timestamps();

            $table->primary('BidangID'); 
            $table->index('UrsID'); 
            $table->index('Kd_Bidang'); 
            $table->index('BidangID_Src'); 

            $table->foreign('UrsID')
                ->references('UrsID')
                ->on('tmUrusan')
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
        Schema::dropIfExists('tmBidangUrusan');
    }
}
