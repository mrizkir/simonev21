<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\Models\User;

class TestReportFormulir78 extends TestCase
{
  /**
   * digunakan untuk menguji daftar program dari satu sasaran
   *
   * @return void
   */
  public function test_case_1()
  {
    $user = User::find(env('USERID'));    
    $RpjmdSasaranID = env('RpjmdSasaranID');

    $data = $this->actingAs($user)->post(
      '/v1/rpjmd/report/formulire78',
      [
        'PeriodeRPJMDID' => env('PeriodeRPJMDID'),
        'RpjmdSasaranID' => env('RpjmdSasaranID'),
      ]
    );

    $response = json_decode($data->response->getContent(), true);

    $payload = $response['payload']['data'];
    
    $this->assertTrue(count($payload) > 0, "Jumlah program di sasaran dengan id ($RpjmdSasaranID) = 0");
  }
}
