<?php

namespace App\Models\Renja;

use App\Helpers\Helper;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\ReportModel;

class FormLRA2OPDMurniModel extends ReportModel
{
  protected $judulLaporan = 'LRA OPD 2';
  protected $entryLvl = 1;

  public function __construct($dataReport, $print = true)
  {
    parent::__construct($dataReport);
    if ($print)
    {
      $this->spreadsheet->getProperties()->setTitle($this->judulLaporan." Tahun ".$this->dataReport['tahun']);
      $this->spreadsheet->getProperties()->setSubject($this->judulLaporan." Tahun ".$this->dataReport['tahun']);
      $this->print();
    }
  }

  private function print()
  {
    $OrgID = $this->dataReport['OrgID'];
    $kode_organisasi = $this->dataReport['kode_organisasi'];
    $no_bulan = $this->dataReport['no_bulan'];
    $tahun = $this->dataReport['tahun'];
    $data = $this->getDataKontrakOPD($OrgID, $tahun, $no_bulan, $this->entryLvl);

    $sheet = $this->spreadsheet->getActiveSheet();
    $sheet->setTitle('LRA OPD 2');
    $sheet->getParent()->getDefaultStyle()->applyFromArray([
      'font' => [
        'name' => 'Arial',
        'size' => '9',
      ],
    ]);

    $lastCol = 'O';
    $row = 1;
    $sheet->mergeCells("A$row:{$lastCol}$row");
    $sheet->setCellValue("A$row", $this->judulLaporan);
    $row += 1;
    $sheet->mergeCells("A$row:{$lastCol}$row");
    $sheet->setCellValue("A$row", strtoupper($this->dataReport['Nm_Organisasi']." [$kode_organisasi]"));
    $row += 1;
    $sheet->mergeCells("A$row:{$lastCol}$row");
    $sheet->setCellValue("A$row", 'KABUPATEN BINTAN');
    $sheet->getStyle("A1:A$row")->applyFromArray([
      'font' => ['bold' => true, 'size' => '11'],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::HORIZONTAL_CENTER,
      ],
    ]);

    $row += 1;
    $sheet->setCellValue("A$row", 'BULAN : '.Helper::getNamaBulan($no_bulan));
    $row += 2;

    $headers = [
      'A' => 'KODE',
      'B' => 'PROGRAM/KEGIATAN/SUB KEGIATAN',
      'C' => 'PAGU DANA (RP)',
      'D' => 'BOBOT (%)',
      'E' => 'TARGET FISIK (%)',
      'F' => 'REALISASI FISIK (%)',
      'G' => 'TTB FISIK (%)',
      'H' => 'TARGET KEUANGAN (RP)',
      'I' => '(%)',
      'J' => 'REALISASI KEUANGAN (RP)',
      'K' => '(%)',
      'L' => 'TTB KEUANGAN (%)',
      'M' => 'LOKASI',
      'N' => 'SISA ANGGARAN (RP)',
      'O' => '(%)',
    ];
    foreach ($headers as $col => $label)
    {
      $sheet->setCellValue($col.$row, $label);
    }
    $headerRow = $row;
    $sheet->getStyle("A$row:{$lastCol}$row")->applyFromArray([
      'font' => ['bold' => true],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::HORIZONTAL_CENTER,
      ],
      'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
      'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFE6E6FA'],
      ],
    ]);
    $sheet->getStyle("A$row:{$lastCol}$row")->getAlignment()->setWrapText(true);

    $widths = [
      'A' => 24, 'B' => 46, 'C' => 20, 'D' => 12, 'E' => 14,
      'F' => 16, 'G' => 14, 'H' => 20, 'I' => 10, 'J' => 22,
      'K' => 10, 'L' => 16, 'M' => 22, 'N' => 20, 'O' => 10,
    ];
    foreach ($widths as $col => $width)
    {
      $sheet->getColumnDimension($col)->setWidth($width);
    }

    $row += 1;
    $rowAwal = $row;
    $total = $data['total_data'];
    foreach ($data['rows'] as $item)
    {
      $sheet->setCellValue("A$row", $item['kode']);
      $sheet->setCellValue("B$row", $item['nama_uraian']);
      $sheet->setCellValue("C$row", $item['pagu_dana1']);
      $sheet->setCellValue("D$row", $item['bobot1']);
      $sheet->setCellValue("E$row", $item['fisik_target1']);
      $sheet->setCellValue("F$row", $item['fisik_realisasi1']);
      $sheet->setCellValue("G$row", $item['fisik_ttb1']);
      $sheet->setCellValue("H$row", $item['keuangan_target1']);
      $sheet->setCellValue("I$row", $item['keuangan_target_persen_1']);
      $sheet->setCellValue("J$row", $item['keuangan_realisasi1']);
      $sheet->setCellValue("K$row", $item['keuangan_realisasi_persen_1']);
      $sheet->setCellValue("L$row", $item['keuangan_ttb1']);
      $sheet->setCellValue("M$row", $item['lokasi']);
      $sheet->setCellValue("N$row", $item['sisa_anggaran']);
      $sheet->setCellValue("O$row", $item['sisa_anggaran_persen']);
      if (!empty($item['isprogram']))
      {
        $sheet->getStyle("A$row:{$lastCol}$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row:{$lastCol}$row")->applyFromArray([
          'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFDCE775'],
          ],
        ]);
      }
      elseif (!empty($item['iskegiatan']))
      {
        $sheet->getStyle("A$row:{$lastCol}$row")->applyFromArray([
          'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFF0F4C3'],
          ],
        ]);
      }
      $row += 1;
    }

    if ($rowAwal == $row)
    {
      $sheet->mergeCells("A$row:{$lastCol}$row");
      $sheet->setCellValue("A$row", 'Tidak ada kegiatan');
      $row += 1;
    }

    $sheet->mergeCells("A$row:B$row");
    $sheet->setCellValue("A$row", 'TOTAL');
    $sheet->setCellValue("C$row", $total['totalPaguOPD']);
    $sheet->setCellValue("D$row", $total['totalPersenBobot']);
    $sheet->setCellValue("E$row", $total['totalPersenTargetFisik']);
    $sheet->setCellValue("F$row", $total['totalPersenRealisasiFisik']);
    $sheet->setCellValue("G$row", $total['total_ttb_fisik']);
    $sheet->setCellValue("H$row", $total['totalTargetKeuanganKeseluruhan']);
    $sheet->setCellValue("I$row", $total['totalPersenTargetKeuangan']);
    $sheet->setCellValue("J$row", $total['totalRealisasiKeuanganKeseluruhan']);
    $sheet->setCellValue("K$row", $total['totalPersenRealisasiKeuangan']);
    $sheet->setCellValue("L$row", $total['total_ttb_keuangan']);
    $sheet->setCellValue("N$row", $total['totalSisaAnggaran']);
    $sheet->setCellValue("O$row", $total['totalPersenSisaAnggaran']);
    $sheet->getStyle("A$row:{$lastCol}$row")->getFont()->setBold(true);
    $sheet->getStyle("A$row:{$lastCol}$row")->applyFromArray([
      'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFFF9800'],
      ],
    ]);
    $lastDataRow = $row;

    if ($lastDataRow >= $rowAwal)
    {
      $sheet->getStyle("A$rowAwal:{$lastCol}$lastDataRow")->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
      ]);
      $sheet->getStyle("A$rowAwal:{$lastCol}$lastDataRow")->getAlignment()->setWrapText(true);
      $sheet->getStyle("C$rowAwal:O$lastDataRow")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
      $sheet->getStyle("C$rowAwal:L$lastDataRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
      $sheet->getStyle("N$rowAwal:O$lastDataRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    $row += 3;
    $sheet->mergeCells("L$row:O$row");
    $sheet->setCellValue("L$row", 'Kabupaten Bintan, '.Helper::tanggal('d F Y'));
    $row += 1;
    $sheet->mergeCells("L$row:O$row");
    $sheet->setCellValue("L$row", 'Pengguna Anggaran');
    $row += 5;
    $sheet->mergeCells("L$row:O$row");
    $sheet->setCellValue("L$row", $this->dataReport['nama_pengguna_anggaran']);
    $row += 1;
    $sheet->mergeCells("L$row:O$row");
    $sheet->setCellValue("L$row", 'Nip.'.Helper::formatNIP($this->dataReport['nip_pengguna_anggaran']));
  }
}
