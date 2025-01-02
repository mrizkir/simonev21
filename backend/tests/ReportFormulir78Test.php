<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\Models\User;

class TestReportFormulir78 extends TestCase
{
  /**
   * digunakan untuk menguji apakah sasaran memiliki daftar program
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
  /**
   * digunakan untuk menguji apakah program di setiap sasaran memiliki indikator kinerja 
   *
   * @return void
   */
  public function test_case_2()
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

    foreach($payload as $v)
    {
      $this->assertTrue(count($v['indikator_kinerja']) > 0, "Jumlah indikator kinerja dari program {$v['Nm_ProgramRPJMD']} = 0");
    }
  }
}
