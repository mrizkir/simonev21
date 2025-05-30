<?php

namespace App\Models\Renja;

use App\Helpers\Helper;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Models\ReportModel;

class FormBOPDMurniModel extends ReportModel
{   
  public function __construct($dataReport, $print = true)
  {
    parent::__construct($dataReport);     
    if ($print)
    {
      $this->spreadsheet->getProperties()->setTitle("Laporan Form B Tahun ".$this->dataReport['tahun']);
      $this->spreadsheet->getProperties()->setSubject("Laporan Form B Tahun ".$this->dataReport['tahun']); 
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

    $sheet = $this->spreadsheet->getActiveSheet();        
    $sheet->setTitle ('LAPORAN FORM B');

    $sheet->getParent()->getDefaultStyle()->applyFromArray([
      'font' => [
        'name' => 'Arial',
        'size' => '9',
      ],
    ]);
    
    $row = 1;        
    $sheet->mergeCells("A$row:U$row");		
    $sheet->setCellValue("A$row", 'LAPORAN FORM B');         
    $row += 1;        
    $sheet->mergeCells("A$row:U$row");		
    $sheet->setCellValue("A$row", strtoupper($this->dataReport['Nm_Organisasi']." [$kode_organisasi]"));         
    $row += 1;        
    $sheet->mergeCells("A$row:U$row");		
    $sheet->setCellValue("A$row", 'KABUPATEN BINTAN');         
    
    $styleArray = array( 
      'font' => array('bold' => true,'size'=>'11'),
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
                 'vertical' => Alignment::HORIZONTAL_CENTER),								
    );   
    $sheet->getStyle("A1:A$row")->applyFromArray($styleArray);

    $row += 1;
    $sheet->setCellValue("A$row", 'BULAN : '.Helper::getNamaBulan($this->dataReport['no_bulan']));                
    $row += 2;
    $row_akhir = $row + 1;
    $sheet->mergeCells("A$row:A$row_akhir");
    $sheet->setCellValue("A$row", 'NOMOR');                
    $sheet->mergeCells("B$row:B$row_akhir");
    $sheet->setCellValue("B$row", 'KODE');
    $sheet->mergeCells("C$row:C$row_akhir");
    $sheet->setCellValue("C$row", 'PROGRAM/KEGIATAN');
    $sheet->mergeCells("D$row:D$row_akhir");
    $sheet->setCellValue("D$row", 'UNIT KERJA');
    $sheet->mergeCells("E$row:E$row_akhir");
    $sheet->setCellValue("E$row", 'PAGU DANA (RP)');
    $sheet->mergeCells("F$row:F$row_akhir");
    $sheet->setCellValue("F$row", 'BOBOT (%)');
    $sheet->mergeCells("G$row:I$row");
    $sheet->setCellValue("G$row", 'FISIK');                
    $sheet->mergeCells("J$row:N$row");
    $sheet->setCellValue("J$row", 'KEUANGAN'); 
    $sheet->mergeCells("O$row:O$row_akhir");
    $sheet->setCellValue("O$row", 'LOKASI');
    $sheet->mergeCells("P$row:Q$row");
    $sheet->setCellValue("P$row", 'SISA ANGGARAN');                 
    $sheet->mergeCells("R$row:U$row");
    $sheet->setCellValue("R$row", 'INDIKATOR KINERJA KELUARAN (OUTPUT)');                 
    $row_akhir = $row + 1;                
    $sheet->setCellValue("G$row_akhir", 'TARGET (%)');                
    $sheet->setCellValue("H$row_akhir", 'REALISASI (%)');  
    $sheet->setCellValue("I$row_akhir", 'TTB (%)');
    $sheet->setCellValue("J$row_akhir", 'TARGET (RP)');
    $sheet->setCellValue("K$row_akhir", 'TARGET (%)');
    $sheet->setCellValue("L$row_akhir", 'REALISASI (RP)');
    $sheet->setCellValue("M$row_akhir", 'REALISASI (%)');
    $sheet->setCellValue("N$row_akhir", 'TTB (%)');
    $sheet->setCellValue("P$row_akhir", '(RP)');
    $sheet->setCellValue("Q$row_akhir", '(%)');                
    $sheet->setCellValue("R$row_akhir", 'INDIKATOR KINERJA'); 
    $sheet->setCellValue("S$row_akhir", 'TARGET KINERJA'); 
    $sheet->setCellValue("T$row_akhir", 'REALISASI KINERJA'); 
    $sheet->setCellValue("U$row_akhir", '(%)'); 
    $row_akhir = $row + 2;
    $sheet->setCellValue("A$row_akhir", '1');                
    $sheet->setCellValue("B$row_akhir", '2');
    $sheet->setCellValue("C$row_akhir", '3');
    $sheet->setCellValue("D$row_akhir", '4');
    $sheet->setCellValue("E$row_akhir", '5');
    $sheet->setCellValue("F$row_akhir", '6');
    $sheet->setCellValue("G$row_akhir", '7');
    $sheet->setCellValue("H$row_akhir", '8');
    $sheet->setCellValue("I$row_akhir", '9');
    $sheet->setCellValue("J$row_akhir", '10');
    $sheet->setCellValue("K$row_akhir", '11');
    $sheet->setCellValue("L$row_akhir", '12');
    $sheet->setCellValue("M$row_akhir", '13');
    $sheet->setCellValue("N$row_akhir", '14');                          
    $sheet->setCellValue("O$row_akhir", '15');
    $sheet->setCellValue("P$row_akhir", '16');
    $sheet->setCellValue("Q$row_akhir", '17');
    $sheet->setCellValue("R$row_akhir", '18');
    $sheet->setCellValue("S$row_akhir", '19');            
    $sheet->setCellValue("T$row_akhir", '20'); 
    $sheet->setCellValue("U$row_akhir", '21'); 
    
    $sheet->getColumnDimension('A')->setWidth(10);
    $sheet->getColumnDimension('B')->setWidth(15);                
    $sheet->getColumnDimension('C')->setWidth(50);
    $sheet->getColumnDimension('D')->setWidth(30);                
    $sheet->getColumnDimension('E')->setWidth(20);                
    $sheet->getColumnDimension('F')->setWidth(12);
    $sheet->getColumnDimension('G')->setWidth(12);    
    $sheet->getColumnDimension('H')->setWidth(17);
    $sheet->getColumnDimension('I')->setWidth(10);                
    $sheet->getColumnDimension('J')->setWidth(20);                
    $sheet->getColumnDimension('K')->setWidth(14);
    $sheet->getColumnDimension('L')->setWidth(20);
    $sheet->getColumnDimension('M')->setWidth(15); 
    $sheet->getColumnDimension('N')->setWidth(10);                
    $sheet->getColumnDimension('O')->setWidth(17);
    $sheet->getColumnDimension('P')->setWidth(20);
    $sheet->getColumnDimension('Q')->setWidth(10);
    $sheet->getColumnDimension('R')->setWidth(40);
    $sheet->getColumnDimension('S')->setWidth(15);
    $sheet->getColumnDimension('T')->setWidth(13);
    $sheet->getColumnDimension('U')->setWidth(9);
    
    $styleArray = array(
      'font' => array('bold' => true),
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::HORIZONTAL_CENTER),
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
    );
    $sheet->getStyle("A$row:U$row_akhir")->applyFromArray($styleArray);
    $sheet->getStyle("A$row:U$row_akhir")->getAlignment()->setWrapText(true);
    
    
    $totalPaguOPD = (float)\DB::table('trRKA')
    ->where('OrgID',$OrgID)                                            
    ->where('TA',$tahun)  
    ->where('EntryLvl', 1)
    ->sum('PaguDana1');        
    
    
    $no_huruf=ord('A');
    $total_sub_kegiatan = 0;
    $total_uraian = 0;
    $totalPersenBobot = 0;
    $totalPersenTargetFisik = 0;
    $totalPersenRealisasiFisik = 0;
    $total_ttb_fisik = 0;
    $totalTargetKeuanganKeseluruhan = 0;
    $totalRealisasiKeuanganKeseluruhan = 0;
    $total_ttb_keuangan = 0;		

    $daftar_program=\DB::table('trRKA')
    ->select(\DB::raw('DISTINCT(kode_program), `Nm_Program`'))
    ->where('OrgID',$OrgID)
    ->where('EntryLvl', 1)
    ->orderByRaw('kode_urusan="X" DESC')
    ->orderBy('kode_bidang', 'ASC')
    ->orderBy('kode_program', 'ASC')
    ->orderBy('kode_kegiatan', 'ASC')
    ->orderBy('kode_sub_kegiatan', 'ASC')
    ->get();
    
    $row=$row_akhir+1;
    $row_awal=$row;        
    foreach ($daftar_program as $data_program)
    {
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
      $sheet->getStyle("A$row:U$row")->applyFromArray($styleArray);

      $kode_program = $data_program->kode_program;
      $sheet->setCellValue("A$row",chr($no_huruf));
      $sheet->setCellValue("B$row",$kode_program);  
      $sheet->setCellValue("C$row",$data_program->Nm_Program);  

      $daftar_kegiatan=\DB::table('trRKA')
      ->select(\DB::raw('DISTINCT(kode_kegiatan), `Nm_Kegiatan`'))
      ->where('kode_program',$kode_program)
      ->where('OrgID', $OrgID)
      ->orderBy('kode_kegiatan', 'ASC')
      ->orderBy('kode_sub_kegiatan', 'ASC')
      ->get();

      
      if(isset($daftar_kegiatan[0]))
      {
        $jumlah_uraian_program = 0;

        $pagu_dana_program = 0;
        $target_fisik_program = 0;
        $realisasi_fisik_program = 0;
        $ttb_fisik_program = 0;
        $target_keuangan_program = 0;
        $realisasi_keuangan_program = 0;

        $program_last_row = $row;
        $row += 1;
        $no_kegiatan = 1;
        foreach ($daftar_kegiatan as $data_kegiatan)
        {
          $styleArray = [
            'font' => [
              'italic' => true,
            ],
            'fill' => [
              'fillType'=>Fill::FILL_SOLID,
              'startColor' => [
                'argb'=>'FFE6E6FA',
              ],
            ],
          ];
          $sheet->getStyle("A$row:U$row")->applyFromArray($styleArray);

          $kode_kegiatan = $data_kegiatan->kode_kegiatan;
          $sheet->setCellValue("A$row",chr($no_huruf) .'.'.$no_kegiatan);
          $sheet->setCellValue("B$row",$kode_kegiatan);  
          $sheet->setCellValue("C$row",$data_kegiatan->Nm_Kegiatan);  

          $daftar_sub_kegiatan = \DB::table('trRKA')									
          ->select(\DB::raw('
            `RKAID`,
            `kode_sub_kegiatan`,
            `Nm_Sub_Kegiatan`,
            `PaguDana1`,
            `lokasi_kegiatan1`,
            `Nm_Sub_Organisasi`,
            keluaran1,
            tk_keluaran1,
            RealisasiKinerja
          '))
          ->where('kode_kegiatan',$kode_kegiatan)
          ->where('OrgID',$OrgID)
          ->where('TA',$tahun)
          ->where('EntryLvl', 1)
          ->orderBy('kode_sub_kegiatan', 'ASC')
          ->get();
          
          if(isset($daftar_sub_kegiatan[0]))
          {
            $pagu_dana_kegiatan = (float)\DB::table('trRKA')
                  ->where('OrgID',$OrgID)
                  ->where('kode_kegiatan',$kode_kegiatan)
                  ->where('EntryLvl', 1)
                  ->sum('PaguDana1');

            $jumlah_uraian_kegiatan = 0;

            $pagu_dana_kegiatan = 0;
            $target_fisik_kegiatan = 0;
            $realisasi_fisik_kegiatan = 0;
            $ttb_fisik_kegiatan = 0;
            $target_keuangan_kegiatan = 0;
            $realisasi_keuangan_kegiatan = 0;

            $kegiatan_last_row = $row;
            $row += 1;
            $no_sub_kegiatan = 1;
            foreach ($daftar_sub_kegiatan as $data_sub_kegiatan)
            {
              $pagu_dana_program += $data_sub_kegiatan->PaguDana1;
              $pagu_dana_kegiatan += $data_sub_kegiatan->PaguDana1;

              $RKAID=$data_sub_kegiatan->RKAID;
              $kode_sub_kegiatan = $data_sub_kegiatan->kode_sub_kegiatan;

              $persen_bobot=Helper::formatPersen($data_sub_kegiatan->PaguDana1,$totalPaguOPD);
              $totalPersenBobot+=$persen_bobot;

              //jumlah baris uraian
              $jumlahuraian = \DB::table('trRKARinc')->where('RKAID',$RKAID)->count();
              $jumlah_uraian_program += $jumlahuraian;
              $jumlah_uraian_kegiatan += $jumlahuraian;

              $data_target=\DB::table('trRKATargetRinc')
                      ->select(\DB::raw('COALESCE(SUM(target1),0) AS totaltarget, COALESCE(SUM(fisik1),0) AS jumlah_fisik'))
                      ->where('RKAID',$RKAID)
                      ->where('bulan1','<=',$no_bulan)
                      ->get();

              $data_realisasi=\DB::table('trRKARealisasiRinc')
                    ->select(\DB::raw('COALESCE(SUM(realisasi1),0) AS realisasi1, COALESCE(SUM(fisik1),0) AS fisik1'))
                    ->where('RKAID',$RKAID)
                    ->where('bulan1','<=',$no_bulan)
                    ->get();

              //menghitung persen target fisik
              $target_fisik_program += $data_target[0]->jumlah_fisik;
              $target_fisik_kegiatan += $data_target[0]->jumlah_fisik;
              $target_fisik=Helper::formatPecahan($data_target[0]->jumlah_fisik,$jumlahuraian);
              $persen_target_fisik= $target_fisik > 100 ? 100.00 : $target_fisik;
              $totalPersenTargetFisik+=$persen_target_fisik;

              //menghitung persen realisasi fisik
              $realisasi_fisik_program += $data_realisasi[0]->fisik1;
              $realisasi_fisik_kegiatan += $data_realisasi[0]->fisik1;
              $persen_realisasi_fisik=Helper::formatPecahan($data_realisasi[0]->fisik1,$jumlahuraian);
              $totalPersenRealisasiFisik+=$persen_realisasi_fisik;

              $persen_tertimbang_fisik = 0.00;
              if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
              {
                $persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100, 2);
              }
              $total_ttb_fisik+=$persen_tertimbang_fisik;

              //menghitung total target dan realisasi keuangan
              $totalTargetKeuangan=$data_target[0]->totaltarget;
              $target_keuangan_program += $totalTargetKeuangan;
              $target_keuangan_kegiatan += $totalTargetKeuangan;
              $totalTargetKeuanganKeseluruhan+=$totalTargetKeuangan;
              $persen_target_keuangan=Helper::formatPersen($totalTargetKeuangan,$data_sub_kegiatan->PaguDana1);

              $totalRealisasiKeuangan=$data_realisasi[0]->realisasi1;
              $realisasi_keuangan_program += $totalRealisasiKeuangan;
              $realisasi_keuangan_kegiatan += $totalRealisasiKeuangan;
              $totalRealisasiKeuanganKeseluruhan+=$totalRealisasiKeuangan;
              $persen_realisasi_keuangan=Helper::formatPersen($totalRealisasiKeuangan,$data_sub_kegiatan->PaguDana1);

              $persen_tertimbang_keuangan = 0.00;
              if ($persen_realisasi_keuangan > 0 && $persen_bobot > 0)
              {
                $persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100, 2);
              }
              $total_ttb_keuangan += $persen_tertimbang_keuangan;

              $sisa_anggaran=$data_sub_kegiatan->PaguDana1-$totalRealisasiKeuangan;							

              $persen_sisa_anggaran = Helper::formatPersen($sisa_anggaran,$data_sub_kegiatan->PaguDana1);
              
              $sheet->setCellValue("A$row",chr($no_huruf) .'.'.$no_kegiatan.'.'.$no_sub_kegiatan);
              $sheet->setCellValue("B$row",$kode_sub_kegiatan);  
              $sheet->setCellValue("C$row",$data_sub_kegiatan->Nm_Sub_Kegiatan);  
              $sheet->setCellValue("D$row",$data_sub_kegiatan->Nm_Sub_Organisasi);  
              $sheet->setCellValue("E$row",Helper::formatUang($data_sub_kegiatan->PaguDana1));  
              $sheet->setCellValue("F$row",$persen_bobot);  
              $sheet->setCellValue("G$row",$persen_target_fisik);  
              $sheet->setCellValue("H$row",$persen_realisasi_fisik);  
              $sheet->setCellValue("I$row",$persen_tertimbang_fisik);  
              $sheet->setCellValue("J$row",Helper::formatUang($totalTargetKeuangan));  
              $sheet->setCellValue("K$row",$persen_target_keuangan);  
              $sheet->setCellValue("L$row",Helper::formatUang($totalRealisasiKeuangan));  
              $sheet->setCellValue("M$row",$persen_realisasi_keuangan);  
              $sheet->setCellValue("N$row",$persen_tertimbang_keuangan);  
              $sheet->setCellValue("O$row",$data_sub_kegiatan->lokasi_kegiatan1);  
              $sheet->setCellValue("P$row",Helper::formatUang($sisa_anggaran));  
              $sheet->setCellValue("Q$row",$persen_sisa_anggaran);
              $sheet->setCellValue("R$row",$data_sub_kegiatan->keluaran1);
              $sheet->setCellValue("S$row",$data_sub_kegiatan->tk_keluaran1);
              $sheet->setCellValue("T$row",$data_sub_kegiatan->RealisasiKinerja);
              $row += 1; 
              $no_sub_kegiatan += 1; 
              $total_sub_kegiatan += 1;
            }
            $persen_bobot=Helper::formatPersen($pagu_dana_kegiatan,$totalPaguOPD);
            $target_fisik=Helper::formatPecahan($target_fisik_kegiatan,$jumlah_uraian_kegiatan);
            $persen_target_fisik= $target_fisik > 100 ? 100.00 : $target_fisik;

            $persen_realisasi_fisik=Helper::formatPecahan($realisasi_fisik_kegiatan,$jumlah_uraian_kegiatan);
            $persen_tertimbang_fisik = 0.00;
            if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
            {
              $persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100, 2);
            }
            
            $persen_target_keuangan=Helper::formatPersen($target_keuangan_kegiatan,$pagu_dana_kegiatan);
            $persen_realisasi_keuangan=Helper::formatPersen($realisasi_keuangan_kegiatan,$pagu_dana_kegiatan);
            $persen_tertimbang_keuangan = 0.00;
            if ($persen_realisasi_keuangan > 0 && $persen_bobot > 0)
            {
              $persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan*$persen_bobot)/100, 2);
            }

            $sisa_anggaran = $pagu_dana_kegiatan - $realisasi_keuangan_kegiatan;
            $persen_sisa_anggaran = Helper::formatPersen($sisa_anggaran,$pagu_dana_kegiatan);

            $sheet->setCellValue("D$kegiatan_last_row", 'N/A');
            $sheet->setCellValue("E$kegiatan_last_row",Helper::formatUang($pagu_dana_kegiatan));  
            $sheet->setCellValue("F$kegiatan_last_row",$persen_bobot);  
            $sheet->setCellValue("G$kegiatan_last_row",$persen_target_fisik);
            $sheet->setCellValue("H$kegiatan_last_row",$persen_realisasi_fisik);  
            $sheet->setCellValue("I$kegiatan_last_row",$persen_tertimbang_fisik);  
            $sheet->setCellValue("J$kegiatan_last_row",Helper::formatUang($target_keuangan_kegiatan));  
            $sheet->setCellValue("K$kegiatan_last_row",$persen_target_keuangan);  
            $sheet->setCellValue("L$kegiatan_last_row",Helper::formatUang($realisasi_keuangan_kegiatan));  
            $sheet->setCellValue("M$kegiatan_last_row",$persen_realisasi_keuangan);  
            $sheet->setCellValue("N$kegiatan_last_row",$persen_tertimbang_keuangan);  
            $sheet->setCellValue("O$kegiatan_last_row", 'N/A');  
            $sheet->setCellValue("P$kegiatan_last_row",Helper::formatUang($sisa_anggaran)); 
            $sheet->setCellValue("Q$kegiatan_last_row",$persen_sisa_anggaran);    
          }
          $no_kegiatan+=1;
        }
        $no_huruf+=1;                
      }
      $persen_bobot=Helper::formatPersen($pagu_dana_program,$totalPaguOPD);
      $target_fisik=Helper::formatPecahan($target_fisik_program,$jumlah_uraian_program);
      $persen_target_fisik= $target_fisik > 100 ? 100.00 : $target_fisik;

      $persen_realisasi_fisik=Helper::formatPecahan($realisasi_fisik_program,$jumlah_uraian_program);
      $persen_tertimbang_fisik = 0.00;
      if ($persen_realisasi_fisik > 0 && $persen_bobot > 0)
      {
        $persen_tertimbang_fisik=number_format(($persen_realisasi_fisik*$persen_bobot)/100, 2);
      }
      
      $persen_target_keuangan=Helper::formatPersen($target_keuangan_program,$pagu_dana_program);
      $persen_realisasi_keuangan=Helper::formatPersen($realisasi_keuangan_program,$pagu_dana_program);
      $persen_tertimbang_keuangan = 0.00;
      if ($persen_realisasi_keuangan > 0 && $persen_bobot > 0)
      {
        $persen_tertimbang_keuangan=number_format(($persen_realisasi_keuangan * $persen_bobot)/100, 2);
      }

      $sisa_anggaran = $pagu_dana_program - $realisasi_keuangan_program;
      $persen_sisa_anggaran = Helper::formatPersen($sisa_anggaran,$pagu_dana_program);
      
      $sheet->setCellValue("D$program_last_row", 'N/A');
      $sheet->setCellValue("E$program_last_row",Helper::formatUang($pagu_dana_program));  
      $sheet->setCellValue("F$program_last_row",$persen_bobot);  
      $sheet->setCellValue("G$program_last_row",$persen_target_fisik);  
      $sheet->setCellValue("H$program_last_row",$persen_realisasi_fisik);  
      $sheet->setCellValue("I$program_last_row",$persen_tertimbang_fisik);  
      $sheet->setCellValue("J$program_last_row",Helper::formatUang($target_keuangan_program));  
      $sheet->setCellValue("K$program_last_row",$persen_target_keuangan);  
      $sheet->setCellValue("L$program_last_row",Helper::formatUang($realisasi_keuangan_program));  
      $sheet->setCellValue("M$program_last_row",$persen_realisasi_keuangan);  
      $sheet->setCellValue("N$program_last_row",$persen_tertimbang_keuangan);  
      $sheet->setCellValue("O$program_last_row", 'N/A');  
      $sheet->setCellValue("P$program_last_row",Helper::formatUang($sisa_anggaran)); 
      $sheet->setCellValue("Q$program_last_row",$persen_sisa_anggaran);  

    }        
    
    if ($totalPersenBobot > 100) {
      $totalPersenBobot = 100.00;
    }
    
    $totalPersenTargetFisik = Helper::formatPecahan($totalPersenTargetFisik,$total_sub_kegiatan);        
    $totalPersenRealisasiFisik=Helper::formatPecahan($totalPersenRealisasiFisik,$total_sub_kegiatan); 
    $totalPersenTargetKeuangan=Helper::formatPersen($totalTargetKeuanganKeseluruhan,$totalPaguOPD);                
    $totalPersenRealisasiKeuangan=Helper::formatPersen($totalRealisasiKeuanganKeseluruhan,$totalPaguOPD);
    $totalSisaAnggaran = $totalPaguOPD - $totalRealisasiKeuanganKeseluruhan;
    $totalPersenSisaAnggaran = Helper::formatPersen($totalSisaAnggaran,$totalPaguOPD);

    $sheet->mergeCells("A$row:D$row");                
    $sheet->setCellValue("A$row", 'Jumlah');                                       
    $sheet->setCellValue("E$row",Helper::formatUang($totalPaguOPD));
    $sheet->setCellValue("F$row",$totalPersenBobot);

    $sheet->setCellValue("G$row",$totalPersenTargetFisik);
    $sheet->setCellValue("H$row",$totalPersenRealisasiFisik); 
    
    $sheet->setCellValue("I$row",$total_ttb_fisik); 
    
    $sheet->setCellValue("J$row",Helper::formatUang($totalTargetKeuanganKeseluruhan));
    $sheet->setCellValue("K$row",$totalPersenTargetKeuangan);
    $sheet->setCellValue("L$row",Helper::formatUang($totalRealisasiKeuanganKeseluruhan));                
    $sheet->setCellValue("M$row",$totalPersenRealisasiKeuangan);
    $sheet->setCellValue("N$row",$total_ttb_keuangan);
    $sheet->setCellValue("P$row",Helper::formatUang($totalSisaAnggaran));
    $sheet->setCellValue("Q$row",$totalPersenSisaAnggaran);

    $styleArray = array(								
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
                 'vertical' => Alignment::HORIZONTAL_CENTER),
      'borders' => array('allBorders' => array('borderStyle' => Border::BORDER_THIN))
    );   																					 
    $sheet->getStyle("A$row_awal:U$row")->applyFromArray($styleArray);
    $sheet->getStyle("A$row_awal:U$row")->getAlignment()->setWrapText(true);

    $styleArray = array(								
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_LEFT)
    );																					 
    $sheet->getStyle("B$row_awal:D$row")->applyFromArray($styleArray);
    $sheet->getStyle("O$row_awal:O$row")->applyFromArray($styleArray);
    $sheet->getStyle("R$row_awal:S$row")->applyFromArray($styleArray);

    $styleArray = array(								
      'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
    );																					 
    $sheet->getStyle("E$row_awal:N$row")->applyFromArray($styleArray);               
    $sheet->getStyle("P$row_awal:Q$row")->applyFromArray($styleArray);

    $sheet->getStyle("A$row:Q$row")->getFont()->setBold(true);

    $row+=3;
    $sheet->mergeCells("L$row:N$row");
    $sheet->setCellValue("L$row", 'Kabupaten Bintan, '.Helper::tanggal('d F Y'));
    $row += 1;
    
    $sheet->mergeCells("L$row:N$row");
    $sheet->setCellValue("L$row", 'Pengguna Anggaran');                                          
        
    $row+=5;
    $sheet->mergeCells("L$row:N$row");
    $sheet->setCellValue("L$row",$this->dataReport['nama_pengguna_anggaran']);
    $row += 1;                
    
    $sheet->mergeCells("L$row:N$row");
    $sheet->setCellValue("L$row", 'Nip.'.Helper::formatNIP($this->dataReport['nip_pengguna_anggaran']));

  }
}