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
            
            $table->uuid('BidangID_1')->nullable();
            $table->string('kode_bidang_1',5)->nullable();
            $table->string('Nm_Bidang_1')->nullable();

            $table->uuid('BidangID_2')->nullable();
            $table->string('kode_bidang_2',5)->nullable();
            $table->string('Nm_Bidang_2')->nullable();

            $table->uuid('BidangID_3')->nullable();
            $table->string('kode_bidang_3')->nullable();
            $table->string('Nm_Bidang_3')->nullable();
            
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
            $table->integer('JumlahSubKegiatan1')->default(0);
            $table->integer('JumlahSubKegiatan2')->default(0);
            $table->decimal('RealisasiKeuangan1',15,2)->default(0);
            $table->decimal('RealisasiKeuangan2',15,2)->default(0);
            $table->decimal('RealisasiFisik1',5,2)->default(0);
            $table->decimal('RealisasiFisik2',5,2)->default(0);
            $table->string('Descr')->nullable();
            $table->year('TA');
            $table->uuid('OrgID_Src')->nullable();     
            $table->timestamps();

            $table->primary('OrgID'); 
            $table->index('BidangID_1'); 
            $table->index('kode_bidang_1'); 
            $table->index('BidangID_2'); 
            $table->index('kode_bidang_2'); 
            $table->index('BidangID_3');
            $table->index('kode_bidang_3');  
            $table->index('kode_organisasi'); 
            $table->index('Kd_Organisasi'); 
            $table->index('Nm_Organisasi');  
            $table->index('OrgID_Src');           

            $table->foreign('BidangID_1')
                ->references('BidangID')
                ->on('tmBidangUrusan')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('BidangID_2')
                ->references('BidangID')
                ->on('tmBidangUrusan')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('BidangID_3')
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
