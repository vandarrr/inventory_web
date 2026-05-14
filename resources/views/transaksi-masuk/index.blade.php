@extends('layouts.kai')
@section('page_title', $pageTitle)

@section('content')

<div class="card shadow-sm border-0">

    <div class="card-body">

        {{-- ================= HEADER FILTER ================= --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h5 class="mb-0 fw-bold">Data Transaksi Masuk</h5>
                <small class="text-muted">Manajemen data barang masuk perusahaan</small>
            </div>

        </div>

        {{-- ================= FILTER SECTION ================= --}}
        <div class="card border-0 bg-light mb-4">
            <div class="card-body">

                <form action="{{ route('transaksi-masuk.index') }}" method="GET">

                    <div class="row g-3 align-items-end">

                        <div class="col-md-4">
                            <label class="form-label">Pengirim</label>
                            <input type="text"
                                   name="pengirim"
                                   class="form-control"
                                   placeholder="Cari pengirim..."
                                   value="{{ request('pengirim') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date"
                                   name="tanggal_awal"
                                   class="form-control"
                                   value="{{ request('tanggal_awal') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date"
                                   name="tanggal_akhir"
                                   class="form-control"
                                   value="{{ request('tanggal_akhir') }}">
                        </div>

                        <div class="col-md-2 d-flex gap-2">

                            <button class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>
                            </button>

                            <a href="{{ route('transaksi-masuk.index') }}"
                               class="btn btn-outline-secondary w-100">
                                Reset
                            </a>

                        </div>

                    </div>

                </form>

            </div>
        </div>

        {{-- ================= ACTION PANEL ================= --}}
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div class="text-muted small">
                Total data: <b>{{ $transaksi->total() }}</b>
            </div>

            <div class="d-flex gap-2">

                <x-form-export-laporan jenisTransaksi="pemasukan" />

                <button type="button"
                        class="btn btn-outline-danger btn-sm"
                        id="btn-clean-history">
                    <i class="fas fa-trash-alt me-1"></i>
                    Clean Data
                </button>

            </div>

        </div>

        {{-- ================= TABLE ================= --}}
        <div class="table-responsive">

            <table class="table table-hover align-middle border">

                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Nomor Transaksi</th>
                        <th>Jumlah Barang</th>
                        <th>Total Harga</th>
                        <th>Pengirim</th>
                        <th>Tanggal</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($transaksi as $index => $item)
                        <tr>

                            <td>{{ $transaksi->firstItem() + $index }}</td>

                            <td class="fw-semibold">
                                {{ $item->nomor_transaksi }}
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ number_format($item->jumlah_barang) }} pcs
                                </span>
                            </td>

                            <td class="fw-semibold text-success">
                                Rp {{ number_format($item->total_harga) }}
                            </td>

                            <td>{{ $item->pengirim }}</td>

                            <td class="text-muted">
                                {{ \Carbon\Carbon::parse($item->created_at)
                                    ->locale('id')
                                    ->translatedFormat('d M Y') }}
                            </td>

                            <td>
                                <a href="{{ route('transaksi-masuk.show', $item->nomor_transaksi) }}"
                                   class="btn btn-sm btn-primary">
                                    Detail
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Tidak ada data transaksi
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-3">
            {{ $transaksi->links() }}
        </div>

    </div>
</div>

@endsection

{{-- ================= SCRIPT CLEAN ================= --}}
@push('script')
<script>
$(document).ready(function(){

    $("#btn-clean-history").on("click", function(){

        swal({
            title: "Konfirmasi Hapus Data",
            text: "Data lebih dari 2 hari akan dihapus permanen.",
            icon: "warning",
            buttons: ["Batal", "Ya, lanjutkan"],
            dangerMode: true,
        }).then((ok) => {

            if(ok){
                $.ajax({
                    url: "{{ route('transaksi-masuk.clean') }}",
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res){
                        swal("Berhasil", res.message, "success")
                            .then(() => location.reload());
                    }
                });
            }

        });

    });

});
</script>
@endpush