<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSnapshotRkakegiatanTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('trSnapshotRKA', function (Blueprint $table) {
      $table->uuid('SnapshotID');
      $table->uuid('RKAID');
      $table->uuid('OrgID');
      $table->uuid('SOrgID');
      $table->uuid('PrgID')->nullable();
      $table->uuid('KgtID')->nullable();
      $table->uuid('SubKgtID')->nullable();
      $table->uuid('SumberDanaID')->nullable();

      $table->string('kode_urusan');
      $table->string('kode_bidang');
      $table->string('kode_organisasi');
      $table->string('kode_sub_organisasi');
      $table->string('kode_program');
      $table->string('kode_kegiatan');
      $table->string('kode_sub_kegiatan');
      
      $table->string('Nm_Urusan');
      $table->string('Nm_Bidang');
      $table->string('Nm_Organisasi');
      $table->string('Nm_Sub_Organisasi');

      $table->string('Nm_Program');
      $table->string('Nm_Kegiatan');
      $table->string('Nm_Sub_Kegiatan');

      $table->text('keluaran1')->nullable();
      $table->text('keluaran2')->nullable();
      $table->string('tk_keluaran1')->nullable();
      $table->string('tk_keluaran2')->nullable();
      $table->text('hasil1')->nullable();
      $table->text('hasil2')->nullable();
      $table->string('tk_hasil1')->nullable();
      $table->string('tk_hasil2')->nullable();
      $table->text('capaian_program1')->nullable();
      $table->text('capaian_program2')->nullable();
      $table->string('tk_capaian1')->nullable();
      $table->string('tk_capaian2')->nullable();
      $table->string('masukan1')->nullable();
      $table->string('masukan2')->nullable();
      $table->string('ksk1')->nullable();
      $table->string('ksk2')->nullable();
      $table->string('sifat_kegiatan1',10)->nullable();
      $table->string('sifat_kegiatan2',10)->nullable();
      $table->string('waktu_pelaksanaan1')->nullable();
      $table->string('waktu_pelaksanaan2')->nullable();
      $table->string('lokasi_kegiatan1')->nullable();
      $table->string('lokasi_kegiatan2')->nullable();
      $table->decimal('PaguDana1',15,2)->defualt(0);
      $table->decimal('PaguDana2',15,2)->default(0);
      $table->decimal('RealisasiKeuangan1',15,2)->defualt(0);
      $table->decimal('RealisasiKeuangan2',15,2)->defualt(0);
      $table->decimal('RealisasiFisik1',5,2)->default(0);
      $table->decimal('RealisasiFisik2',5,2)->default(0);
      $table->uuid('nip_pa1')->nullable();
      $table->uuid('nip_pa2')->nullable();
      $table->uuid('nip_kpa1')->nullable();
      $table->uuid('nip_kpa2')->nullable();
      $table->uuid('nip_ppk1')->nullable();
      $table->uuid('nip_ppk2')->nullable();
      $table->uuid('nip_pptk1')->nullable();
      $table->uuid('nip_pptk2')->nullable();
      $table->uuid('user_id'); 
      $table->tinyInteger('EntryLvl')->default(0);
      $table->string('Descr')->nullable();            
      $table->year('TA');
      $table->integer('TABULAN', 6);
      $table->boolean('Locked')->default(0);
      $table->uuid('RKAID_Src')->nullable();
      $table->timestamps();

      $table->primary('SnapshotID');
      $table->index('RKAID');
      $table->index('OrgID');
      $table->index('SOrgID');
      $table->index('PrgID');
      $table->index('KgtID');
      $table->index('SubKgtID');
      $table->index('SumberDanaID');

      $table->index('kode_urusan');
      $table->index('kode_bidang');
      $table->index('kode_organisasi');
      $table->index('kode_sub_organisasi');
      $table->index('kode_program');
      $table->index('kode_kegiatan');
      $table->index('kode_sub_kegiatan');
      $table->index('Nm_Kegiatan');
      $table->index('Nm_Sub_Kegiatan');
      $table->index('nip_pa1');
      $table->index('nip_pa2');
      $table->index('nip_kpa1');
      $table->index('nip_kpa2');
      $table->index('nip_ppk1');
      $table->index('nip_ppk2');
      $table->index('nip_pptk1');
      $table->index('nip_pptk2');
      $table->index('RKAID_Src');
      $table->index('TABULAN');
    });       
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {        
    Schema::dropIfExists('trSnapshotRKA');
  }
}
