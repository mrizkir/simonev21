<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class JenisSumberDanaTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \DB::statement('DELETE FROM tmJenisSumberDana');

    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 1,
      'Nm_Alias' => 'DAU',
      'Nm_Jenis_SumberDana' => 'DANA ALOKASI UMUM',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);

    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 2,
      'Nm_Alias'=>'DAK FISIK',
      'Nm_Jenis_SumberDana' => 'DANA ALOKASI KHUSUS FISIK',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);

    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 3,
      'Nm_Alias' => 'DAK NON FISIK',
      'Nm_Jenis_SumberDana' => 'DANA ALOKASI KHUSUS NON FISIK',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);

    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 4,
      'Nm_Alias' => 'PENDAPATAN',
      'Nm_Jenis_SumberDana' => 'PENDAPATAN',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);
    
    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 5,
      'Nm_Alias' => 'PAD',
      'Nm_Jenis_SumberDana' => 'PENDAPATAN ASLI DAERAH',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);
    
    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 6,
      'Nm_Alias' => 'DBH',
      'Nm_Jenis_SumberDana' => 'DANA BAGI HASIL',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);
    
    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 7,
      'Nm_Alias' => 'PAJAK',
      'Nm_Jenis_SumberDana' => 'PAJAK',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);

    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 8,
      'Nm_Alias' => 'PBJ',
      'Nm_Jenis_SumberDana' => 'PBJ',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);
    
    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 9,
      'Nm_Alias' => 'SILPA',
      'Nm_Jenis_SumberDana' => 'SISA LEBIH PEMBIAYAAN ANGGARAN',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);
    
    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 10,
      'Nm_Alias' => 'BANTUAN PRPIN`',
      'Nm_Jenis_SumberDana' => 'BANTUAN PROGRAM INDONESIA PINTAR`',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);
    
    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 11,
      'Nm_Alias' => 'RETRIBUSI',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);
    
    \DB::table('tmJenisSumberDana')->insert([
      'Id_Jenis_SumberDana' => 12,
      'Nm_Alias' => 'DD',
      'Nm_Jenis_SumberDana' => 'DANA DESENTRALISASI',
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);
  }
}
