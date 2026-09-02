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


                                    <div class="col-md-5">
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
                                            style="background: #1069E3;">Export Excel</a>
                                        <table id="datatable" class="table w-100">
                                            <thead class="bg-lightgray">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Nama Lengkap</th>
                                                    <th>Tanggal Register</th>
                                                    <th>No HP</th>
                                                    <th>Foto</th>
                                                    <th>Paket</th>
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
                let IDProv = '',
                    IDKab = '',
                    IDCabang = '',
                    IDAgen = '',
                    IDPaket = '';

                // DataTables
                const table = $('#datatable').DataTable({
                    dom: 'Blfrtip',
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "Semua"]
                    ],
                    buttons: [{
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 5]
                        },
                        action: exportAll,
                    }],
                    processing: true,
                    serverSide: true,
                    order: [],
                    ajax: {
                        url: `{{ url('admin/Laporan/get_jamaah') }}`,
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    },
                    columns: [{
                            data: 'No',
                            className: "text-center"
                        },
                        {
                            data: 'NamaJamaah'
                        },
                        {
                            data: null,
                            render: function(data) {
                                return format_date_dmy(data.Tgl_Register);
                            }
                        },
                        {
                            data: 'NoHP'
                        },
                        {
                            data: null,
                            render: function(data) {
                                let foto = '';
                                if (!data.FotoProfil) {
                                    foto = `{{ asset('assets/admin-page/icon/no-image.svg') }}`;
                                } else {
                                    foto = `{{ asset('assets/foto-profil') }}/${data.FotoProfil}`;
                                }
                                return `<button class="show-image btn p-0" data-foto="${foto}"><img src="${foto}" style="width:50px;height:50px;border-radius:50%;"></button>`;
                            }
                        },
                        {
                            data: 'NamaPaket'
                        }
                    ],
                    language: {
                        url: `{{ asset('assets/admin-page/datatable/id.json') }}`
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [0, 1, 2, 3, 4, 5]
                    }]
                });

                // Export Excel
                $("#btn-export").on("click", function() {
                    $(".buttons-excel").trigger("click");
                });

                // Search
                $('#form-search').on('submit', function(e) {
                    e.preventDefault();
                    table.search($('#search-keyword').val()).draw();
                });

                // Export All Function
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

                // Set Kabupaten based on Provinsi
                function setkab(prov) {
                    $("#IDKab").empty().append(`<option value="">Pilih Kabupaten</option>`);
                    $.ajax({
                        url: `{{ url('admin/Lokasi/getkab') }}/${prov}`,
                        type: 'GET',
                        success: function(res) {
                            res = JSON.parse(res);
                            res.data.forEach(el => {
                                let option = `<option value="${el.IDKab}">${el.NamaKab}</option>`;
                                $("#IDKab").append(option);
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
