<?php
/**
 * File    : export_excel.php
 * Fungsi  : Mengekspor seluruh data pegawai ke file Excel (.xlsx)
 * Library : PhpSpreadsheet (komponen pre-existing, diinstall via Composer)
 *           Ini adalah implementasi Unit Kompetensi 6:
 *           Menggunakan Library atau Komponen Pre-existing
 * Author  : [Tiara]
 * Tanggal : [17-05-2026]
 * Versi   : 1.0.0
 */

require_once 'config/db.php';

// Memuat autoloader Composer untuk mengaktifkan PhpSpreadsheet
require_once 'vendor/autoload.php';

// Menggunakan namespace PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

// Membuat objek Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet       = $spreadsheet->getActiveSheet();
$sheet->setTitle('Data Pegawai');

// =============================================
// PENGATURAN LEBAR KOLOM
// =============================================
$sheet->getColumnDimension('A')->setWidth(6);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(8);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(20);

// =============================================
// BARIS 1: JUDUL LAPORAN
// =============================================
$sheet->mergeCells('A1:G1');
$sheet->setCellValue('A1', 'LAPORAN DATA PEGAWAI - SIPEGAWAI');
$sheet->getStyle('A1')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0000FF']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER],
]);
$sheet->getRowDimension(1)->setRowHeight(30);

// =============================================
// BARIS 2: TANGGAL CETAK
// =============================================
$sheet->mergeCells('A2:G2');
$sheet->setCellValue('A2', 'Dicetak pada: ' . date('d F Y, H:i:s'));
$sheet->getStyle('A2')->applyFromArray([
    'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748b']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);

// =============================================
// BARIS 3: KOSONG (pemisah)
// =============================================
$sheet->getRowDimension(3)->setRowHeight(8);

// =============================================
// BARIS 4: HEADER KOLOM
// =============================================
$headers = ['NO', 'NAMA PEGAWAI', 'JENIS KELAMIN', 'PENDIDIKAN', 'USIA', 'TGL BERGABUNG', 'JABATAN'];
$kolom   = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

foreach ($headers as $i => $header) {
    $cell = $kolom[$i] . '4';
    $sheet->setCellValue($cell, $header);
}

// Style header kolom
$sheet->getStyle('A4:G4')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000080']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                     'color'       => ['rgb' => 'FFFFFF']]],
]);
$sheet->getRowDimension(4)->setRowHeight(22);

// =============================================
// BARIS 5+: DATA PEGAWAI DARI DATABASE
// =============================================
$query  = "SELECT * FROM pegawai ORDER BY id ASC";
$result = mysqli_query($conn, $query);

$baris = 5;
$no    = 1;

while ($row = mysqli_fetch_assoc($result)) {
    // Format tanggal bergabung ke format Indonesia
    $tgl = $row['tanggal_bergabung']
         ? date('d/m/Y', strtotime($row['tanggal_bergabung']))
         : '-';

    // Mengisi data ke sel spreadsheet
    $sheet->setCellValue('A' . $baris, $no);
    $sheet->setCellValue('B' . $baris, $row['nama']);
    $sheet->setCellValue('C' . $baris, $row['jenis_kelamin']);
    $sheet->setCellValue('D' . $baris, $row['pendidikan_terakhir']);
    $sheet->setCellValue('E' . $baris, $row['usia']);
    $sheet->setCellValue('F' . $baris, $tgl);
    $sheet->setCellValue('G' . $baris, $row['jabatan']);

    // Warna baris selang-seling (zebra stripe) untuk kemudahan membaca
    $bgWarna = ($no % 2 === 0) ? 'EFF6FF' : 'FFFFFF';

    $sheet->getStyle('A' . $baris . ':G' . $baris)->applyFromArray([
        'fill'      => ['fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $bgWarna]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                         'color'       => ['rgb' => 'E2E8F0']]],
    ]);

    // Kolom NO rata tengah
    $sheet->getStyle('A' . $baris)->getAlignment()
          ->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Kolom Jenis Kelamin, Pendidikan, Usia rata tengah
    $sheet->getStyle('C' . $baris . ':E' . $baris)->getAlignment()
          ->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->getRowDimension($baris)->setRowHeight(20);
    $baris++;
    $no++;
}

// =============================================
// BARIS TOTAL (footer)
// =============================================
$sheet->mergeCells('A' . $baris . ':F' . $baris);
$sheet->setCellValue('A' . $baris, 'Total Pegawai');
$sheet->setCellValue('G' . $baris, $no - 1 . ' orang');
$sheet->getStyle('A' . $baris . ':G' . $baris)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
]);

// =============================================
// MENGHASILKAN FILE DAN MENGIRIM KE BROWSER
// =============================================

// Set nama file dengan tanggal
$namaFile = 'Data_Pegawai_SiPegawai_' . date('Y-m-d') . '.xlsx';

// Header HTTP untuk download file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $namaFile . '"');
header('Cache-Control: max-age=0');

// Menulis file ke output browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>