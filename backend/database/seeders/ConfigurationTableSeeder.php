<?php

namespace database\Seeders;

use Illuminate\Database\Seeder;

use Carbon\Carbon;
use App\Models\System\ConfigurationModel;

class ConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM configuration');
        
        \DB::table('configuration')->insert([
            'config_id'=>"101",
            'config_group'=>'identitas',
            'config_key'=>'NAMA_APP',
            'config_value'=>'SISTEM INFORMASI MONITORING DAN EVALUASI PEMBANGUNAN versi 21',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);    

        \DB::table('configuration')->insert([
            'config_id'=>"102",
            'config_group'=>'identitas',
            'config_key'=>'NAMA_APP_ALIAS',
            'config_value'=>'SIMONEV VERSI 21',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);        
        
        \DB::table('configuration')->insert([
            'config_id'=>"103",
            'config_group'=>'identitas',
            'config_key'=>'NAMA_OPD',
            'config_value'=>'BADAN PERENCANAAN DAN PENELITIAN PENGEMBANGAN KABUPATEN BINTAN',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"104",
            'config_group'=>'identitas',
            'config_key'=>'NAMA_OPD_ALIAS',
            'config_value'=>'BAPELITBANG KAB. BINTAN',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"201",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_TA',
            'config_value'=>date('Y'),
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"202",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_BULAN_REALISASI',
            'config_value'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"203",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_MASA_PELAPORAN',
            'config_value'=>'murni',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"701",
            'config_group'=>'report',
            'config_key'=>'HEADER_1',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"702",
            'config_group'=>'report',
            'config_key'=>'HEADER_2',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"703",
            'config_group'=>'report',
            'config_key'=>'HEADER_3',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"704",
            'config_group'=>'report',
            'config_key'=>'HEADER_4',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"705",
            'config_group'=>'report',
            'config_key'=>'HEADER_ADDRESS',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        // theme
        \DB::table('configuration')->insert([
            'config_id'=>"801",
            'config_group'=>'theme',
            'config_key'=>'V-SYSTEM-BAR-CSS-CLASS',
            'config_value'=>'black lighten-2 white--text',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"802",
            'config_group'=>'theme',
            'config_key'=>'V-APP-BAR-CSS-CLASS',
            'config_value'=>'purple darken-4 green--text font-weight-bold',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"803",
            'config_group'=>'theme',
            'config_key'=>'V-APP-BAR-COLOR',
            'config_value'=>'green',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"804",
            'config_group'=>'theme',
            'config_key'=>'V-APP-BAR-NAV-ICON-CSS-CLASS',
            'config_value'=>'grey--text',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"805",
            'config_group'=>'theme',
            'config_key'=>'V-NAVIGATION-DRAWER-CSS-CLASS',
            'config_value'=>'purple darken-3',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"806",
            'config_group'=>'theme',
            'config_key'=>'V-LIST-ITEM-BOARD-CSS-CLASS',
            'config_value'=>'purple accent-1',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"807",
            'config_group'=>'theme',
            'config_key'=>'V-LIST-ITEM-BOARD-COLOR',
            'config_value'=>'purple',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"808",
            'config_group'=>'theme',
            'config_key'=>'V-LIST-ITEM-ACTIVE-CSS-CLASS',
            'config_value'=>'purple darken-3 white--text',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"809",
            'config_group'=>'theme',
            'config_key'=>'V-APP-BAR-NAV-ICON-CSS-CLASS',
            'config_value'=>'grey--text',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
			'config_id'=>"901",
			'config_group'=>'server',
			'config_key'=>'TOKEN_TTL_EXPIRE',
			'config_value'=>'60',//minute
			'created_at'=>Carbon::now(),
			'updated_at'=>Carbon::now()
		]);

        ConfigurationModel::toCache();
    }
}