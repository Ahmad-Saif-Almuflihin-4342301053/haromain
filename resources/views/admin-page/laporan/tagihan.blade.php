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
                            <div class="row">

                                {{-- Tanggal Berangkat --}}
                                <div class="col-md-3">
                                    <input type="text" class="daterangepicker form-control m-0"
                                        placeholder="Tanggal Berangkat" style="position: static;" id="range">
                                </div>

                                {{-- Travel --}}
                                <div class="col-md-3">
                                    <select name="IDTravel" id="IDTravel" class="form-control select2">
                                        <option value="">Pilih Travel</option>
                                        @foreach ($travel as $row)
                                            <option value="{{ $row->IDTravel }}" @selected($row->IDTravel == $IDTravel)>
                                                {{ $row->NamaTravel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Cabang --}}
                                <div class="col-md-3">
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

                                {{-- Paket --}}
                                <div class="col-md-3 mt-2">
                                    <select name="IDPaket" id="IDPaket" class="form-control select2">
                                        <option value="">Pilih Paket</option>
                                        @if (!empty($paket) && count($paket) > 0)
                                            @foreach ($paket as $row)
                                                <option value="{{ $row->IDPaket }}">
                                                    {{ $row->NamaPaket }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                {{-- Agen --}}
                                <div class="col-md-4 mt-2">
                                    <select name="IDAgen" id="IDAgen" class="form-control select2">
                                        <option value="">Pilih Agen</option>
                                        @if (!empty($agen) && count($agen) > 0)
                                            @foreach ($agen as $row)
                                                <option value="{{ $row->IDPerson }}">
                                                    {{ $row->NamaJamaah }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                {{-- Search --}}
                                <div class="col-md-5 mt-2">
                                    <form id="form-search" method="get" class="w-100">
                                        <div class="input-search d-flex align-items-center px-3">
                                            <i class="fa fa-search text-gray mr-2"></i>
                                            <input type="text" class="input-text" id="search-keyword"
                                                placeholder="Cari Data">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-3"></div>

                                {{-- Total Tagihan --}}
                                <div class="col-md-4 py-3">
                                    <div class="color-card bg-light m-auto p-3">
                                        <p class="font-weight-semibold mb-0">Total Tagihan</p>
                                        <span class="small" id="Total"></span>
                                    </div>
                                </div>

                                {{-- Table --}}
                                <div class="table-responsive mt-4">
                                    <a id="btn-export" class="btn btn-inverse-primary float-right text-white mb-2"
                                        style="background: #1069E3;">
                                        Export Excel
                                    </a>

                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                <th>Tanggal Berangkat</th>
                                                <th>Nama Jamaah</th>
                                                <th>Paket</th>
                                                <th>Cabang</th>
                                                <th>Agen</th>
                                                <th>Batas Akhir Pembayaran</th>
                                                <th>Tagihan</th>
                                                <th>Terbayar</th>
                                                <th>Sisa Tagihan</th>
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    },
                    action: exportAll,
                }],
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: `${baseURL}admin/Laporan/get_tagihan`,
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
                            return format_date_dmy(data.TglBerangkat)
                        }
                    },
                    {
                        data: 'NamaJamaah'
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
                            return format_date_dmy(data.TglMaxBayar)
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let info = data.InfoTagihan == null ?
                                0 :
                                format_nominal(data.InfoTagihan.toLocaleString());

                            return `Rp ${info}`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let info = data.InfoTerbayar == null ?
                                0 :
                                format_nominal(data.InfoTerbayar.toLocaleString());

                            return `Rp ${info}`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let info = data.InfoSisaTagihan == null ?
                                0 :
                                format_nominal(data.InfoSisaTagihan.toLocaleString());

                            return `Rp ${info}`;
                        }
                    }

                ],
                language: {
                    url: `${baseURL}assets/admin-page/datatable/id.json`,
                },
                columnDefs: [{
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }],
                drawCallback: function() {
                    let total = table.ajax.json().total.jml
                    console.log(total);
                    if (total) {
                        $("#Total").text(`Rp ${format_nominal(total.toLocaleString())}`)
                    } else {
                        $("#Total").text(`Rp 0`)
                    }
                }
            });

            $("#btn-export").on("click", function() {
                $(".buttons-excel").trigger("click");
            });
            $('#form-search').on('submit', function(e) {
                e.preventDefault()
                table.search($('#search-keyword').val()).draw();
            });
            $("#IDCabang").on('change', function() {
                filter()
            })
            $("#IDAgen").on('change', function() {
                filter()
            })
            $("#IDPaket").on('change', function() {
                filter()
            })
            $("#IDTravel").on('change', function() {
                filter()
                setPaket()
                setCabang()
            })
            //filter
            let minDate = '',
                maxDate = '';

            let range = $(".daterangepicker").daterangepicker();
            range.val('');
            range.on('apply.daterangepicker', function(ev, picker) {
                minDate = picker.startDate.format('YYYY-MM-DD');
                maxDate = picker.endDate.format('YYYY-MM-DD');
                filter()
            });
            setPaket()
            setCabang()

            function filter() {
                let IDCabang = $("#IDCabang").val()
                let IDPaket = $("#IDPaket").val()
                let Agen = $("#IDAgen").val()
                let jamaah = $("#IDPerson").val()
                let IDTravel = $("#IDTravel").val()

                // let url = `${baseURL}admin/Laporan/get_tagihan?min=${minDate}&max=${maxDate}&cb=${IDCabang}&agen=${Agen}&jm=${jamaah}&paket=${IDPaket}&trv=${IDTravel}`

                let url = `${baseURL}admin/Laporan/get_tagihan`
                table.ajax.url(url).load();

                table.settings()[0].ajax.data = {
                    min: minDate,
                    max: maxDate,
                    cb: IDCabang,
                    agen: Agen,
                    jm: jamaah,
                    paket: IDPaket,
                    trv: IDTravel,

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
                // Requery the server with the new one-time export settings
                dt.ajax.reload();
            };

            function setPaket() {
                $("#IDPaket").empty()
                $("#IDPaket").append(`<option value="">Pilih Paket</option>`)
                travel = $("#IDTravel").val()
                $.ajax({
                    url: `${baseURL}admin/tagihan/Tagihantravel/getpaket?id=${travel}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            let option =
                                `<option value="${el.IDPaket}" >${el.NamaPaket}</option>`;
                            $("#IDPaket").append(option)
                        });
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });
            }

            function setCabang() {
                $("#IDCabang").empty()
                $("#IDCabang").append(`<option value="">Pilih Cabang</option>`)
                travel = $("#IDTravel").val()
                $.ajax({
                    url: `${baseURL}admin/tagihan/Tagihantravel/getcabang?id=${travel}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            let option =
                                `<option value="${el.IDCabang}" >${el.NamaCabang}</option>`;
                            $("#IDCabang").append(option)
                        });
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });
            }
        });
    </script>
@endpush
