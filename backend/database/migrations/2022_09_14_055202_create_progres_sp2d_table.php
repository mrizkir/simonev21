<?php
/**
 * table untuk menyimpan informasi progress sp2d
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgresSp2dTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('progressp2d', function (Blueprint $table) {
      $table->uuid('progres_id');
      $table->uuid('OrgID');
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
      $table->decimal('Sp2d1',15,2)->default(0);
      $table->decimal('Sp2d2',15,2)->default(0);      
      $table->tinyInteger('Bulan');
      $table->year('TA');
      $table->timestamps();

      $table->primary('progres_id'); 
      $table->index('OrgID'); 
      $table->index('Bulan'); 
      $table->index('TA'); 
    });        
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('progressp2d');
  }
}
