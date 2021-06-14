<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSipdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sipd', function (Blueprint $table) {
            $table->uuid('SIPDID');            
            $table->uuid('OrgID')->nullable();
            
            $table->uuid('BidangID_1')->nullable();
            $table->string('kode_bidang_1',5)->nullable();
            $table->string('Nm_Bidang_1')->nullable();

            $table->uuid('BidangID_2')->nullable();
            $table->string('kode_bidang_2',5)->nullable();
            $table->string('Nm_Bidang_2')->nullable();

            $table->uuid('BidangID_3')->nullable();
            $table->string('kode_bidang_3')->nullable();
            $table->string('Nm_Bidang_3')->nullable();
            
            $table->string('kode_organisasi')->nullable();
            $table->string('Nm_Organisasi')->nullable();  

            $table->uuid('SOrgID')->nullable();
            $table->string('kode_sub_organisasi')->nullable();
            $table->string('Nm_Sub_Organisasi')->nullable(); 

            $table->string('master_kode')->nullable();
            $table->string('kd_Urusan1',5)->nullable();
            $table->string('kd_Bidang',5)->nullable();
            $table->string('kode_unit_sipd')->nullable();
            $table->string('kode_sub_unit_sipd')->nullable();
            $table->string('kd_program')->nullable();
            $table->string('kd_kegiatan')->nullable();
            $table->string('kd_sub_kegiatan')->nullable();            
            $table->string('kd_urusan2')->nullable();
            $table->string('nm_urusan')->nullable();
            $table->string('kd_bidang_urusan')->nullable();
            $table->string('nm_bidang_urusan')->nullable();
            $table->string('nm_unit')->nullable();
            $table->string('nm_sub_unit')->nullable();
            $table->string('kd_prog_gabungan')->nullable();
            $table->string('nm_program')->nullable();
            $table->string('kd_keg_gabung')->nullable();
            $table->text('nm_kegiatan')->nullable();
            $table->string('kd_sub_keg_gabung')->nullable();
            $table->text('nm_sub_kegiatan')->nullable();
            
            $table->string('kd_rek1')->nullable();            
            $table->string('kd_rek2')->nullable();            
            $table->string('kd_rek3')->nullable();            
            $table->string('kd_rek4')->nullable();            
            $table->string('kd_rek5')->nullable();            
            $table->string('kd_rek6')->nullable();            

            $table->string('nm_rek1')->nullable();            
            $table->string('nm_rek2')->nullable();            
            $table->string('nm_rek3')->nullable();            
            $table->string('nm_rek4')->nullable();            
            $table->string('nm_rek5')->nullable();            
            $table->string('nm_rek6')->nullable();            

            $table->decimal('PaguUraian1', 15,2)->default(0);            
            $table->decimal('PaguUraian2', 15,2)->default(0);                           

            $table->year('TA');                    
            $table->tinyInteger('EntryLevel')->default(1);
            $table->timestamps();

            $table->primary('SIPDID');    
            $table->index('kode_organisasi');    
            $table->index('kode_sub_unit_sipd');                
            $table->index('master_kode');                
            $table->index('kd_prog_gabungan');                
            $table->index('kd_keg_gabung');                
            $table->index('kd_sub_keg_gabung');                
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sipd');
    }
}
