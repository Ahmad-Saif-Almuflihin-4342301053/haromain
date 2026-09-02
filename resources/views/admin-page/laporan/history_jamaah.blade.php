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

                                <div class="col-md-3" id="select-prov">
                                    <select name="IDProv" id="IDProv" class="form-control select2" required>
                                        <option value="">Pilih Provinsi</option>
                                        @if (isset($prov))
                                            @foreach ($prov as $row)
                                                <option value="{{ $row->IDProv }}">{{ $row->NamaProv }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <select name="IDKab" id="IDKab" class="form-control select2">
                                        <option value="">Pilih Kabupaten</option>
                                        @if (isset($kab))
                                            @foreach ($kab as $row)
                                                <option value="{{ $row->IDKab }}">{{ $row->NamaKab }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <select name="IDAgen" id="IDAgen" class="form-control select2">
                                        <option value="">Pilih Agen</option>
                                        @foreach ($agen as $row)
                                            <option value="{{ $row->IDPerson }}">{{ $row->NamaJamaah }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <select name="IDTravel" id="IDTravel" class="form-control select2">
                                        <option value="">Pilih Travel</option>
                                        @foreach ($travel as $row)
                                            <option value="{{ $row->IDTravel }}"
                                                {{ $row->IDTravel == $IDTravel ? 'selected' : '' }}>
                                                {{ $row->NamaTravel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mt-2">
                                    <select name="IDCabang" id="IDCabang" class="form-control select2">
                                        <option value="">Pilih Cabang</option>
                                        @if (!empty($cabang) && count($cabang) > 0)
                                            @foreach ($cabang as $row)
                                                <option value="{{ $row->IDCabang }}">
                                                    {{ $row->NamaCabang }}
                                                </option>
                                            @endforeach
                                        @endif

                                    </select>
                                </div>

                                <div class="col-md-3 mt-2">
                                    <select name="paket" id="IDPaket" class="form-control select2">
                                        <option value="">Pilih Paket</option>
                                        @if (!empty($paket) && count($paket) > 0)
                                            @foreach ($paket as $row)
                                                <option value="{{ $row->IDPaket }}">{{ $row->NamaPaket }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-6 mt-1">
                                    <form id="form-search" method="get" class="w-100 py-2">
                                        <div class="input-search d-flex align-items-center px-3">
                                            <i class="fa fa-search text-gray mr-2"></i>
                                            <input type="text" name="" class="input-text" id="search-keyword"
                                                placeholder="Cari Data">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="table-responsive mt-4">
                                    <a id="btn-export" class="btn btn-inverse-primary float-right text-white mb-2"
                                        style="background: #1069E3;">
                                        Export Excel
                                    </a>
                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Jamaah</th>
                                                <th>Provinsi</th>
                                                <th>Kabupaten</th>
                                                <th>Alamat</th>
                                                <th>Paket</th>
                                                <th>Cabang</th>
                                                <th>Agen</th>
                                                <th>Tanggal Berangkat</th>
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
    </div>
    <!-- main-panel ends -->
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Variabel filter
            let IDProv = '',
                IDKab = '',
                IDCabang = '',
                IDAgen = '',
                IDPaket = '',
                IDTravel = '';

            // Set default paket dan cabang jika dibutuhkan
            setPaket();
            setCabang();

            // Inisialisasi DataTable
            const table = $('#datatable').DataTable({
                dom: 'Blfrtip',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                buttons: [{
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    },
                    action: exportAll
                }],
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "{{ url('admin/Laporan/get_history_jamaah') }}",
                    type: "POST",
                    data: function(d) {
                        d.IDProv = IDProv;
                        d.IDKab = IDKab;
                        d.IDCabang = IDCabang;
                        d.IDAgen = IDAgen;
                        d.IDPaket = IDPaket;
                        d.IDTravel = IDTravel;
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columns: [{
                        data: 'No',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },
                    {
                        data: 'NamaJamaah'
                    },
                    {
                        data: 'NamaProv'
                    },
                    {
                        data: 'NamaKab'
                    },
                    {
                        data: 'Alamat'
                    },
                    {
                        data: 'NamaPaket'
                    },
                    {
                        data: 'NamaCabang'
                    },
                    {
                        data: 'NamaAgen'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return format_date_dmy(data.TglBerangkat);
                        }
                    },
                ],
                language: {
                    url: "{{ asset('assets/admin-page/datatable/id.json') }}",
                },
                columnDefs: [{
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }]
            });

            $("#btn-export").on("click", function() {
                $(".buttons-excel").trigger("click");
            });

            // Event search
            $('#form-search').on('submit', function(e) {
                e.preventDefault()
                table.search($('#search-keyword').val()).draw();
            });

            // Event filter
            $("#IDCabang").on('change', function() {
                IDCabang = $(this).val();
                filter();
            });
            $("#IDProv").on('change', function() {
                setkab($(this).val());
                IDProv = $(this).val();
                filter();
            });
            $("#IDKab").on('change', function() {
                IDKab = $(this).val();
                filter();
            });
            $("#IDAgen").on('change', function() {
                IDAgen = $(this).val();
                filter();
            });
            $("#IDPaket").on('change', function() {
                IDPaket = $(this).val();
                filter();
            });
            $("#IDTravel").on('change', function() {
                IDTravel = $(this).val();
                IDPaket = '';
                IDCabang = '';
                setPaket();
                setCabang();
                filter();
            });

            function filter() {
                let url =
                    `{{ url('admin/Laporan/get_history_jamaah') }}?prov=${IDProv}&kab=${IDKab}&cb=${IDCabang}&agen=${IDAgen}&paket=${IDPaket}&trv=${IDTravel}`;
                    console.log(url);
                table.ajax.url(url).load();
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

            function setkab(prov) {
                $("#IDKab").empty().append(`<option value="">Pilih Kabupaten</option>`);
                $.ajax({
                    url: `{{ url('admin/Lokasi/getkab') }}/${prov}`,
                    type: 'GET',
                    success: function(res) {
                        res.data.forEach(el => {
                            $("#IDKab").append(
                                `<option value="${el.IDKab}">${el.NamaKab}</option>`);
                        });
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error');
                    }
                });
            }

            function setPaket() {
                $("#IDPaket").empty().append(`<option value="">Pilih Paket</option>`);
                let travel = $("#IDTravel").val();
                $.ajax({
                    url: `{{ url('admin/tagihan/Tagihantravel/getpaket') }}?id=${travel}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            $("#IDPaket").append(
                                `<option value="${el.IDPaket}">${el.NamaPaket}</option>`);
                        });
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error');
                    }
                });
            }

            function setCabang() {
                $("#IDCabang").empty().append(`<option value="">Pilih Cabang</option>`);
                let travel = $("#IDTravel").val();
                $.ajax({
                    url: `{{ url('admin/tagihan/Tagihantravel/getcabang') }}?id=${travel}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            $("#IDCabang").append(
                                `<option value="${el.IDCabang}">${el.NamaCabang}</option>`);
                        });
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error');
                    }
                });
            }
        });
    </script>
@endpush
