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
            'name'=>"DASHBOARD_SHOW",
            'guard_name'=>'api',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
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
            'DMASTER-KODEFIKASI-KELOMPOK-URUSAN',
            'DMASTER-KODEFIKASI-BIDANG-URUSAN',
            'DMASTER-KODEFIKASI-PROGRAM',
            'DMASTER-KODEFIKASI-KEGIATAN',
            'DMASTER-KODEFIKASI-SUB-KEGIATAN',

            'DMASTER-KODEFIKASI-REKENING-AKUN',
            'DMASTER-KODEFIKASI-REKENING-KELOMPOK',
            'DMASTER-KODEFIKASI-REKENING-JENIS',
            'DMASTER-KODEFIKASI-REKENING-OBJEK',
            'DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK',
            'DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK',

            'DMASTER-SUMBER-DANA',

            'DMASTER-OPD',
            'DMASTER-UNIT-KERJA',
            'DMASTER-JENIS-PELAKSANAAN',
            'DMASTER-JENIS-PEMBANGUNAN',
            'DMASTER-ASN',
            'DMASTER-PEJABAT',
            'DMASTER-TA',            

            'RPJMD-VISI',
            'RPJMD-MISI',
            'RPJMD-TUJUAN',
            'RPJMD-SASARAN',
            'RPJMD-STRATEGI',
            'RPJMD-ARAH-KEBIJAKAN',
            'RPJMD-PROGRAM-PEMBANGUNAN',
            'RPJMD-INDIKASI-PROGRAM',

            'RENSTRA-TUJUAN',
            'RENSTRA-SASARAN',
            'RENSTRA-STRATEGI',
            'RENSTRA-ARAH-KEBIJAKAN',

            'RENJA-RKA-MURNI',
            'RENJA-RKA-PERUBAHAN',

            'SYSTEM-SETTING-PERMISSIONS',
            'SYSTEM-SETTING-ROLES',

            'SYSTEM-USERS-SUPERADMIN',
            'SYSTEM-USERS-BAPELITBANG',
            'SYSTEM-USERS-OPD',
            'SYSTEM-USERS-PPTK',
            'SYSTEM-USERS-DEWAN',
            'SYSTEM-USERS-TAPD',            
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
            'name'=>"RENSTRA-EVALUASI_BROWSE",
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