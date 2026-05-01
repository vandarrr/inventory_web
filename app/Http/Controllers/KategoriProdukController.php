<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeKategoriProdukRequest;
use App\Http\Requests\updateKategoriProdukRequest;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;

class KategoriProdukController extends Controller
{
public   $pageTitle = 'Kategori Produk'; 
public function index()
    {
        $pageTitle = $this->pageTitle;
        $perPage = request()->query('perPage') ?? 10;
        $search = request()->query('search');
        $query = KategoriProduk::query();

        if ($search) {
            $query->where('nama_kategori', 'like', '%' . $search . '%');
        }

        $kategori = $query->paginate($perPage)->appends(request()->query());
        confirmDelete('Hapus data kategori Produk tidak dapat dibatalkan, Lanjutkan ?');
        return view('kategori-produk.index', compact('pageTitle', 'kategori'));
    }

    public function store(storeKategoriProdukRequest $request) 
    {
        KategoriProduk::create([
            'nama_kategori' => $request->nama_kategori
        ]);
        toast()->success('Kategori produk berhasil ditambahkan');
        return redirect()->route('master-data.kategori-produk.index');
    }

    public function update(updateKategoriProdukRequest $request, KategoriProduk $kategori_produk) 
    {
        $kategori_produk->nama_kategori = $request->nama_kategori;
        $kategori_produk->save();
        toast()->success('Kategori produk berhasil diubah');
        return redirect()->route('master-data.kategori-produk.index');

    }

    public function destroy(KategoriProduk $kategoriProduk)
    {
        $kategoriProduk->delete();
        toast()->success('kategori Produk berhasil dihapus');
        return redirect()->route('master-data.kategori-produk.index');
    }
}
