<?php
/**
 * table untuk menyimpan informasi progress sp2d
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLockedOPDTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('lockedopd', function (Blueprint $table) {
      $table->uuid('lockedid');
      $table->uuid('OrgID');      
      $table->tinyInteger('Bulan');
      $table->year('TA');
      $table->tinyInteger('Locked');
      $table->enum('masa', ['murni', 'perubahan'])->nullable();
      $table->timestamps();

      $table->primary('lockedid'); 
      $table->index('OrgID'); 
      $table->index('Bulan'); 
      $table->index('TA'); 
      $table->index('masa'); 

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
    Schema::dropIfExists('lockedopd');
  }
}
