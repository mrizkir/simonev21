<?php

namespace App\Models\DMaster;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\ReportModel;

class DaftarSumberDanaModel extends ReportModel
{
  public function __construct($dataReport, $print = true)
  {
    parent::__construct($dataReport);
    if ($print) {
      $this->spreadsheet->getProperties()->setTitle('Daftar Sumber Dana T.A ' . $this->dataReport['tahun']);
      $this->spreadsheet->getProperties()->setSubject('Daftar Sumber Dana T.A ' . $this->dataReport['tahun']);
      $this->print();
    }
  }

  private function print()
  {
    $tahun = $this->dataReport['tahun'];
    $sumberdana = $this->dataReport['sumberdana'];

    $sheet = $this->spreadsheet->getActiveSheet();
    $sheet->setTitle('DAFTAR SUMBER DANA');

    $sheet->getParent()->getDefaultStyle()->applyFromArray([
      'font' => [
        'name' => 'Arial',
        'size' => '9',
      ],
    ]);

    $row = 1;
    $sheet->mergeCells("A$row:G$row");
    $sheet->setCellValue("A$row", 'DAFTAR SUMBER DANA');
    $row += 1;
    $sheet->mergeCells("A$row:G$row");
    $sheet->setCellValue("A$row", 'TAHUN ANGGARAN ' . $tahun);
    $row += 1;
    $sheet->mergeCells("A$row:G$row");
    $sheet->setCellValue("A$row", 'KABUPATEN BINTAN');

    $styleArray = [
      'font' => ['bold' => true, 'size' => '11'],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
      ],
    ];
    $sheet->getStyle("A1:A$row")->applyFromArray($styleArray);

    $row += 2;
    $sheet->setCellValue("A$row", 'NO');
    $sheet->setCellValue("B$row", 'SumberDanaID');
    $sheet->setCellValue("C$row", 'Id_Jenis_SumberDana');
    $sheet->setCellValue("D$row", 'Kd_SumberDana');
    $sheet->setCellValue("E$row", 'Nm_SumberDana');
    $sheet->setCellValue("F$row", 'Nm_Jenis_SumberDana');
    $sheet->setCellValue("G$row", 'Descr');

    $sheet->getColumnDimension('A')->setWidth(6);
    $sheet->getColumnDimension('B')->setWidth(38);
    $sheet->getColumnDimension('C')->setWidth(18);
    $sheet->getColumnDimension('D')->setWidth(14);
    $sheet->getColumnDimension('E')->setWidth(40);
    $sheet->getColumnDimension('F')->setWidth(25);
    $sheet->getColumnDimension('G')->setWidth(40);

    $headerStyle = [
      'font' => ['bold' => true],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
      ],
      'borders' => [
        'allBorders' => [
          'borderStyle' => Border::BORDER_THIN,
        ],
      ],
      'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFE0E0E0'],
      ],
    ];
    $sheet->getStyle("A$row:G$row")->applyFromArray($headerStyle);
    $row += 1;

    $no = 1;
    foreach ($sumberdana as $item) {
      $sheet->setCellValue("A$row", $no);
      $sheet->setCellValue("B$row", $item->SumberDanaID ?? '');
      $sheet->setCellValue("C$row", $item->Id_Jenis_SumberDana ?? '');
      $sheet->setCellValue("D$row", $item->Kd_SumberDana ?? '');
      $sheet->setCellValue("E$row", $item->Nm_SumberDana ?? '');
      $sheet->setCellValue("F$row", $item->Nm_Jenis_SumberDana ?? '');
      $sheet->setCellValue("G$row", $item->Descr ?? '');

      $rowStyle = [
        'alignment' => [
          'vertical' => Alignment::VERTICAL_CENTER,
          'wrapText' => true,
        ],
        'borders' => [
          'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
          ],
        ],
      ];
      $sheet->getStyle("A$row:G$row")->applyFromArray($rowStyle);
      $row += 1;
      $no++;
    }
  }
}
