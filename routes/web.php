<?php

use App\Http\Controllers\ExportLaporanTransaksiController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\LaporanKenaikanHargaController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\StokBarangController;
use App\Http\Controllers\TransaksiKeluarController;
use App\Http\Controllers\TransaksiMasukController;
use App\Http\Controllers\TransaksiReturController;
use App\Http\Controllers\VarianProdukController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// /master-data/kategori-produk/create
// /master-data/kategori-produk.index

Route::middleware('auth')->group(function () {

    Route::prefix('get-data')->name('get-data.')->group(function () {
        Route::get('/varian-produk', [VarianProdukController::class, 'getAllVarianJson'])->name('varian-produk');
        Route::get('/transaksi-keluar', [TransaksiKeluarController::class, 'getTransaksiKeluar'])->name('transaksi-keluar');
        Route::get('/transaksi-keluar/{nomor_transaksi}', [TransaksiKeluarController::class, 'getTransaksiKeluarItems'])->name('transaksi-keluar-items');
        Route::get('/transaksi-keluar-detail/{id}', [TransaksiKeluarController::class, 'getDetail']);

        Route::get('/transaksi-masuk', [TransaksiMasukController::class, 'getTransaksiMasuk'])->name('transaksi-masuk');
        Route::get('/transaksi-masuk-detail/{id}', [TransaksiMasukController::class, 'getDetail']);
    });

    Route::post('export-laporan-transaksi', [ExportLaporanTransaksiController::class, 'exportLaporanTransaksi'])->name
    ('export-laporan-transaksi');

    Route::resource('laporan-kenaikan-harga', LaporanKenaikanHargaController::class)->only(['index', 'update']);

    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::resource('kategori-produk', KategoriProdukController::class);
        Route::resource('produk', ProdukController::class);
        Route::resource('varian-produk', VarianProdukController::class)->only(['store', 'update', 'destroy']);
        Route::resource('stok-barang', StokBarangController::class)->only(['index']);
    });

    Route::get('/kartu-stok/{nomor_sku}', [KartuStokController::class, 'kartuStok'])->name('kartu-stok');
    Route::resource('transaksi-masuk', TransaksiMasukController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('transaksi-keluar', TransaksiKeluarController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('transaksi-retur', TransaksiReturController::class)->only(['index', 'create', 'store', 'show']);
    
    Route::delete('/transaksi-masuk-clean', [TransaksiMasukController::class, 'cleanHistory'])->name('transaksi-masuk.clean');

});