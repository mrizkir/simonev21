<?php

namespace App\Jobs;

use Log;

class UpdateRealisasiMurniJob extends Job
{
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    //dapatkan daftar file		
    $directory = app()->basePath('storage/app/realisasi_m');

    // dapatkan content dalam file
    $files = scandir($directory);

    // Remove '.' and '..' from the list
    $files = array_diff($files, array('.', '..'));

    
    // Loop through each file and display it in the table
    foreach ($files as $file) 
    {
      echo "$file";
      Log::info($file);
    }
  }
}
