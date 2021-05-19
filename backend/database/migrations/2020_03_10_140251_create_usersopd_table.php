<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersopdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usersopd', function (Blueprint $table) {
            $table->uuid('id');            
            $table->uuid('user_id');                                  
            $table->uuid('OrgID');  
            $table->string('kode_organisasi');
            $table->string('Nm_Organisasi');            
            $table->string('Alias_Organisasi');
            $table->year('ta');
            $table->boolean('locked')->default(0);             
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('OrgID');  

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
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usersopd');
    }
}