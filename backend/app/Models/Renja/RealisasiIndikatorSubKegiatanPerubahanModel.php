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
    $sheet->mergeCells("A$row:J$row");
    $sheet->setCellValue("A$row", 'LAPORAN REALISASI INDIKATOR SUB KEGIATAN');
    $row += 1;
    $sheet->mergeCells("A$row:J$row");
    $sheet->setCellValue("A$row", strtoupper($this->dataReport['Nm_Sub_Organisasi']." [$kode_sub_organisasi]"));
    $row += 1;
    $sheet->mergeCells("A$row:J$row");
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
    $sheet->setCellValue("E$row", 'TARGET');
    $sheet->setCellValue("F$row", 'REALISASI');
    $sheet->setCellValue("G$row", 'PENCAPAIAN (%)');
    $sheet->setCellValue("H$row", 'PAGU');
    $sheet->setCellValue("I$row", 'REALISASI PAGU');
    $sheet->setCellValue("J$row", 'RASIO REALISASI (%)');

    $sheet->getColumnDimension('A')->setWidth(8);
    $sheet->getColumnDimension('B')->setWidth(50);
    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(20);
    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->getColumnDimension('H')->setWidth(20);
    $sheet->getColumnDimension('I')->setWidth(20);
    $sheet->getColumnDimension('J')->setWidth(18);

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
    $sheet->getStyle("A$row:J$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row:J$row")->getAlignment()->setWrapText(true);

    $row += 1;
    $row_awal = $row;

    $totalPaguUnit = (float)\DB::table('trRKA')
      ->where('SOrgID', $SOrgID)
      ->where('TA', $tahun)
      ->where('EntryLvl', 2)
      ->sum('PaguDana1');

    $no = 1;
    $total_target = 0;
    $total_realisasi = 0;
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

      // Get components (uraian) for this sub kegiatan
      $daftar_komponen = \DB::table('trRKARinc')
        ->select(\DB::raw('
          `RKARincID`,
          `NamaUraian1`,
          `PaguUraian1`
        '))
        ->where('RKAID', $RKAID)
        ->orderBy('kode_uraian1', 'ASC')
        ->get();

      // If no components, still show the sub kegiatan row
      if (count($daftar_komponen) == 0)
      {
        // Get target and realisasi for the sub kegiatan
        $data_target = \DB::table('trRKATargetRinc')
          ->select(\DB::raw('COALESCE(SUM(target1),0) AS totaltarget'))
          ->where('RKAID', $RKAID)
          ->where('bulan1', '<=', $no_bulan)
          ->first();

        $data_realisasi = \DB::table('trRKARealisasiRinc')
          ->select(\DB::raw('COALESCE(SUM(realisasi1),0) AS realisasi1'))
          ->where('RKAID', $RKAID)
          ->where('bulan1', '<=', $no_bulan)
          ->first();

        $target = $data_target->totaltarget ?? 0;
        $realisasi = $data_realisasi->realisasi1 ?? 0;
        $pencapaian = $target > 0 ? Helper::formatPersen($realisasi, $target) : 0;
        $realisasi_pagu = $realisasi;
        $rasio_realisasi = Helper::formatPersen($realisasi, $data_sub_kegiatan->PaguDana1);

        $sheet->setCellValue("A$row", $no++);
        $sheet->setCellValue("B$row", $data_sub_kegiatan->Nm_Sub_Kegiatan);
        $sheet->setCellValue("C$row", $data_sub_kegiatan->keluaran1 ?? '-');
        $sheet->setCellValue("D$row", '-');
        $sheet->setCellValue("E$row", Helper::formatUang($target));
        $sheet->setCellValue("F$row", Helper::formatUang($realisasi));
        $sheet->setCellValue("G$row", $pencapaian);
        $sheet->setCellValue("H$row", Helper::formatUang($data_sub_kegiatan->PaguDana1));
        $sheet->setCellValue("I$row", Helper::formatUang($realisasi_pagu));
        $sheet->setCellValue("J$row", $rasio_realisasi);

        $total_target += $target;
        $total_realisasi += $realisasi;
        $total_pagu += $data_sub_kegiatan->PaguDana1;
        $total_realisasi_pagu += $realisasi_pagu;

        $row += 1;
      }
      else
      {
        // For each component, create a row
        foreach ($daftar_komponen as $komponen)
        {
          // Get target and realisasi for this component
          $data_target = \DB::table('trRKATargetRinc')
            ->select(\DB::raw('COALESCE(SUM(target1),0) AS totaltarget'))
            ->where('RKAID', $RKAID)
            ->where('RKARincID', $komponen->RKARincID)
            ->where('bulan1', '<=', $no_bulan)
            ->first();

          $data_realisasi = \DB::table('trRKARealisasiRinc')
            ->select(\DB::raw('COALESCE(SUM(realisasi1),0) AS realisasi1'))
            ->where('RKAID', $RKAID)
            ->where('RKARincID', $komponen->RKARincID)
            ->where('bulan1', '<=', $no_bulan)
            ->first();

          $target = $data_target->totaltarget ?? 0;
          $realisasi = $data_realisasi->realisasi1 ?? 0;
          $pencapaian = $target > 0 ? Helper::formatPersen($realisasi, $target) : 0;
          $realisasi_pagu = $realisasi;
          $rasio_realisasi = $komponen->PaguUraian1 > 0 ? Helper::formatPersen($realisasi, $komponen->PaguUraian1) : 0;

          $sheet->setCellValue("A$row", $no++);
          $sheet->setCellValue("B$row", $data_sub_kegiatan->Nm_Sub_Kegiatan);
          $sheet->setCellValue("C$row", $data_sub_kegiatan->keluaran1 ?? '-');
          $sheet->setCellValue("D$row", $komponen->NamaUraian1);
          $sheet->setCellValue("E$row", Helper::formatUang($target));
          $sheet->setCellValue("F$row", Helper::formatUang($realisasi));
          $sheet->setCellValue("G$row", $pencapaian);
          $sheet->setCellValue("H$row", Helper::formatUang($komponen->PaguUraian1));
          $sheet->setCellValue("I$row", Helper::formatUang($realisasi_pagu));
          $sheet->setCellValue("J$row", $rasio_realisasi);

          $total_target += $target;
          $total_realisasi += $realisasi;
          $total_pagu += $komponen->PaguUraian1;
          $total_realisasi_pagu += $realisasi_pagu;

          $row += 1;
        }
      }
    }

    // Calculate totals
    $total_pencapaian = $total_target > 0 ? Helper::formatPersen($total_realisasi, $total_target) : 0;
    $total_rasio_realisasi = $total_pagu > 0 ? Helper::formatPersen($total_realisasi_pagu, $total_pagu) : 0;

    // Total row
    $sheet->mergeCells("A$row:D$row");
    $sheet->setCellValue("A$row", 'JUMLAH');
    $sheet->setCellValue("E$row", Helper::formatUang($total_target));
    $sheet->setCellValue("F$row", Helper::formatUang($total_realisasi));
    $sheet->setCellValue("G$row", $total_pencapaian);
    $sheet->setCellValue("H$row", Helper::formatUang($total_pagu));
    $sheet->setCellValue("I$row", Helper::formatUang($total_realisasi_pagu));
    $sheet->setCellValue("J$row", $total_rasio_realisasi);

    $styleArray = array(
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::HORIZONTAL_CENTER),
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
    );
    $sheet->getStyle("A$row_awal:J$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row_awal:J$row")->getAlignment()->setWrapText(true);

    $styleArray = array(
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_LEFT)
    );
    $sheet->getStyle("B$row_awal:D$row")->applyFromArray($styleArray);
    $sheet->getStyle("C$row_awal:C$row")->applyFromArray($styleArray);

    $styleArray = array(
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
    );
    $sheet->getStyle("A$row_awal:A$row")->applyFromArray($styleArray);
    $sheet->getStyle("G$row_awal:G$row")->applyFromArray($styleArray);
    $sheet->getStyle("J$row_awal:J$row")->applyFromArray($styleArray);

    $styleArray = array(
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
    );
    $sheet->getStyle("E$row_awal:F$row")->applyFromArray($styleArray);
    $sheet->getStyle("H$row_awal:I$row")->applyFromArray($styleArray);

    $sheet->getStyle("A$row:J$row")->getFont()->setBold(true);

    $row += 3;
    $sheet->mergeCells("H$row:J$row");
    $sheet->setCellValue("H$row", 'Kabupaten Bintan, '.Helper::tanggal('d F Y'));
    $row += 1;

    $sheet->mergeCells("H$row:J$row");
    $sheet->setCellValue("H$row", 'Pengguna Anggaran');

    $row += 5;
    $sheet->mergeCells("H$row:J$row");
    $sheet->setCellValue("H$row", $this->dataReport['nama_pengguna_anggaran']);
    $row += 1;

    $sheet->mergeCells("H$row:J$row");
    $sheet->setCellValue("H$row", 'Nip.'.Helper::formatNIP($this->dataReport['nip_pengguna_anggaran']));
  }
}

