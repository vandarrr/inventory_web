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

        <div class="row mt-5">

    {{-- kiri (form) --}}
    <div class="col-4">
        <div>
            <label class="form-label">Pilih Produk</label>
            <select class="form-control" id="select-transaksi-items">
                <option value="" selected>Pilih Produk</option>
            </select>
        </div>

        <div class="mt-2">
            <label class="form-label">Note</label>
            <textarea class="form-control" id="note" rows="3"></textarea>
        </div>

        <div class="mt-2">
            <label class="form-label">Qty</label>
            <input type="number" class="form-control" id="qty">
        </div>

        <div class="mt-3">
            <button class="btn btn-dark w-100" id="btn-add">Tambahkan</button>
        </div>
    </div>

    {{-- kanan (table) --}}
    <div class="col-8">
        <label class="form-label">Daftar Barang Siap Retur</label>
        <table class="table" id="table-retur">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Note</th>
                    <th>Qty</th>
                    <th>Total Harga</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Grand Total</th>
                    <th id="grand-total">0</th>
                </tr>
            </tfoot>
        </table>
    </div>
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
            url:"{{ route('get-data.transaksi-masuk') }}",
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

    $('#select-transaksi-items').on('select2:select', function(e){
        selectedItem = e.params.data;
    });

    $("#btn-add").on("click", function(e) {
    e.preventDefault();

    let note = $("#note").val();
    let qty = $("#qty").val();

    if(!selectedItem.id || !note || !qty) {
        swal({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Input belum lengkap',
            timer:3000
        });
        return;
    }

    if(qty <= 0){
        swal({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Qty tidak boleh kurang dari 1',
            timer:3000
        });
        return;
    }

    if(qty > selectedItem.qty) {
        swal({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Qty melebihi barang yang dikirim',
            timer:3000
        });
        return;
    }

    let qtyInt = parseInt(qty);
    let hargaInt = parseInt(selectedItem.harga);

    let existingItem = returItems.find(item => item.nomor_sku === selectedItem.nomor_sku);

    if (existingItem) {
        existingItem.qty += qtyInt
        existingItem.sub_total += hargaInt * qtyInt
    } else {
        returItems.push({
            varian_id:selectedItem.id,
            text:selectedItem.text,
            note:note,
            qty:qtyInt,
            harga:hargaInt,
            sub_total:hargaInt * qtyInt,
            nomor_batch:selectedItem.nomor_batch,
            nomor_sku:selectedItem.nomor_sku
        })
    }

    $("#select-transaksi-items").val("").trigger("change")
    $("#note").val("")
    $("#qty").val("")
    renderTable()

    console.log("LOLOS VALIDASI ✅");
});

    function renderTable() {
        let tableBody = $("#table-retur tbody")
        tableBody.empty()

        returItems.forEach((item, index) => {
            let row = `
            <tr>
                <td>${index + 1}</td>
                <td>${item.text}</td>
                <td>${item.note}</td>
                <td>${item.qty}</td>
                <td>${numberFormat.format(item.sub_total)}</td>
                <td>
                    <button class='btn btn-danger btn-sm btn-round btn-icon btn-delete' data-varian-id="${item.varian_id}">
                        <i class='fas fa-trash'></i>
                    </button>
                </td>
            </tr>    
        `
        tableBody.append(row)    
        });

        if(returItems.length < 1) {
            tableBody.append(`
            <tr>
                <td colspan ="6" class="text-center">Tidak ada data yang akan diretur</td>
            `)
        }

        let grandTotal = returItems.reduce((total, item) => total + item.sub_total, 0)
        $("#grand-total").html(`Rp. ${numberFormat.format(grandTotal)}`)
    }

    $(document).on("click",".btn-delete", function () {
        let varian_id = parseInt($(this).data('varian-id'));
        returItems = returItems.filter(item => parseInt(item.varian_id) !== varian_id);
        renderTable();
    });

    renderTable();

    $("#btn-submit-retur").on("click", function () {
        let nomor_transaksi = $("#nomor_transaksi").html();

        if(returItems.length < 1) {
            swal({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Tidak ada data yang akan diretur',
                timer:3000
            })
            return;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('transaksi-retur.store') }}",
            data: {
                _token: "{{ csrf_token() }}",
                nomor_transaksi:nomor_transaksi,
                items:returItems
            },
            success: function (res) {
                if(res.success) {
                    window.location.href = res.redirect_url
                }
            }
        });
    });

    // ================= LOAD DETAIL =================
    function loadDetailTransaksi(id){

    $.ajax({
        url: `/get-data/transaksi-masuk-detail/${id}`,
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
                        <td>Rp. ${numberFormat.format(item.harga)}</td>
                        <td>Rp. ${numberFormat.format(item.sub_total)}</td>
                    </tr>
                `);
            });

            let items = res.items.map((item)=>{
                return {
                    id:item.id,
                    text: `${item.produk} ${item.varian ? item.varian.nama_varian : ''}`,
                    sub_total: item.harga * item.qty,
                    qty: item.qty,
                    harga: item.harga,
                    nomor_batch: item.nomor_batch,
                    nomor_sku: item.nomor_sku
                }
            })

            $("#select-transaksi-items").select2({
                placeholder: "Pilih Produk",
                allowClear: true,
                theme: "bootstrap-5",
                data: items
            })

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