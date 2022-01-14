<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateSubKegiatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmSubKegiatan', function (Blueprint $table) {
            $table->uuid('SubKgtID');
            $table->uuid('KgtID');
            $table->string('Kd_SubKegiatan',5);
            $table->string('kode_sub_kegiatan');
            $table->string('Nm_SubKegiatan');
            $table->string('Descr')->nullable();
            $table->year('TA');           
            $table->boolean('Locked')->default(1);
            $table->uuid('SubKgtID_Src')->nullable();             
            $table->timestamps();
            
            $table->primary('SubKgtID');
            $table->index('KgtID');
            $table->index('kode_sub_kegiatan');
            $table->index('SubKgtID_Src');

            $table->foreign('KgtID')
                ->references('KgtID')
                ->on('tmKegiatan')
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
        Schema::dropIfExists('tmSubKegiatan');
    }
}