@extends('layouts.master')

@section('content')

@push('css')
    <style>
        .btn-tab {
            border-radius: 0;
        }

        .tab-active {
            border-bottom: 3px solid #e5a11d;
            color: black !important;
        }
    </style>
@endpush

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between flex-wrap">
                                <div class="col-md-4">
                                    <input type="text" class="daterangepicker form-control m-0"
                                        placeholder="Tanggal Transaksi" style="position: static;" id="range">
                                </div>
                                <div class="col-md-4">
                                    <select name="IDCabang" id="IDCabang" class="form-control select2">
                                        @foreach ($cabang as $key => $row)
                                            <option value="{{ $row->IDCabang }}" {{ $loop->first ? 'selected' : '' }}>
                                                {{ $row->NamaCabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <form id="form-search" method="get" class="w-100">
                                        <div class="input-search d-flex align-items-center px-3">
                                            <i class="fa fa-search text-gray mr-2"></i>
                                            <input type="text" name="keyword" class="input-text" id="search-keyword"
                                                placeholder="Cari Data">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="table-responsive mt-4 w-100">
                                    <a id="btn-export" class="btn btn-inverse-primary float-right text-white mb-2"
                                        style="background: #1069E3;">Export Excel</a>

                                    <table id="datatable" class="table table-bordered w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                <th>Tanggal Bayar</th>
                                                <th>Cabang</th>
                                                <th>Jenis Pembayaran</th>
                                                <th>Uraian Transaksi</th>
                                                <th>Debet</th>
                                                <th>Kredit</th>
                                                <th>Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- DataTables akan load data via AJAX --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            let minDate = '';
            let maxDate = '';

            const table = $('#datatable').DataTable({
                dom: 'Blfrtip',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                buttons: [{
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    },
                    action: exportAll,
                }],
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: '{{ url('admin/Laporan/get_aruskas') }}',
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
                        data: null,
                        render: data => format_date_dmy(data.TanggalTransaksi)
                    },
                    {
                        data: 'NamaCabang'
                    },
                    {
                        data: 'JenisTransaksi'
                    },
                    {
                        data: 'UraianTransaksi'
                    },
                    {
                        data: null,
                        render: data => data.Debet != null ? `Rp ${format_nominal(data.Debet)}` : ''
                    },
                    {
                        data: null,
                        render: data => data.Kredit != null ? `Rp ${format_nominal(data.Kredit)}` : ''
                    },
                    {
                        data: null,
                        render: data => `Rp ${format_nominal(data.Saldo ?? 0)}`
                    },
                ],
                language: {
                    url: '{{ asset('assets/admin-page/datatable/id.json') }}',
                },
                columnDefs: [{
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6, 7]
                }],
            });

            // Export button
            $("#btn-export").on("click", function() {
                $(".buttons-excel").trigger("click");
            });

            // Form search
            $('#form-search').on('submit', function(e) {
                e.preventDefault();
                table.search($('#search-keyword').val()).draw();
            });

            // Filter cabang
            $("#IDCabang").on('change', filter);

            // Date range filter
            let range = $(".daterangepicker").daterangepicker();
            range.val('');
            range.on('apply.daterangepicker', function(ev, picker) {
                minDate = picker.startDate.format('YYYY-MM-DD');
                maxDate = picker.endDate.format('YYYY-MM-DD');
                filter();
            });

            function filter() {
                let cabang = $("#IDCabang").val();
                // let url = `{{ url('admin/Laporan/get_aruskas') }}?start=${minDate}&end=${maxDate}&cb=${cabang}`;
                let url = `{{ url('admin/Laporan/get_aruskas') }}`;
                table.ajax.url(url).load();


                table.settings()[0].ajax.data = {
                    startDate: minDate,
                    endDate: maxDate,
                    cb: cabang,

                };
                table.ajax.reload();
            }

            function exportAll(e, dt, button, config) {
                var self = this;
                var oldStart = dt.settings()[0]._iDisplayStart;

                dt.one('preXhr', function(e, s, data) {
                    data.start = 0;
                    data.length = 2147483647;

                    dt.one('preDraw', function(e, settings) {
                        if (button[0].className.indexOf('buttons-excel') >= 0) {
                            $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt,
                                    button, config) :
                                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt,
                                    button, config);
                        }

                        dt.one('preXhr', function(e, s, data) {
                            settings._iDisplayStart = oldStart;
                            data.start = oldStart;
                        });

                        setTimeout(dt.ajax.reload, 0);
                        return false;
                    });
                });

                dt.ajax.reload();
            }
        });
    </script>
@endpush
