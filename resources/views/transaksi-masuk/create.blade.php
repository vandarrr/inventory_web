@extends('layouts.kai')
@section('page_title', $pageTitle)

@section('content')
<div class="card py-5">
    <div class="card-body">

        <form class="row col-12 justify-content-between" id="form-add-produk">
            <div class="alert  d-none" id="alert-danger" style="box-shadow: none !important"></div>

             <div class="row">
                <div class="form-group w-25">
                    <label for="pengirim" class="form-label">Pengirim</label>
                    <input type="text" name="pengirim" id="pengirim" class="form-control">
                </div>

                <div class="form-group w-25">
                    <label for="kontak" class="form-label">Kontak</label>
                    <input type="text" name="kontak" id="kontak" class="form-control">
                </div>

                <div class="form-group mt-1">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" cols="30" rows="2" class="form-control"></textarea>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-4">
                    <select id="select-produk" class="form-control border py-3"></select>
                </div>

                <div class="col-2">
                    <input type="text" name="nomor_batch" id="nomor_batch" class="form-control" placeholder="Nomor Batch">
                </div>

                <div class="col-2">
                    <input type="number" name="qty" id="qty" class="form-control" placeholder="Qty">
                </div>

                <div class="col-2">
                    <input type="number" name="harga" id="harga" class="form-control" placeholder="Harga">
                </div>

                <div class="col-2">
                    <button type="submit" class="btn btn-dark btn-round w-100" id="btn-add">Tambahkan</button>
                </div>
            </div>
        </form>

        <table class="table mt-5" id="table-produk">
            <thead>
                <tr>
                    <th class="text-center" style="width: 15px">No</th>
                    <th>Produk</th>
                    <th>Batch</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Sub Total</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-end">Grand Total</th>
                    <th id="grand-total">0</th>
                </tr>
                <tr>
                    <th colspan="7" class="text-end">
                        <form id='form-transaksi'>
                            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                        </form>
                    </th>
                </tr>
            </tfoot>
        </table>

    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {

    const numberFormat = new Intl.NumberFormat('id-ID');

    let selectedProduk = [];
    let selectedOption = null; // ✅ WAJIB

    // ================= SELECT2 =================
    $('#select-produk').select2({
        placeholder: 'Pilih produk',
        theme: 'bootstrap-5',
        ajax: {
            url: "{{ route('get-data.varian-produk') }}",
            dataType: 'json',
            delay: 250,
            data: function(params){
                return { search: params.term }
            },
            processResults: function(data) {
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.text,
                        nomor_sku: item.nomor_sku
                    }))
                }
            }
        }
    });

    $('#select-produk').on('select2:select', function(e){
        selectedOption = e.params.data;
    });

    // ================= SUBMIT =================
    $("#form-add-produk").on("submit", function(e) {
        e.preventDefault();

        let qty = parseInt($("#qty").val()) || 0;
        let harga = parseInt($("#harga").val()) || 0;
        let nomor_batch = $("#nomor_batch").val();

        if(!selectedOption || !selectedOption.id || qty < 1 || harga < 1 || !nomor_batch) {
            alert("Input belum lengkap");
            return;
        }

        let sub_total = qty * harga;

        let existingItem = selectedProduk.find(item => item.nomor_sku === selectedOption.nomor_sku);

        if(existingItem) {
            existingItem.qty = parseInt(existingItem.qty) + parseInt(qty);
            existingItem.harga = parseInt(harga); // ✅ update harga ke harga terbaru
            existingItem.sub_total = existingItem.qty * existingItem.harga; // ✅ hitung ulang sub_total
        } else {
            selectedProduk.push({
                nomor_sku: selectedOption.nomor_sku,
                text: selectedOption.text,
                qty: qty,
                harga: harga,
                nomor_batch: nomor_batch,
                sub_total: sub_total // ✅ konsisten
            })
        }
        

        // reset input
        $("#select-produk").val(null).trigger('change');
        $("#qty").val('');
        $("#harga").val('');
        $("#nomor_batch").val('');
        selectedOption = null;

        renderTable();
    });

    // ================= RENDER TABLE =================
    function renderTable() {
        let tableBody = $("#table-produk tbody");
        tableBody.empty();

        let grandTotal = 0;

        selectedProduk.forEach((item, index) => {

            grandTotal += item.sub_total;

            tableBody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.text}</td>
                    <td>${item.nomor_batch}</td>
                    <td>${item.qty}</td>
                    <td>${numberFormat.format(item.harga)}</td>
                    <td>${numberFormat.format(item.sub_total)}</td>
                    <td>
                    <button class="btn btn-danger btn-round btn-delete btn-icon" data-nomor-sku="${item.nomor_sku}">
                        <i class="fas fa-trash"></i>
                    </button>
                    </td>
                </tr>
            `);
        });

        $(document).on("click"," .btn-delete", function() {
            let nomorSku = $(this).data("nomor-sku");
            selectedProduk = selectedProduk.filter(item => item.nomor_sku !== nomorSku);
            renderTable();
        });

        if(selectedProduk.length === 0) {
            tableBody.append(`<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>`);
        }

        $("#grand-total").text(numberFormat.format(grandTotal));
    }

    renderTable();

    $("#form-transaksi").on("submit", function (e) {
        e.preventDefault();
        if(selectedProduk.length === 0 ){
            swal({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Wajib menuliskan 1 produk yang dicatat',
                timer:3000 
            })
            return;
        }

        $.ajax({
            method: "POST",
            url: "{{ route('transaksi-masuk.store') }}",
            data: {
                _token: "{{ csrf_token() }}",
                items: selectedProduk,
                pengirim: $("#pengirim").val(),
                kontak: $("#kontak").val(),
                keterangan: $("#keterangan").val(),
            },
            success: function(response) {
                window.location.href = response.redirect_url;

            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                console.log(errors);
                if(errors){
                    renderError(errors);
                    return;
                }
            }
        });
    });

    function renderError(errors) {
        let alertBox = $("#alert-danger");
        alertBox.empty();
        Object.values(errors).forEach(err => {
            err.forEach(msg => {
                alertBox.append(`<p>${msg}</p>`);
            })
        })
        
        alertBox.removeClass('d-none').show();
    }



});
</script>
@endpush