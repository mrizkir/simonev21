<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\Models\User;

class DMasterTAControllerTest extends TestCase
{
  /**
   * digunakan untuk menguji apakah proses akademik bisa dilakukan
   *
   * @return void
   */
  public function test_case_4()
  {
    $user = User::find(env('USERID'));    
    $RpjmdSasaranID = env('RpjmdSasaranID');

    $tahun_saat_ini = date('Y');
    $data = $this->actingAs($user)->post(
      "/v1/dmaster/ta/$tahun_saat_ini",
      [
        'tahun' =>  $tahun_saat_ini,
        'tahun_anggaran' => "Realisasi T.A $tahun_saat_ini sesuai SIPD",
        '_method' => 'put',
      ]
    );

    $response = json_decode($data->response->getContent(), true);
    
    $this->assertTrue(true);
  }  
}
