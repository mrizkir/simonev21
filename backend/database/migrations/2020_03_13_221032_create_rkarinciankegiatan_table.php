<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRkarinciankegiatanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('trRKARinc', function (Blueprint $table) {
      $table->uuid('RKARincID');
      $table->uuid('RKAID');
      $table->uuid('SIPDID')->nullable();
      $table->uuid('JenisPembangunanID')->nullable();
      $table->uuid('JenisPelaksanaanID')->nullable();
      $table->uuid('SumberDanaID')->nullable();
      $table->uuid('SubRObyID')->nullable();
      $table->string('kode_rek_3', 10)->nullable();
      $table->string('kode_uraian1')->nullable();
      $table->string('kode_uraian2')->nullable();
      $table->string('NamaUraian1')->nullable();
      $table->string('NamaUraian2')->nullable();
      $table->string('volume1')->nullable();
      $table->string('volume2')->nullable();
      $table->string('satuan1',50)->nullable();            
      $table->string('satuan2',50)->nullable();            
      $table->decimal('harga_satuan1',15,2);
      $table->decimal('harga_satuan2',15,2);
      $table->decimal('PaguUraian1',15,2);
      $table->decimal('PaguUraian2',15,2);
      $table->string('idlok',10)->nullable();
      $table->string('ket_lok')->nullable();
      $table->string('rw',5)->nullable();
      $table->string('rt',5)->nullable();
      $table->string('nama_perusahaan')->nullable();
      $table->string('alamat_perusahaan')->nullable();
      $table->string('no_telepon',25)->nullable();
      $table->string('nama_direktur')->nullable();
      $table->string('npwp',25)->nullable();
      $table->string('no_kontrak')->nullable();
      $table->date('tgl_kontrak')->nullable();
      $table->date('tgl_mulai_pelaksanaan')->nullable();
      $table->date('tgl_selesai_pelaksanaan')->nullable();
      $table->tinyInteger('status_lelang')->default(0);
      $table->tinyInteger('EntryLvl')->default(0);
      $table->string('Descr')->nullable();            
      $table->year('TA'); 
      $table->boolean('Locked')->default(0);
      $table->uuid('RKARincID_Src')->nullable();
      $table->timestamps();

      $table->primary('RKARincID');
      $table->index('SIPDID');
      $table->index('RKAID');
      $table->index('JenisPelaksanaanID');
      $table->index('SumberDanaID');
      $table->index('JenisPembangunanID');            
      $table->index('SubRObyID');
      $table->index('kode_rek_3');
      $table->index('kode_uraian1');
      $table->index('kode_uraian2');
      $table->index('RKARincID_Src');
      
      $table->foreign('SIPDID')
          ->references('SIPDID')
          ->on('sipd')
          ->onDelete('set null')
          ->onUpdate('cascade');

      $table->foreign('RKAID')
          ->references('RKAID')
          ->on('trRKA')
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
    Schema::dropIfExists('trRKARinc');
  }
}
