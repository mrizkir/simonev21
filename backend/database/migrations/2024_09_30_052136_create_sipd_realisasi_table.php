<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSipdRealisasiTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('sipd_realisasi', function (Blueprint $table) 
    {
      $table->uuid('SIPDID')->nullable();
      $table->uuid('OrgID')->nullable();
      $table->uuid('SOrgID')->nullable();
      $table->uuid('PrgID')->nullable();
      $table->uuid('KgtID')->nullable();
      $table->uuid('SubKgtID')->nullable();

      $table->uuid('RKARealisasiRincID')->nullable();
      $table->uuid('RKAID')->nullable();
      $table->uuid('RKARincID')->nullable();

      $table->string('kode_organisasi')->nullable();
      $table->string('Nm_Organisasi')->nullable();  

      $table->string('kode_sub_organisasi')->nullable();
      $table->string('Nm_Sub_Organisasi')->nullable(); 

      $table->char('kode_urusan', 2)->nullable();
      $table->string('nama_urusan')->nullable();
      $table->char('kode_bidang_urusan', 6)->nullable();
      $table->string('nama_bidang_urusan')->nullable();
      $table->char('kode_program', 9)->nullable();
      $table->string('nama_program')->nullable();
      $table->char('kode_kegiatan', 13)->nullable();
      $table->string('nama_kegiatan')->nullable();
      $table->char('kode_sub_kegiatan', 18)->nullable(); 
      $table->string('nama_sub_kegiatan')->nullable();
      $table->char('kode_rekening', 18)->nullable(); 
      $table->string('nama_rekening')->nullable();

      $table->decimal('PaguUraian1', 15,2)->default(0);            
      $table->decimal('PaguUraian2', 15,2)->default(0);
      
      $table->decimal('Realisasi1', 15,2)->default(0);            
      $table->decimal('Realisasi2', 15,2)->default(0);                           

      $table->tinyInteger('bulan1');
      $table->tinyInteger('bulan2');

      $table->year('TA');                    
      $table->tinyInteger('EntryLevel')->default(1);

      $table->tinyInteger('status')->default(0);
      $table->text('Descr')->nullable();

      $table->timestamps();

      $table->primary('SIPDID');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('sipd_realisasi');
  }
}
