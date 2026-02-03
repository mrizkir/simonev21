<?php

namespace App\Models\DMaster;

use App\Helpers\Helper;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\ReportModel;

class DaftarOPDModel extends ReportModel
{
  public function __construct($dataReport, $print = true)
  {
    parent::__construct($dataReport);
    if ($print) 
    {
      $this->spreadsheet->getProperties()->setTitle('Daftar Organisasi Perangkat Daerah (OPD) T.A ' . $this->dataReport['tahun']);
      $this->spreadsheet->getProperties()->setSubject('Daftar OPD T.A ' . $this->dataReport['tahun']);
      $this->print();
    }
  }

  private function print()
  {
    $tahun = $this->dataReport['tahun'];
    $opd = $this->dataReport['opd'];
    $jumlah_apbd = $this->dataReport['jumlah_apbd'];
    $jumlah_apbdp = $this->dataReport['jumlah_apbdp'];

    $sheet = $this->spreadsheet->getActiveSheet();
    $sheet->setTitle('DAFTAR OPD');

    $sheet->getParent()->getDefaultStyle()->applyFromArray([
      'font' => [
        'name' => 'Arial',
        'size' => '9',
      ],
    ]);

    $row = 1;
    $sheet->mergeCells("A$row:P$row");
    $sheet->setCellValue("A$row", 'DAFTAR ORGANISASI PERANGKAT DAERAH (OPD)');
    $row += 1;
    $sheet->mergeCells("A$row:P$row");
    $sheet->setCellValue("A$row", 'TAHUN ANGGARAN ' . $tahun);
    $row += 1;
    $sheet->mergeCells("A$row:P$row");
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
    $sheet->setCellValue("C$row", 'KODE OPD');
    $sheet->setCellValue("D$row", 'NAMA OPD');
    $sheet->setCellValue("E$row", 'BidangID_1');
    $sheet->setCellValue("F$row", 'kode_bidang_1');
    $sheet->setCellValue("G$row", 'Nm_Bidang_1');
    $sheet->setCellValue("H$row", 'BidangID_2');
    $sheet->setCellValue("I$row", 'kode_bidang_2');
    $sheet->setCellValue("J$row", 'Nm_Bidang_2');
    $sheet->setCellValue("K$row", 'BidangID_3');
    $sheet->setCellValue("L$row", 'kode_bidang_3');
    $sheet->setCellValue("M$row", 'Nm_Bidang_3');
    $sheet->setCellValue("N$row", 'KEPALA OPD');
    $sheet->setCellValue("O$row", 'APBD');
    $sheet->setCellValue("P$row", 'APBDP');

    $sheet->getColumnDimension('A')->setWidth(6);
    $sheet->getColumnDimension('B')->setWidth(38);
    $sheet->getColumnDimension('C')->setWidth(18);
    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->getColumnDimension('E')->setWidth(12);
    $sheet->getColumnDimension('F')->setWidth(14);
    $sheet->getColumnDimension('G')->setWidth(25);
    $sheet->getColumnDimension('H')->setWidth(12);
    $sheet->getColumnDimension('I')->setWidth(14);
    $sheet->getColumnDimension('J')->setWidth(25);
    $sheet->getColumnDimension('K')->setWidth(12);
    $sheet->getColumnDimension('L')->setWidth(14);
    $sheet->getColumnDimension('M')->setWidth(25);
    $sheet->getColumnDimension('N')->setWidth(30);
    $sheet->getColumnDimension('O')->setWidth(18);
    $sheet->getColumnDimension('P')->setWidth(18);

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
    $sheet->getStyle("A$row:P$row")->applyFromArray($headerStyle);
    $row += 1;

    $no = 1;
    foreach ($opd as $item) 
    {
      $sheet->setCellValue("A$row", $no);
      $sheet->setCellValue("B$row", $item->OrgID ?? '');
      $sheet->setCellValue("C$row", $item->kode_organisasi ?? '');
      $sheet->setCellValue("D$row", $item->Nm_Organisasi ?? '');
      $sheet->setCellValue("E$row", $item->BidangID_1 ?? '');
      $sheet->setCellValue("F$row", $item->kode_bidang_1 ?? '');
      $sheet->setCellValue("G$row", $item->Nm_Bidang_1 ?? '');
      $sheet->setCellValue("H$row", $item->BidangID_2 ?? '');
      $sheet->setCellValue("I$row", $item->kode_bidang_2 ?? '');
      $sheet->setCellValue("J$row", $item->Nm_Bidang_2 ?? '');
      $sheet->setCellValue("K$row", $item->BidangID_3 ?? '');
      $sheet->setCellValue("L$row", $item->kode_bidang_3 ?? '');
      $sheet->setCellValue("M$row", $item->Nm_Bidang_3 ?? '');
      $sheet->setCellValue("N$row", ($item->NamaKepalaSKPD ?? '') . ' / NIP. ' . ($item->NIPKepalaSKPD ?? ''));
      $sheet->setCellValue("O$row", Helper::formatUang($item->PaguDana1 ?? 0));
      $sheet->setCellValue("P$row", Helper::formatUang($item->PaguDana2 ?? 0));

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
      $sheet->getStyle("A$row:P$row")->applyFromArray($rowStyle);
      $sheet->getStyle("O$row:P$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $row += 1;
      $no++;
    }

    $sheet->setCellValue("A$row", '');
    $sheet->setCellValue("B$row", '');
    $sheet->setCellValue("C$row", '');
    $sheet->setCellValue("D$row", '');
    $sheet->setCellValue("E$row", '');
    $sheet->setCellValue("F$row", '');
    $sheet->setCellValue("G$row", '');
    $sheet->setCellValue("H$row", '');
    $sheet->setCellValue("I$row", '');
    $sheet->setCellValue("J$row", '');
    $sheet->setCellValue("K$row", '');
    $sheet->setCellValue("L$row", '');
    $sheet->setCellValue("M$row", '');
    $sheet->setCellValue("N$row", 'TOTAL');
    $sheet->setCellValue("O$row", Helper::formatUang($jumlah_apbd));
    $sheet->setCellValue("P$row", Helper::formatUang($jumlah_apbdp));

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
    $sheet->getStyle("A$row:P$row")->applyFromArray($totalStyle);
    $sheet->getStyle("N$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
  }
}
