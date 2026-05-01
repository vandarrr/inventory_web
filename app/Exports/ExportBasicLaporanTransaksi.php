<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportBasicLaporanTransaksi
{
    protected $transaksi, $jenisTransaksi, $tanggalAwal, $tanggalAkhir;

    public function __construct($transaksi, $jenisTransaksi, $tanggalAwal, $tanggalAkhir)
    {
        $this->transaksi = $transaksi;
        $this->jenisTransaksi = $jenisTransaksi;
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function generate()
    {
        Carbon::setLocale('id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 🔥 TITLE
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI ' . strtoupper($this->jenisTransaksi));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 🔥 PERIODE
        $periode = '-';
        if ($this->tanggalAwal && $this->tanggalAkhir) {
            $periode = date('d M Y', strtotime($this->tanggalAwal)) . ' s/d ' .
                       date('d M Y', strtotime($this->tanggalAkhir));
        }

        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', 'Periode ' . $periode);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 🔥 HEADER (TANPA ITEMS)
        $headers = [
            'No',
            'Tanggal Transaksi',
            'Nomor Transaksi',
            $this->jenisTransaksi == 'pemasukan' ? 'Pengirim' : 'Penerima',
            'Kontak',
            'Petugas',
            'Jumlah Barang',
            'Total Harga',
            'Keterangan'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }

        // 🔥 STYLE HEADER
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9D9D9'],
            ],
        ]);

        // 🔥 DATA (TANPA ITEMS)
        $row = 5;

        foreach ($this->transaksi as $index => $trx) {

            $sheet->setCellValue('A'.$row, $index + 1);

            $sheet->setCellValue(
                'B'.$row,
                Carbon::parse($trx->created_at)
                    ->locale('id')
                    ->translatedFormat('l, d F Y')
            );

            $sheet->setCellValue('C'.$row, $trx->nomor_transaksi);
            $sheet->setCellValue('D'.$row,$this->jenisTransaksi == 'pemasukan'? $trx->pengirim: $trx->penerima);
            $sheet->setCellValue('E'.$row, $trx->kontak);
            $sheet->setCellValue('F'.$row, $trx->petugas);
            $sheet->setCellValue('G'.$row, $trx->jumlah_barang);

            $sheet->setCellValue(
                'H'.$row,
                'Rp ' . number_format($trx->total_harga, 0, ',', '.')
            );

            $sheet->setCellValue('I'.$row, $trx->keterangan);

            $row++;
        }

        $lastRow = $row - 1;

        // 🔥 ALIGNMENT
        $sheet->getStyle('A5:I'.$lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // TOTAL HARGA KE KANAN
        $sheet->getStyle('H5:H'.$lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // WRAP TEXT
        $sheet->getStyle('A4:I'.$lastRow)
            ->getAlignment()
            ->setWrapText(true);

        // BORDER
        $sheet->getStyle('A4:I'.$lastRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // AUTO WIDTH
        foreach (range('A','I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    public function download($spreadsheet, $fileName)
    {
        $writer = new Xlsx($spreadsheet);

        ob_end_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}