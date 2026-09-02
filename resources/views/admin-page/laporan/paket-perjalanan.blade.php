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
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="col-md-4">
                                    <input type="text" class="daterangepicker form-control m-0"
                                        placeholder="Tanggal Perjalanan" style="position: static;" id="range">
                                </div>
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <form id="form-search" method="get" class="w-100">
                                        <div class="input-search d-flex align-items-center px-3">
                                            <i class="fa fa-search text-gray mr-2"></i>
                                            <input type="text" class="input-text" id="search-keyword"
                                                placeholder="Cari Data">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="table-responsive mt-4">
                                    <a id="btn-export" class="btn btn-inverse-primary float-right text-white mb-2"
                                        style="background: #1069E3;">Export Excel</a>
                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Travel</th>
                                                <th>Nama Paket</th>
                                                <th style="width: 170px;">Tujuan</th>
                                                <th>Kategori Paket</th>
                                                <th>Keberangkatan</th>
                                                <th>Kepulangan</th>
                                                <th>Jumlah Jamaah</th>
                                                <th>Jumlah Paket Tracking</th>
                                                <th>Status</th>
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
                    "action": exportAll,
                }],
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: `${baseURL}admin/Laporan/get_paketperjalanan`,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type: "POST"
                },
                columns: [{
                        data: 'No',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },
                    {
                        data: 'NamaTravel'
                    },
                    {
                        data: 'NamaPaket'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let tujuan = JSON.parse(data.KotaTujuan)
                            let kota = '';
                            if (tujuan != null && tujuan != '') {
                                tujuan.forEach((item, index) => {
                                    kota = `${kota} <li>${item}, </li>`
                                });
                            }

                            return `<ol>${kota}</ol>`
                        }
                    },
                    {
                        data: 'KategoriPaket'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return format_date_dmy(data.TglBerangkat)
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return format_date_dmy(data.TglPulang)
                        }
                    },

                    {
                        data: 'JumlahJamaah'
                    },
                    {
                        data: 'JumlahTracking'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let status = ``
                            let today = new Date()
                            let tglberangkat = new Date(data.TglBerangkat)
                            let tglpulang = new Date(data.TglPulang)

                            if (tglberangkat != null && tglberangkat != '' && tglpulang != null &&
                                tglpulang != '') {
                                if (tglberangkat > today && tglpulang > today) {
                                    status = 'Persiapan'
                                } else if (tglberangkat <= today && tglpulang >= today) {
                                    status = 'Perjalanan'
                                } else if (tglberangkat < today && tglpulang < today) {
                                    status = 'Selesai'
                                }
                            }

                            return status
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

            //filter durasi
            let minDate = '',
                maxDate = '';
            let range = $(".daterangepicker").daterangepicker();
            range.val('');
            range.on('apply.daterangepicker', function(ev, picker) {
                minDate = picker.startDate.format('YYYY-MM-DD');
                maxDate = picker.endDate.format('YYYY-MM-DD');
                filter()
            });

            function filter() {
                let trv = $("#IDTravel").val();

                table.ajax.url(`${baseURL}admin/Laporan/get_paketperjalanan`).load();

                table.settings()[0].ajax.data = {
                    startDate: minDate,
                    endDate: maxDate,
                    trv: trv
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
