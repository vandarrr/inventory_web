@extends('layouts.kai')
@section('page_title',$pageTitle)
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-end">
        <button class="btn btn-primary" id="btn-submit-retur">Simpan Retur</button>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="nomor_transaksi" class="form-label">Nomor Transaksi</label>
            <select class="form-control border" id="select-transaksi"></select>
        </div>
        <div class="mt-5">
            <h5 id="nomor_transaksi"></h5>
            <p class="m-0" id="tanggal"></p>
            <p class="m-0" id="pengirim"></p>
            <p class="m-0" id="kontak"></p>
            <p class="m-0" id="jumlah_barang"></p>
            <p class="m-0" id="total_harga"></p>
        </div>
        <div class="my-3">
            <label class="form-label">Detail Barang</label>
            <table class="table" id="table-items">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Nomor Batch</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Sub Total</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('script')
<script>
$(document).ready(function () {

    let selectedItem = {};
    let returItems = [];
    const numberFormat = new Intl.NumberFormat('id-ID');

    // ================= SELECT2 =================
    $("#select-transaksi").select2({
        placeholder: 'Pilih Transaksi',
        allowClear: true,
        theme: 'bootstrap-5',
        ajax: {
            url:"{{ route('get-data.transaksi-keluar') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term
                }
            },
            processResults: function(data) {  
                return {
                    results:data.map((item)=> {
                        return {
                            id: item.id,
                            text: item.text
                        }
                    })
                }
            }
        }
    });

    // ================= EVENT PILIH TRANSAKSI =================
    $('#select-transaksi').on('select2:select', function(e){

        let data = e.params.data;

        $("#nomor_transaksi").text(data.text);

        loadDetailTransaksi(data.id);
    });

    // ================= LOAD DETAIL =================
    function loadDetailTransaksi(id){

    $.ajax({
        url: `/get-data/transaksi-keluar-detail/${id}`,
        type: 'GET',
        success: function(res){

            console.log(res);

            $("#nomor_transaksi").text(res.nomor_transaksi);
            $("#tanggal").text("Tanggal : " + res.created_at);
            $("#pengirim").text("Pengirim : " + res.pengirim);
            $("#kontak").text("Kontak : " + res.kontak);
            $("#jumlah_barang").text("Jumlah Barang : " + res.jumlah_barang + " pcs");
            $("#total_harga").text("Total : Rp " + numberFormat.format(res.total_harga));

            let tbody = $("#table-items tbody");
            tbody.empty();

            res.items.forEach((item, index) => {
                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.produk} ${item.varian.nama_varian}</td>
                        <td>${item.nomor_batch ?? '-'}</td>
                        <td>${item.qty}</td>
                        <td>${numberFormat.format(item.harga)}</td>
                        <td>${numberFormat.format(item.sub_total)}</td>
                    </tr>
                `);
            });

        },
        error: function(err){
            console.log(err);
            alert("Gagal ambil detail transaksi");
        }
    });
}
   

});
</script>
@endpush