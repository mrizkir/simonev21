<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;
use Exception;

use PhpOffice\PhpSpreadsheet\IOFactory;

use Ramsey\Uuid\Uuid;

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
    try 
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
        $meta_file = pathinfo($path_file);
        if($meta_file['extension'] == 'xlsx')
        {
          \Log::channel(self::LOG_CHANNEL)->info("$$$$$$ PROSES FILE EXCEL $file $$$$$$$");
          \Log::channel(self::LOG_CHANNEL)->info("** PROSES FILE EXCEL $path_file"); 
          
          if(\App\Helpers\Helper::isValidDate($meta_file['filename']))
          { 
            $tahun = \App\Helpers\Helper::tanggal('Y', $meta_file['filename']);
            $bulan = \App\Helpers\Helper::tanggal('n', $meta_file['filename']);
            
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
              $no_urut = 1;
              foreach ($sheetData as $row)
              {     
                if($no_urut > 1 && $row['AB'] > 0)
                {
                  $kode_opd[0] = str_replace('.', '-', substr($row['B'], 0, 4));
                  $kode_opd[1] = str_replace('.', '-', substr($row['B'], 5, 4));
                  $kode_opd[2] = str_replace('.', '-', substr($row['B'], 10, 4));
                  $kode_opd[3] = substr($row['B'], 15, 2);
                  
                  $kode_opd_ = "{$kode_opd[0]}.{$kode_opd[1]}.{$kode_opd[2]}.{$kode_opd[3]}";

                  $kode_sub_organisasi[0] = str_replace('.', '-', substr($row['B'], 0, 4));
                  $kode_sub_organisasi[1] = str_replace('.', '-', substr($row['B'], 5, 4));
                  $kode_sub_organisasi[2] = str_replace('.', '-', substr($row['B'], 10, 4));
                  $kode_sub_organisasi[3] = substr($row['B'], 15, 2);
                  $kode_sub_organisasi[4] = (int)substr($row['B'], 18, 4);
                  $kode_sub_organisasi[4] = $kode_sub_organisasi[4] == '0' ? '01' : str_pad($kode_sub_organisasi[4], 2, '0', STR_PAD_LEFT);
                  
                  $kode_sub_organisasi_ = "{$kode_sub_organisasi[0]}.{$kode_sub_organisasi[1]}.{$kode_sub_organisasi[2]}.{$kode_sub_organisasi[3]}.{$kode_sub_organisasi[4]}";

                  $kode_rekening = $row['T'];

                  \Log::channel(self::LOG_CHANNEL)->info("** KODE ORGANISASI = $kode_opd_");
                  \Log::channel(self::LOG_CHANNEL)->info("** NAMA ORGANISASI = {$row['C']}");
                  \Log::channel(self::LOG_CHANNEL)->info("** KODE SUB ORGANISASI = $kode_sub_organisasi_");
                  \Log::channel(self::LOG_CHANNEL)->info("** NAMA SUB ORGANISASI = {$row['E']}");
                  \Log::channel(self::LOG_CHANNEL)->info("** KODE SUB KEGIATAN = {$row['R']}");
                  \Log::channel(self::LOG_CHANNEL)->info("** NAMA SUB KEGIATAN = {$row['S']}");
                  \Log::channel(self::LOG_CHANNEL)->info("** KODE REKENING = $kode_rekening");
                  \Log::channel(self::LOG_CHANNEL)->info("** NAMA REKENING = {$row['U']}");
                  \Log::channel(self::LOG_CHANNEL)->info("** REALISASI = {$row['AB']}");
            
                  \DB::table('sipd_realisasi')
                  ->where('kode_sub_organisasi', $kode_sub_organisasi_)
                  ->where('kode_rekening', $row['T'])
                  ->where('TA', $tahun)
                  ->where('bulan1', $bulan)
                  ->where('EntryLevel', 1)
                  ->delete();
                  
                  $data_sipd_realisasi = [
                    'SIPDID' => Uuid::uuid4()->toString(),                    
                    'kode_organisasi' => $kode_opd_,
                    'Nm_Organisasi' => $row['C'],
                    'kode_sub_organisasi' => $kode_sub_organisasi_,
                    'Nm_Sub_Organisasi' => $row['E'],
                    'kode_urusan' => $row['J'],
                    'nama_urusan' => $row['K'],
                    'kode_bidang_urusan' => $row['L'],
                    'nama_bidang_urusan' => $row['M'],
                    'kode_program' => $row['N'],
                    'nama_program' => $row['O'],
                    'kode_kegiatan' => $row['P'],
                    'nama_kegiatan' => $row['Q'],
                    'kode_sub_kegiatan' => $row['R'],
                    'nama_sub_kegiatan' => $row['S'],
                    'kode_rekening' => $row['T'],
                    'nama_rekening' => $row['U'],
                    'Realisasi1' => $row['AB'],
                    'bulan1' => $bulan,
                    'TA' => $tahun,
                    'EntryLevel' => 1,
                    'created_at' => \App\Helpers\Helper::tanggal('Y-m-d H:i:s'),
                    'updated_at' => \App\Helpers\Helper::tanggal('Y-m-d H:i:s'),
                  ];

                  $data_rekening = \DB::table('trRKARinc AS a')
                  ->select(\DB::raw('
                    b.OrgID,
                    b.SOrgID,
                    b.PrgID,
                    b.KgtID,
                    b.SubKgtID,
                    a.RKAID,
                    a.RKARincID
                  '))
                  ->join('trRKA AS b', 'a.RKAID', 'b.RKAID')
                  ->where('kode_organisasi', $kode_opd_)
                  ->where('kode_sub_organisasi', $kode_sub_organisasi_)
                  ->where('kode_uraian1', $kode_rekening)
                  ->where('A.TA', $tahun)
                  ->where('A.EntryLvl', 1)
                  ->first();

                  if(!is_null($data_rekening))
                  {
                    $data_sipd_realisasi['OrgID'] = $data_rekening->OrgID;
                    $data_sipd_realisasi['SOrgID'] = $data_rekening->SOrgID;
                    $data_sipd_realisasi['PrgID'] = $data_rekening->PrgID;
                    $data_sipd_realisasi['KgtID'] = $data_rekening->KgtID;
                    $data_sipd_realisasi['SubKgtID'] = $data_rekening->SubKgtID;
                    $data_sipd_realisasi['RKAID'] = $data_rekening->RKAID;
                    $data_sipd_realisasi['RKARincID'] = $data_rekening->RKARincID;
                  }
                  \DB::table('sipd_realisasi')
                  ->insert($data_sipd_realisasi);    
                  
                  \Log::channel(self::LOG_CHANNEL)->info("** DONE **");
                }
                $no_urut += 1;
              }
            }
            if (Storage::disk('local')->exists("realisasi_m/$file")) {
              Storage::disk('local')->delete("realisasi_m/$file");
              \Log::channel(self::LOG_CHANNEL)->info("** FILE $file dihapus");
            }            
            else
            {
              \Log::channel(self::LOG_CHANNEL)->info("** FILE $path_file tidak ada");
            }
            \Log::channel(self::LOG_CHANNEL)->info("$$$$$$ ALL DONE $$$$$$$");
          }
          else
          {
            throw new Exception("Nama file tidak valid $file");
          }
        }
      }
    }
    catch(Exception $e) 
    {
      \Log::channel(self::LOG_CHANNEL)->error($e->getMessage());   
    } 
  }
}
