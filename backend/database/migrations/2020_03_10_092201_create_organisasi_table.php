<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganisasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmOrg', function (Blueprint $table) {
            $table->uuid('OrgID');
            $table->uuid('BidangID')->nullable();
            $table->string('Nm_Bidang');
            $table->string('kode_organisasi');
            $table->string('Kd_Organisasi',5);
            $table->string('Nm_Organisasi');            
            $table->string('Alias_Organisasi');
            $table->string('Alamat');
            $table->string('NamaKepalaSKPD');
            $table->string('NIPKepalaSKPD');
            $table->decimal('PaguDana1',15,2)->default(0);
            $table->decimal('PaguDana2',15,2)->default(0);
            $table->integer('JumlahProgram1')->default(0);
            $table->integer('JumlahProgram2')->default(0);
            $table->integer('JumlahKegiatan1')->default(0);
            $table->integer('JumlahKegiatan2')->default(0);
            $table->decimal('RealisasiKeuangan1',15,2)->default(0);
            $table->decimal('RealisasiKeuangan2',15,2)->default(0);
            $table->decimal('RealisasiFisik1',5,2)->default(0);
            $table->decimal('RealisasiFisik2',5,2)->default(0);
            $table->string('Descr')->nullable();
            $table->year('TA');       
            $table->timestamps();

            $table->primary('OrgID'); 
            $table->index('BidangID'); 
            $table->index('kode_organisasi'); 
            $table->index('Kd_Organisasi'); 
            $table->index('Nm_Organisasi');             

            $table->foreign('BidangID')
                ->references('BidangID')
                ->on('tmBidangUrusan')
                ->onDelete('set null')
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
        Schema::dropIfExists('tmOrg');
    }
}
