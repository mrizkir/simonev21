<?php

namespace App\Models\RPJMD;

use App\Helpers\Helper;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\ReportModel;

class RPJMDReportArahKebijakanModel extends ReportModel
{   
  public function __construct($dataReport)
  {
    parent::__construct($dataReport);     
  }     
  public function printCascading()
  {
    $PeriodeRPJMDID = $this->dataReport['PeriodeRPJMDID'];

    $this->spreadsheet->getProperties()->setTitle("Cascading RPJMD");
    $this->spreadsheet->getProperties()->setSubject("Cascading RPJMD");     

    $sheet = $this->spreadsheet->getActiveSheet();        
    $sheet->setTitle ('Cascading RPJMD');

    $sheet->getParent()->getDefaultStyle()->applyFromArray([
      'font' => [
        'name' => 'Arial',
        'size' => '12',
      ],
    ]);
    
    $row = 1;        

    $styleArray = array(
      'font' => array('bold' => true),
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::HORIZONTAL_CENTER),
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
    );
    $sheet->getStyle("A$row:E$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row:E$row")->getAlignment()->setWrapText(true);

    $sheet->setCellValue("A$row", 'MISI');
    $sheet->setCellValue("B$row", 'TUJUAN');
    $sheet->setCellValue("C$row", 'SASARAN');
    $sheet->setCellValue("D$row", 'STRATEGI');
    $sheet->setCellValue("E$row", 'ARAH KEBIJAKAN');

    $sheet->getColumnDimension('A')->setWidth(50);
    $sheet->getColumnDimension('B')->setWidth(50);
    $sheet->getColumnDimension('C')->setWidth(50);
    $sheet->getColumnDimension('D')->setWidth(50);
    $sheet->getColumnDimension('E')->setWidth(50);

    $daftar_misi = \DB::table('tmRpjmdMisi')
    ->where('PeriodeRPJMDID', $PeriodeRPJMDID)
    ->orderBy('Kd_RpjmdMisi', 'asc')
    ->get();
    
    $row += 1;
    $row_awal = $row;
    foreach($daftar_misi as $data_misi)
    {
      $sheet->setCellValue("A$row", $data_misi->Nm_RpjmdMisi);

      $daftar_tujuan = \DB::table('tmRpjmdTujuan')
      ->where('RpjmdMisiID', $data_misi->RpjmdMisiID)
      ->orderBy('Kd_RpjmdTujuan', 'asc')
      ->get();

      if(is_null($daftar_tujuan))
      {
        $row += 1;
      }
      else
      {
        foreach($daftar_tujuan as $data_tujuan)
        {
          $sheet->setCellValue("B$row", $data_tujuan->Nm_RpjmdTujuan);

          $daftar_sasaran = \DB::table('tmRpjmdSasaran')
          ->where('RpjmdTujuanID', $data_tujuan->RpjmdTujuanID)
          ->orderBy('Kd_RpjmdSasaran', 'asc')
          ->get();

          if(is_null($daftar_sasaran))
          {
            $row += 1;
          }
          else
          {
            foreach($daftar_sasaran as $data_sasaran)
            {
              $sheet->setCellValue("C$row", $data_sasaran->Nm_RpjmdSasaran);

              $daftar_strategi = \DB::table('tmRpjmdStrategi')
              ->where('RpjmdSasaranID', $data_sasaran->RpjmdSasaranID)
              ->orderBy('Kd_RpjmdStrategi', 'asc')
              ->get();

              if(is_null($daftar_strategi))
              {
                $row += 1;
              }
              else
              {
                foreach($daftar_strategi as $data_strategi)
                {
                  $sheet->setCellValue("D$row", $data_strategi->Nm_RpjmdStrategi);

                  $daftar_arah_kebijakan = \DB::table('tmRpjmdArahKebijakan')
                  ->where('RpjmdStrategiID', $data_strategi->RpjmdStrategiID)
                  ->orderBy('Kd_RpjmdArahKebijakan', 'asc')
                  ->get();

                  if(is_null($daftar_arah_kebijakan))
                  {
                    $row += 1;
                  }
                  else
                  {
                    foreach($daftar_arah_kebijakan as $data_arah_kebijakan)
                    {
                      $sheet->setCellValue("E$row", $data_arah_kebijakan->Nm_RpjmdArahKebijakan);

                      $row += 1;
                    }
                  }
                }                
              }              
            }            
          }
        }
      }
    }
    $row = $row - 1;
    $styleArray = array(								    
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
    );   																					 
    $sheet->getStyle("A$row_awal:E$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row_awal:E$row")->getAlignment()->setWrapText(true);
    
  }
  public function printCascadingProgram()
  {
    $PeriodeRPJMDID = $this->dataReport['PeriodeRPJMDID'];

    $this->spreadsheet->getProperties()->setTitle("Cascading RPJMD");
    $this->spreadsheet->getProperties()->setSubject("Cascading RPJMD");     

    $sheet = $this->spreadsheet->getActiveSheet();        
    $sheet->setTitle ('Cascading RPJMD');

    $sheet->getParent()->getDefaultStyle()->applyFromArray([
      'font' => [
        'name' => 'Arial',
        'size' => '12',
      ],
    ]);
    
    $row = 1;        

    $styleArray = array(
      'font' => array('bold' => true),
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::HORIZONTAL_CENTER),
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
    );
    $sheet->getStyle("A$row:F$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row:F$row")->getAlignment()->setWrapText(true);

    $sheet->setCellValue("A$row", 'MISI');
    $sheet->setCellValue("B$row", 'TUJUAN');
    $sheet->setCellValue("C$row", 'SASARAN');
    $sheet->setCellValue("D$row", 'STRATEGI');
    $sheet->setCellValue("E$row", 'ARAH KEBIJAKAN');
    $sheet->setCellValue("F$row", 'PROGRAM');

    $sheet->getColumnDimension('A')->setWidth(50);
    $sheet->getColumnDimension('B')->setWidth(50);
    $sheet->getColumnDimension('C')->setWidth(50);
    $sheet->getColumnDimension('D')->setWidth(50);
    $sheet->getColumnDimension('E')->setWidth(50);
    $sheet->getColumnDimension('F')->setWidth(70);

    $daftar_misi = \DB::table('tmRpjmdMisi')
    ->where('PeriodeRPJMDID', $PeriodeRPJMDID)
    ->orderBy('Kd_RpjmdMisi', 'asc')
    ->get();
    
    $row += 1;
    $row_awal = $row;
    foreach($daftar_misi as $data_misi)
    {
      $sheet->setCellValue("A$row", $data_misi->Nm_RpjmdMisi);

      $daftar_tujuan = \DB::table('tmRpjmdTujuan')
      ->where('RpjmdMisiID', $data_misi->RpjmdMisiID)
      ->orderBy('Kd_RpjmdTujuan', 'asc')
      ->get();

      if(is_null($daftar_tujuan))
      {
        $row += 1;
      }
      else
      {
        foreach($daftar_tujuan as $data_tujuan)
        {
          $sheet->setCellValue("B$row", $data_tujuan->Nm_RpjmdTujuan);

          $daftar_sasaran = \DB::table('tmRpjmdSasaran')
          ->where('RpjmdTujuanID', $data_tujuan->RpjmdTujuanID)
          ->orderBy('Kd_RpjmdSasaran', 'asc')
          ->get();

          if(is_null($daftar_sasaran))
          {
            $row += 1;
          }
          else
          {
            foreach($daftar_sasaran as $data_sasaran)
            {
              $sheet->setCellValue("C$row", $data_sasaran->Nm_RpjmdSasaran);

              $daftar_strategi = \DB::table('tmRpjmdStrategi')
              ->where('RpjmdSasaranID', $data_sasaran->RpjmdSasaranID)
              ->orderBy('Kd_RpjmdStrategi', 'asc')
              ->get();

              if(is_null($daftar_strategi))
              {
                $row += 1;
              }
              else
              {
                foreach($daftar_strategi as $data_strategi)
                {
                  $sheet->setCellValue("D$row", $data_strategi->Nm_RpjmdStrategi);

                  $daftar_arah_kebijakan = \DB::table('tmRpjmdArahKebijakan')
                  ->where('RpjmdStrategiID', $data_strategi->RpjmdStrategiID)
                  ->orderBy('Kd_RpjmdArahKebijakan', 'asc')
                  ->get();

                  if(is_null($daftar_arah_kebijakan))
                  {
                    $row += 1;
                  }
                  else
                  {
                    foreach($daftar_arah_kebijakan as $data_arah_kebijakan)
                    {
                      $sheet->setCellValue("E$row", $data_arah_kebijakan->Nm_RpjmdArahKebijakan);
                      
                      $daftar_program = \DB::table('tmRpjmdRelasiArahKebijakanProgram AS a')
                      ->select(\DB::Raw('
                        a.Kd_ProgramRPJMD,
                        a.Nm_ProgramRPJMD
                      '))
                      ->join('tmProgram AS b', 'b.PrgID', 'a.PrgID')
                      ->where('a.RpjmdArahKebijakanID', $data_arah_kebijakan->RpjmdArahKebijakanID)                      
                      ->orderBy('a.Kd_ProgramRPJMD', 'ASC')
                      ->get();

                      $jumlah_program = count($daftar_program);
                      $sheet->setCellValue("F$row", '-');
                      if($jumlah_program > 0)
                      {
                        $i = 0;
                        $textWithNewLine = '';
                        foreach ($daftar_program as $data_program) 
                        {
                          if ($jumlah_program > $i + 1) 
                          {    
                            $textWithNewLine .= "{$data_program->Kd_ProgramRPJMD} {$data_program->Nm_ProgramRPJMD}\n";                          
                          }
                          else
                          {
                            $textWithNewLine .= "{$data_program->Kd_ProgramRPJMD} {$data_program->Nm_ProgramRPJMD}";                          
                          }
                          $i = $i + 1;      
                        }
                        $sheet->setCellValue("F$row", $textWithNewLine);                        
                      }                      
                      $row += 1;
                    }
                  }
                }                
              }              
            }            
          }
        }
      }
    }
    $row = $row - 1;
    $styleArray = array(								    
      'alignment' => array(
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_TOP
      ),
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
    );   																					 
    $sheet->getStyle("A$row_awal:F$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row_awal:F$row")->getAlignment()->setWrapText(true);
    
  }
}