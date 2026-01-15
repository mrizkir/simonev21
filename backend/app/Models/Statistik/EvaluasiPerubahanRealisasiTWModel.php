<?php

namespace App\Models\Statistik;

use App\Helpers\Helper;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\ReportModel;
use App\Models\DMaster\OrganisasiModel;
use App\Models\Statistik2Model;

class EvaluasiPerubahanRealisasiTWModel extends ReportModel
{   
  public function __construct($dataReport, $print = true)
  {
    parent::__construct($dataReport);     
    if ($print)
    {
      $this->spreadsheet->getProperties()->setTitle("Evaluasi Realisasi Perubahan Per T.W Tahun ".$this->dataReport['tahun']);
      $this->spreadsheet->getProperties()->setSubject("Evaluasi Realisasi Perubahan Per T.W Tahun ".$this->dataReport['tahun']); 
      $this->print();             
    }        
  }    
  private function print()  
  {
    $tahun = $this->dataReport['tahun'];
    $tw_realisasi = $this->dataReport['tw_realisasi'];
    
    switch ($tw_realisasi)
    {
      case 1:
        $bulan = 3;
        $nama_tw = 'Triwulan I';
      break;
      case 2:
        $bulan = 6;
        $nama_tw = 'Triwulan II';
      break;
      case 3:
        $bulan = 9;
        $nama_tw = 'Triwulan III';
      break;
      case 4:
        $bulan = 12;
        $nama_tw = 'Triwulan IV';
      break;
    }

    $evaluasi_realisasi = [];

    $daftar_opd = OrganisasiModel::select(\DB::raw('
      `OrgID`,
      kode_organisasi,
      `Nm_Organisasi`
    '))
    ->where('TA', $tahun)
    ->orderBy('kode_organisasi', 'ASC')
    ->get();

    $index = 0;
    $TotalPaguDana = 0;
    $TotalTargetFisik = 0;
    $TotalRealisasiFisik = 0;
    $TotalTargetKeuangan = 0;
    $TotalRealisasiKeuangan = 0;
    $TotalPersenRealisasiKeuangan = 0;
    
    foreach ($daftar_opd as $v) {
      $pagu_dana = 0;
      $target_fisik = 0;
      $realisasi_fisik = 0;
      $target_keuangan = 0;
      $realisasi_keuangan = 0;
      $persen_realisasi_keuangan = 0;

      $data_opd = Statistik2Model::select(\DB::raw('
        PaguDana2,
        TargetFisik2,
        RealisasiFisik2,
        TargetKeuangan2,
        RealisasiKeuangan2,
        PersenRealisasiKeuangan2
      '))
      ->where('OrgID', $v->OrgID)
      ->where('TA', $tahun)
      ->where('Bulan', $bulan)
      ->where('EntryLvl', 2)
      ->first();

      if (!is_null($data_opd))
      {
        $pagu_dana = $data_opd->PaguDana2;
        $target_fisik = $data_opd->TargetFisik2;
        $realisasi_fisik = $data_opd->RealisasiFisik2;
        $target_keuangan = $data_opd->TargetKeuangan2;
        $realisasi_keuangan = $data_opd->RealisasiKeuangan2;
        $persen_realisasi_keuangan = $data_opd->PersenRealisasiKeuangan2;
      }
      $index = $index + 1;
      $evaluasi_realisasi[] = [
        'index' => $index,
        'kode_organisasi' => $v->kode_organisasi,
        'Nm_Organisasi' => $v->Nm_Organisasi,
        'pagu_dana' => $pagu_dana,
        'target_fisik' => $target_fisik,
        'realisasi_fisik' => $realisasi_fisik,        
        'target_keuangan' => $target_keuangan,
        'realisasi_keuangan' => $realisasi_keuangan,
        'persen_keuangan' => $persen_realisasi_keuangan,        
      ];
      $TotalPaguDana += $pagu_dana;
      $TotalTargetFisik += $target_fisik;
      $TotalRealisasiFisik += $realisasi_fisik;
      $TotalTargetKeuangan += $target_keuangan;
      $TotalRealisasiKeuangan += $realisasi_keuangan;
      $TotalPersenRealisasiKeuangan += $persen_realisasi_keuangan;      
    }

    $sheet = $this->spreadsheet->getActiveSheet();
    $sheet->setTitle('Eval Realisasi Perubah');

    $sheet->getParent()->getDefaultStyle()->applyFromArray([
      'font' => [
        'name' => 'Arial',
        'size' => '9',
      ],
    ]);

    // Set title
    $row = 1;
    $sheet->setCellValue('A' . $row, 'EVALUASI REALISASI PERUBAHAN PER T.W');
    $sheet->mergeCells('A' . $row . ':I' . $row);
    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $row += 1;
    $sheet->setCellValue('A' . $row, 'Tahun Anggaran: ' . $tahun . ' | ' . $nama_tw);
    $sheet->mergeCells('A' . $row . ':I' . $row);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Set headers
    $row += 2;
    $headers = ['NO', 'KODE', 'NAMA OPD', 'PAGU DANA', 'TARGET FISIK (%)', 'REALISASI FISIK (%)', 'TARGET KEUANGAN', 'REALISASI KEUANGAN', '% REALISASI KEUANGAN'];
    $col = 'A';
    foreach ($headers as $header) {
      $sheet->setCellValue($col . $row, $header);
      $sheet->getStyle($col . $row)->getFont()->setBold(true);
      $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
      $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
      $sheet->getStyle($col . $row)->applyFromArray([
        'borders' => [
          'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
          ],
        ],
      ]);
      $col++;
    }

    // Set data
    $row += 1;
    $row_awal = $row;
    foreach ($evaluasi_realisasi as $item) {
      $sheet->setCellValue('A' . $row, $item['index']);
      $sheet->setCellValue('B' . $row, $item['kode_organisasi']);
      $sheet->setCellValue('C' . $row, $item['Nm_Organisasi']);
      $sheet->setCellValue('D' . $row, $item['pagu_dana']);
      $sheet->setCellValue('E' . $row, $item['target_fisik']);
      $sheet->setCellValue('F' . $row, $item['realisasi_fisik']);
      $sheet->setCellValue('G' . $row, $item['target_keuangan']);
      $sheet->setCellValue('H' . $row, $item['realisasi_keuangan']);
      $sheet->setCellValue('I' . $row, $item['persen_keuangan']);

      // Format number cells
      $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
      $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
      $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');

      // Apply borders
      foreach (range('A', 'I') as $col) {
        $sheet->getStyle($col . $row)->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => Border::BORDER_THIN,
            ],
          ],
        ]);
      }

      $row++;
    }

    // Set total row
    $total_index = $index > 0 ? $index : 1;
    $sheet->setCellValue('A' . $row, 'TOTAL');
    $sheet->mergeCells('A' . $row . ':C' . $row);
    $sheet->setCellValue('D' . $row, $TotalPaguDana);
    $sheet->setCellValue('E' . $row, Helper::formatPecahan($TotalTargetFisik, $total_index));
    $sheet->setCellValue('F' . $row, Helper::formatPecahan($TotalRealisasiFisik, $total_index));
    $sheet->setCellValue('G' . $row, $TotalTargetKeuangan);
    $sheet->setCellValue('H' . $row, $TotalRealisasiKeuangan);
    $sheet->setCellValue('I' . $row, Helper::formatPecahan($TotalPersenRealisasiKeuangan, $total_index));

    // Format total row
    $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
    $sheet->getStyle('A' . $row . ':I' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFC107');
    $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
    foreach (range('A', 'I') as $col) {
      $sheet->getStyle($col . $row)->applyFromArray([
        'borders' => [
          'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
          ],
        ],
      ]);
    }

    // Alignment
    $styleArray = array(								
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
                 'vertical' => Alignment::VERTICAL_CENTER),
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
    );   																					 
    $sheet->getStyle("A$row_awal:I$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row_awal:I$row")->getAlignment()->setWrapText(true);

    $styleArray = array(								
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_LEFT)
    );																					 
    $sheet->getStyle("B$row_awal:C$row")->applyFromArray($styleArray);

    $styleArray = array(								
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
    );																					 
    $sheet->getStyle("D$row_awal:I$row")->applyFromArray($styleArray);

    // Auto size columns
    foreach (range('A', 'I') as $col) {
      $sheet->getColumnDimension($col)->setAutoSize(true);
    }
  }
}
