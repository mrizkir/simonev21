<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVUrusanView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement(
            'CREATE VIEW v_urusan AS 
            SELECT                 
                tu.`UrsID`,
                tbu.`BidangID`, 
                tu.`Kd_Urusan`, 
                tbu.`Kd_Bidang`, 
                CONCAT(tu.`Kd_Urusan`, \'.\', tbu.`Kd_Bidang`) AS `Kode_Bidang`, 
                tu.`Nm_Urusan`, 
                tbu.`Nm_Bidang`, 
                tu.`TA` 
            FROM `tmBidangUrusan` tbu 
            INNER JOIN `tmUrusan` tu ON (tbu.`UrsID`=tbu.`UrsID`)
            ORDER BY
                tu.`Kd_Urusan` ASC,
                tbu.`Kd_Bidang` ASC
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('DROP VIEW v_urusan');
    }
}
