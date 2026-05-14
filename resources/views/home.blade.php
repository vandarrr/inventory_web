@extends('layouts.kai')

@section('content')
<div class="container">

    {{-- 🔷 HEADER --}}
    <div class="mb-4">
        <h3>Dashboard Gudang LPG</h3>
        <small class="text-muted">Monitoring stok & aktivitas gudang</small>
    </div>

    {{-- 🟩 KPI --}}
    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <small>Total Stok</small>
                    <h3>{{ $totalStok }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <small>Transaksi Hari Ini</small>
                    <h3>{{ $transaksiHariIni }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body">
                    <small>Stok Menipis</small>
                    <h3>{{ $stokMenipis->count() }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-dark text-white">
                <div class="card-body">
                    <small>Jumlah Produk</small>
                    <h3>{{ $stokPerProduk->count() }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- 📊 GRAFIK --}}
    <div class="card shadow-sm mt-3">
        <div class="card-header">Grafik Transaksi</div>
        <div class="card-body">
            <canvas id="chart"></canvas>
        </div>
    </div>

    {{-- 📦 INSIGHT --}}
    <div class="row mt-4">

        {{-- 🔥 TOP PRODUK --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Top Stok</div>
                <div class="card-body">
                    @foreach($topProduk as $item)
                        <div class="d-flex justify-content-between">
                            <span>{{ $item->produk->nama_produk }} {{ $item->nama_varian }}</span>
                            <strong>{{ $item->stok_varian }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ⚠️ STOK MENIPIS --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-danger">Stok Menipis</div>
                <div class="card-body">
                    @forelse($stokMenipis as $item)
                        <div class="d-flex justify-content-between">
                            <span>{{ $item->produk->nama_produk }} {{ $item->nama_varian }}</span>
                            <strong class="text-danger">{{ $item->stok_varian }}</strong>
                        </div>
                    @empty
                        <p class="text-muted">Semua stok aman</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- 📋 TRANSAKSI --}}
    <div class="card mt-4 shadow-sm">
        <div class="card-header">Transaksi Terbaru</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksiTerbaru as $trx)
                        <tr>
                            <td>{{ $trx->nomor_transaksi }}</td>
                            <td>
                                @if($trx->jenis_transaksi == 'pemasukan')
                                    <span class="badge bg-success">Masuk</span>
                                @else
                                    <span class="badge bg-danger">Keluar</span>
                                @endif
                            </td>
                            <td>{{ $trx->jumlah_barang }}</td>
                            <td>{{ $trx->created_at->format('d-m-Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Chart --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const data = @json($grafik);

new Chart(document.getElementById('chart'), {
    type: 'line',
    data: {
        labels: data.map(d => d.tanggal),
        datasets: [
            {
                label: 'Masuk',
                data: data.map(d => d.masuk),
                borderWidth: 2
            },
            {
                label: 'Keluar',
                data: data.map(d => d.keluar),
                borderWidth: 2
            }
        ]
    }
});
</script>

@endsection