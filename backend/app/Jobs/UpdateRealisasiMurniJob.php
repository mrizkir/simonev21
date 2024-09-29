<?php

namespace App\Jobs;

use PhpOffice\PhpSpreadsheet\IOFactory;

class UpdateRealisasiMurniJob extends Job
{
  const LOG_CHANNEL = 'update-realisasi-murni';
  
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
    $inputFileType = 'Xlsx';

    // dapatkan content dalam file
    $files = scandir($directory);

    // Remove '.' and '..' from the list
    $files = array_diff($files, array('.', '..'));

    // Loop through each file and display it in the table
    foreach ($files as $file) 
    {
      $path_file = "$directory/$file";
      $ext = pathinfo($path_file, PATHINFO_EXTENSION);
      if($ext == 'xlsx')
      {
        \Log::channel(self::LOG_CHANNEL)->info("$$$$$$ PROSES FILE EXCEL $file $$$$$$$");
        \Log::channel(self::LOG_CHANNEL)->info("** PROSES FILE EXCEL $path_file");
        
        $reader = IOFactory::createReader($inputFileType);        
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path_file);

        // Access the worksheet
        $worksheet = $spreadsheet->getActiveSheet();

        // Get the highest row and column numbers
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $chunkSize = 20;

        for ($startRow = 2; $startRow <= $highestRow; $startRow += $chunkSize)
        {
          // Create a new Instance of our Read Filter, passing in the limits on which rows we want to read
          $chunkFilter = new \App\Helpers\PhpSpreadsheetChunkReadFilter($startRow, $chunkSize);
          // Tell the Reader that we want to use the new Read Filter that we've just Instantiated
          $reader->setReadFilter($chunkFilter);
          // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
          $spreadsheet = $reader->load($path_file);
          
          $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
          foreach ($sheetData as $row) {
            // Iterate over each cell in the current row
            foreach ($row as $cell) {
              \Log::channel(self::LOG_CHANNEL)->info("$cell");
            }
          }
        }
      }
    }
  }
}
