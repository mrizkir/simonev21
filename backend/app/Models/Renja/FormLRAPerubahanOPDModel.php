<?php

namespace App\Models\Renja;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\ReportModel;
use App\Helpers\Helper;
use App\Helpers\HelperKegiatan;

class FormLRAPerubahanOPDModel extends ReportModel
{   
  public function __construct($dataReport, $print = true)
  {
    parent::__construct($dataReport);
    if ($print)
    {
      $this->spreadsheet->getProperties()->setTitle("LRA Tahun ".$this->dataReport['tahun']);
      $this->spreadsheet->getProperties()->setSubject("LRA Tahun ".$this->dataReport['tahun']); 
      $this->print();             
    }
  }
  private function print()
  {
    $OrgID = $this->dataReport['OrgID'];
    $kode_organisasi=$this->dataReport['kode_organisasi'];
    $no_bulan = $this->dataReport['no_bulan'];
    $nama_bulan = Helper::getNamaBulan($no_bulan);
    $tahun = $this->dataReport['tahun'];       

    $date = new \DateTime("$tahun-$no_bulan-01");
    $jumlah_hari = $date->format('t');

    $sheet = $this->spreadsheet->getActiveSheet();        
    $sheet->setTitle ('LAPORAN RENCANA ANGGARAN');

    $sheet->getParent()->getDefaultStyle()->applyFromArray([
      'font' => [
        'name' => 'Arial',
        'size' => '9',
      ],
    ]);

    $row = 1;        
    $sheet->mergeCells("A$row:E$row");		
    $sheet->setCellValue("A$row", 'PEMERINTAH KABUPATEN BINTAN');     
    $row += 1;        
    $sheet->mergeCells("A$row:E$row");		
    $sheet->setCellValue("A$row", strtoupper($this->dataReport['Nm_Organisasi']." [$kode_organisasi]"));     
    $row += 1;        
    $sheet->mergeCells("A$row:E$row");		
    $sheet->setCellValue("A$row", "TAHUN ANGGARAN $tahun");     
    $row += 1;        
    $sheet->mergeCells("A$row:E$row");		
    $sheet->setCellValue("A$row", "01 Januari $tahun sampai $jumlah_hari $nama_bulan $tahun");     

    $styleArray = array( 
      'font' => array('bold' => true,'size'=>'11'),
      'alignment' => array(
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::HORIZONTAL_CENTER
      ),
    );   
    $sheet->getStyle("A1:A$row")->applyFromArray($styleArray);

    $row += 2;
    $row_awal = $row;
    $sheet->setCellValue("A$row", 'KODE REKENING');                
    $sheet->setCellValue("B$row", 'URAIAN');                
    $sheet->setCellValue("C$row", 'ANGGARAN');                
    $sheet->setCellValue("D$row", 'REALISASI');                
    $sheet->setCellValue("E$row", '%');   
    $row += 1;
    $sheet->setCellValue("A$row", '1');                
    $sheet->setCellValue("B$row", '2');
    $sheet->setCellValue("C$row", '3');
    $sheet->setCellValue("D$row", '4');
    $sheet->setCellValue("E$row", '5');

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(80);                
    $sheet->getColumnDimension('C')->setWidth(24);
    $sheet->getColumnDimension('D')->setWidth(24);                
    $sheet->getColumnDimension('E')->setWidth(24);                


    $styleArray = array(
      'font' => array('bold' => true),
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::HORIZONTAL_CENTER),
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
    );
    $sheet->getStyle("A$row_awal:E$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row_awal:E$row")->getAlignment()->setWrapText(true);
    
    $row += 1;
    $row_awal = $row;

    $data_lra = $this->getDataLRA($OrgID, $no_bulan, $tahun, 2);
    $tangkat_lra = $this->getRekeningProyekLRA();    
    $data = [];

    if(isset($tangkat_lra[1]))
    {
      $tingkat_1 = $tangkat_lra[1];
      $tingkat_2 = $tangkat_lra[2];
      $tingkat_3 = $tangkat_lra[3];
      $tingkat_4 = $tangkat_lra[4];
      $tingkat_5 = $tangkat_lra[5];
      $tingkat_6 = $tangkat_lra[6];

      foreach($tingkat_1 as $k1 => $v1)
      {
        $totalPaguRealisasi_Rek1 = \App\Models\Renja\FormLRAPerubahanOPDModel::calculateEachLevelLRA($data_lra, $k1, 'Kd_Rek_1');
        $persen_realisasi_rek1 = Helper::formatPersen($totalPaguRealisasi_Rek1['totalrealisasi'], $totalPaguRealisasi_Rek1['totalpagu']);
        $sheet->setCellValueExplicit("A$row", $k1, DataType::TYPE_STRING);        
        $sheet->setCellValue("B$row", $v1);
        $sheet->setCellValue("C$row", Helper::formatUang($totalPaguRealisasi_Rek1['totalpagu']));
        $sheet->setCellValue("D$row", Helper::formatUang($totalPaguRealisasi_Rek1['totalrealisasi']));
        $sheet->setCellValue("E$row", $persen_realisasi_rek1);

        $styleArray = array(
          'font' => array('bold' => true),          
        );
        $sheet->getStyle("A$row:E$row")->applyFromArray($styleArray);
        
        $row += 1;        
        foreach($tingkat_2 as $k2 => $v2)
        {
          $rek1_level2 = HelperKegiatan::getRekeningPrefixByLevel($k2, 1);          
          if(HelperKegiatan::normalizeRekeningForCompare($rek1_level2) === HelperKegiatan::normalizeRekeningForCompare($k1))
          {
            $totalPaguRealisasi_Rek2 = \App\Models\Renja\FormLRAPerubahanOPDModel::calculateEachLevelLRA($data_lra, $k2, 'Kd_Rek_2');
            $persen_realisasi_rek2 = Helper::formatPersen($totalPaguRealisasi_Rek2['totalrealisasi'], $totalPaguRealisasi_Rek2['totalpagu']);
            $sheet->setCellValueExplicit("A$row", $k2, DataType::TYPE_STRING);
            $sheet->setCellValue("B$row", $v2);
            $sheet->setCellValue("C$row", Helper::formatUang($totalPaguRealisasi_Rek2['totalpagu']));
            $sheet->setCellValue("D$row", Helper::formatUang($totalPaguRealisasi_Rek2['totalrealisasi']));
            $sheet->setCellValue("E$row", $persen_realisasi_rek2);

            $styleArray = array(
              'font' => array('bold' => true),          
            );
            $sheet->getStyle("A$row:E$row")->applyFromArray($styleArray);

            $row += 1;            
            foreach($tingkat_3 as $k3 => $v3)
            {
              $rek2_level3 = HelperKegiatan::getRekeningPrefixByLevel($k3, 2);              
              if(HelperKegiatan::normalizeRekeningForCompare($rek2_level3) === HelperKegiatan::normalizeRekeningForCompare($k2))
              {
                $totalPaguRealisasi_Rek3 = \App\Models\Renja\FormLRAPerubahanOPDModel::calculateEachLevelLRA($data_lra, $k3, 'Kd_Rek_3');
                $persen_realisasi_rek3 = Helper::formatPersen($totalPaguRealisasi_Rek3['totalrealisasi'], $totalPaguRealisasi_Rek3['totalpagu']);
                $sheet->setCellValue("A$row", $k3);
                $sheet->setCellValue("B$row", $v3);
                $sheet->setCellValue("C$row", Helper::formatUang($totalPaguRealisasi_Rek3['totalpagu']));
                $sheet->setCellValue("D$row", Helper::formatUang($totalPaguRealisasi_Rek3['totalrealisasi']));
                $sheet->setCellValue("E$row", $persen_realisasi_rek3);

                $styleArray = array(
                  'font' => array('bold' => true),          
                );
                $sheet->getStyle("A$row:E$row")->applyFromArray($styleArray);

                $row += 1;                
                foreach($tingkat_4 as $k4 => $v4)
                {
                  $rek3_level4 = HelperKegiatan::getRekeningPrefixByLevel($k4, 3);
                  if(HelperKegiatan::normalizeRekeningForCompare($rek3_level4) === HelperKegiatan::normalizeRekeningForCompare($k3))
                  {
                    $totalPaguRealisasi_Rek4 = \App\Models\Renja\FormLRAPerubahanOPDModel::calculateEachLevelLRA($data_lra, $k4, 'Kd_Rek_4');
                    $persen_realisasi_rek4 = Helper::formatPersen($totalPaguRealisasi_Rek4['totalrealisasi'], $totalPaguRealisasi_Rek4['totalpagu']);
                    $sheet->setCellValue("A$row", $k4);
                    $sheet->setCellValue("B$row", $v4);
                    $sheet->setCellValue("C$row", Helper::formatUang($totalPaguRealisasi_Rek4['totalpagu']));
                    $sheet->setCellValue("D$row", Helper::formatUang($totalPaguRealisasi_Rek4['totalrealisasi']));
                    $sheet->setCellValue("E$row", $persen_realisasi_rek4);

                    $styleArray = array(
                      'font' => array('bold' => true),          
                    );
                    $sheet->getStyle("A$row:E$row")->applyFromArray($styleArray);

                    $row += 1;                
                    foreach($tingkat_5 as $k5 => $v5)
                    {
                      $rek4_level5 = HelperKegiatan::getRekeningPrefixByLevel($k5, 4);
                      if(HelperKegiatan::normalizeRekeningForCompare($rek4_level5) === HelperKegiatan::normalizeRekeningForCompare($k4))
                      {
                        $totalPaguRealisasi_Rek5 = \App\Models\Renja\FormLRAPerubahanOPDModel::calculateEachLevelLRA($data_lra, $k5, 'Kd_Rek_5');
                        $persen_realisasi_rek5 = Helper::formatPersen($totalPaguRealisasi_Rek5['totalrealisasi'], $totalPaguRealisasi_Rek5['totalpagu']);
                        $sheet->setCellValue("A$row", $k5);
                        $sheet->setCellValue("B$row", $v5);
                        $sheet->setCellValue("C$row", Helper::formatUang($totalPaguRealisasi_Rek5['totalpagu']));
                        $sheet->setCellValue("D$row", Helper::formatUang($totalPaguRealisasi_Rek5['totalrealisasi']));
                        $sheet->setCellValue("E$row", $persen_realisasi_rek5);
                        $row += 1;                

                        foreach($tingkat_6 as $k6 => $v6)
                        {
                          $rek5_level6 = HelperKegiatan::getRekeningPrefixByLevel($k6, 5);
                          if(HelperKegiatan::normalizeRekeningForCompare($rek5_level6) === HelperKegiatan::normalizeRekeningForCompare($k5))
                          {
                            $data_lra_detail = $data_lra[$k6];
                            $total_pagu_uraian_6 = 0;
                            $total_realisasi_6 = 0;
                            foreach($data_lra_detail as $d)
                            {
                              $total_pagu_uraian_6 += $d['pagu_uraian'];
                              $total_realisasi_6 += $d['realisasi'];
                            }
                            $persen_realisasi_6 = Helper::formatPersen($total_realisasi_6, $total_pagu_uraian_6);
                            $sheet->setCellValue("A$row", $k6);
                            $sheet->setCellValue("B$row", $v6);
                            $sheet->setCellValue("C$row", Helper::formatUang($total_pagu_uraian_6));
                            $sheet->setCellValue("D$row", Helper::formatUang($total_realisasi_6));
                            $sheet->setCellValue("E$row", $persen_realisasi_6);
                            $row += 1;                
                          }
                        }
                      }
                    }
                  }
                }
              }
            }

            $sheet->setCellValue("B$row", "JUMLAH $v2");
            $sheet->setCellValue("C$row", Helper::formatUang($totalPaguRealisasi_Rek2['totalpagu']));
            $sheet->setCellValue("D$row", Helper::formatUang($totalPaguRealisasi_Rek2['totalrealisasi']));
            $sheet->setCellValue("E$row", $persen_realisasi_rek2);

            $styleArray = [
              'font' => [
                'bold' => true,
              ],
              'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                  'argb'=>'FFF0F8FF',
                ],
              ],
            ];
            $sheet->getStyle("A$row:E$row")->applyFromArray($styleArray);
            $row += 2;
          }
        }

        $sheet->setCellValue("B$row", "JUMLAH BELANJA");
        $sheet->setCellValue("C$row", Helper::formatUang($totalPaguRealisasi_Rek1['totalpagu']));
        $sheet->setCellValue("D$row", Helper::formatUang($totalPaguRealisasi_Rek1['totalrealisasi']));
        $sheet->setCellValue("E$row", $persen_realisasi_rek1);

        $styleArray = [
          'font' => [
            'bold' => true,
          ],
          'fill' => [
            'fillType'=>Fill::FILL_SOLID,
            'startColor' => [
              'argb'=>'FFF0F8FF',
            ],
          ],
        ];
        $sheet->getStyle("A$row:E$row")->applyFromArray($styleArray);        
      }
      

      $styleArray = array(								
        'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT,
                   'vertical' => Alignment::HORIZONTAL_CENTER),
        'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
      );   																					 
      $sheet->getStyle("A$row_awal:E$row")->applyFromArray($styleArray);
      $sheet->getStyle("A$row_awal:E$row")->getAlignment()->setWrapText(true);

      $styleArray = array(								
        'alignment' => array('horizontal' => Alignment::HORIZONTAL_LEFT)
      );																					       
      $sheet->getStyle("A$row_awal:B$row")->applyFromArray($styleArray);

      $styleArray = array(								
        'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
      );																					       
      $sheet->getStyle("E$row_awal:E$row")->applyFromArray($styleArray);

      $row += 2;
      $row_awal = $row;

      $sheet->mergeCells("C$row:E$row");
      $sheet->setCellValue("C$row", 'Kab. Bintan, ' . Helper::tanggal('d F Y'));
      $row += 1;
      $sheet->mergeCells("C$row:E$row");
      $sheet->setCellValue("C$row", "Kepala {$this->dataReport['Nm_Organisasi']}");      

      $row += 4;
      $sheet->mergeCells("C$row:E$row");
      $sheet->setCellValue("C$row", $this->dataReport['nama_pengguna_anggaran']);      
      $row += 1;
      $sheet->mergeCells("C$row:E$row");
      $sheet->setCellValue("C$row", 'NIP. ' . $this->dataReport['nip_pengguna_anggaran']);      


    }
  }
}