<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubOrganisasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmSOrg', function (Blueprint $table) {
            $table->uuid('SOrgID');
            $table->uuid('OrgID');
            
            $table->string('kode_sub_organisasi');
            $table->string('Kd_Sub_Organisasi',5);
            $table->string('Nm_Sub_Organisasi');            
            $table->string('Alias_Sub_Organisasi');
            $table->string('Alamat');
            $table->string('NamaKepalaUnitKerja');
            $table->string('NIPKepalaUnitKerja');
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
            $table->uuid('SOrgID_Src')->nullable();     
            $table->timestamps();

            $table->primary('SOrgID');
            $table->index('OrgID');            

            $table->foreign('OrgID')
                    ->references('OrgID')
                    ->on('tmOrg')
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
        Schema::dropIfExists('tmSOrg');
    }
}
