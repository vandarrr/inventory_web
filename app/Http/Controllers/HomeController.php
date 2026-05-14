<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VarianProduk;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 🔷 TOTAL STOK (dari varian produk)
        $totalStok = VarianProduk::sum('stok_varian');

        // 🔷 TRANSAKSI HARI INI
        $transaksiHariIni = Transaksi::whereDate('created_at', now())->count();

        // 🔷 TRANSAKSI TERBARU
        $transaksiTerbaru = Transaksi::latest()->limit(5)->get();

        // 🔷 STOK PER PRODUK (untuk tabel dashboard)
        $stokPerProduk = VarianProduk::with('produk')
            ->get()
            ->map(function ($item) {
                return [
                    'produk' => $item->produk->nama_produk . ' ' . $item->nama_varian,
                    'stok' => $item->stok_varian
                ];
            });

        // 🔷 TOP 5 STOK TERBANYAK
        $topProduk = VarianProduk::with('produk')
            ->orderByDesc('stok_varian')
            ->limit(5)
            ->get();

        // 🔷 STOK MENIPIS (≤ 10)
        $stokMenipis = VarianProduk::with('produk')
            ->where('stok_varian', '<=', 10)
            ->get();

        // 🔷 DATA GRAFIK (7 hari terakhir)
        $grafik = DB::table('transaksis')
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw("SUM(CASE WHEN jenis_transaksi = 'pemasukan' THEN jumlah_barang ELSE 0 END) as masuk"),
                DB::raw("SUM(CASE WHEN jenis_transaksi = 'pengeluaran' THEN jumlah_barang ELSE 0 END) as keluar")
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->limit(7)
            ->get();

        return view('home', compact(
            'totalStok',
            'transaksiHariIni',
            'transaksiTerbaru',
            'stokPerProduk',
            'topProduk',
            'stokMenipis',
            'grafik'
        ));
    }
}