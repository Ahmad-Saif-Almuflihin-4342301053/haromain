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

                                {{-- Date Range --}}
                                <div class="col-md-4">
                                    <input type="text" class="daterangepicker form-control m-0"
                                        placeholder="Tanggal Transaksi" style="position: static;" id="range">
                                </div>

                                {{-- LEVEL ADMIN --}}
                                @if (auth()->user()->LevelID == 1)
                                    <div class="col-md-4">
                                        <select id="IDTravel" class="form-control select2">
                                            <option value="">Pilih Travel</option>
                                            @foreach ($travel as $row)
                                                <option value="{{ $row->IDTravel }}"
                                                    {{ $row->IDTravel == ($IDTravel ?? '') ? 'selected' : '' }}>
                                                    {{ $row->NamaTravel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input type="hidden" id="IDPaket" value="">

                                    {{-- LEVEL TRAVEL --}}
                                @elseif(auth()->user()->LevelID == 2)
                                    <div class="col-md-4">
                                        <select id="IDPaket" class="form-control select2">
                                            <option value="">Pilih Paket</option>
                                            @foreach ($paket as $row)
                                                <option value="{{ $row->IDPaket }}">
                                                    {{ $row->NamaPaket }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input type="hidden" id="IDTravel" value="">
                                @endif

                                {{-- Search --}}
                                <div class="col-md-4">
                                    <form id="form-search" class="w-100">
                                        <div class="input-search d-flex align-items-center px-3">
                                            <i class="fa fa-search text-gray mr-2"></i>
                                            <input type="text" class="input-text" id="search-keyword"
                                                placeholder="Cari Data">
                                            <button type="submit" class="btn btn-primary">
                                                Cari
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Table --}}
                                <div class="table-responsive mt-4">
                                    <a id="btn-export" class="btn btn-inverse-primary float-right text-white mb-2"
                                        style="background:#1069E3;">
                                        Export Excel
                                    </a>

                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                <th>Tanggal Bayar</th>
                                                <th>Nama Travel</th>
                                                <th>Nama Paket</th>
                                                <th>Nama Cabang</th>
                                                <th>Jumlah Jamaah</th>
                                                <th>Biaya</th>
                                                <th>Total</th>
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
    </div>

@endsection
@push('js')

    <script>
        $(document).ready(function() {
            //datatables
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
                    "action": exportAll,
                }],
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: `${baseURL}admin/Laporan/get_pembayaran`,
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
                        render: function(data, type, row) {
                            return format_date_dmy(data.TanggalTransaksi)
                        }
                    },
                    {
                        data: 'NamaTravel'
                    },
                    {
                        data: 'NamaPaket'
                    },
                    {
                        data: 'NamaCabang'
                    },
                    {
                        data: 'JumlahTracking'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `Rp ${format_nominal(data.NominalPaid)}`
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `Rp ${format_nominal(data.Kredit)}`
                        }
                    },


                ],
                language: {
                    url: `${baseURL}assets/admin-page/datatable/id.json`,
                },
                columnDefs: [{
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6, 7]
                }],
            });
            $("#btn-export").on("click", function() {
                $(".buttons-excel").trigger("click");
            });
            $('#form-search').on('submit', function(e) {
                e.preventDefault()
                table.search($('#search-keyword').val()).draw();
            });
            $("#IDTravel").on('change', function() {
                filter()
            })
            $("#IDPaket").on('change', function() {
                filter()
            })

            //filter durasi
            let minDate = '',
                maxDate = '';
            let trv = ''
            let paket = ''
            let range = $(".daterangepicker").daterangepicker();
            range.val('');
            range.on('apply.daterangepicker', function(ev, picker) {
                minDate = picker.startDate.format('YYYY-MM-DD');
                maxDate = picker.endDate.format('YYYY-MM-DD');
                filter()
            });

            function filter() {
                trv = $("#IDTravel").val()
                paket = $("#IDPaket").val()

                table.ajax.url(`${baseURL}admin/Laporan/get_pembayaran`).load();

                table.settings()[0].ajax.data = {
                    startDate: minDate,
                    endDate: maxDate,
                    trv: trv,
                    paket: paket,
                };
                table.ajax.reload();
            }

            function exportAll(e, dt, button, config) {
                var self = this;
                var oldStart = dt.settings()[0]._iDisplayStart;
                dt.one('preXhr', function(e, s, data) {
                    // Just this once, load all data from the server...
                    data.start = 0;
                    data.length = 2147483647;
                    dt.one('preDraw', function(e, settings) {
                        // Call the original action function
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
            };
        });
    </script>

@endpush
