<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM roles');
        \DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1;');
        
        \DB::table('roles')->insert([
            [
                'name'=>'superadmin',
                'guard_name'=>'api',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ], 
            [
                'name'=>'bapelitbang',
                'guard_name'=>'api',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ],    
            [
                'name'=>'tapd',
                'guard_name'=>'api',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ],   
            [
                'name'=>'opd',
                'guard_name'=>'api',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ],    
            [
                'name'=>'dewan',
                'guard_name'=>'api',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ], 
            [
                'name'=>'pptk',
                'guard_name'=>'api',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ],             
        ]); 
        
        $role = Role::findByName('bapelitbang');
        $records=[
            'DASHBOARD_SHOW',
            'DMASTER-GROUP_BROWSE',  
            'RPJMD-GROUP_BROWSE',  
            'RENSTRA-GROUP_BROWSE',  
            'RKPD-GROUP_BROWSE',
            'RENJA-GROUP_BROWSE',            
            'SYSTEM-USERS-GROUP_BROWSE',
            
            'DMASTER-KODEFIKASI-URUSAN_BROWSE',
            'DMASTER-KODEFIKASI-URUSAN_STORE',
            'DMASTER-KODEFIKASI-URUSAN_SHOW',
            'DMASTER-KODEFIKASI-URUSAN_SHOW',
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

        ];
        $role->syncPermissions($records);
    }
}
