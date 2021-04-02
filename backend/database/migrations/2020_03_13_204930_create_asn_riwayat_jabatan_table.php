<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsnRiwayatJabatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trRiwayatJabatanASN', function (Blueprint $table) {
            $table->uuid('RiwayatJabatanASNID',19);
            $table->uuid('ASNID');
            $table->string('OrgID',19);                        
            $table->string('Jenis_Jabatan',4);    
            $table->year('TA');                    
            $table->boolean('Locked')->default(0);
            $table->timestamps();

            $table->primary('RiwayatJabatanASNID');    
            $table->index('ASNID');    
            $table->index('OrgID');    

            $table->foreign('OrgID')
                    ->references('OrgID')
                    ->on('tmOrg')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->foreign('ASNID')
                    ->references('ASNID')
                    ->on('tmASN')
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
        Schema::dropIfExists('trRiwayatJabatanASN');
    }
}
