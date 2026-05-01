<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\Exports\ExportBasicLaporanTransaksi;
use App\Exports\ExportDetailLaporanTransaksi;
use App\Models\Transaksi;
use App\Http\Requests\ExportLaporanTransaksiRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportLaporanTransaksiController extends Controller
{
    public function exportLaporanTransaksi(ExportLaporanTransaksiRequest $request)
    {
        return $this->downloadReport(
            $request->jenis_transaksi,
            $request->pengirim,
            $request->penerima,
            $request->tanggal_awal,
            $request->tanggal_akhir,
            $request->has('is_completed') // 🔥 FIX
        );
    }

    public function downloadReport($jenisTransaksi, $pengirim, $penerima, $tanggalAwal, $tanggalAkhir, $isCompleted)
    {
        $query = Transaksi::query();

        if ($jenisTransaksi) {
            $query->where('jenis_transaksi', $jenisTransaksi);
        }

        if ($jenisTransaksi == 'pemasukan' && $pengirim) {
            $query->where('pengirim', 'like', '%' . $pengirim . '%');
        }

        if ($jenisTransaksi == 'pengeluaran' && $penerima) {
            $query->where('penerima', 'like', '%' . $penerima . '%');
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $tanggalAwal = Carbon::parse($tanggalAwal)->startOfDay();
            $tanggalAkhir = Carbon::parse($tanggalAkhir)->endOfDay();

            $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
        }

        // 🔥 SWITCH EXPORT
        if ($isCompleted) {

            // ✅ DETAIL (dengan items)
            $transaksi = $query->with('items')->get();

            $export = new ExportDetailLaporanTransaksi(
                $transaksi,
                $jenisTransaksi,
                $tanggalAwal,
                $tanggalAkhir
            );

        } else {

            // ✅ BASIC (tanpa items)
            $transaksi = $query->get();

            $export = new ExportBasicLaporanTransaksi(
                $transaksi,
                $jenisTransaksi,
                $tanggalAwal,
                $tanggalAkhir
            );
        }

        $spreadsheet = $export->generate();

        $fileName = 'LAPORAN_' . strtoupper($jenisTransaksi);

        if ($isCompleted) {
            $fileName .= '_DETAIL';
        }

        return $export->download(
            $spreadsheet,
            $fileName . '.xlsx'
        );
    }
}