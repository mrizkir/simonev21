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
		\DB::statement("CREATE VIEW v_rencana_fisik_anggaran_kas AS
			SELECT 
				`trRKATargetRinc`.`RKARincID` AS `RKARincID`, 
				CONCAT('{',
					GROUP_CONCAT(
						TRIM(
							LEADING '{' FROM TRIM(
								TRAILING '}' FROM JSON_OBJECT(CONCAT('fisik_',`trRKATargetRinc`.`bulan1`),`trRKATargetRinc`.`fisik1`)
							)
						)
					),
				'}') AS `fisik1`,
				CONCAT('{',
					GROUP_CONCAT(
						TRIM(
							LEADING '{' FROM TRIM(
								TRAILING '}' FROM JSON_OBJECT(CONCAT('fisik_',`trRKATargetRinc`.`bulan2`),`trRKATargetRinc`.`fisik2`)
							)
						)
					),
				'}') AS `fisik2`,
				CONCAT('{',
					GROUP_CONCAT(
						TRIM(
							LEADING '{' FROM TRIM(
								TRAILING '}' FROM JSON_OBJECT(CONCAT('anggaran_',`trRKATargetRinc`.`bulan1`),`trRKATargetRinc`.`target1`)
							)
						)
					),
				'}') AS `anggaran1`,
				CONCAT('{',
					GROUP_CONCAT(
						TRIM(
							LEADING '{' FROM TRIM(
								TRAILING '}' FROM JSON_OBJECT(CONCAT('anggaran_',`trRKATargetRinc`.`bulan2`),`trRKATargetRinc`.`target2`)
							)
						)
					),
				'}') AS `anggaran2`
			FROM 
				`trRKATargetRinc` 
			GROUP BY
				`RKARincID`
		");				
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
