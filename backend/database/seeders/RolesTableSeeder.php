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
            'DMASTER-GROUP',
            'RPJMD-GROUP',
            'RENSTRA-GROUP',
            'RKPD-GROUP',
            'RENJA-GROUP',
            'SYSTEM-SETTING-GROUP',
            'SYSTEM-USERS-GROUP',
            
            'DMASTER-KODEFIKASI-URUSAN_BROWSE',
            'DMASTER-KODEFIKASI-URUSAN_STORE',
            'DMASTER-KODEFIKASI-URUSAN_SHOW',
            'DMASTER-KODEFIKASI-URUSAN_UPDATE',
            'DMASTER-KODEFIKASI-URUSAN_DESTROY',

            'DMASTER-KODEFIKASI-BIDANG-URUSAN_BROWSE',
            'DMASTER-KODEFIKASI-BIDANG-URUSAN_STORE',
            'DMASTER-KODEFIKASI-BIDANG-URUSAN_SHOW',
            'DMASTER-KODEFIKASI-BIDANG-URUSAN_UPDATE',
            'DMASTER-KODEFIKASI-BIDANG-URUSAN_DESTROY',

            'DMASTER-KODEFIKASI-PROGRAM_BROWSE',
            'DMASTER-KODEFIKASI-PROGRAM_STORE',
            'DMASTER-KODEFIKASI-PROGRAM_SHOW',
            'DMASTER-KODEFIKASI-PROGRAM_UPDATE',
            'DMASTER-KODEFIKASI-PROGRAM_DESTROY',
            
            'DMASTER-KODEFIKASI-KEGIATAN_BROWSE',
            'DMASTER-KODEFIKASI-KEGIATAN_STORE',
            'DMASTER-KODEFIKASI-KEGIATAN_SHOW',
            'DMASTER-KODEFIKASI-KEGIATAN_UPDATE',
            'DMASTER-KODEFIKASI-KEGIATAN_DESTROY',

            'DMASTER-KODEFIKASI-SUB-KEGIATAN_BROWSE',
            'DMASTER-KODEFIKASI-SUB-KEGIATAN_STORE',
            'DMASTER-KODEFIKASI-SUB-KEGIATAN_SHOW',
            'DMASTER-KODEFIKASI-SUB-KEGIATAN_UPDATE',
            'DMASTER-KODEFIKASI-SUB-KEGIATAN_DESTROY',
            
            'DMASTER-KODEFIKASI-REKENING-AKUN_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-AKUN_STORE',
            'DMASTER-KODEFIKASI-REKENING-AKUN_SHOW',
            'DMASTER-KODEFIKASI-REKENING-AKUN_UPDATE',
            'DMASTER-KODEFIKASI-REKENING-AKUN_DESTROY',

            'DMASTER-KODEFIKASI-REKENING-KELOMPOK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-KELOMPOK_STORE',
            'DMASTER-KODEFIKASI-REKENING-KELOMPOK_SHOW',
            'DMASTER-KODEFIKASI-REKENING-KELOMPOK_UPDATE',
            'DMASTER-KODEFIKASI-REKENING-KELOMPOK_DESTROY',

            'DMASTER-KODEFIKASI-REKENING-JENIS_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-JENIS_STORE',
            'DMASTER-KODEFIKASI-REKENING-JENIS_SHOW',
            'DMASTER-KODEFIKASI-REKENING-JENIS_UPDATE',
            'DMASTER-KODEFIKASI-REKENING-JENIS_DESTROY',

            'DMASTER-KODEFIKASI-REKENING-OBJEK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-OBJEK_STORE',
            'DMASTER-KODEFIKASI-REKENING-OBJEK_SHOW',
            'DMASTER-KODEFIKASI-REKENING-OBJEK_UPDATE',
            'DMASTER-KODEFIKASI-REKENING-OBJEK_DESTROY',

            'DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_STORE',
            'DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_SHOW',
            'DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_UPDATE',
            'DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_DESTROY',
            
            'DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_STORE',
            'DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_SHOW',
            'DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_UPDATE',
            'DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_DESTROY',
            
            'DMASTER-SUMBER-DANA_BROWSE',
            'DMASTER-SUMBER-DANA_STORE',
            'DMASTER-SUMBER-DANA_SHOW',
            'DMASTER-SUMBER-DANA_UPDATE',
            'DMASTER-SUMBER-DANA_DESTROY',

            'DMASTER-OPD_BROWSE',
            'DMASTER-OPD_STORE',
            'DMASTER-OPD_SHOW',
            'DMASTER-OPD_UPDATE',
            'DMASTER-OPD_DESTROY',
            
            'DMASTER-UNIT-KERJA_BROWSE',
            'DMASTER-UNIT-KERJA_STORE',
            'DMASTER-UNIT-KERJA_SHOW',
            'DMASTER-UNIT-KERJA_UPDATE',
            'DMASTER-UNIT-KERJA_DESTROY',

            'DMASTER-JENIS-PELAKSANAAN_BROWSE',
            'DMASTER-JENIS-PELAKSANAAN_STORE',
            'DMASTER-JENIS-PELAKSANAAN_SHOW',
            'DMASTER-JENIS-PELAKSANAAN_UPDATE',
            'DMASTER-JENIS-PELAKSANAAN_DESTROY',

            'DMASTER-JENIS-PEMBANGUNAN_BROWSE',
            'DMASTER-JENIS-PEMBANGUNAN_STORE',
            'DMASTER-JENIS-PEMBANGUNAN_SHOW',
            'DMASTER-JENIS-PEMBANGUNAN_UPDATE',
            'DMASTER-JENIS-PEMBANGUNAN_DESTROY',

            'DMASTER-ASN_BROWSE',
            'DMASTER-ASN_STORE',
            'DMASTER-ASN_SHOW',
            'DMASTER-ASN_UPDATE',
            'DMASTER-ASN_DESTROY',

            'DMASTER-PEJABAT_BROWSE',
            'DMASTER-PEJABAT_STORE',
            'DMASTER-PEJABAT_SHOW',
            'DMASTER-PEJABAT_UPDATE',
            'DMASTER-PEJABAT_DESTROY',

            'DMASTER-TA_BROWSE',
            'DMASTER-TA_STORE',
            'DMASTER-TA_SHOW',
            'DMASTER-TA_UPDATE',
            'DMASTER-TA_DESTROY',

            'RPJMD-VISI_BROWSE',
            'RPJMD-VISI_STORE',
            'RPJMD-VISI_SHOW',
            'RPJMD-VISI_UPDATE',
            'RPJMD-VISI_DESTROY',

            'RPJMD-MISI_BROWSE',
            'RPJMD-MISI_STORE',
            'RPJMD-MISI_SHOW',
            'RPJMD-MISI_UPDATE',
            'RPJMD-MISI_DESTROY',          

            'RPJMD-TUJUAN_BROWSE',
            'RPJMD-TUJUAN_STORE',
            'RPJMD-TUJUAN_SHOW',
            'RPJMD-TUJUAN_UPDATE',
            'RPJMD-TUJUAN_DESTROY',          
            
            'RPJMD-SASARAN_BROWSE',
            'RPJMD-SASARAN_STORE',
            'RPJMD-SASARAN_SHOW',
            'RPJMD-SASARAN_UPDATE',
            'RPJMD-SASARAN_DESTROY',

            'RPJMD-STRATEGI_BROWSE',
            'RPJMD-STRATEGI_STORE',
            'RPJMD-STRATEGI_SHOW',
            'RPJMD-STRATEGI_UPDATE',
            'RPJMD-STRATEGI_DESTROY',

            'RPJMD-ARAH-KEBIJAKAN_BROWSE',
            'RPJMD-ARAH-KEBIJAKAN_STORE',
            'RPJMD-ARAH-KEBIJAKAN_SHOW',
            'RPJMD-ARAH-KEBIJAKAN_UPDATE',
            'RPJMD-ARAH-KEBIJAKAN_DESTROY',

            'RPJMD-PROGRAM-PEMBANGUNAN_BROWSE',
            'RPJMD-PROGRAM-PEMBANGUNAN_STORE',
            'RPJMD-PROGRAM-PEMBANGUNAN_SHOW',
            'RPJMD-PROGRAM-PEMBANGUNAN_UPDATE',
            'RPJMD-PROGRAM-PEMBANGUNAN_DESTROY',

            'RPJMD-INDIKASI-PROGRAM_BROWSE',
            'RPJMD-INDIKASI-PROGRAM_STORE',
            'RPJMD-INDIKASI-PROGRAM_SHOW',
            'RPJMD-INDIKASI-PROGRAM_UPDATE',
            'RPJMD-INDIKASI-PROGRAM_DESTROY',
            'RPJMD-EVALUASI_BROWSE',

            'RENSTRA-TUJUAN_BROWSE',
            'RENSTRA-TUJUAN_STORE',
            'RENSTRA-TUJUAN_SHOW',
            'RENSTRA-TUJUAN_UPDATE',
            'RENSTRA-TUJUAN_DESTROY',

            'RENSTRA-SASARAN_BROWSE',
            'RENSTRA-SASARAN_STORE',
            'RENSTRA-SASARAN_SHOW',
            'RENSTRA-SASARAN_UPDATE',
            'RENSTRA-SASARAN_DESTROY',

            'RENSTRA-STRATEGI_BROWSE',
            'RENSTRA-STRATEGI_STORE',
            'RENSTRA-STRATEGI_SHOW',
            'RENSTRA-STRATEGI_UPDATE',
            'RENSTRA-STRATEGI_DESTROY',

            'RENSTRA-ARAH-KEBIJAKAN_BROWSE',
            'RENSTRA-ARAH-KEBIJAKAN_STORE',
            'RENSTRA-ARAH-KEBIJAKAN_SHOW',
            'RENSTRA-ARAH-KEBIJAKAN_UPDATE',
            'RENSTRA-ARAH-KEBIJAKAN_DESTROY',          
            'RENSTRA-EVALUASI_BROWSE',

            'RENJA-RKA-MURNI_BROWSE',
            'RENJA-RKA-MURNI_STORE',
            'RENJA-RKA-MURNI_SHOW',
            'RENJA-RKA-MURNI_UPDATE',
            'RENJA-RKA-MURNI_DESTROY',

            'RENJA-RKA-PERUBAHAN_BROWSE',
            'RENJA-RKA-PERUBAHAN_STORE',
            'RENJA-RKA-PERUBAHAN_SHOW',
            'RENJA-RKA-PERUBAHAN_UPDATE',
            'RENJA-RKA-PERUBAHAN_DESTROY',

            'RENJA-EVALUASI-MURNI_BROWSE',
            'RENJA-EVALUASI-PERUBAHAN_BROWSE',

            'RENJA-FORM-A-MURNI_BROWSE',
            'RENJA-FORM-A-PERUBAHAN_BROWSE',

            'RENJA-FORM-B-MURNI_BROWSE',
            'RENJA-FORM-B-PERUBAHAN_BROWSE',

            'RKPD-EVALUASI-MURNI_BROWSE',
            'RKPD-EVALUASI-PERUBAHAN_BROWSE',
            
            'USER_STOREPERMISSIONS',
            'USER_REVOKEPERMISSIONS',

            'SYSTEM-USERS-BAPELITBANG_BROWSE',
            'SYSTEM-USERS-BAPELITBANG_STORE',
            'SYSTEM-USERS-BAPELITBANG_SHOW',
            'SYSTEM-USERS-BAPELITBANG_UPDATE',
            'SYSTEM-USERS-BAPELITBANG_DESTROY',

            'SYSTEM-USERS-OPD_BROWSE',
            'SYSTEM-USERS-OPD_STORE',
            'SYSTEM-USERS-OPD_SHOW',
            'SYSTEM-USERS-OPD_UPDATE',
            'SYSTEM-USERS-OPD_DESTROY',

            'SYSTEM-USERS-PPTK_BROWSE',
            'SYSTEM-USERS-PPTK_STORE',
            'SYSTEM-USERS-PPTK_SHOW',
            'SYSTEM-USERS-PPTK_UPDATE',
            'SYSTEM-USERS-PPTK_DESTROY',

            'SYSTEM-USERS-DEWAN_BROWSE',
            'SYSTEM-USERS-DEWAN_STORE',
            'SYSTEM-USERS-DEWAN_SHOW',
            'SYSTEM-USERS-DEWAN_UPDATE',
            'SYSTEM-USERS-DEWAN_DESTROY',

            'SYSTEM-USERS-TAPD_BROWSE',
            'SYSTEM-USERS-TAPD_STORE',
            'SYSTEM-USERS-TAPD_SHOW',
            'SYSTEM-USERS-TAPD_UPDATE',
            'SYSTEM-USERS-TAPD_DESTROY',

        ];
        $role->syncPermissions($records);
        
        $role = Role::findByName('opd');
        $records=[
            'DASHBOARD_SHOW',
            'DMASTER-GROUP',
            'RPJMD-GROUP',
            'RENSTRA-GROUP',
            'RKPD-GROUP',
            'RENJA-GROUP',
            'SYSTEM-SETTING-GROUP',
            'SYSTEM-USERS-GROUP',
            
            'DMASTER-KODEFIKASI-URUSAN_BROWSE',
            'DMASTER-KODEFIKASI-URUSAN_SHOW',

            'DMASTER-KODEFIKASI-BIDANG-URUSAN_BROWSE',
            'DMASTER-KODEFIKASI-BIDANG-URUSAN_SHOW',
            

            'DMASTER-KODEFIKASI-PROGRAM_BROWSE',
            'DMASTER-KODEFIKASI-PROGRAM_SHOW',
            
            'DMASTER-KODEFIKASI-KEGIATAN_BROWSE',
            'DMASTER-KODEFIKASI-KEGIATAN_SHOW',

            'DMASTER-KODEFIKASI-SUB-KEGIATAN_BROWSE',
            'DMASTER-KODEFIKASI-SUB-KEGIATAN_SHOW',
            
            'DMASTER-KODEFIKASI-REKENING-AKUN_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-AKUN_SHOW',

            'DMASTER-KODEFIKASI-REKENING-KELOMPOK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-KELOMPOK_SHOW',

            'DMASTER-KODEFIKASI-REKENING-JENIS_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-JENIS_SHOW',

            'DMASTER-KODEFIKASI-REKENING-OBJEK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-OBJEK_SHOW',

            'DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_SHOW',
            
            'DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_SHOW',
            
            'DMASTER-SUMBER-DANA_BROWSE',
            'DMASTER-SUMBER-DANA_SHOW',

            'DMASTER-OPD_BROWSE',
            'DMASTER-OPD_STORE',
            'DMASTER-OPD_SHOW',
            'DMASTER-OPD_UPDATE',
            
            'DMASTER-UNIT-KERJA_BROWSE',
            'DMASTER-UNIT-KERJA_STORE',
            'DMASTER-UNIT-KERJA_SHOW',
            'DMASTER-UNIT-KERJA_UPDATE',

            'DMASTER-JENIS-PELAKSANAAN_BROWSE',
            'DMASTER-JENIS-PELAKSANAAN_SHOW',

            'DMASTER-JENIS-PEMBANGUNAN_BROWSE',
            'DMASTER-JENIS-PEMBANGUNAN_SHOW',

            'DMASTER-ASN_BROWSE',
            'DMASTER-ASN_SHOW',

            'DMASTER-PEJABAT_BROWSE',
            'DMASTER-PEJABAT_STORE',
            'DMASTER-PEJABAT_SHOW',
            'DMASTER-PEJABAT_UPDATE',
            'DMASTER-PEJABAT_DESTROY',

            'DMASTER-TA_BROWSE',
            'DMASTER-TA_SHOW',

            'RPJMD-VISI_BROWSE',
            'RPJMD-VISI_SHOW',

            'RPJMD-MISI_BROWSE',
            'RPJMD-MISI_SHOW',

            'RPJMD-TUJUAN_BROWSE',
            'RPJMD-TUJUAN_SHOW',
            
            'RPJMD-SASARAN_BROWSE',
            'RPJMD-SASARAN_SHOW',

            'RPJMD-STRATEGI_BROWSE',
            'RPJMD-STRATEGI_SHOW',

            'RPJMD-ARAH-KEBIJAKAN_BROWSE',
            'RPJMD-ARAH-KEBIJAKAN_SHOW',

            'RPJMD-PROGRAM-PEMBANGUNAN_BROWSE',
            'RPJMD-PROGRAM-PEMBANGUNAN_SHOW',

            'RPJMD-INDIKASI-PROGRAM_BROWSE',
            'RPJMD-INDIKASI-PROGRAM_SHOW',            

            'RENSTRA-TUJUAN_BROWSE',
            'RENSTRA-TUJUAN_STORE',
            'RENSTRA-TUJUAN_SHOW',
            'RENSTRA-TUJUAN_UPDATE',
            'RENSTRA-TUJUAN_DESTROY',

            'RENSTRA-SASARAN_BROWSE',
            'RENSTRA-SASARAN_STORE',
            'RENSTRA-SASARAN_SHOW',
            'RENSTRA-SASARAN_UPDATE',
            'RENSTRA-SASARAN_DESTROY',

            'RENSTRA-STRATEGI_BROWSE',
            'RENSTRA-STRATEGI_STORE',
            'RENSTRA-STRATEGI_SHOW',
            'RENSTRA-STRATEGI_UPDATE',
            'RENSTRA-STRATEGI_DESTROY',

            'RENSTRA-ARAH-KEBIJAKAN_BROWSE',
            'RENSTRA-ARAH-KEBIJAKAN_STORE',
            'RENSTRA-ARAH-KEBIJAKAN_SHOW',
            'RENSTRA-ARAH-KEBIJAKAN_UPDATE',
            'RENSTRA-ARAH-KEBIJAKAN_DESTROY',          
            'RENSTRA-EVALUASI_BROWSE',

            'RENJA-RKA-MURNI_BROWSE',
            'RENJA-RKA-MURNI_STORE',
            'RENJA-RKA-MURNI_SHOW',
            'RENJA-RKA-MURNI_UPDATE',
            'RENJA-RKA-MURNI_DESTROY',

            'RENJA-RKA-PERUBAHAN_BROWSE',
            'RENJA-RKA-PERUBAHAN_STORE',
            'RENJA-RKA-PERUBAHAN_SHOW',
            'RENJA-RKA-PERUBAHAN_UPDATE',
            'RENJA-RKA-PERUBAHAN_DESTROY',

            'RENJA-EVALUASI-MURNI_BROWSE',
            'RENJA-EVALUASI-PERUBAHAN_BROWSE',

            'RENJA-FORM-A-MURNI_BROWSE',
            'RENJA-FORM-A-PERUBAHAN_BROWSE',

            'RENJA-FORM-B-MURNI_BROWSE',
            'RENJA-FORM-B-PERUBAHAN_BROWSE',

            'RKPD-EVALUASI-MURNI_BROWSE',
            'RKPD-EVALUASI-PERUBAHAN_BROWSE',
            
            'USER_STOREPERMISSIONS',
            'USER_REVOKEPERMISSIONS',


            'SYSTEM-USERS-OPD_BROWSE',
            'SYSTEM-USERS-OPD_STORE',
            'SYSTEM-USERS-OPD_SHOW',
            'SYSTEM-USERS-OPD_UPDATE',
            'SYSTEM-USERS-OPD_DESTROY',

            'SYSTEM-USERS-PPTK_BROWSE',
            'SYSTEM-USERS-PPTK_STORE',
            'SYSTEM-USERS-PPTK_SHOW',
            'SYSTEM-USERS-PPTK_UPDATE',
            'SYSTEM-USERS-PPTK_DESTROY',
        ];
        $role->syncPermissions($records);    
        
        $role = Role::findByName('pptk');
        $records=[
            'DASHBOARD_SHOW',
            'DMASTER-GROUP',
            'RPJMD-GROUP',
            'RENSTRA-GROUP',
            'RKPD-GROUP',
            'RENJA-GROUP',
            
            'DMASTER-KODEFIKASI-URUSAN_BROWSE',
            'DMASTER-KODEFIKASI-URUSAN_SHOW',

            'DMASTER-KODEFIKASI-BIDANG-URUSAN_BROWSE',
            'DMASTER-KODEFIKASI-BIDANG-URUSAN_SHOW',
            

            'DMASTER-KODEFIKASI-PROGRAM_BROWSE',
            'DMASTER-KODEFIKASI-PROGRAM_SHOW',
            
            'DMASTER-KODEFIKASI-KEGIATAN_BROWSE',
            'DMASTER-KODEFIKASI-KEGIATAN_SHOW',

            'DMASTER-KODEFIKASI-SUB-KEGIATAN_BROWSE',
            'DMASTER-KODEFIKASI-SUB-KEGIATAN_SHOW',
            
            'DMASTER-KODEFIKASI-REKENING-AKUN_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-AKUN_SHOW',

            'DMASTER-KODEFIKASI-REKENING-KELOMPOK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-KELOMPOK_SHOW',

            'DMASTER-KODEFIKASI-REKENING-JENIS_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-JENIS_SHOW',

            'DMASTER-KODEFIKASI-REKENING-OBJEK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-OBJEK_SHOW',

            'DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-RINCIAN-OBJEK_SHOW',
            
            'DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_BROWSE',
            'DMASTER-KODEFIKASI-REKENING-SUB-RINCIAN-OBJEK_SHOW',
            
            'DMASTER-SUMBER-DANA_BROWSE',
            'DMASTER-SUMBER-DANA_SHOW',

            'DMASTER-OPD_BROWSE',
            'DMASTER-OPD_SHOW',   
            
            'DMASTER-UNIT-KERJA_BROWSE',
            'DMASTER-UNIT-KERJA_SHOW',            

            'DMASTER-JENIS-PELAKSANAAN_BROWSE',
            'DMASTER-JENIS-PELAKSANAAN_SHOW',

            'DMASTER-JENIS-PEMBANGUNAN_BROWSE',
            'DMASTER-JENIS-PEMBANGUNAN_SHOW',

            'DMASTER-ASN_BROWSE',
            'DMASTER-ASN_SHOW',

            'DMASTER-PEJABAT_BROWSE',
            'DMASTER-PEJABAT_SHOW',

            'DMASTER-TA_BROWSE',
            'DMASTER-TA_SHOW',

            'RPJMD-VISI_BROWSE',
            'RPJMD-VISI_SHOW',

            'RPJMD-MISI_BROWSE',
            'RPJMD-MISI_SHOW',

            'RPJMD-TUJUAN_BROWSE',
            'RPJMD-TUJUAN_SHOW',
            
            'RPJMD-SASARAN_BROWSE',
            'RPJMD-SASARAN_SHOW',

            'RPJMD-STRATEGI_BROWSE',
            'RPJMD-STRATEGI_SHOW',

            'RPJMD-ARAH-KEBIJAKAN_BROWSE',
            'RPJMD-ARAH-KEBIJAKAN_SHOW',

            'RPJMD-PROGRAM-PEMBANGUNAN_BROWSE',
            'RPJMD-PROGRAM-PEMBANGUNAN_SHOW',

            'RPJMD-INDIKASI-PROGRAM_BROWSE',
            'RPJMD-INDIKASI-PROGRAM_SHOW',

            'RPJMD-EVALUASI_BROWSE',
            
            'RENSTRA-TUJUAN_BROWSE',
            'RENSTRA-TUJUAN_SHOW',

            'RENSTRA-SASARAN_BROWSE',
            'RENSTRA-SASARAN_SHOW',

            'RENSTRA-STRATEGI_BROWSE',
            'RENSTRA-STRATEGI_SHOW',

            'RENSTRA-ARAH-KEBIJAKAN_BROWSE',
            'RENSTRA-ARAH-KEBIJAKAN_SHOW',  

            'RENSTRA-EVALUASI_BROWSE',

            'RENJA-RKA-MURNI_BROWSE',
            'RENJA-RKA-MURNI_STORE',
            'RENJA-RKA-MURNI_SHOW',
            'RENJA-RKA-MURNI_UPDATE',
            'RENJA-RKA-MURNI_DESTROY',

            'RENJA-RKA-PERUBAHAN_BROWSE',
            'RENJA-RKA-PERUBAHAN_STORE',
            'RENJA-RKA-PERUBAHAN_SHOW',
            'RENJA-RKA-PERUBAHAN_UPDATE',
            'RENJA-RKA-PERUBAHAN_DESTROY',

            'RENJA-EVALUASI-MURNI_BROWSE',
            'RENJA-EVALUASI-PERUBAHAN_BROWSE',

            'RENJA-FORM-A-MURNI_BROWSE',
            'RENJA-FORM-A-PERUBAHAN_BROWSE',

            'RENJA-FORM-B-MURNI_BROWSE',
            'RENJA-FORM-B-PERUBAHAN_BROWSE',

            'RKPD-EVALUASI-MURNI_BROWSE',
            'RKPD-EVALUASI-PERUBAHAN_BROWSE',
        ];
        $role->syncPermissions($records);    

        $role = Role::findByName('tapd');
        $records=[
            'DASHBOARD_SHOW',            
            'RPJMD-GROUP',
            'RENSTRA-GROUP',
            'RKPD-GROUP',
            'RENJA-GROUP',            
            
            'DMASTER-TA_BROWSE',
            'DMASTER-TA_SHOW',

            'RPJMD-VISI_BROWSE',
            'RPJMD-VISI_SHOW',

            'RPJMD-MISI_BROWSE',
            'RPJMD-MISI_SHOW',

            'RPJMD-TUJUAN_BROWSE',
            'RPJMD-TUJUAN_SHOW',
            
            'RPJMD-SASARAN_BROWSE',
            'RPJMD-SASARAN_SHOW',

            'RPJMD-STRATEGI_BROWSE',
            'RPJMD-STRATEGI_SHOW',

            'RPJMD-ARAH-KEBIJAKAN_BROWSE',
            'RPJMD-ARAH-KEBIJAKAN_SHOW',

            'RPJMD-PROGRAM-PEMBANGUNAN_BROWSE',
            'RPJMD-PROGRAM-PEMBANGUNAN_SHOW',

            'RPJMD-INDIKASI-PROGRAM_BROWSE',
            'RPJMD-INDIKASI-PROGRAM_SHOW',

            'RPJMD-EVALUASI_BROWSE',
            
            'RENSTRA-TUJUAN_BROWSE',
            'RENSTRA-TUJUAN_SHOW',

            'RENSTRA-SASARAN_BROWSE',
            'RENSTRA-SASARAN_SHOW',

            'RENSTRA-STRATEGI_BROWSE',
            'RENSTRA-STRATEGI_SHOW',

            'RENSTRA-ARAH-KEBIJAKAN_BROWSE',
            'RENSTRA-ARAH-KEBIJAKAN_SHOW',  

            'RENSTRA-EVALUASI_BROWSE',

            'RENJA-RKA-MURNI_BROWSE',
            'RENJA-RKA-MURNI_STORE',
            'RENJA-RKA-MURNI_SHOW',
            'RENJA-RKA-MURNI_UPDATE',
            'RENJA-RKA-MURNI_DESTROY',

            'RENJA-RKA-PERUBAHAN_BROWSE',
            'RENJA-RKA-PERUBAHAN_STORE',
            'RENJA-RKA-PERUBAHAN_SHOW',
            'RENJA-RKA-PERUBAHAN_UPDATE',
            'RENJA-RKA-PERUBAHAN_DESTROY',

            'RENJA-EVALUASI-MURNI_BROWSE',
            'RENJA-EVALUASI-PERUBAHAN_BROWSE',

            'RENJA-FORM-A-MURNI_BROWSE',
            'RENJA-FORM-A-PERUBAHAN_BROWSE',

            'RENJA-FORM-B-MURNI_BROWSE',
            'RENJA-FORM-B-PERUBAHAN_BROWSE',

            'RKPD-EVALUASI-MURNI_BROWSE',
            'RKPD-EVALUASI-PERUBAHAN_BROWSE',
        ];
        $role->syncPermissions($records);    

        $role = Role::findByName('dewan');
        $records=[
            'DASHBOARD_SHOW',            
            'RPJMD-GROUP',
            'RENSTRA-GROUP',
            'RKPD-GROUP',
            'RENJA-GROUP',            
            
            'DMASTER-TA_BROWSE',
            'DMASTER-TA_SHOW',

            'RPJMD-VISI_BROWSE',
            'RPJMD-VISI_SHOW',

            'RPJMD-MISI_BROWSE',
            'RPJMD-MISI_SHOW',

            'RPJMD-TUJUAN_BROWSE',
            'RPJMD-TUJUAN_SHOW',
            
            'RPJMD-SASARAN_BROWSE',
            'RPJMD-SASARAN_SHOW',

            'RPJMD-STRATEGI_BROWSE',
            'RPJMD-STRATEGI_SHOW',

            'RPJMD-ARAH-KEBIJAKAN_BROWSE',
            'RPJMD-ARAH-KEBIJAKAN_SHOW',

            'RPJMD-PROGRAM-PEMBANGUNAN_BROWSE',
            'RPJMD-PROGRAM-PEMBANGUNAN_SHOW',

            'RPJMD-INDIKASI-PROGRAM_BROWSE',
            'RPJMD-INDIKASI-PROGRAM_SHOW',

            'RPJMD-EVALUASI_BROWSE',
            
            'RENSTRA-TUJUAN_BROWSE',
            'RENSTRA-TUJUAN_SHOW',

            'RENSTRA-SASARAN_BROWSE',
            'RENSTRA-SASARAN_SHOW',

            'RENSTRA-STRATEGI_BROWSE',
            'RENSTRA-STRATEGI_SHOW',

            'RENSTRA-ARAH-KEBIJAKAN_BROWSE',
            'RENSTRA-ARAH-KEBIJAKAN_SHOW',  

            'RENSTRA-EVALUASI_BROWSE',

            'RENJA-RKA-MURNI_BROWSE',
            'RENJA-RKA-MURNI_STORE',
            'RENJA-RKA-MURNI_SHOW',
            'RENJA-RKA-MURNI_UPDATE',
            'RENJA-RKA-MURNI_DESTROY',

            'RENJA-RKA-PERUBAHAN_BROWSE',
            'RENJA-RKA-PERUBAHAN_STORE',
            'RENJA-RKA-PERUBAHAN_SHOW',
            'RENJA-RKA-PERUBAHAN_UPDATE',
            'RENJA-RKA-PERUBAHAN_DESTROY',

            'RENJA-EVALUASI-MURNI_BROWSE',
            'RENJA-EVALUASI-PERUBAHAN_BROWSE',

            'RENJA-FORM-A-MURNI_BROWSE',
            'RENJA-FORM-A-PERUBAHAN_BROWSE',

            'RENJA-FORM-B-MURNI_BROWSE',
            'RENJA-FORM-B-PERUBAHAN_BROWSE',

            'RKPD-EVALUASI-MURNI_BROWSE',
            'RKPD-EVALUASI-PERUBAHAN_BROWSE',
        ];
        $role->syncPermissions($records);    
    }
}
