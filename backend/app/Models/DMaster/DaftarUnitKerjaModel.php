<?php

namespace App\Models\DMaster;

use App\Helpers\Helper;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\ReportModel;

class DaftarUnitKerjaModel extends ReportModel
{
  public function __construct($dataReport, $print = true)
  {
    parent::__construct($dataReport);
    if ($print) 
    {
      $this->spreadsheet->getProperties()->setTitle('Daftar Unit Kerja T.A ' . $this->dataReport['tahun']);
      $this->spreadsheet->getProperties()->setSubject('Daftar Unit Kerja T.A ' . $this->dataReport['tahun']);
      $this->print();
    }
  }

  private function print()
  {
    $tahun = $this->dataReport['tahun'];
    $unitkerja = $this->dataReport['unitkerja'];
    $jumlah_apbd = $this->dataReport['jumlah_apbd'];
    $jumlah_apbdp = $this->dataReport['jumlah_apbdp'];

    $sheet = $this->spreadsheet->getActiveSheet();
    $sheet->setTitle('DAFTAR UNIT KERJA');

    $sheet->getParent()->getDefaultStyle()->applyFromArray([
      'font' => [
        'name' => 'Arial',
        'size' => '9',
      ],
    ]);

    $row = 1;
    $sheet->mergeCells("A$row:I$row");
    $sheet->setCellValue("A$row", 'DAFTAR UNIT KERJA');
    $row += 1;
    $sheet->mergeCells("A$row:I$row");
    $sheet->setCellValue("A$row", 'TAHUN ANGGARAN ' . $tahun);
    $row += 1;
    $sheet->mergeCells("A$row:I$row");
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
    $sheet->setCellValue("B$row", 'ID');
    $sheet->setCellValue("C$row", 'KODE UNIT KERJA');
    $sheet->setCellValue("D$row", 'NAMA UNIT KERJA');
    $sheet->setCellValue("E$row", 'OPD');
    $sheet->setCellValue("F$row", 'BIDANG URUSAN');
    $sheet->setCellValue("G$row", 'KEPALA UNIT KERJA');
    $sheet->setCellValue("H$row", 'APBD');
    $sheet->setCellValue("I$row", 'APBDP');

    $sheet->getColumnDimension('A')->setWidth(6);
    $sheet->getColumnDimension('B')->setWidth(38);
    $sheet->getColumnDimension('C')->setWidth(22);
    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->getColumnDimension('E')->setWidth(35);
    $sheet->getColumnDimension('F')->setWidth(35);
    $sheet->getColumnDimension('G')->setWidth(30);
    $sheet->getColumnDimension('H')->setWidth(18);
    $sheet->getColumnDimension('I')->setWidth(18);

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
    $sheet->getStyle("A$row:I$row")->applyFromArray($headerStyle);
    $row += 1;

    $no = 1;
    foreach ($unitkerja as $item) {
      $nm_bidang = trim(($item->Nm_Bidang_1 ?? '') . ' ' . ($item->Nm_Bidang_2 ?? '') . ' ' . ($item->Nm_Bidang_3 ?? ''));
      $sheet->setCellValue("A$row", $no);
      $sheet->setCellValue("B$row", $item->SOrgID ?? '');
      $sheet->setCellValue("C$row", $item->kode_sub_organisasi ?? '');
      $sheet->setCellValue("D$row", $item->Nm_Sub_Organisasi ?? '');
      $sheet->setCellValue("E$row", $item->Nm_Organisasi ?? '');
      $sheet->setCellValue("F$row", $nm_bidang);
      $sheet->setCellValue("G$row", ($item->NamaKepalaUnitKerja ?? '') . ' / NIP. ' . ($item->NIPKepalaUnitKerja ?? ''));
      $sheet->setCellValue("H$row", Helper::formatUang($item->PaguDana1 ?? 0));
      $sheet->setCellValue("I$row", Helper::formatUang($item->PaguDana2 ?? 0));

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
      $sheet->getStyle("A$row:I$row")->applyFromArray($rowStyle);
      $sheet->getStyle("H$row:I$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $row += 1;
      $no++;
    }

    $sheet->setCellValue("A$row", '');
    $sheet->setCellValue("B$row", '');
    $sheet->setCellValue("C$row", '');
    $sheet->setCellValue("D$row", '');
    $sheet->setCellValue("E$row", '');
    $sheet->setCellValue("F$row", '');
    $sheet->setCellValue("G$row", 'TOTAL');
    $sheet->setCellValue("H$row", Helper::formatUang($jumlah_apbd));
    $sheet->setCellValue("I$row", Helper::formatUang($jumlah_apbdp));

    $totalStyle = [
      'font' => ['bold' => true],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_RIGHT,
        'vertical' => Alignment::VERTICAL_CENTER,
      ],
      'borders' => [
        'allBorders' => [
          'borderStyle' => Border::BORDER_THIN,
        ],
      ],
      'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFFFEB3B'],
      ],
    ];
    $sheet->getStyle("A$row:I$row")->applyFromArray($totalStyle);
    $sheet->getStyle("G$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
  }
}
