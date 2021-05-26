<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVRencanaFisikAnggaranKasView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE VIEW v_rencana_fisik_anggaran_kas AS
            SELECT 
                `RKARincID`,                
                JSON_OBJECTAGG(bulan1,fisik1) AS fisik1,
                JSON_OBJECTAGG(bulan2,fisik2) AS fisik2,
                JSON_OBJECTAGG(bulan1,target1) AS anggaran1,
                JSON_OBJECTAGG(bulan2,target2) AS anggaran2
            FROM 
                `trRKATargetRinc` 
            GROUP BY
                `RKARincID`
        ');				
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('DROP VIEW v_rencana_fisik_anggaran_kas');
    }
}
