@extends('layouts.master')

@push('css')
    <style>
        #datatable th.col-nama-leader,
        #datatable td.col-nama-leader {
            min-width: 320px;
            width: 320px;
            white-space: normal !important;
            word-break: break-word;
        }
    </style>
@endpush

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">


            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between flex-wrap">

                                <form id="form-search" method="GET" class="w-100">
                                    <div class="row no-gutters w-100">
                                        <div class="col-6 pr-2">
                                            <select name="status" class="form-control w-100">
                                                <option value="">Semua Status</option>
                                                <option value="1">Aktif</option>
                                                <option value="0">Nonaktif</option>
                                            </select>
                                        </div>
                                        <div class="col-6 pl-2">
                                            <div class="input-search d-flex align-items-center px-3 w-100">
                                                <i class="fa fa-search text-gray mr-2"></i>
                                                <input type="text" name="keyword" class="input-text w-100" id="search-keyword"
                                                    placeholder="Cari Data" value="{{ request('keyword') }}">
                                                <button type="submit" class="btn btn-primary ml-2">Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive mt-4">
                                    <a href="{{ route('admin.paket.tambah') }}" class="btn btn-primary float-right">
                                        Tambah
                                    </a>

                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Paket</th>
                                                <th>Tujuan Paket</th>
                                                <th class="col-nama-leader">Nama Leader</th>
                                                <th>Maskapai</th>
                                                <th>Jenis Penerbangan</th>
                                                <th>Berangkat Dari</th>
                                                <th>Landing Di</th>
                                                <th>Tanggal Berangkat</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Dokumen</th>
                                                <th>Status</th>
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
    </div>

    <div class="modal fade" id="tourModal" tabindex="-1" aria-labelledby="tourModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tourModalLabel">Tour Leader</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <select id="select-leader" class="form-control select2" multiple="multiple" required></select>

                    <input type="hidden" id="id-leader">
                    <input type="hidden" id="prev-leader">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-primary btn-submit-leader">
                        Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {

            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                scrollX: true,
                order: [],
                ajax: {
                    url: `${baseURL}admin/Paket/get`,
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: function(d) {
                        d.status = $('select[name="status"]').val();
                    }
                },
                columns: [{
                        data: 'No',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },
                    {
                        data: 'NamaPaket'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: true,
                        render: function(data, type, row) {
                            let kota = '';
                            if (data.KotaTujuan != null && data.KotaTujuan != '') {
                                let KotaTujuan = JSON.parse(data.KotaTujuan)
                                KotaTujuan.forEach((item, index) => {
                                    if (index == 0) {
                                        kota = item;
                                    } else {
                                        kota = kota + ', ' + item
                                    }
                                });
                            }
                            return kota;
                        }
                    },
                    {
                        data: 'NamaLeader',
                        className: 'col-nama-leader'
                    },
                    {
                        data: 'Maskapai'
                    },
                    {
                        data: 'JenisPenerbangan'
                    },
                    {
                        data: 'BerangkatDari'
                    },
                    {
                        data: 'LandingDi'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return format_date_ymd(data.TglBerangkat)
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return format_date_ymd(data.TglPulang)
                        }
                    },
                    {
                        data: 'Dokumen',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let checked = data.IsAktif == 1 ? 'checked' : ''
                            return `<input type="checkbox" ${checked} value="${data.IsAktif}" data-id="${data.IDPaket}" class="bs-toggle" data-toggle="toggle" data-onstyle="success" data-size="sm" data-on="Aktif" data-off="Nonaktif">`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let leader =
                                `<a class="btn btn-inverse-success btn-leader p-2 mr-2" title="Set Tour Leader" data-toggle="modal" data-target="#tourModal" data-id=${data.IDPaket} data-prevleader="${data.IDLeader ? data.IDLeader : ''}"> <i class="fa fa-users"></i> </a>`
                            let edit =
                                `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="${baseURL}admin/Paket/edit?id=${btoa(data.IDPaket)}"> <i class="fa fa-edit text-behance"></i> </a>`
                            let del =
                                `<button class="btn btn-inverse-danger btn-del p-2 mr-2" data-id="${data.IDPaket}"> <i class="fa fa-trash"></i> </button>`
                            let rndn =
                                `<a class="btn btn-inverse-warning p-2" title="Set Travel Itenerary" href="${baseURL}admin/Paket/tambah_itenerary?pkt=${btoa(data.IDPaket)}&trv=${btoa(data.IDTravel)}"> <i class="fa fa-clipboard"></i> </a>`
                            return `<div class="d-flex">${leader + edit + del + rndn}</div>`
                        }
                    }
                ],
                columnDefs: [{
                    targets: 3,
                    width: '320px'
                }],
                language: {
                    url: `${baseURL}assets/admin-page/datatable/id.json`,
                },
                "fnDrawCallback": function() {
                    $('.bs-toggle').bootstrapToggle();
                },
            });
            $('#form-search').on('submit', function(e) {
                e.preventDefault()
                table.search($('#search-keyword').val()).draw();
            });

            $('select[name="status"]').on('change', function() {
                table.draw();
            });

            $("#datatable").on('change', '.bs-toggle', function(e) {
                let isaktif = $(this).val()
                let id = $(this).data('id')
                $.ajax({
                    url: `${baseURL}admin/Paket/setaktif?isaktif=${isaktif}&id=${id}`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {

                        if (res.status) {
                            table.ajax.reload();
                            toast('success', res.message)
                        } else {
                            toast('error', res.message)
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });
            });
            $("#datatable").on('click', '.btn-del', function(e) {
                let id = $(this).data('id')
                Swal.fire({
                    title: `Anda yakin ingin menghapus data?`,
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${baseURL}admin/Paket/hapus?id=${id}`,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(res) {

                                if (res.status) {
                                    table.ajax.reload();
                                    toast('success', res.message)
                                } else {
                                    toast('error', res.message)
                                }
                            },
                            error: function(xhr, status, error) {
                                toast('error',
                                    'Data tidak dapat dihapus karena sudah digunakan transaksi'
                                )
                            }
                        });
                    }
                })

            });

            let select = $('#select-leader')
            select.select2({
                width: '100%',
                dropdownParent: $('#tourModal'),
                placeholder: 'Pilih Tour Leader',
                allowClear: true,
            })

            $(".btn-submit-leader").on('click', function(e) {
                let id = $("#id-leader").val()
                let leaders = select.val() || []
                $.ajax({
                    url: `${baseURL}admin/Paket/leader`,
                    type: 'GET',
                    data: {
                        id,
                        leaders,
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {

                        if (res.status) {
                            table.ajax.reload();
                            toast('success', res.message)
                            $('#tourModal').modal('hide');
                        } else {
                            toast('error', res.message)
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem error')
                    }
                });
            });
            $("#datatable").on('click', '.btn-leader', function(e) {
                let id = $(this).data('id')
                let prevleader = $(this).data('prevleader')
                let prevLeaders = (prevleader || '')
                    .toString()
                    .split(',')
                    .map((s) => s.trim())
                    .filter(Boolean)

                $.ajax({
                    url: `${baseURL}admin/Paket/find?id=${id}`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {


                        $("#id-leader").val(id)
                        $("#prev-leader").val(prevleader)

                        select.empty()
                        select.val(null).trigger('change')

                        if (res.status) {
                            res.data.forEach(el => {
                                let option = new Option(el.NamaJamaah, el.IDPerson, false, false)
                                select.append(option)
                            });
                            select.val(prevLeaders).trigger('change')
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', '')
                    }
                });

            });

        });
    </script>
@endpush
