<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVRekeningView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE VIEW v_rekening AS
            SELECT 
                Z.`SubRObyID`,
                A.`RObyID`,
                B.`ObyID`,
                C.`JnsID`,
                D.`KlpID`,
                E.`AkunID`,
                CONCAT(E.`Kd_Rek_1`,\'.\',D.`Kd_Rek_2`,\'.\',C.`Kd_Rek_3`,\'.\',B.`Kd_Rek_4`,\'.\',A.`Kd_Rek_5`,\'.\',Z.`Kd_Rek_6`) AS `kode_rek_6`,
                CONCAT(E.`Kd_Rek_1`,\'.\',D.`Kd_Rek_2`,\'.\',C.`Kd_Rek_3`,\'.\',B.`Kd_Rek_4`,\'.\',A.`Kd_Rek_5`) AS `kode_rek_5`,
                CONCAT(E.`Kd_Rek_1`,\'.\',D.`Kd_Rek_2`,\'.\',C.`Kd_Rek_3`,\'.\',B.`Kd_Rek_4`) AS `kode_rek_4`,
                CONCAT(E.`Kd_Rek_1`,\'.\',D.`Kd_Rek_2`,\'.\',C.`Kd_Rek_3`) AS `kode_rek_3`,
                CONCAT(E.`Kd_Rek_1`,\'.\',D.`Kd_Rek_2`) AS `kode_rek_2`,
                E.`Kd_Rek_1`,
                D.`Kd_Rek_2`,
                C.`Kd_Rek_3`,
                B.`Kd_Rek_4`,
                A.`Kd_Rek_5`,
                Z.`Kd_Rek_6`,
                Z.`SubRObyNm`,
                A.`RObyNm`,
                B.`ObyNm`,
                C.`JnsNm`,
                D.`KlpNm`,
                E.`Nm_Akun`,
                E.`TA`
            FROM `tmSubROby` Z
            JOIN `tmROby` A ON Z.`RObyID`=A.`RObyID`
            JOIN `tmOby` B ON A.`ObyID`=B.`ObyID`
            JOIN `tmJns` C ON B.`JnsID`=C.`JnsID`
            JOIN `tmKlp` D ON C.`KlpID`=D.`KlpID`
            JOIN `tmAkun` E ON D.`AkunID`=E.`AkunID` 
            ORDER BY E.`Kd_Rek_1` ASC,
                    D.`Kd_Rek_2` ASC,
                    C.`Kd_Rek_3` ASC,
                    B.`Kd_Rek_4` ASC,
                    A.`Kd_Rek_5` ASC,
                    Z.`Kd_Rek_6` ASC
        ');				
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('DROP VIEW v_rekening');
    }
}
