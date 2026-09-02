@extends('layouts.master')

@section('content')

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <!-- first row starts here -->
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div class="row w-100">
                                <div class="form-group col-md-4">
                                    <select name="" id="IDPaket" class="form-control select2">
                                        <option value="">Pilih Paket</option>
                                        @foreach ($paket as $row)
                                            <option value="{{ $row->NamaPaket }}">{{ $row->NamaPaket }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <form id="form-search" method="get" class="col-md-8">
                                    <div class="input-search d-flex align-items-center px-3">
                                        <i class="fa fa-search text-gray mr-2"></i>
                                        <input type="text" name="" class="input-text" id="search-keyword"
                                            placeholder="Cari Data">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive mt-4">
                                <table id="datatable" class="table w-100">
                                    <thead class="bg-lightgray">
                                        <tr>
                                            <th>No.</th>
                                            <th>No Invoice</th>
                                            <th>Tanggal Invoice</th>
                                            <th>Nama Paket</th>
                                            <th>Nominal</th>
                                            <th>Bayar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- content-wrapper ends -->

    <!-- partial -->
</div>
<!-- main-panel ends -->

<!-- Modal Virtual Account -->
<div class="modal fade" id="modalVA" tabindex="-1" aria-labelledby="modalVALabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVALabel">Rincian Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-va">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="Travel">Nomor Invoice</label>
                        <input type="text" name="invoice" id="show-NoInvoice" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="" class="d-block">Pilih Bank</label>
                        <select name="bank" id="bank" class="form-control select2" required>
                            <option value="">Pilih Bank</option>

                            @foreach ($bank as $row)
                                <option value="{{ $row['code'] }}">{{ $row['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Total Pembayaran</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary rounded-lg text-white">Rp</span>
                            </div>
                            <input type="text" id="show-Total" placeholder="Tarif" value="" readonly
                                class="form-control nominal">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btn-va">Generate Virtual Account</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Invoice -->
<div class="modal fade" id="modalInvoice" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="min-width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invoice Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <embed type="text/html" id="embed-invoice" src="" width="100%" height="600">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" id="btn-bayar" class="btn btn-primary">Bayar</button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('js')
<script>
    $(document).ready(function() {
        // Datatables
        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "{{ url('admin/tagihan/biayaaplikasi/get') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            },
            columns: [{
                    data: 'No',
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
                {
                    data: 'NoInvoice'
                },
                {
                    data: 'TanggalInvoice'
                },
                {
                    data: 'NamaPaket'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return 'Rp ' + format_nominal(data.Kredit);
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (data.NoVa != null && data.NoVa != '') {
                            return `<a type="button" href="{{ url('admin/tagihan/Pembayaran') }}?inv=${btoa(data.NoInvoice)}" class="btn btn-primary btn-bayar" data-invoice="${data.NoInvoice}" data-total="${format_nominal(data.Kredit)}">Lanjutkan Pembayaran</a>`;
                        } else {
                            return `<button type="button" class="btn btn-primary btn-bayar" data-invoice="${data.NoInvoice}" data-total="${format_nominal(data.Kredit)}" data-toggle="modal" data-target="#modalVA">Bayar</button>`;
                        }
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<a class="btn btn-inverse-info btn-show p-2 mr-2" data-invoice="${data.NoInvoice}" data-total="${format_nominal(data.Kredit)}" data-toggle="modal" data-target="#modalInvoice"> <i class="fa fa-eye text-behance"></i> </a>`;
                    }
                }
            ],
            language: {
                url: "{{ asset('assets/admin-page/datatable/id.json') }}"
            },
            fnDrawCallback: function() {
                $('.bs-toggle').bootstrapToggle();
            }
        });

        // Search
        $('#form-search').on('submit', function(e) {
            e.preventDefault();
            table.search($('#search-keyword').val()).draw();
        });

        // Filter Paket
        $('#IDPaket').on('change', function(e) {
            table.search($(this).val()).draw();
        });

        // Show modal VA
        $("#datatable").on('click', '.btn-bayar', function() {
            $("#show-NoInvoice").val($(this).data('invoice'));
            $("#show-Total").val($(this).data('total'));
            $("#show-NoVA").val('');
        });

        // Button bayar di modal invoice
        $("#btn-bayar").on('click', function() {

            $("#modalInvoice").modal('hide');
            $("#modalVA").modal('show');
            $("#show-NoInvoice").val($(this).data('invoice'));
            $("#show-Total").val($(this).data('total'));
            $("#show-NoVA").val('');
        });

        $("#datatable").on('click', '.btn-show', function() {
            let invoice = $(this).data('invoice');
            console.log(invoice)
            let total = $(this).data('total');
            let src = "{{ url('admin/tagihan/Tagihantravel/invoice') }}?inv=" + invoice;
            $("#embed-invoice").attr('src', src);
            $("#btn-bayar").attr('data-total', total);
            $("#btn-bayar").attr('data-invoice', invoice);
        });

        $("#form-va").on('submit', function(e) {
            e.preventDefault();
            $("#btn-va").prop("disabled", true);
            let inv = $("#show-NoInvoice").val();
            let bank = $("#bank").val();
            $.ajax({
                url: "{{ url('admin/tagihan/Pembayaran/generate_virtual_account') }}?inv=" +
                    inv + "&bank=" + bank,
                type: 'GET',
                success: function(res) {
                    res = typeof res === 'string' ? JSON.parse(res) : res;
                    if (res.status == 'success') {
                        window.location.href = res.redirect;
                    } else {
                        toast('error', res.message);
                        $("#btn-va").prop("disabled", false);
                    }
                },
                error: function(xhr, status, error) {
                    toast('error', 'Gagal mendapatkan data');
                    $("#btn-va").prop("disabled", false);
                }
            });
        });
    });
</script>
@endpush
