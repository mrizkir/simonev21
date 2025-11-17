<?php

namespace App\Models\Renja;

use App\Helpers\Helper;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\ReportModel;

class RealisasiIndikatorSubKegiatanPerubahanModel extends ReportModel
{
  public function __construct($dataReport, $print = true)
  {
    parent::__construct($dataReport);
    if ($print)
    {
      $this->spreadsheet->getProperties()->setTitle("Laporan Realisasi Indikator Sub Kegiatan Tahun ".$this->dataReport['tahun']);
      $this->spreadsheet->getProperties()->setSubject("Laporan Realisasi Indikator Sub Kegiatan Tahun ".$this->dataReport['tahun']);
      $this->print();
    }
  }

  private function print()
  {
    $SOrgID = $this->dataReport['SOrgID'];
    $kode_sub_organisasi = $this->dataReport['kode_sub_organisasi'];
    $no_bulan = $this->dataReport['no_bulan'];
    $nama_bulan = Helper::getNamaBulan($no_bulan);
    $tahun = $this->dataReport['tahun'];

    $sheet = $this->spreadsheet->getActiveSheet();
    $sheet->setTitle('LAPORAN INDIKATOR SUB KEGIATAN');

    $sheet->getParent()->getDefaultStyle()->applyFromArray([
      'font' => [
        'name' => 'Arial',
        'size' => '9',
      ],
    ]);

    $row = 1;
    $sheet->mergeCells("A$row:I$row");
    $sheet->setCellValue("A$row", 'LAPORAN REALISASI INDIKATOR SUB KEGIATAN');
    $row += 1;
    $sheet->mergeCells("A$row:I$row");
    $sheet->setCellValue("A$row", strtoupper($this->dataReport['Nm_Sub_Organisasi']." [$kode_sub_organisasi]"));
    $row += 1;
    $sheet->mergeCells("A$row:I$row");
    $sheet->setCellValue("A$row", 'KABUPATEN BINTAN');

    $styleArray = array(
      'font' => array('bold' => true, 'size' => '11'),
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::HORIZONTAL_CENTER),
    );
    $sheet->getStyle("A1:A$row")->applyFromArray($styleArray);

    $row += 1;
    $sheet->setCellValue("A$row", 'BULAN : '.Helper::getNamaBulan($this->dataReport['no_bulan']));
    $row += 2;

    // Header columns
    $sheet->setCellValue("A$row", 'NO');
    $sheet->setCellValue("B$row", 'NAMA SUB KEGIATAN');
    $sheet->setCellValue("C$row", 'INDIKATOR KEGIATAN');
    $sheet->setCellValue("D$row", 'KOMPONEN');
    $sheet->setCellValue("E$row", 'REALISASI');
    $sheet->setCellValue("F$row", 'PENCAPAIAN (%)');
    $sheet->setCellValue("G$row", 'PAGU');
    $sheet->setCellValue("H$row", 'REALISASI PAGU');
    $sheet->setCellValue("I$row", 'RASIO REALISASI (%)');

    $sheet->getColumnDimension('A')->setWidth(8);
    $sheet->getColumnDimension('B')->setWidth(50);
    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->getColumnDimension('G')->setWidth(20);
    $sheet->getColumnDimension('H')->setWidth(20);
    $sheet->getColumnDimension('I')->setWidth(18);

    $styleArray = array(
      'font' => array('bold' => true),
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::HORIZONTAL_CENTER),
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN)),
      'fill' => array(
        'fillType' => Fill::FILL_SOLID,
        'startColor' => array('argb' => 'FFE0E0E0'),
      ),
    );
    $sheet->getStyle("A$row:I$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row:I$row")->getAlignment()->setWrapText(true);

    $row += 1;
    $row_awal = $row;

    $totalPaguUnit = (float)\DB::table('trRKA')
      ->where('SOrgID', $SOrgID)
      ->where('TA', $tahun)
      ->where('EntryLvl', 2)
      ->sum('PaguDana1');

    $no = 1;
    $total_pagu = 0;
    $total_realisasi_pagu = 0;

    // Get all sub kegiatan for this unit kerja
    $daftar_sub_kegiatan = \DB::table('trRKA')
      ->select(\DB::raw('
        `RKAID`,
        `kode_sub_kegiatan`,
        `Nm_Sub_Kegiatan`,
        `PaguDana1`,
        `keluaran1`,
        `tk_keluaran1`,
        `RealisasiKinerja`
      '))
      ->where('SOrgID', $SOrgID)
      ->where('TA', $tahun)
      ->where('EntryLvl', 2)
      ->orderByRaw('kode_urusan="X" DESC')
      ->orderBy('kode_bidang', 'ASC')
      ->orderBy('kode_program', 'ASC')
      ->orderBy('kode_kegiatan', 'ASC')
      ->orderBy('kode_sub_kegiatan', 'ASC')
      ->get();

    foreach ($daftar_sub_kegiatan as $data_sub_kegiatan)
    {
      $RKAID = $data_sub_kegiatan->RKAID;

      // Get realisasi dana for the sub kegiatan
      $data_realisasi = \DB::table('trRKARealisasiRinc')
        ->select(\DB::raw('COALESCE(SUM(realisasi1),0) AS realisasi1'))
        ->where('RKAID', $RKAID)
        ->where('bulan1', '<=', $no_bulan)
        ->first();

      $realisasi_dana = $data_realisasi->realisasi1 ?? 0;
      $realisasi_pagu = $realisasi_dana;
      $rasio_realisasi = Helper::formatPersen($realisasi_dana, $data_sub_kegiatan->PaguDana1);

      // Extract number from tk_keluaran1 for komponen
      $tk_keluaran1 = '-';
      if (!empty($data_sub_kegiatan->keluaran1)) {
        $numberString = filter_var($data_sub_kegiatan->tk_keluaran1, FILTER_SANITIZE_NUMBER_INT);
        if (!empty($numberString)) {
          $tk_keluaran1 = (int)$numberString;
        }
      }

      // Extract number from RealisasiKinerja for realisasi
      $realisasi = '-';
      if (!empty($data_sub_kegiatan->RealisasiKinerja)) {
        $numberString = filter_var($data_sub_kegiatan->RealisasiKinerja, FILTER_SANITIZE_NUMBER_INT);
        if (!empty($numberString)) {
          $realisasi = (int)$numberString;
        }
      }

      // Calculate pencapaian: ($realisasi / $tk_keluaran1) * 100
      $pencapaian = 0;
      if (is_numeric($realisasi) && is_numeric($tk_keluaran1) && $tk_keluaran1 != 0) {
        $pencapaian = Helper::formatPersen($realisasi, $tk_keluaran1);
      }

      $sheet->setCellValue("A$row", $no++);
      $sheet->setCellValue("B$row", $data_sub_kegiatan->Nm_Sub_Kegiatan);
      $sheet->setCellValue("C$row", $data_sub_kegiatan->keluaran1 ?? '-');
      $sheet->setCellValue("D$row", $tk_keluaran1);
      $sheet->setCellValue("E$row", $realisasi);
      $sheet->setCellValue("F$row", $pencapaian);
      $sheet->setCellValue("G$row", Helper::formatUang($data_sub_kegiatan->PaguDana1));
      $sheet->setCellValue("H$row", Helper::formatUang($realisasi_pagu));
      $sheet->setCellValue("I$row", $rasio_realisasi);

      $total_pagu += $data_sub_kegiatan->PaguDana1;
      $total_realisasi_pagu += $realisasi_pagu;

      $row += 1;
    }

    // Calculate totals
    $total_rasio_realisasi = $total_pagu > 0 ? Helper::formatPersen($total_realisasi_pagu, $total_pagu) : 0;

    // Total row
    $sheet->mergeCells("A$row:D$row");
    $sheet->setCellValue("A$row", 'JUMLAH');
    $sheet->setCellValue("E$row", '');
    $sheet->setCellValue("F$row", '');
    $sheet->setCellValue("G$row", Helper::formatUang($total_pagu));
    $sheet->setCellValue("H$row", Helper::formatUang($total_realisasi_pagu));
    $sheet->setCellValue("I$row", $total_rasio_realisasi);

    $styleArray = array(
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::HORIZONTAL_CENTER),
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
    );
    $sheet->getStyle("A$row_awal:I$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row_awal:I$row")->getAlignment()->setWrapText(true);

    $styleArray = array(
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_LEFT)
    );
    $sheet->getStyle("B$row_awal:D$row")->applyFromArray($styleArray);
    $sheet->getStyle("C$row_awal:C$row")->applyFromArray($styleArray);

    $styleArray = array(
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
    );
    $sheet->getStyle("A$row_awal:A$row")->applyFromArray($styleArray);
    $sheet->getStyle("F$row_awal:F$row")->applyFromArray($styleArray);
    $sheet->getStyle("I$row_awal:I$row")->applyFromArray($styleArray);

    $styleArray = array(
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
    );
    $sheet->getStyle("E$row_awal:E$row")->applyFromArray($styleArray);
    $sheet->getStyle("G$row_awal:H$row")->applyFromArray($styleArray);

    $sheet->getStyle("A$row:I$row")->getFont()->setBold(true);

    $row += 3;
    $sheet->mergeCells("G$row:I$row");
    $sheet->setCellValue("G$row", 'Kabupaten Bintan, '.Helper::tanggal('d F Y'));
    $row += 1;

    $sheet->mergeCells("G$row:I$row");
    $sheet->setCellValue("G$row", 'Pengguna Anggaran');

    $row += 5;
    $sheet->mergeCells("G$row:I$row");
    $sheet->setCellValue("G$row", $this->dataReport['nama_pengguna_anggaran']);
    $row += 1;

    $sheet->mergeCells("G$row:I$row");
    $sheet->setCellValue("G$row", 'Nip.'.Helper::formatNIP($this->dataReport['nip_pengguna_anggaran']));
  }
}

