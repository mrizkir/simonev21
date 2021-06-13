<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateKegiatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmKegiatan', function (Blueprint $table) {
            $table->uuid('KgtID');
            $table->uuid('PrgID');
            $table->string('Kd_Kegiatan',5);
            $table->string('kode_kegiatan');
            $table->string('Nm_Kegiatan');
            $table->string('Descr')->nullable();
            $table->year('TA');          
            $table->uuid('KgtID_Src')->nullable();                         
            $table->timestamps();
            
            $table->primary('KgtID');
            $table->index('PrgID');
            $table->index('kode_kegiatan');
            $table->index('KgtID_Src');

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
        Schema::dropIfExists('tmKegiatan');
    }
}