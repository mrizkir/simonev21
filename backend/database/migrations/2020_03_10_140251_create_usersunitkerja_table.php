<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersunitkerjaTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('usersunitkerja', function (Blueprint $table) {
      $table->uuid('id');
      $table->uuid('user_id');                                  
      $table->uuid('OrgID');  
      $table->uuid('SOrgID');  
      $table->string('kode_organisasi');
      $table->string('Nm_Organisasi');            
      $table->string('Alias_Organisasi');
      $table->string('kode_sub_organisasi');
      $table->string('Nm_Sub_Organisasi');            
      $table->string('Alias_Sub_Organisasi');
      $table->year('ta');
      $table->boolean('locked')->default(0);             
      $table->timestamps();
      
      $table->primary('id');

      $table->index('user_id');
      $table->index('OrgID');  
      $table->index('SOrgID');  

      $table->foreign('user_id')
          ->references('id')
          ->on('users')
          ->onDelete('cascade')
          ->onUpdate('cascade');

      $table->foreign('OrgID')
          ->references('OrgID')
          ->on('tmOrg')
          ->onDelete('cascade')
          ->onUpdate('cascade');            

      $table->foreign('SOrgID')
          ->references('SOrgID')
          ->on('tmSOrg')
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
    Schema::dropIfExists('usersunitkerja');
  }
}