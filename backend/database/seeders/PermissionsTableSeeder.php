<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM permissions');
        \DB::statement('ALTER TABLE permissions AUTO_INCREMENT = 1');
        
        \DB::table('permissions')->insert([
            'name'=>"DMASTER-GROUP_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('permissions')->insert([
            'name'=>"RPJMD-GROUP_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('permissions')->insert([
            'name'=>"RENSTRA-GROUP_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('permissions')->insert([
            'name'=>"RKPD-GROUP_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('permissions')->insert([
            'name'=>"RENJA-GROUP_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        //system
        \DB::table('permissions')->insert([
            'name'=>"SYSTEM-SETTING-GROUP",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('permissions')->insert([
            'name'=>"SYSTEM-USERS-GROUP",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        $modules = [
            'DASHBOARD',
            'DMASTER-KELOMPOK-URUSAN',
            'DMASTER-BIDANG-URUSAN',
            'DMASTER-REKENING-TRANSAKSI',
            'DMASTER-REKENING-KELOMPOK',
            'DMASTER-REKENING-JENIS',
            'DMASTER-REKENING-RINCIAN',
            'DMASTER-REKENING-OBJEK',
            'DMASTER-OPD',
            'DMASTER-UNIT-KERJA',
            'DMASTER-JENIS-PELAKSANAAN',
            'DMASTER-JENIS-PEMBANGUNAN',
            'DMASTER-ASN',
            'DMASTER-PEJABAT',
            'DMASTER-TA',            

            'RENJA-RKA-MURNI',
            'RENJA-RKA-PERUBAHAN',

            'SYSTEM-SETTING-PERMISSIONS',
            'SYSTEM-SETTING-ROLES',

            'SYSTEM-USERS-SUPERADMIN',
            'SYSTEM-USERS-USERS-BAPELITBANG',
            'SYSTEM-USERS-USERS-OPD',
            'SYSTEM-USERS-USERS-PPTK',
            'SYSTEM-USERS-USERS-DEWAN',
            'SYSTEM-USERS-USERS-TAPD',            
        ];
        $records=[];
        foreach($modules as $v)
        {
            $records=array(
                ['name'=>"{$v}_BROWSE",'guard_name'=>'api','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],
                ['name'=>"{$v}_SHOW",'guard_name'=>'api','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],
                ['name'=>"{$v}_STORE",'guard_name'=>'api','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],
                ['name'=>"{$v}_UPDATE",'guard_name'=>'api','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()],
                ['name'=>"{$v}_DESTROY",'guard_name'=>'api','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]
            );
            \DB::table('permissions')->insert($records);
        }
        
        \DB::table('permissions')->insert([
            'name'=>"RPJMD-EVALUASI_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('permissions')->insert([
            'name'=>"EVALUASI-EVALUASI_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('permissions')->insert([
            'name'=>"RKPD-EVALUASI-MURNI_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        \DB::table('permissions')->insert([
            'name'=>"RKPD-EVALUASI-PERUBAHAN_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('permissions')->insert([
            'name'=>"RENJA-FORM-A-MURNI_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        \DB::table('permissions')->insert([
            'name'=>"RENJA-FORM-A-PERUBAHAN_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        \DB::table('permissions')->insert([
            'name'=>"RENJA-FORM-B-MURNI_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        \DB::table('permissions')->insert([
            'name'=>"RENJA-FORM-B-PERUBAHAN_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        \DB::table('permissions')->insert([
            'name'=>"RENJA-EVALUASI-MURNI_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        \DB::table('permissions')->insert([
            'name'=>"RENJA-EVALUASI-PERUBAHAN_BROWSE",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);        

        \DB::table('permissions')->insert([
            'name'=>"USER_STOREPERMISSIONS",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        \DB::table('permissions')->insert([
            'name'=>"USER_REVOKEPERMISSIONS",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}