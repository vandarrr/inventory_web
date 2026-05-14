@extends('layouts.kai')
@section('page_title', $pageTitle)

@section('content')
<div class="bg-gray-100 min-h-screen p-4">

    <div class="bg-white rounded-4 shadow-sm p-4">

        <h4 class="fw-bold mb-4"></h4>

        <form id="form-add-produk">
            
            <div class="alert alert-danger d-none mb-4" id="alert-danger"></div>

            <!-- FORM HEADER -->
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Pengirim</label>
                    <input type="text" name="pengirim" id="pengirim" 
                        class="form-control rounded-3">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Kontak</label>
                    <input type="text" name="kontak" id="kontak" 
                        class="form-control rounded-3">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="2" 
                        class="form-control rounded-3"></textarea>
                </div>
            </div>

            <!-- FORM PRODUK -->
            <div class="row g-3 mt-4 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Produk</label>
                    <select id="select-produk" class="form-control rounded-3"></select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Batch</label>
                    <input type="text" id="nomor_batch" name="nomor_batch" 
                        class="form-control rounded-3">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Qty</label>
                    <input type="number" id="qty" name="qty" 
                        class="form-control rounded-3">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Harga</label>
                    <input type="number" id="harga" name="harga" 
                        class="form-control rounded-3">
                </div>

                <div class="col-md-2">
                    <button type="submit" 
                        class="btn w-100 text-white rounded-3"
                        style="background: #16a34a;">
                        + Tambah
                    </button>
                </div>
            </div>

        </form>

        <!-- TABLE -->
        <div class="table-responsive mt-5">
            <table class="table table-bordered align-middle" id="table-produk">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Produk</th>
                        <th>Batch</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Sub Total</th>
                        <th class="text-center">Aksi</th>
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
                            <form id="form-transaksi">
                                <button type="submit" 
                                    class="btn text-white px-4 py-2 rounded-3"
                                    style="background: #2563eb;">
                                    Simpan Transaksi
                                </button>
                            </form>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>
@endsection


@push('script')
<script>
$(document).ready(function() {

    const numberFormat = new Intl.NumberFormat('id-ID');

    let selectedProduk = [];
    let selectedOption = null;

    // ================= SELECT2 =================
    $('#select-produk').select2({
        placeholder: 'Pilih produk',
        allowClear: true,
        theme: 'default',
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

    // ================= TAMBAH PRODUK =================
    $("#form-add-produk").on("submit", function(e) {
        e.preventDefault();

        let qty = parseInt($("#qty").val()) || 0;
        let harga = parseInt($("#harga").val()) || 0;
        let nomor_batch = $("#nomor_batch").val();

        if(!selectedOption || !selectedOption.id || qty < 1 || harga < 1 || !nomor_batch) {
            swal({
                icon: 'warning',
                title: 'Input belum lengkap',
                text: 'Semua field wajib diisi'
            });
            return;
        }

        let sub_total = qty * harga;

        let existingItem = selectedProduk.find(item => item.nomor_sku === selectedOption.nomor_sku);

        if(existingItem) {
            existingItem.qty += qty;
            existingItem.harga = harga;
            existingItem.sub_total = existingItem.qty * existingItem.harga;
        } else {
            selectedProduk.push({
                nomor_sku: selectedOption.nomor_sku,
                text: selectedOption.text,
                qty: qty,
                harga: harga,
                nomor_batch: nomor_batch,
                sub_total: sub_total
            });
        }

        // reset
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
                    <td class="text-center">${index + 1}</td>
                    <td>${item.text}</td>
                    <td>${item.nomor_batch}</td>
                    <td>${item.qty}</td>
                    <td>Rp. ${numberFormat.format(item.harga)}</td>
                    <td>Rp. ${numberFormat.format(item.sub_total)}</td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm rounded-3 btn-delete" 
                            data-nomor-sku="${item.nomor_sku}">
                            Hapus
                        </button>
                    </td>
                </tr>
            `);
        });

        // FIX BUG DELETE
        $(document).off("click", ".btn-delete").on("click", ".btn-delete", function() {
            let nomorSku = $(this).data("nomor-sku");
            selectedProduk = selectedProduk.filter(item => item.nomor_sku !== nomorSku);
            renderTable();
        });

        if(selectedProduk.length === 0) {
            tableBody.append(`<tr><td colspan="7" class="text-center text-muted">Belum ada data</td></tr>`);
        }

        $("#grand-total").text(numberFormat.format(grandTotal));
    }

    renderTable();

    // ================= SIMPAN =================
    $("#form-transaksi").on("submit", function (e) {
        e.preventDefault();

        if(selectedProduk.length === 0 ){
            swal({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Minimal 1 produk harus diinput'
            });
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
                if(errors){
                    renderError(errors);
                }
            }
        });
    });

    function renderError(errors) {
        let alertBox = $("#alert-danger");
        alertBox.empty();

        Object.values(errors).forEach(err => {
            err.forEach(msg => {
                alertBox.append(`<div>${msg}</div>`);
            })
        });

        alertBox.removeClass('d-none').fadeIn();
    }

});
</script>
@endpush